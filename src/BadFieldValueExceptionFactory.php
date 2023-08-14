<?php
declare(strict_types=1);

namespace BladL\NovaPoshta;

use BladL\NovaPoshta\Normalizer\BadFieldExceptionFactoryInterface;
use BladL\NovaPoshta\Exception\BadFieldValueException;

/**
 * @implements BadFieldExceptionFactoryInterface<BadFieldValueException>
 */
final class BadFieldValueExceptionFactory implements BadFieldExceptionFactoryInterface
{

    public function createBadFieldException(string $message): BadFieldValueException
    {
        return new BadFieldValueException($message);
    }
}
