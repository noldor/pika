<?php

declare(strict_types=1);

namespace Support\Response;

use Support\Exceptions\JsonException;
use Support\Exceptions\UnknownResponseStatusException;

class JsonResponse implements ResponseInterface
{
    protected const VERSION = 1.1;

    protected const CONTENT_TYPE = 'application/json';

    protected const RESULT = true;

    protected const STATUSES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $headers;

    public function __construct(array $content = null, int $status = 200, string $message = '', array $headers = [])
    {
        $this->setStatus($status);
        $this->content = $content;
        $this->message = $message;
        $this->headers = \array_merge(
            ['Content-Type' => static::CONTENT_TYPE],
            $headers
        );
    }

    public function send(): void
    {
        $this->setResponseCode();

        $this->sendHeaders();

        $this->sendContent();
    }

    private function setResponseCode(): void
    {
        \http_response_code($this->status);
    }

    private function sendHeaders(): void
    {
        if (\headers_sent()) {
            return;
        }

        foreach ($this->headers as $name => $value) {
            \header("{$name}: {$value}", true);
        }
    }

    private function sendContent(): void
    {
        echo $this->getJsonContent();
    }

    private function getData(): array
    {
        return [
            'result' => static::RESULT,
            'message' => $this->message,
            'data' => $this->content
        ];
    }

    private function setStatus(int $status): self
    {
        if (! \array_key_exists($status, static::STATUSES)) {
            throw new UnknownResponseStatusException("Invalid response code: {$status}");
        }

        $this->status = $status;

        return $this;
    }

    private function getJsonContent(): string
    {
        $json = \json_encode($this->getData());

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new JsonException(\sprintf('Can not encode $data to json, because %s', \json_last_error_msg()));
        }

        return $json;
    }
}
