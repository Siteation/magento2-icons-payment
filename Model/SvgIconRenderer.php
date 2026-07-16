<?php declare(strict_types=1);

/**
 * Siteation - https://siteation.dev/
 * Copyright © Siteation. All rights reserved.
 * See LICENSE file for details.
 */

namespace Siteation\IconsPayment\Model;

use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

/**
 * Framework-agnostic inline-SVG renderer for the payment icon library.
 *
 * A dependency-light re-implementation of the render behaviour the storefront gets
 * from Hyvä's SvgIcons — but with NO Hyvä dependency, so the standalone checkout
 * (and any non-Hyvä context) can render the same payment marks. It reads the SVG
 * straight from this module's view/frontend/web/svg/<set>/ directory and inlines
 * it, applying width/height/class/extra attributes and a <title>, and
 * disambiguating repeated internal ids so several icons on one page never clash.
 *
 * The Hyvä-only concerns (theme asset fallback, Hyvä icon cache, Alpine attribute
 * masking) are deliberately omitted: payment marks carry no Alpine bindings and
 * are not theme-overridable here. The Hyvä package keeps those via its own
 * SvgIcons-based view models, which now resolve their assets from THIS module.
 */
class SvgIconRenderer
{
    private const MODULE = 'Siteation_IconsPayment';

    /**
     * Per-request usage count per internal SVG id, so a repeated id gets a unique
     * suffix on the second+ render (mirrors Hyvä's SvgIcons::disambiguateSvgIds).
     *
     * @var array<string,int>
     */
    private static array $idUsageCounts = [];

    private ?string $svgBaseDir = null;

    public function __construct(
        private readonly ModuleDirReader $moduleDirReader
    ) {
    }

    /**
     * Render an inline <svg> for the given icon in the given set.
     *
     * @param string $set Icon set subdirectory: default | flat | mono
     * @param string $icon Icon file name without the .svg suffix (e.g. "ideal")
     * @param string $classNames Space-separated CSS classes for the root <svg>
     * @param int|null $width Width in px, rendered as an attribute (null to omit)
     * @param int|null $height Height in px (null to omit)
     * @param array<string,mixed> $attributes Extra root attributes (key => value)
     * @return string Inline SVG markup, or '' when the icon file does not exist
     */
    public function render(
        string $set,
        string $icon,
        string $classNames = '',
        ?int $width = 24,
        ?int $height = 24,
        array $attributes = []
    ): string {
        $file = $this->getSvgBaseDir() . '/' . $set . '/' . $icon . '.svg';
        if (!is_file($file)) {
            // Payment marks are decorative — a missing icon must never break the page.
            return '';
        }

        if (!$this->isAriaHidden($attributes) && !isset($attributes['role'])) {
            $attributes['role'] = 'img';
        }

        $raw = (string) file_get_contents($file);

        return $this->disambiguateSvgIds(
            $this->applySvgArguments($raw, $classNames, $width, $height, $attributes, $icon)
        );
    }

    private function getSvgBaseDir(): string
    {
        if ($this->svgBaseDir === null) {
            $this->svgBaseDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_VIEW_DIR, self::MODULE)
                . '/frontend/web/svg';
        }
        return $this->svgBaseDir;
    }

    /**
     * Apply width/height/class/extra attributes + a <title> to the root <svg>.
     */
    private function applySvgArguments(
        string $origSvg,
        string $classNames,
        ?int $width,
        ?int $height,
        array $attributes,
        string $icon
    ): string {
        $svgXml = new \SimpleXMLElement($origSvg);

        if (trim($classNames)) {
            $svgXml['class'] = $classNames;
        }
        if ($width) {
            $svgXml['width'] = (string) $width;
        }
        if ($height) {
            $svgXml['height'] = (string) $height;
        }

        foreach ($attributes as $key => $value) {
            if (!empty($key) && $key !== 'title' && !isset($svgXml[strtolower($key)])) {
                $svgXml[strtolower($key)] = is_bool($value)
                    ? ($value ? 'true' : 'false')
                    : (string) $value;
            }
        }

        if (!$this->isAriaHidden($attributes) && !$this->hasTitle($svgXml)) {
            $svgXml->addChild('title', htmlspecialchars((string) ($attributes['title'] ?? $icon)));
        }

        return $this->stripXmlDeclaration((string) $svgXml->asXML());
    }

    /**
     * Give repeated internal ids a unique suffix (and rewrite their #references),
     * so rendering several icons that share an internal id on one page is safe.
     */
    private function disambiguateSvgIds(string $svgContent): string
    {
        $svgXml = new \SimpleXMLElement($svgContent);
        $idAttributes = $svgXml->xpath('//@id') ?: [];
        $replacements = [];

        foreach ($idAttributes as $idAttr) {
            $id = (string) $idAttr->id;
            if (isset(self::$idUsageCounts[$id])) {
                $uniqueId = $id . '_' . (++self::$idUsageCounts[$id]);
                $replacements['/#' . preg_quote($id, '/') . '\b/'] = '#' . $uniqueId;
                $idAttr->id = $uniqueId;
            } else {
                self::$idUsageCounts[$id] = 1;
            }
        }

        $svgContent = $this->stripXmlDeclaration((string) $svgXml->asXML());

        return $replacements
            ? (string) preg_replace(array_keys($replacements), array_values($replacements), $svgContent)
            : $svgContent;
    }

    private function hasTitle(\SimpleXMLElement $svgXml): bool
    {
        foreach ($svgXml->children() as $child) {
            if ($child->getName() === 'title') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    private function isAriaHidden(array $attributes): bool
    {
        return array_key_exists('aria-hidden', $attributes)
            && ($attributes['aria-hidden'] === true || $attributes['aria-hidden'] === 'true');
    }

    private function stripXmlDeclaration(string $xml): string
    {
        return str_replace("<?xml version=\"1.0\"?>\n", '', $xml);
    }
}
