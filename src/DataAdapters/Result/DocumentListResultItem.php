<?php

declare(strict_types=1);

namespace BladL\NovaPoshta\DataAdapters\Result;

use BladL\NovaPoshta\Decorators\Enums\DocumentStatusCode;
use BladL\NovaPoshta\DataAdapters\Entities\Document\Information;
use BladL\NovaPoshta\DataAdapters\Entities\Traits\Ref;
use UnexpectedValueException;

final readonly class DocumentListResultItem extends Information
{
    use Ref;

    public function isPrinted(): bool
    {
        return $this->data->bool('Printed');
    }

    public function getWeight(): float
    {
        return $this->data->float('Weight');
    }

    public function getDocumentNumber(): string
    {
        return $this->getField('IntDocNumber');
    }

    public function getShippingCost(): float
    {
        return $this->data->float('CostOnSite');
    }

    public function getAssessedCost(): float
    {
        return $this->data->float('Cost');
    }

    public function getStatusCode(): DocumentStatusCode
    {
        return DocumentStatusCode::from(
            $this->data->nullOrInt('StateId') ?? throw new UnexpectedValueException('StateId is null')
        );
    }

    public function getStateName(): string
    {
        return $this->getField('StateName');
    }

    public function getScanSheetNumber(): ?string
    {
        return $this->data->nullOrString('ScanSheetNumber');
    }
}
