<?php

declare(strict_types=1);

namespace Support\Response;

use Support\Exceptions\JsonException;
use Support\Exceptions\UnknownResponseCodeException;

class JsonResponse implements ResponseInterface
{
    protected const VERSION = 1.1;

    protected const CONTENT_TYPE = 'application/json';

    protected const RESULT = true;

    protected const CODES = [
        100,
        101,
        102,
        103,

        200,
        201,
        202,
        203,
        204,
        205,
        206,
        207,
        208,
        226,

        300,
        301,
        302,
        303,
        304,
        305,
        307,
        308,

        400,
        401,
        402,
        403,
        404,
        405,
        406,
        407,
        408,
        409,
        410,
        411,
        412,
        413,
        414,
        415,
        416,
        417,
        418,
        421,
        422,
        423,
        424,
        425,
        426,
        428,
        429,
        431,
        451,

        500,
        501,
        502,
        503,
        504,
        505,
        506,
        507,
        508,
        510,
        511
    ];

    /**
     * @var string
     */
    protected $data;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $headers;

    public function __construct(array $data, int $code = 200, string $message = null)
    {
        $this->data = $data;
        $this->setCode($code);
        $this->message = $message;
        $this->headers = ['Content-Type' => static::CONTENT_TYPE];
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function send(): void
    {
        $this->setResponseCode();

        $this->sendHeaders();

        $this->sendContent();
    }

    private function setResponseCode(): void
    {
        \http_response_code($this->code);
    }

    private function sendHeaders(): void
    {
        foreach ($this->headers as $name => $value) {
            \sendHeader("{$name}: {$value}");
        }
    }

    private function sendContent(): void
    {
        echo $this->getJsonContent();
    }

    private function getResponseData(): array
    {
        return [
            'result' => static::RESULT,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    private function setCode(int $code): self
    {
        if (! \in_array($code, static::CODES, true)) {
            throw new UnknownResponseCodeException("Invalid response code: {$code}");
        }

        $this->code = $code;

        return $this;
    }

    private function getJsonContent(): string
    {
        $json = \json_encode($this->getResponseData(), empty($this->getData()) ? \JSON_FORCE_OBJECT : 0);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new JsonException(\sprintf('Can not encode $data to json, because %s', \json_last_error_msg()));
        }

        return $json;
    }
}
