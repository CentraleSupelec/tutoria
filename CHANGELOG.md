# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## Added
- Sending errors from controllers to the front-end and showing them to the user when submitting a form

## [1.1.0] - 2023-09-19

## Added
- LTI authentication for tutees (with account creation if it does not exist) !17
- Logout button in Sonata Admin !20

## Fixes
- Moved Homepage to /accueil !20

## [1.0.1] - 2023-09-14

### Fixes
- `phpredis` version fixed to 5.3.7 in `Dockerfile` !19
- CAS response processing adjusted to work with UPSaclay SSO !19

## [1.0.0] - 2023-09-13

### Added

- Initial release of application
- Tutee dashboard
- Tutor dashboard
- Admin sonata
