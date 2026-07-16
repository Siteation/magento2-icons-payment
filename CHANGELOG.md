# Changelog

All notable changes to this package are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and
this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0]

### Added
- Initial release. The framework-agnostic payment-icon library, split out of
  `siteation/magento2-hyva-icons-payment` so the SVG marks can render in any Magento 2
  context (Luma, Hyvä, Nebula, or a standalone/framework-agnostic surface like the
  Siteation checkout) with no Hyvä dependency.
- `Model\SvgIconRenderer` — a dependency-light inline-SVG renderer (size / class /
  attributes / `<title>`, id-disambiguation, missing-icon safe).
- `ViewModel\PaymentIcons`, `PaymentIconsFlat`, `PaymentIconsMono` — plain
  `ArgumentInterface` view models (one per style), with the `renderHtml()` contract +
  magic per-icon accessors on `PaymentIconsInterface`.
- The `default` / `flat` / `mono` SVG sets (40 marks each).
