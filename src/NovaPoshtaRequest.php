<?php

declare(strict_types=1);

namespace BladL\NovaPoshta;

use BladL\NovaPoshta\DataAdapters\ResponseContainer;
use BladL\NovaPoshta\Exception\BadFieldValueException;
use BladL\NovaPoshta\Exception\QueryFailed\BadBodyException;
use BladL\NovaPoshta\Exception\QueryFailed\CurlException;
use BladL\NovaPoshta\Exception\QueryFailed\ErrorResultException;
use BladL\NovaPoshta\Exception\QueryFailed\JsonParseException;
use BladL\NovaPoshta\Exception\QueryFailed\QueryFailedException;
use BladL\NovaPoshta\Normalizer\ObjectNormalizer;
use JsonException;
use Psr\Log\LoggerInterface;
use function is_array;
use function is_bool;

/**
 * @internal
 */
final readonly class NovaPoshtaRequest
{
    public function __construct(
        private string          $payload,
        private LoggerInterface $logger,
        private int             $timeout
    ) {
    }

    /**
     * @return ObjectNormalizer<BadFieldValueException>
     * @throws BadBodyException
     * @throws ErrorResultException
     * @throws JsonParseException
     */
    private function validateResponse(string $result): ObjectNormalizer
    {

        try {
            $resp = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->critical('Failed to decode response.', [
                'output' => $result,
                'jsonMsg' => $e->getMessage(),
            ]);
            throw new JsonParseException($result, $e);
        }
        if (!is_array($resp)) {
            throw new BadBodyException('Response is not array');
        }
        if (isset($resp['errors'])) {
            $errors = $resp['errors'];
            if (!is_array($errors) || !array_is_list($errors)) {
                throw new BadBodyException('Errors is not list');
            }
            $errorCodes = $resp['errorCodes'];
            if (!is_array($errorCodes) || !array_is_list($errorCodes)) {
                throw new BadBodyException('Error codes is not list');
            }
            if (!empty($errors)) {
                $this->logger->error('NovaPoshta logical error', [
                    'errors' => $errors,
                ]);
                throw new ErrorResultException($errors, $errorCodes);
            }
        }
        return new ObjectNormalizer($resp, exceptionFactory: new BadFieldValueExceptionFactory());
    }

    /**
     * @throws QueryFailedException
     */
    public function handle(): ResponseContainer
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.novaposhta.ua/v2.0/json/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_POSTFIELDS => $this->payload,
            CURLOPT_HTTPHEADER => ['content-type: application/json'],
        ]);
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $errNo = curl_errno($curl);
        curl_close($curl);
        if ($err || $errNo || is_bool($result)) {
            $this->logger->alert('NovaPoshta cURl error', [
                'curlErr' => $err,
                'curlErrNo' => $errNo,
                'output' => $result,
            ]);
            throw new CurlException($err, $errNo);
        }
        $this->logger->debug('NovaPoshta service responded', ['output' => $result]);

        return new ResponseContainer($this->validateResponse($result));
    }
}
