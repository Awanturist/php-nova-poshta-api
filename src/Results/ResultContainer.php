<?php

declare(strict_types=1);

namespace BladL\NovaPoshta\Results;

final class ResultContainer
{
    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getData(): array
    {
        return $this->response['data'];
    }
}
