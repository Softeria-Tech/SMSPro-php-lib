<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Base;
use Smspro\Sms\Constants;
use Smspro\Sms\Database\Repository\LogRepository;
use Smspro\Sms\Entity\RateLimitInfo;
use Smspro\Sms\Entity\TableMapping;
use Smspro\Sms\Exception\BulkSendException;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Exception\RateLimitException;
use Smspro\Sms\Interfaces\LogRepositoryInterface;
use Smspro\Sms\Lib\Utils;
use Smspro\Sms\Message;
use Smspro\Sms\Objects\FieldMapper;
use Smspro\Sms\Response;
use Generator;
use Throwable;

class BulkMessageCommandHandler
{
    private const MAX_ASYNC_RETRY = 3;

    private int $asyncRetry = 0;

    private int $counter = 0;

    private int $batchLoop = 2;

    private int $batch = 1;

    public function __construct(
        private readonly Message|Base $message,
        private readonly ?LogRepositoryInterface $logRepository = null
    ) {
    }

    /**
     * Handles the bulk message command.
     *
     * @return Generator<Message>
     */
    public function handle(BulkMessageCommand $command): Generator
    {
        $data = $command->data;
        $destinations = $this->getDestinations($data['to'], $command->callback?->bulkChunkLimit);
        unset($data['to']);
        $rawMessage = $data['message'] ?? null;

        foreach ($destinations as $destination) {
            $this->counter++;
            try {
                $personalizedContent = (new PersonalizationCommandHandler())
                    ->handle(new PersonalizationCommand($destination, $rawMessage));
                $data['message'] = $personalizedContent->message;

                $response = $this->send($data, $personalizedContent->destination);
                $this->doLog($response, $command, $data['from']);

                yield $response;
            } catch (SmsproException $exception) {
                error_log(
                    'ERROR: SMS sending failure to ' .
                    (is_array($destination) ? implode(',', $destination) : $destination) .
                    ' - ' . $exception->getMessage()
                );
            }

            if ($this->counter === $this->batchLoop) {
                $this->batchLoop += $this->batch;
                usleep(4000000);
            }
        }
    }

    private function send(array $data, string|array $destination): Response\Message
    {
        call_user_func(Constants::CLEAR_OBJECT);
        /** @var Message|Response\Message $message */
        $message = $this->message;
        foreach ($data as $key => $value) {
            $message->{$key} = $value;
        }
        $message->to = $destination;
        $this->asyncRetry = 0;

        return $this->asyncSend($message);
    }

    private function asyncSend(Message|Response\Message $message): Response\Message
    {
        try {
            return $message->send();
        } catch (RateLimitException $exception) {
            if ($this->asyncRetry > self::MAX_ASYNC_RETRY) {
                throw new BulkSendException('Too many retries');
            }

            $limitInfo = json_decode($exception->getMessage(), false);
            $rateLimitInfo = new RateLimitInfo(
                $limitInfo->limit ?? 1,
                $limitInfo->remaining ?? 0,
                $limitInfo->reset ?? time() - 1
            );

            $wait = $rateLimitInfo->reset - time();
            if ($wait >= 0 && sleep($wait) === 0) {
                $this->asyncRetry++;

                return $this->asyncSend($message);
            }
            throw new BulkSendException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    private function doLog(Response\Message $response, BulkMessageCommand $command, string $sender): void
    {
        $callback = $command->callback;
        if ($callback->dbConfig === null && $callback->driver?->getDbConfig() === null) {
            return;
        }

        $tableMapping = $callback?->tableMapping ?? TableMapping::default();
        $entryDataMapper = new FieldMapper($tableMapping, $response, $sender);

        try {
            $repository = $this->logRepository ?? new LogRepository($callback->dbConfig, $callback->driver);
            $repository->save($entryDataMapper->get());
        } catch (Throwable $exception) {
            error_log('ERROR: Handling bulk SMS: ' . $exception->getMessage());
        }
    }

    private function getDestinations(string|array $to, ?int $limit = null): array|string
    {
        $limit = $limit ?? Constants::SMS_MAX_RECIPIENTS;
        $destinations = is_array($to) && !Utils::isMultiArray($to) ? array_unique($to) : $to;

        return ($limit > 1 && !Utils::isMultiArray($destinations)) ?
            array_chunk($destinations, $limit, true) : $destinations;
    }
}
