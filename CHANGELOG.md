# Lorient Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 0.1.11-13 - 2021-06-17
### Fixed
- Address fallback issue (owner not integer)
### Added
- getSamplesByOrder and getSamplesByOrder variables

## 0.1.11-12 - 2021-06-07
### Fixed
- (string) to UpdateAll (SQL Strict errors)

## 0.1.10 - 2021-06-07
### Added
- Spam prevention methods (Mailboxlayer) newsletter sign-up

## 0.1.9 - 2020-01-28
### Fixed
- Orders->mailOrder(): missing `{`

## 0.1.8 - 2020-01-27
### Fixed
- Orders->mailOrder(): check if $specs is iterable/array

## 0.1.7 - 2019-11-27
### Added
- Add session for email list submissions

## 0.1.6 - 2019-06-20
### Added
- Colour lookup for order emails

## 0.1.5 - 2019-06-20
### Added
- Add snaptcha exclusion rules to controllers

## 0.1.4 - 2019-05-17
### Changed
- Update `orders/place` method (remove confirm step)

## 0.1.3 - 2019-05-09
### Changed
- CC order confirmation email and improved logging (from)

## 0.1.2 - 2019-05-03
### Fixed
- array check on implode (orders->mailOrder()) 

## 0.1.1 - 2019-05-03
### Changed
- add logging to order emails (orders->mailOrder()) 

## 0.1.0 - 2019-04-26
### Added
- new `paginateQuery` variable to build pagination that works with GET parameters

## 0.0.16-17 - 2019-04-26
### Fixed
- order response via JSON

## 0.0.14-15 - 2019-04-12
### Fixed
- issue retrieving non-entry elements when querying cart

## 0.0.13 - 2019-04-10
### Fixed
- issue retrieving non-entry elements when querying cart

## 0.0.12 - 2019-03-21
### Fixed
- favourites variables - check if element exists before returning

## 0.0.11 - 2019-03-21
### Fixed
- cart variables - check if element exists before returning

## 0.0.10 - 2019-03-15
### Changed
- saveAddress() controller updated to check if exists in Order records

## 0.0.9 - 2019-03-13
### Fixed
- getSavedOrders() variable updated

## 0.0.8 - 2019-03-12
### Added
- Ordering functionality added for context (eg saved brochures)

## 0.0.7 - 2019-03-11
### Added
- Order email functionality

## 0.0.1 - 2018-04-27
### Added
- Initial release
