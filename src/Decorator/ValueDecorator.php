<?php

declare(strict_types=1);

namespace BladL\NovaPoshta\Decorator;

use BladL\NovaPoshta\DataAdapters\Entity;
use BladL\NovaPoshta\Exception\BadFieldValueException;
use Throwable;

use function is_array;

/**
 * @template T of Throwable
 */
final readonly class ValueDecorator
{
    /**
     * @param mixed $data
     * @param BadFieldExceptionFactoryInterface<T> $exceptionFactory
     */
    public function __construct(
        private mixed                             $data,
        private BadFieldExceptionFactoryInterface $exceptionFactory
    ) {
    }

    /**
     * @throws T
     * @return ObjectDecorator<T>
     */
    public function object(): ObjectDecorator
    {
        $value = $this->data;
        if (is_array($value)) {
            /**
             * @var array<string,mixed> $value
             */
            return new ObjectDecorator($value, exceptionFactory: $this->exceptionFactory);
        }

        throw new $this->exceptionFactory->createBadFieldException('Field is not object');
    }

    /**
     * @return  list<ObjectDecorator<T>>
     * @throws T
     */
    public function objectList(): array
    {
        $list = [];
        foreach ($this->data as $item) {
            $list[] = new ObjectDecorator($item, exceptionFactory: $this->exceptionFactory);
        }
        return $list;
    }

    /**
     * @throws T
     */
    public function float(): float
    {
        return (float)$this->scalar();
    }

    /**
     * @throws T
     */
    public function integer(): int
    {
        return (int)$this->scalar();
    }

    /**
     * @return list<ValueDecorator<T>>
     * @throws T
     */
    public function list(): array
    {
        $list = [];
        $data = $this->data;
        if (!is_array($data) || !array_is_list($data)) {
            throw new $this->exceptionFactory->createBadFieldException('Field is not list');
        }
        foreach ($data as $datum) {
            $list[] = new ValueDecorator($datum, exceptionFactory: $this->exceptionFactory);
        }

        return $list;
    }

    /**
     * @throws T
     */
    public function bool(): bool
    {
        return (bool)$this->scalar();
    }

    /**
     * @throws T
     */
    public function string(): string
    {
        return (string)$this->scalar();
    }

    /**
     * @throws T
     */
    public function nullableScalar(): string|float|int|null|bool
    {
        $value = $this->data;
        if (!is_scalar($value) && null !== $value) {
            throw new $this->exceptionFactory->createBadFieldException('Field is not scalar');

        }
        return $value;
    }


    /**
     * @throws T
     */
    public function scalar(): string|float|int|bool
    {
        $value = $this->nullableScalar();
        if (null === $value) {
            throw new $this->exceptionFactory->createBadFieldException('Field is null');
        }
        return $value;
    }


}
