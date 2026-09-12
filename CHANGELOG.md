# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

[Unreleased]: https://github.com/Siteation/magento2-icons-payment/compare/1.0.1...main

## 1.0.1 - 2026-09-12

### Fixed

- Icons now live in `view/base/web/svg`, so they resolve in every area. Magento falls
  back from `<module>/view/<area>/web` to `<module>/view/base/web` and has no path from
  adminhtml into `view/frontend`, which left the icons unreachable in adminhtml.
  Nothing changes for callers, who address the icons as `Siteation_IconsPayment::svg`.

## 1.0.0 - 2026-09-12

Initial release 🎉
