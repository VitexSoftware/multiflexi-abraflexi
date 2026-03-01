# AGENTS.md

## Project Overview

This project provides AbraFlexi ERP integration support for MultiFlexi as a separate Debian-packaged addon. It produces two binary packages from one source:

- **multiflexi-abraflexi** — credential prototype for `php-vitexsoftware-multiflexi-core` (`MultiFlexi\CredentialProtoType\AbraFlexi`)
- **multiflexi-abraflexi-ui** — UI form helper for `multiflexi-web` (`MultiFlexi\Ui\CredentialType\AbraFlexi`)

## Directory Structure

- `src/MultiFlexi/CredentialProtoType/AbraFlexi.php` — core credential prototype class
- `src/MultiFlexi/Ui/CredentialType/AbraFlexi.php` — web UI credential form helper
- `src/images/AbraFlexi.svg` — logo asset
- `debian/` — Debian packaging
- `tests/` — PHPUnit tests

## Build & Test

```bash
make vendor    # install composer dependencies
make phpunit   # run tests
make cs        # fix coding standards
make deb       # build Debian packages
```

## Coding Standards

- PHP 8.1+ with strict types
- PSR-12 via ergebnis/php-cs-fixer-config
- Run `make cs` before committing

## Debian Packaging

The `debian/control` defines two binary packages with proper dependency chains:
- `multiflexi-abraflexi` depends on `php-vitexsoftware-multiflexi-core` and `multiflexi-cli (>= 2.2.0)`
- `multiflexi-abraflexi-ui` depends on `multiflexi-abraflexi` and `multiflexi-web`

The `postinst` for `multiflexi-abraflexi` runs `multiflexi-cli crprototype sync` to register the credential prototype.

## Key Classes

### MultiFlexi\CredentialProtoType\AbraFlexi
Extends `\MultiFlexi\CredentialProtoType` and implements `\MultiFlexi\credentialTypeInterface`.
Defines fields: ABRAFLEXI_URL, ABRAFLEXI_LOGIN, ABRAFLEXI_PASSWORD, ABRAFLEXI_COMPANY.

### MultiFlexi\Ui\CredentialType\AbraFlexi
Extends `\MultiFlexi\Ui\CredentialFormHelperPrototype`.
Tests server connectivity (cURL), authenticates via AbraFlexi REST API, verifies company access, and lists available companies in the web UI.
