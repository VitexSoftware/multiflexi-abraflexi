# multiflexi-abraflexi

AbraFlexi ERP integration support for [MultiFlexi](https://multiflexi.eu).

## Description

This package provides AbraFlexi credential management for MultiFlexi, split into two Debian packages:

| Package | Enhances | Provides |
|---------|----------|----------|
| `multiflexi-abraflexi` | `php-vitexsoftware-multiflexi-core` | Credential prototype with AbraFlexi URL, login, password and company fields |
| `multiflexi-abraflexi-ui` | `multiflexi-web` | Server connectivity test, authentication check, company listing |

## Credential Fields

- **ABRAFLEXI_URL** — AbraFlexi server URI (e.g. `https://demo.flexibee.eu:5434`)
- **ABRAFLEXI_LOGIN** — AbraFlexi user login (default: `winstrom`)
- **ABRAFLEXI_PASSWORD** — AbraFlexi user password (default: `winstrom`)
- **ABRAFLEXI_COMPANY** — Company database name (default: `demo`)

## UI Features

The web interface component provides:
- Server connectivity test (cURL to `/start`)
- SSL certificate validation with warnings
- Authentication test via AbraFlexi REST API
- Company listing from the server
- Company existence verification

## Installation

### From Debian packages

```bash
apt install multiflexi-abraflexi multiflexi-abraflexi-ui
```

### From source (development)

```bash
composer install
make phpunit
make cs
```

## Building Debian Packages

```bash
make deb
```

This produces `multiflexi-abraflexi_*.deb` and `multiflexi-abraflexi-ui_*.deb` in the parent directory.

## License

MIT — see [debian/copyright](debian/copyright) for details.

## MultiFlexi

[![MultiFlexi](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/)
