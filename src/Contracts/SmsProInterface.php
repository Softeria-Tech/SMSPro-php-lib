<?php

namespace SofteriaTech\SmsPro\Contracts;

interface SmsProInterface
{
    /**
     * Send SMS to one or multiple recipients
     *
     * @param string|array $mobiles
     * @param string $message
     * @param string|null $senderId
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function send($mobiles, string $message, ?string $senderId = null): array;

    /**
     * Send SMS to a contact group
     *
     * @param string $groupId
     * @param string $message
     * @param string|null $senderId
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function sendToGroup(string $groupId, string $message, ?string $senderId = null): array;

    /**
     * Get account balance
     *
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function getBalance(): array;

    /**
     * Get all sender IDs
     *
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function getSenderIds(): array;

    /**
     * Get all contact groups
     *
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function getGroups(): array;

    /**
     * Get a specific contact group
     *
     * @param string $groupId
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function getGroup(string $groupId): array;

    /**
     * Update a contact group
     *
     * @param string $name
     * @param string $contacts
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function updateGroup(string $name, string $contacts): array;

    /**
     * Get supported countries
     *
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function getSupportedCountries(): array;

    /**
     * Verify mobile number with OTP
     *
     * @param string $mobile
     * @param string $code
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function verifyMobile(string $mobile, string $code): array;

    /**
     * Send OTP to mobile number
     *
     * @param string $mobile
     * @param string $template
     * @param string|null $senderId
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function sendOTP(string $mobile, string $template, ?string $senderId = null): array;

    /**
     * Validate mobile number format
     *
     * @param string $mobile
     * @return array
     * @throws \SofteriaTech\SmsPro\Exceptions\SmsProException
     */
    public function validateMobile(string $mobile): array;

    /**
     * Get last API response
     *
     * @return array
     */
    public function getLastResponse(): array;
}