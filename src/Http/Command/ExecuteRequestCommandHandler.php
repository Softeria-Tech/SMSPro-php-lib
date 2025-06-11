<?php

declare(strict_types=1);

namespace Smspro\Sms\Http\Command;

use GuzzleHttp\ClientInterface;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Exception\HttpClientException;
use Smspro\Sms\Http\Client;
use Smspro\Sms\Http\Response;
use Smspro\Sms\Lib\Utils;

class ExecuteRequestCommandHandler
{
    public function __construct(
        private readonly ?Client $client = null,
        private readonly ?ClientInterface $httpClient = null
    ) {
    }

    public function handle(ExecuteRequestCommand $command): Response
    {
        if (null === $command->credential) {
            throw new SmsproException('Credentials are missing !');
        }
        $client = $this->client ?? new Client($command->endpoint, $command->credential->toArray());
        $data = $command->data;

        if (array_key_exists('encrypt', $data)) {
            unset($data['encrypt']);
        }
        if (array_key_exists('mobiles', $data)) {
            $data['mobiles'] = Utils::formatRecipient($data['mobiles']);
        }

        try {
            return $client->performRequest($command->type, $data, $command->headers, $this->httpClient);
        } catch (HttpClientException $exception) {
            throw new SmsproException(
                ['_error' => $exception->getMessage()],
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
