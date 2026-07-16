# Siteation — Magento 2 Payment Icons (framework-agnostic)

The payment-method icon **library** (Visa, Mastercard, iDEAL, PayPal, Bancontact,
Apple Pay, Klarna, … — 40 marks in three styles) plus a **plain inline-SVG
renderer**, with **no theme or framework dependency**.

Because it depends on nothing but `magento/framework`, it renders the same marks in
**any** Magento 2 context:

- **Luma** and Luma-family themes
- **Hyvä** themes (directly, or via the thin `siteation/magento2-hyva-icons-payment`
  adapter for Hyvä-CMS icon-picker support)
- **Nebula** and other custom themes
- **Framework-agnostic** surfaces such as the Siteation standalone checkout — no
  Hyvä, no Tailwind, no theme build required

This package is the single source of truth for the SVG library; the Hyvä package is a
thin adapter over it.

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

Inject a view model as a block argument (works in any theme — no `ViewModelRegistry`
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

`renderHtml(string $icon, string $classNames = '', ?int $width = 24, ?int $height =
24, array $attributes = []): string` returns inline `<svg>` markup. A missing icon
returns `''` (never fatals). Repeated internal SVG ids are disambiguated
automatically, so many icons on one page never clash.

To render the marks a store actually accepts, pair this with
`siteation/magento2-storeinfo-payments`, whose `StorePayments::getPaymentMethods()`
returns the icon codes for the store's active gateways.

## Regenerating the icon method hints

After adding/removing an SVG, refresh the `@method` hints on `PaymentIconsInterface`:

```bash
php bin/generate-icons-signatures
```
