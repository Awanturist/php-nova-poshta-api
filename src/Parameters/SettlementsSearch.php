<?php

declare(strict_types=1);

namespace BladL\NovaPoshta\Parameters;

use BladL\NovaPoshta\Decorator\ParametersDecorator;

class SettlementsSearch extends ParametersDecorator
{
    public function __construct(string $string, int $limit)
    {
        $this->setStr('CityName', $string);
        $this->setInt('Limit', $limit);
        $this->setInt('Page', 1);
    }
}
