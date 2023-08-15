<?php
/**
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 */
declare(strict_types=1);

namespace BladL\NovaPoshta\Decorators\Enums;

/**
 * CounterpartyListItem person type
 */
enum CounterpartyPersonType: string
{
    case Sender = 'Sender';
    case Recipient = 'Recipient';
    case ThirdPerson = 'ThirdPerson';
    public function toString(): string
    {
        return $this->value;
    }
}
