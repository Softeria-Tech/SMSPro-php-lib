<?php

namespace SofteriaTech\SmsPro\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(string|array $mobiles, string $message, ?string $senderId = null)
 * @method static array sendToGroup(string $groupId, string $message, ?string $senderId = null)
 * @method static array getBalance()
 * @method static array getSenderIds()
 * @method static array getGroups()
 * @method static array getGroup(string $groupId)
 * @method static array updateGroup(string $name, string $contacts)
 * @method static array getSupportedCountries()
 * @method static array verifyMobile(string $mobile, string $code)
 * @method static array sendOTP(string $mobile, string $template, ?string $senderId = null)
 * @method static array validateMobile(string $mobile)
 * @method static array getLastResponse()
 *
 * @see \SofteriaTech\SmsPro\SmsPro
 */
class SmsPro extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'smspro';
    }
}