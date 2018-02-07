# Changelog

All Notable changes to `unitarum` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased] - YYYY-MM-DD

### Added
- Method for TRUNCATE tables

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## [0.3.1] - 2018-01-29
### Fixed
- Get all mysql rows from schema

## [0.3.0] - 2018-01-28
### Fixed
- Move sqlite to adapter
### Added
- Mysql adapter

## [0.2.2] - 2018-01-22
- [#11] Removed char ` in table name.
- [#12] Enable PDO exceptions. Removed custom exceptions from DataBase.
- [#13] Added db exception.

## [0.2.1] - 2018-01-20
- [#6] Check string length in hydrator.
- [#7] Not marge array without second entity.
- [#8] Show sql in exception message.

## [0.2] - 2018-01-19

### Added
- Documentation.
- License.
- Prepare for packegist.

## [0.1] - 2018-01-18

### Added
- Base structure.
- Working with sqlite.
- Working with entities.
- Unit tests.