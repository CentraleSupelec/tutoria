# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.3] - 2023-10-12

## Fixes
- 🔧 Add trusted headers for redirections !51

## [1.4.2] - 2023-10-12

## Fixes
- 🔧 Register server host and scheme in router parameters !50

## [1.4.1] - 2023-10-12

## Fixes
- 📈 Analytics tracking with matomo (do not use constant variable in javascript snippet) !49

## [1.4.0] - 2023-10-12

## Added
- 📈 Analytics tracking with matomo !48

## [1.3.2] - 2023-10-06

## Fixes
- 🚑️ Hotfix remove tutoring entity constraints on first completion !42

## [1.3.1] - 2023-10-05

## Fixes
- ✏️ Rename Academic Level to Formation in Sonata !40

## [1.3.0] - 2023-10-05

## Added
- ⬆️ Upgrade to symfony v6.3 !30
- ✨ Toast message when register/unregister !31
- 📄 Add LICENSE !33

## Fixes
- 🩹 Working online meeting link !25
- 💄 Refactor UI tutoring filter !26
- 🔥 App logo no redirect !27
- 💬 Add `AllDefaultWeekdaysHaveAtLeastOneSession` constraint + fix all validation wording !28
- 🚸 Sort tutoring sessions by start date !29
- ♻️ Refactor subscribe js fetch flow with await !31
- 💬 Changed multiple wording and added some notes !32
- ♻️ Refactor academic year in tutoring instead of academic level !34
- 🐛 Remove trailing slash on CAS logout redirect url !35
- ✏️ Changed multiple wording + ui session flex + admin tutoring show !36

## [1.2.0] - 2023-09-25

## Added
- 🥅 Sending errors from controllers to the front-end and showing them to the user when submitting a form !22

## [1.1.0] - 2023-09-19

## Added
- ✨ LTI authentication for tutees (with account creation if it does not exist) !17
- ✨ Logout button in Sonata Admin !20

## Fixes
- 🎨 Moved Homepage to /accueil !20

## [1.0.1] - 2023-09-14

### Fixes
- 📌 `phpredis` version fixed to 5.3.7 in `Dockerfile` !19
- 🔧 CAS response processing adjusted to work with UPSaclay SSO !19

## [1.0.0] - 2023-09-13

### Added

- 🚀 Initial release of application
- ✨ Tutee dashboard
- ✨ Tutor dashboard
- ✨ Admin sonata
