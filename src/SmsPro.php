<?php

namespace SofteriaTech\SmsPro;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use SofteriaTech\SmsPro\Contracts\SmsProInterface;
use SofteriaTech\SmsPro\Exceptions\SmsProException;

class SmsPro implements SmsProInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $senderId;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var array
     */
    protected $lastResponse = [];

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param string $senderId
     * @param string $baseUrl
     * @param int $timeout
     */
    public function __construct(string $apiKey, string $senderId = '', string $baseUrl = '', int $timeout = 30)
    {
        $this->apiKey = $apiKey;
        $this->senderId = $senderId;
        $this->baseUrl = $baseUrl ?: 'https://sms.softeriatech.com/api/v1/bulksms';
        $this->timeout = $timeout;

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function send($mobiles, string $message, ?string $senderId = null): array
    {
        $mobiles = $this->formatMobiles($mobiles);
        $senderId = $senderId ?: $this->senderId;

        $payload = [
            'mobiles' => $mobiles,
            'message' => $message,
            'pro_api_key' => $this->apiKey,
        ];

        if (!empty($senderId)) {
            $payload['sender_name'] = $senderId;
        }

        try {
            $response = $this->client->post('/send', [
                'json' => $payload,
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            $this->lastResponse = [
                'status' => $response->getStatusCode() === 200,
                'response' => $data,
                'raw' => $body,
            ];

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Unknown error occurred';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            // Check for error response (200 with error)
            if (isset($data['success']) && $data['success'] === false) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'SMS sending failed';
                throw new SmsProException($errorMsg, 400);
            }

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro API Error: ' . $e->getMessage(), [
                'payload' => $payload,
            ]);

            throw new SmsProException('Failed to send SMS: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendToGroup(string $groupId, string $message, ?string $senderId = null): array
    {
        // First get the group contacts
        $group = $this->getGroup($groupId);
        
        if (!$group || empty($group['contacts'])) {
            throw new SmsProException('Group not found or has no contacts');
        }

        $mobiles = array_filter(explode(',', $group['contacts']));
        
        return $this->send($mobiles, $message, $senderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance(): array
    {
        try {
            $response = $this->client->post('/units', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Failed to get balance';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            $this->lastResponse = [
                'status' => true,
                'response' => $data,
                'raw' => $body,
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Balance Error: ' . $e->getMessage());
            throw new SmsProException('Failed to get balance: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderIds(): array
    {
        try {
            $response = $this->client->post('/senderids', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Failed to get sender IDs';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            $this->lastResponse = [
                'status' => $data['status'] ?? true,
                'response' => $data,
                'raw' => $body,
                'sender_ids' => $data['data'] ?? [],
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Sender IDs Error: ' . $e->getMessage());
            throw new SmsProException('Failed to get sender IDs: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): array
    {
        try {
            $response = $this->client->post('/grouplist', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Failed to get groups';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            $this->lastResponse = [
                'status' => $data['status'] ?? true,
                'response' => $data,
                'raw' => $body,
                'groups' => $data['data'] ?? [],
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Groups Error: ' . $e->getMessage());
            throw new SmsProException('Failed to get groups: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(string $groupId): array
    {
        $groups = $this->getGroups();
        
        foreach ($groups['groups'] as $group) {
            if ((string) $group['id'] === (string) $groupId) {
                return $group;
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(string $name, string $contacts): array
    {
        try {
            $response = $this->client->post('/updatecontacts', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                    'name' => $name,
                    'contacts' => $contacts,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Failed to update group';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            $this->lastResponse = [
                'status' => $data['status'] ?? true,
                'response' => $data,
                'raw' => $body,
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Update Group Error: ' . $e->getMessage());
            throw new SmsProException('Failed to update group: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCountries(): array
    {
        try {
            $response = $this->client->post('/countries', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Failed to get countries';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            $this->lastResponse = [
                'status' => $data['status'] ?? true,
                'response' => $data,
                'raw' => $body,
                'countries' => $data['data'] ?? [],
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Countries Error: ' . $e->getMessage());
            throw new SmsProException('Failed to get countries: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function verifyMobile(string $mobile, string $code): array
    {
        try {
            $response = $this->client->post('/mverify', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                    'mobiles' => $this->formatMobile($mobile),
                    'code' => $code,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            $this->lastResponse = [
                'status' => $response->getStatusCode() === 200 && isset($data['status']) && $data['status'] === true,
                'response' => $data,
                'raw' => $body,
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Verify Error: ' . $e->getMessage());
            throw new SmsProException('Failed to verify mobile: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendOTP(string $mobile, string $template, ?string $senderId = null): array
    {
        $senderId = $senderId ?: $this->senderId;

        try {
            $response = $this->client->post('/mverify', [
                'form_params' => [
                    'pro_api_key' => $this->apiKey,
                    'mobiles' => $this->formatMobile($mobile),
                    'sender_name' => $senderId,
                    'template' => $template,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = $data['msg'] ?? $data['message'] ?? 'Failed to send OTP';
                throw new SmsProException($errorMsg, $response->getStatusCode());
            }

            $this->lastResponse = [
                'status' => $data['status'] ?? true,
                'response' => $data,
                'raw' => $body,
                'otp' => $data['data']['otp'] ?? null,
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro OTP Error: ' . $e->getMessage());
            throw new SmsProException('Failed to send OTP: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMobile(string $mobile): array
    {
        try {
            $response = $this->client->get('/mobile', [
                'query' => [
                    'mobile' => $mobile,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            $this->lastResponse = [
                'status' => $response->getStatusCode() === 200,
                'response' => $data,
                'raw' => $body,
            ];

            return $this->lastResponse;

        } catch (GuzzleException $e) {
            Log::channel(config('smspro.log_channel', 'daily'))->error('SMSPro Validate Error: ' . $e->getMessage());
            throw new SmsProException('Failed to validate mobile: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLastResponse(): array
    {
        return $this->lastResponse;
    }

    /**
     * Format mobile numbers
     *
     * @param string|array $mobiles
     * @return string
     */
    protected function formatMobiles($mobiles): string
    {
        if (is_array($mobiles)) {
            return implode(',', array_map([$this, 'formatMobile'], $mobiles));
        }

        return $this->formatMobile($mobiles);
    }

    /**
     * Format single mobile number
     *
     * @param string $mobile
     * @return string
     */
    protected function formatMobile(string $mobile): string
    {
        // Remove spaces, dashes, and plus signs
        $mobile = preg_replace('/[\s\-+]/', '', $mobile);

        // Remove leading 0 if present and not already having country code
        if (strlen($mobile) > 9 && substr($mobile, 0, 1) === '0') {
            $mobile = substr($mobile, 1);
        }

        // If number doesn't start with country code, prepend default
        $countryCode = config('smspro.default_country_code', '254');
        if (strlen($mobile) < 10 && !preg_match('/^' . $countryCode . '/', $mobile)) {
            $mobile = $countryCode . $mobile;
        }

        return $mobile;
    }
}