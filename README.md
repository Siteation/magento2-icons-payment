# Siteation, Magento 2 Payment Icons

The payment-method icon **library** (Visa, Mastercard, iDEAL, PayPal, Bancontact,
Apple Pay, Klarna, … 40 marks in three styles) plus view models that render it.

The view models are a wrapper around one decision:

| Store | `renderHtml()` gives you | Renderer |
| ----- | ------------------------ | -------- |
| Hyvä installed | inline `<svg>` | Hyvä's `SvgIcons`, pointed at this package's assets |
| anything else | `<img src="…">` | this package |

So the marks work in Luma, Nebula, a custom theme or a standalone checkout with
nothing but `magento/framework`, and a Hyvä store gets the theme fallback, icon cache
and Alpine handling it already has, without a second copy of the SVG library.

## Installation

```bash
composer require siteation/magento2-icons-payment
bin/magento setup:upgrade
```

## Icon styles

Three sets live under `view/frontend/web/svg/`:

| Set | View model | Look |
| --- | ---------- | ---- |
| `default` | `PaymentIcons` | Card with the brand mark |
| `flat` | `PaymentIconsFlat` | Flat brand mark |
| `mono` | `PaymentIconsMono` | Monochrome |

## Usage

Inject a view model as a block argument (works in any theme, no `ViewModelRegistry`
required) and call `renderHtml()`:

```xml
<!-- layout XML -->
<block name="my.block" template="My_Module::icons.phtml">
    <arguments>
        <argument name="payment_icons" xsi:type="object">Siteation\IconsPayment\ViewModel\PaymentIcons</argument>
    </arguments>
</block>
```

```php
<?php
/** @var \Siteation\IconsPayment\ViewModel\PaymentIcons $icons */
$icons = $block->getData('payment_icons');
?>
<?= /* @noEscape */ $icons->renderHtml('ideal', 'my-icon', 40, 26, ['title' => 'iDEAL']) ?>
<?= /* @noEscape */ $icons->idealHtml('my-icon', 40, 26) ?><!-- magic accessor -->
```

One method, `renderHtml(string $icon, string $classNames = '', ?int $width = 24,
?int $height = 16, array $attributes = []): string`. What it returns depends on the
store, nothing else changes:

```html
<!-- with Hyva -->
<svg class="my-icon" width="40" height="26" …><title>iDEAL</title>…</svg>

<!-- without -->
<img src="…/Siteation_IconsPayment/svg/default/ideal.svg" class="my-icon" width="40"
     height="26" alt="iDEAL" loading="lazy" decoding="async">
```

A missing icon returns `''`, so a typo never fatals a checkout. Extra `$attributes`
land on the root element either way, so `['loading' => 'eager']` works for marks above
the fold.

Sizing defaults to `24 × 16`, the intrinsic size of every mark in the library. Pass
your own width and height to scale, and keep the 3:2 ratio or the `<img>` will
stretch.

### Accessibility

Pass a `title` in `$attributes` when a mark is the only thing naming a payment
option. It becomes the `<title>` of an inline icon and the `alt` text of an `<img>`.

Without one the fallback renders `alt=""`, which is how HTML says decorative. That is
the right default for marks sitting next to a label that repeats the same name.

### What the fallback cannot do

Two things need inline SVG, so they need a Hyvä store: styling the icon internals from
your stylesheet, and the `mono` set, whose `currentColor` fills render black inside an
`<img>`.

To render the marks a store actually accepts, pair this with
`siteation/magento2-storeinfo-payments`, whose `StorePayments::getPaymentMethods()`
returns the icon codes for the store's active gateways.

## Regenerating the icon method hints

After adding/removing an SVG, refresh the `@method` hints on `PaymentIconsInterface`:

```bash
php bin/generate-icons-signatures
```
