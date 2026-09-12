# Changelog

All notable changes to this package are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and
this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0]

### Added
- Initial release. The payment-icon library, split out of
  `siteation/magento2-hyva-icons-payment` so the SVG marks can render in any Magento 2
  context (Luma, Hyvä, Nebula, or a standalone surface like the Siteation checkout).
- `ViewModel\PaymentIcons`, `PaymentIconsFlat`, `PaymentIconsMono` — plain
  `ArgumentInterface` view models (one per style), with the `renderHtml()` contract on
  `PaymentIconsInterface` plus magic per-icon accessors.
- `renderHtml()` hands the icon to Hyvä's `SvgIcons` where that class is installed, so
  a Hyvä store gets inline SVG with the theme fallback, icon cache and Alpine handling
  it already has, from the assets this package ships. Without Hyvä the mark falls back
  to an `<img>` at the static file, which needs nothing but `magento/framework`.
- The `default` / `flat` / `mono` SVG sets (40 marks each).

### Notes
- Hyvä's `SvgIcons` is constructed directly from injected `magento/framework`
  services, not fetched from the object manager, so the optional dependency costs no
  service locator. Being outside DI, it cannot be plugged in or preferenced.
- Sizing defaults to `24 × 16`, the intrinsic size of every mark.
- The `<img>` fallback cannot reach inside the document, so the `mono` set renders
  black and CSS cannot address the shapes on a store without Hyvä.
