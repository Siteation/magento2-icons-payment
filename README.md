# Siteation - Magento 2 Payment Icons

[![Packagist Version](https://img.shields.io/packagist/v/siteation/magento2-icons-payment?style=for-the-badge)](https://packagist.org/packages/siteation/magento2-icons-payment)
![Supported Magento Versions](https://raw.githubusercontent.com/Siteation/.github/main/assets/badges/magento-2.4-support.png)
[![Hyvä Themes Supported](https://img.shields.io/badge/Hyva_Themes-Supported-3df0af.svg?longCache=true&style=for-the-badge)](https://hyva.io/)
[![License](https://raw.githubusercontent.com/Siteation/.github/main/assets/badges/license.png)](https://github.com/Siteation/magento2-icons-payment/blob/main/LICENSE)

This Magento 2 module adds payment method icons to your storefront, as a library of
40 icons in three styles, with view models to render them.

Unlike most icon packs, this one is not tied to a frontend. It needs nothing but
`magento/framework`, so the icons render in Luma, Nebula, a custom theme or a
standalone checkout. Install it alongside Hyvä and the same call renders inline SVG
through Hyvä's own `SvgIcons`, so a Hyvä store keeps the theme fallback, icon cache
and Alpine handling it already has.

## Installation

Install the package via:

```bash
composer require siteation/magento2-icons-payment
bin/magento setup:upgrade
```

## How to use

Each style has its own view model, so you pick the look by picking the view model:

| Style     | View model                                          | Look                     |
| --------- | --------------------------------------------------- | ------------------------ |
| `default` | `Siteation\IconsPayment\ViewModel\PaymentIcons`     | Card with the brand logo |
| `flat`    | `Siteation\IconsPayment\ViewModel\PaymentIconsFlat` | Flat brand logo          |
| `mono`    | `Siteation\IconsPayment\ViewModel\PaymentIconsMono` | Monochrome               |

### Preview of each style

#### Default

![ideal-logo] ![maestro-logo] ![paypal-logo]

[ideal-logo]: ./view/base/web/svg/default/ideal.svg
[maestro-logo]: ./view/base/web/svg/default/maestro.svg
[paypal-logo]: ./view/base/web/svg/default/paypal.svg

#### Flat

![ideal-logo-flat] ![maestro-logo-flat] ![paypal-logo-flat]

[ideal-logo-flat]: ./view/base/web/svg/flat/ideal.svg
[maestro-logo-flat]: ./view/base/web/svg/flat/maestro.svg
[paypal-logo-flat]: ./view/base/web/svg/flat/paypal.svg

#### Mono

The monochrome icons are drawn with `currentColor`, so they take the text colour of
whatever you place them in. They are not previewed here for that reason.

## Get Payment Icons in your own Template blocks.

First get the view model in your template, using the following sample:

<details open><summary>Hyva - Sample Phtml file head</summary>

```php
<?php declare(strict_types=1);

use Hyva\Theme\Model\ViewModelRegistry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Escaper;
use Siteation\IconsPayment\ViewModel\PaymentIcons;

/** @var ViewModelRegistry $viewModels */
/** @var Template $block */
/** @var Escaper $escaper */

/** @var PaymentIcons $paymentIcons */
$paymentIcons = $viewModels->require(PaymentIcons::class);
```

</details>

<details><summary>Luma - Sample Phtml file head</summary>

_For Luma templates, pass the view model as a block argument in your layout XML:_

```xml
<block name="my.block" template="My_Module::icons.phtml">
    <arguments>
        <argument name="payment_icons" xsi:type="object">Siteation\IconsPayment\ViewModel\PaymentIcons</argument>
    </arguments>
</block>
```

```php
<?php declare(strict_types=1);

use Magento\Framework\View\Element\Template;
use Magento\Framework\Escaper;
use Siteation\IconsPayment\ViewModel\PaymentIcons;

/** @var Template $block */
/** @var Escaper $escaper */

/** @var PaymentIcons $paymentIcons */
$paymentIcons = $block->getData('payment_icons');
```

</details>

After this you can render any icon in your phtml:

```php
<?php
// Render by name
$paymentIcons->renderHtml('ideal', 'p-1', 64, 48, ['title' => 'Pay with iDEAL']);

// Or through the per icon accessor, the same as the Heroicons in Hyva
$paymentIcons->idealHtml('p-1', 64, 48, ['title' => 'Pay with iDEAL']);
```

What comes out depends on the store, and nothing else about the call changes:

```html
<!-- with Hyva -->
<svg class="p-1" width="64" height="48" role="img">
    <title>Pay with iDEAL</title>
    …
</svg>

<!-- without -->
<img
    src="…/Siteation_IconsPayment/svg/default/ideal.svg"
    class="p-1"
    width="64"
    height="48"
    alt="Pay with iDEAL"
    loading="lazy"
    decoding="async"
/>
```

Extra attributes land on the root element either way, so `['loading' => 'eager']`
works for icons above the fold.

Sizing defaults to `24 × 24`. Pass your own width and height to scale, in any ratio:
the icons are drawn on a `24 × 16` canvas and are scaled to fit the box you give
them, never stretched to fill it.

### Accessibility

Pass a `title` when an icon is the only thing naming a payment option. It becomes the
`<title>` of an inline icon and the `alt` text of an `<img>`.

Without one the `<img>` renders `alt=""`, which is how HTML says decorative. That is
the right default for icons sitting next to a label that repeats the same name.

### What the `<img>` fallback cannot do

Two things need inline SVG, so they need a Hyvä store:

- Styling the icon internals from your stylesheet
- The `mono` style, whose `currentColor` fills render black inside an `<img>`

## Available icons

`abn-b2b-afterpay` • `alipay` • `amazonpay` • `amex` • `applepay` • `bancontact` • `banktransfer` • `belfius` • `biller` • `billie` • `billink` • `cash-on-delivery` • `creditcard` • `direct-debit` • `eps` • `giftcard` • `giropay` • `googlepay` • `ideal` • `ideal-wero` • `in-3` • `kbc` • `klarna` • `maestro` • `mastercard` • `multibanco` • `mybank` • `payconiq` • `paypal` • `paysafecard` • `przelewy-24` • `riverty` • `sepa` • `sofort` • `stripe` • `trustly` • `visa` • `vpay` • `wechatpay` • `wero`

> [!NOTE]
> `in3` and `przelewy24` are accepted as aliases of `in-3` and `przelewy-24`, so both
> spellings resolve to the same icon.

## Missing or Outdated Payment Icons?

We strive to keep this list of payment options and their logos accurate and up to date.

If you notice a missing icon or an outdated logo, please help us improve the list by
opening an issue or, even better, submitting a pull request.

## Enhance Your Store with Automatic Payment Icons

This icon pack integrates with our [StoreInfo Payments] module to automatically
display payment icons based on your configured Magento 2 payment options.

Interested, then checkout the [StoreInfo Payments] to display your Payment options
Automatically!

[StoreInfo Payments]: https://github.com/Siteation/magento2-storeinfo-payments

## More Siteation Modules

We have a whole suite of modules that add even more features to your store.

- [StoreInfo](https://github.com/Siteation/magento2-storeinfo) – Use your store information directly on the storefront.
- [StoreInfo USPS](https://github.com/Siteation/magento2-storeinfo-usps) – Display USPS details in the header, footer, and more.
- [StoreInfo Menus](https://github.com/Siteation/magento2-storeinfo-menus) – Manage static menus, like footer menus, directly from the backend.
- [StoreInfo Payments](https://github.com/Siteation/magento2-storeinfo-payments) – Show active payment methods on the frontend without manually adding them.

## Icon License

The payment icons used in this module were created by Siteation under a MIT License

## Disclaimer

All used trademarks, brands and/or names are the property of their respective owners.

The use of these trademarks, brands and/or names does not indicate endorsement of the
property holder by us, nor vice versa.
