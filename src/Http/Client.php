<?php

declare(strict_types=1);

namespace Smspro\Sms\Http;

use GuzzleHttp\Client as SmsproClient;
use Smspro\Sms\Constants;
use Smspro\Sms\Exception\HttpClientException;
use Throwable;
use Valitron\Validator;

/**
 * Class Client
 */
class Client
{
    public const GET_REQUEST = 'GET';
    public const POST_REQUEST = 'POST';

    protected ?string $endpoint = null;

    protected array $userAgent = [];

    protected array $requestVerbs = [self::GET_REQUEST => 'query', self::POST_REQUEST => 'form_params'];

    private array $authentication;

    private array $headers = [];


    /**
     * @param int $timeout > 0
     *
     * @throws HttpClientException if timeout settings are invalid
     */
    public function __construct(string $endpoint, array $hAuthentication, int $timeout = 0)
    {
        $this->endpoint = $endpoint;
        $this->authentication = $hAuthentication;
        $this->addUserAgentString($this->getFromInfo());
        $this->addUserAgentString(Constants::getPhpVersion());
    }

    public function addUserAgentString(string $userAgent): void
    {
        $this->userAgent[] = $userAgent;
    }

    /** @throws HttpClientException */
    public function performRequest(
        string $method,
        array $data = [],
        array $headers = []
    ): Response {
        $this->setHeader($headers);
        //VALIDATE HEADERS
        $hHeaders = $this->getHeaders();
        $requestMethod = strtoupper($method);
        $oValidator = new Validator(array_merge([
            'request' => $requestMethod
        ], $hHeaders));

        if (empty($this->validatorDefault($oValidator))) {
            throw new HttpClientException(json_encode($oValidator->errors()));
        }

        return $this->applyRequest($hHeaders, $data, $requestMethod);
    }

    /** @return string userAgentString */
    protected function getUserAgentString(): string
    {
        return implode(' ', $this->userAgent);
    }

    protected function getAuthKeys(): array
    {
        return $this->authentication;
    }

    protected function setHeader(array $option = []): void
    {
        $this->headers += $option;
    }

    protected function getHeaders(): array
    {
        $default = [];
        if ($hAuth = $this->getAuthKeys()) {
            $default = [
                'Pro_api_key' => $hAuth['pro_api_key'],
                'X-Api-Secret' => $hAuth['api_secret'],
                'User-Agent' => $this->getUserAgentString(),
            ];
        }

        return $this->headers += $default;
    }

    protected function getFormat(): string
    {
        $asEndPoint = explode('.', $this->endpoint);

        return end($asEndPoint);
    }

    protected function getFromInfo(): string
    {
        $identity = 'Smspro/ApiClient/';
        if (defined('WP_SMSPRO_SMS_VERSION')) {
            $sWPV = '';
            global $wp_version;
            if ($wp_version) {
                $sWPV = $wp_version;//@codeCoverageIgnore
            }
            $identity = 'WP' . $sWPV . '/SmsproClientSMS' . WP_SMSPRO_SMS_VERSION . Constants::DS;
        }

        return $identity . Constants::CLIENT_VERSION;
    }

    private function applyRequest(array $headers, array $data, string $type): Response
    {
        echo 'Request Type: ' . $type . PHP_EOL;
        echo 'Request URL: ' . $this->endpoint . PHP_EOL;
        echo 'Request Headers: ' . json_encode($headers) . PHP_EOL;
        echo 'Request Data: ' . json_encode($data) . PHP_EOL;
        try {
            $request = new SmsproClient();
            $response =$request->request(
                $type,
                $this->endpoint,
                [
                    'headers' => $headers,
                    $this->requestVerbs[$type] => $data,
                ]
            );
            echo 'Response Status Code: ' . $response->getStatusCode() . PHP_EOL;
            echo 'Response Reason Phrase: ' . $response->getReasonPhrase() . PHP_EOL;
            //echo 'Response Body: ' . $response->getBody() . PHP_EOL;
            if ($response->getStatusCode() === 200) {
                return new Response($response, $this->getFormat());
            }
            
        } catch (Throwable $exception) {
            // Log the exception or handle it as needed
            echo $exception->getTraceAsString() . PHP_EOL;
            throw new HttpClientException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
        echo 'Status Code:'.$response->getStatusCode() . ', Phrase:' . $response->getReasonPhrase();
        throw new HttpClientException('Request cannot be performed successfully !');
    }

    /** Validate request params */
    private function validatorDefault(Validator $validator): bool
    {
        $validator->rule('required', ['Pro_api_key']);
        $validator->rule('optional', ['User-Agent']);

        return $validator->rule('in', 'request', array_keys($this->requestVerbs))->validate();
    }
}
