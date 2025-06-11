<?php

declare(strict_types=1);

namespace Smspro\Sms;

use Smspro\Sms\Lib\Utils;
use LogicException;

use const PHP_VERSION_ID;

/**
 * Class Constants
 *
 * Provides constants and utility functions for the Smspro SMS package.
 */
class Constants
{
    /** Client-specific constants. */
    public const CLIENT_VERSION = '1.0.0';

    public const CLIENT_TIMEOUT = 30; // 30 seconds

    public const MIN_PHP_VERSION = 80100;

    /** API-related constants. */
    public const END_POINT_URL = 'https://sms.softeriatech.com/api';

    public const END_POINT_VERSION = 'v1/bulksms';

    public const JSON_RESPONSE_FORMAT = 'json';

    /** Resource-related constants. */
    public const RESOURCE_VIEW = 'view';

    public const RESOURCE_BALANCE = 'units';

    public const RESOURCE_TOP_UP = 'topup';

    public const RESOURCE_SEND_SMS = 'send';

    /** SMS-related constants. */
    public const SMS_MAX_RECIPIENTS = 50;

    public const PERSONALIZE_MSG_KEYS = ['%NAME%'];

    /** Miscellaneous constants. */
    public const DS = '/';

    public const CLEAR_OBJECT = [Base::class, 'clear'];

    public const MAP_MOBILE = [Utils::class, 'mapMobile'];

    public const CREDENTIAL_ELEMENTS = ['pro_api_key', 'api_secret'];

    /**
     * Returns the PHP version.
     *
     * @throws LogicException when the PHP version is below the minimum required.
     */
    public static function getPhpVersion(): string
    {

        if (PHP_VERSION_ID < static::MIN_PHP_VERSION) {
            throw new LogicException(
                'Your PHP version belongs to a release that is no longer supported. ' .
                'You should upgrade your PHP version as soon as possible, ' .
                'as it may be exposed to un-patched security vulnerabilities',
                E_USER_ERROR
            );
        }

        return 'PHP/' . PHP_VERSION_ID;
    }

    /** Returns the SMS path. */
    public static function getSMSPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
}
