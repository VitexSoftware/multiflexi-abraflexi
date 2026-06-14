<?php

declare(strict_types=1);

/**
 * This file is part of the MultiFlexi package
 *
 * https://multiflexi.eu/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MultiFlexi\CredentialProtoType;

/**
 * Description of AbraFlexi.
 *
 * author Vitex <info@vitexsoftware.cz>
 *
 * @no-named-arguments
 */
class AbraFlexi extends \MultiFlexi\CredentialProtoType implements \MultiFlexi\credentialTypeInterface, \MultiFlexi\checkableCredentialInterface
{
    public static string $logo = 'AbraFlexi.svg';

    public function __construct()
    {
        parent::__construct();

        // Define internal configuration fields
        $loginField = new \MultiFlexi\ConfigField('ABRAFLEXI_LOGIN', 'string', _('AbraFlexi Login'), _('AbraFlexi user login'));
        $loginField->setHint('winstrom')->setValue('winstrom');

        $passwordField = new \MultiFlexi\ConfigField('ABRAFLEXI_PASSWORD', 'string', _('AbraFlexi Password'), _('AbraFlexi user password'));
        $passwordField->setHint('winstrom')->setValue('winstrom');

        $urlField = new \MultiFlexi\ConfigField('ABRAFLEXI_URL', 'string', _('AbraFlexi Server URI'), _('AbraFlexi server URI'));
        $urlField->setHint('https://demo.flexibee.eu:5434')->setValue('https://demo.flexibee.eu:5434');

        $companyField = new \MultiFlexi\ConfigField('ABRAFLEXI_COMPANY', 'string', _('AbraFlexi Company'), _('Company to be handled'));
        $companyField->setHint('demo')->setValue('demo');

        $this->configFieldsProvided->addField($loginField);
        $this->configFieldsProvided->addField($passwordField);
        $this->configFieldsProvided->addField($urlField);
        $this->configFieldsProvided->addField($companyField);
    }

    #[\Override]
    public function prepareConfigForm(): void
    {
        // Implement the configuration form logic if needed
    }

    public function name(): string
    {
        return _('AbraFlexi');
    }

    public function description(): string
    {
        return _('AbraFlexi credential type for integration with AbraFlexi API');
    }

    #[\Override]
    public function uuid(): string
    {
        return '3f2504e0-4f89-11d3-9a0c-0305e82c3301';
    }

    #[\Override]
    public function logo(): string
    {
        return self::$logo;
    }

    #[\Override]
    public function checkAvailability(): \MultiFlexi\CredentialCheckResult
    {
        $url  = (string) ($this->configFieldsProvided->getFieldByCode('ABRAFLEXI_URL')?->getValue()      ?? '');
        $login = (string) ($this->configFieldsProvided->getFieldByCode('ABRAFLEXI_LOGIN')?->getValue()   ?? '');
        $pass  = (string) ($this->configFieldsProvided->getFieldByCode('ABRAFLEXI_PASSWORD')?->getValue() ?? '');

        if ($url === '' || $login === '' || $pass === '') {
            $missing = array_keys(array_filter([
                'ABRAFLEXI_URL'      => $url   === '',
                'ABRAFLEXI_LOGIN'    => $login === '',
                'ABRAFLEXI_PASSWORD' => $pass  === '',
            ]));

            return new \MultiFlexi\CredentialCheckResult(
                \MultiFlexi\CredentialState::Misconfigured,
                sprintf(_('Required fields not set: %s'), implode(', ', $missing)),
                time(),
            );
        }

        // Probe the AbraFlexi server root with basic auth (same endpoint apps use).
        $ch = curl_init(rtrim($url, '/').'/c/');
        curl_setopt_array($ch, [
            \CURLOPT_NOBODY         => true,
            \CURLOPT_CONNECTTIMEOUT => 5,
            \CURLOPT_TIMEOUT        => 5,
            \CURLOPT_USERPWD        => $login.':'.$pass,
            \CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($ch);
        $errno    = curl_errno($ch);
        $httpCode = (int) curl_getinfo($ch, \CURLINFO_HTTP_CODE);

        if ($errno !== 0) {
            return new \MultiFlexi\CredentialCheckResult(
                \MultiFlexi\CredentialState::Unavailable,
                sprintf(_('AbraFlexi server unreachable: %s'), curl_strerror($errno)),
                time(),
                60,
            );
        }

        if ($httpCode === 401 || $httpCode === 403) {
            return new \MultiFlexi\CredentialCheckResult(
                \MultiFlexi\CredentialState::Misconfigured,
                sprintf(_('Authentication failed (HTTP %d) — check ABRAFLEXI_LOGIN and ABRAFLEXI_PASSWORD'), $httpCode),
                time(),
            );
        }

        if ($httpCode >= 500) {
            return new \MultiFlexi\CredentialCheckResult(
                \MultiFlexi\CredentialState::Unavailable,
                sprintf(_('AbraFlexi server returned HTTP %d'), $httpCode),
                time(),
                60,
            );
        }

        return new \MultiFlexi\CredentialCheckResult(\MultiFlexi\CredentialState::Available, '', time(), 300);
    }
}
