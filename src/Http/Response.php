<?php

declare(strict_types=1);

namespace Smspro\Sms\Http;

use GuzzleHttp\Psr7\Response as Psr7Response;
use stdClass;

/**
 * Class Response
 *
 * @author Softeria Tech
 */
class Response
{
    /** @var string */
    public const BAD_STATUS = 'KO';

    /** @var string */
    public const GOOD_STATUS = 'OK';

    private const SUCCESS_HTTP_CODE = 200;

    protected array|stdClass $data;

    public function __construct(private readonly Psr7Response $response, private readonly ?string $format = null)
    {        
        $this->assertData();
    }

    public function getBody(): string
    {
        return $this->response->getBody()->getContents();
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getJson(): array
    {
        if ($this->getStatusCode() !== self::SUCCESS_HTTP_CODE) {
            $message = $this->getBody() ?: 'request failed!';

            return ['status' => static::BAD_STATUS, 'message' => $message];
        }

        return array_merge(['status' => static::GOOD_STATUS], $this->data);
    }

    private function assertData(): void
    {
        $contents= $this->getBody();
        $dataValue = json_decode($contents, true);
        if (empty($dataValue)) {
            $this->data = [];
            return;
        }
        $this->data = $dataValue;
    }

}
