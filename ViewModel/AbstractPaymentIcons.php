<?php declare(strict_types=1);

/**
 * Siteation - https://siteation.dev/
 * Copyright © Siteation. All rights reserved.
 * See LICENSE file for details.
 */

namespace Siteation\IconsPayment\ViewModel;

use Hyva\Theme\ViewModel\SvgIcons;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Base view model for a payment icon set (default / flat / mono), bound to its set
 * through di.xml.
 *
 * Hyvä's SvgIcons renders the mark inline where that package is installed. Without it
 * the mark falls back to an <img> at the static file, which needs nothing but
 * magento/framework, but cannot be reached from a stylesheet, so the mono set renders
 * black there.
 */
abstract class AbstractPaymentIcons implements ArgumentInterface, PaymentIconsInterface
{
    private const ICON_PATH_PREFIX = 'Siteation_IconsPayment::svg';

    private ?SvgIcons $hyvaIcons;

    public function __construct(
        private readonly AssetRepository $assetRepository,
        CacheInterface $cache,
        DesignInterface $design,
        ScopeConfigInterface $scopeConfig,
        private readonly string $iconSet = 'default'
    ) {
        // Injecting SvgIcons would make Hyvä a hard dependency, since DI resolves an
        // argument by its type even when it is nullable. Everything it needs is a
        // framework service, so build it here where the class is installed.
        $this->hyvaIcons = class_exists(SvgIcons::class)
            ? new SvgIcons(
                assetRepository: $assetRepository,
                cache: $cache,
                design: $design,
                iconPathPrefix: self::ICON_PATH_PREFIX,
                iconSet: $iconSet,
                scopeConfig: $scopeConfig
            )
            : null;
    }

    /**
     * @param string $icon Icon file name without .svg (e.g. "ideal")
     * @param string $classNames Space-separated CSS classes for the root element
     * @param int|null $width Width in px (null or 0 to omit the attribute)
     * @param int|null $height Height in px (null or 0 to omit)
     * @param array<string,mixed> $attributes Extra attributes (key => value). A
     *        "title" entry names the icon, as a <title> inline or as alt text.
     * @return string Markup, or an empty string when the icon does not exist
     */
    public function renderHtml(
        string $icon,
        string $classNames = '',
        ?int $width = 24,
        ?int $height = 24,
        array $attributes = []
    ): string {
        // Icons are file names. Anything else would reach outside the set.
        if (!preg_match('/^[a-z0-9-]+$/', $icon)) {
            return '';
        }

        try {
            return $this->hyvaIcons?->renderHtml($icon, $classNames, $width, $height, $attributes)
                ?? $this->renderImage($icon, $classNames, $width, $height, $attributes);
        } catch (NotFoundException) {
            // Payment marks are decorative: a missing icon must not break checkout.
            return '';
        }
    }

    /**
     * Magic accessor behind the `@method` hints on PaymentIconsInterface, so a
     * template can call `idealHtml()` instead of `renderHtml('ideal')`.
     *
     * @param string $method
     * @param array<int,mixed> $args
     */
    public function __call(string $method, array $args): string
    {
        if (!str_ends_with($method, 'Html')) {
            return '';
        }

        // abnB2bAfterpayHtml() is the abn-b2b-afterpay icon.
        $icon = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', '-', substr($method, 0, -4));

        return $this->renderHtml(strtolower((string) $icon), ...$args);
    }

    /**
     * @param array<string,mixed> $attributes
     * @throws NotFoundException When the icon is not in this set
     */
    private function renderImage(
        string $icon,
        string $classNames,
        ?int $width,
        ?int $height,
        array $attributes
    ): string {
        $asset = $this->assetRepository->createAsset(
            sprintf('%s/%s/%s.svg', self::ICON_PATH_PREFIX, $this->iconSet, $icon)
        );
        $asset->getSourceFile();

        $html = '<img';
        foreach (array_merge([
            'src' => $asset->getUrl(),
            'class' => trim($classNames) ?: null,
            'width' => $width ?: null,
            'height' => $height ?: null,
            // An unnamed mark sits next to a label repeating it, which is what alt="" is for.
            'alt' => (string) ($attributes['title'] ?? ''),
            'loading' => 'lazy',
            'decoding' => 'async',
        ], $attributes) as $name => $value) {
            if ($name === 'title' || $value === null) {
                continue;
            }
            $value = is_bool($value) ? var_export($value, true) : (string) $value;
            $html .= sprintf(' %s="%s"', $name, htmlspecialchars($value, ENT_QUOTES));
        }

        return $html . '>';
    }
}
