<?php declare(strict_types=1);

/**
 * Siteation - https://siteation.dev/
 * Copyright © Siteation. All rights reserved.
 * See LICENSE file for details.
 */

namespace Siteation\IconsPayment\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Siteation\IconsPayment\Model\SvgIconRenderer;

/**
 * Base view model for a payment icon set (default / flat / mono).
 *
 * A plain `ArgumentInterface` view model — inject it as a block argument in any
 * theme, Hyvä or not. The concrete subclasses set their icon set via di.xml, so
 * `PaymentIconsFlat->renderHtml('ideal')` renders view/frontend/web/svg/flat/ideal.svg.
 *
 * `renderHtml()` matches the signature the storefront/checkout already call on the
 * Hyvä view models, so templates work unchanged with either package.
 */
abstract class AbstractPaymentIcons implements ArgumentInterface, PaymentIconsInterface
{
    public function __construct(
        private readonly SvgIconRenderer $renderer,
        private readonly string $iconSet = 'default'
    ) {
    }

    /**
     * @param string $icon Icon file name without .svg (e.g. "ideal")
     * @param string $classNames Space-separated CSS classes for the root <svg>
     * @param int|null $width Width in px (null to omit the attribute)
     * @param int|null $height Height in px (null to omit)
     * @param array<string,mixed> $attributes Extra root attributes (key => value)
     */
    public function renderHtml(
        string $icon,
        string $classNames = '',
        ?int $width = 24,
        ?int $height = 24,
        array $attributes = []
    ): string {
        return $this->renderer->render($this->iconSet, $icon, $classNames, $width, $height, $attributes);
    }

    /**
     * Magic accessor so `idealHtml()` maps to `renderHtml('ideal')` — matches the
     * Hyvä view models' ergonomics and the @method hints on PaymentIconsInterface.
     *
     * @param string $method
     * @param array<int,mixed> $args
     */
    public function __call(string $method, array $args): string
    {
        if (preg_match('/^(.*)Html$/', $method, $matches)) {
            return $this->renderHtml($this->camelCaseToKebabCase($matches[1]), ...$args);
        }
        return '';
    }

    private function camelCaseToKebabCase(string $str): string
    {
        return strtolower((string) preg_replace('/(.|[0-9])([A-Z]|[0-9])/', '$1-$2', $str));
    }
}
