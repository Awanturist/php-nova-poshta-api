<?php

declare(strict_types=1);

namespace BladL\NovaPoshta\DataAdapters\Result;

use BladL\NovaPoshta\DataAdapters\Entities\Location\SettlementStreetItem;
use BladL\NovaPoshta\DataAdapters\Result;
use BladL\NovaPoshta\Normalizer\ObjectNormalizer;

final readonly class SearchSettlementResult extends Result
{
    /**
     * @return list<SettlementStreetItem>
     */
    public function getStreets(): array
    {
        $addresses = ($this->container->getDataAsObjectList()[0])->field('Addresses')->objectList();
        return array_map(
            static fn (ObjectNormalizer $data) => new SettlementStreetItem($data),
            $addresses
        );
    }

    public function getTotalCount(): int
    {
        return (($this->container->getDataAsObjectList()[0]))->field('TotalCount')->integer();
    }
}
