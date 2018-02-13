<?php

declare(strict_types = 1);

namespace Puzzle\ValueObjects;

use Puzzle\Pieces\ConvertibleToString;

class SelfValidatedUuid implements ConvertibleToString, \JsonSerializable
{
    private
        $uuid;

    public function __construct(?string $uuid = null)
    {
        if($uuid === null)
        {
            $uuid = (string) \Ramsey\Uuid\Uuid::uuid4();
        }
        else
        {
            $this->validateUuid($uuid);
        }

        $this->uuid = $uuid;
    }

    public function value(): string
    {
        return $this->uuid;
    }

    public function equals(SelfValidatedUuid $uuid): bool
    {
        return $this->uuid === $uuid->value();
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    private function validateUuid(string $string): void
    {
        $validator = \Symfony\Component\Validator\Validation::createValidator();

        $errors = $validator->validate(
            $string,
            new \Symfony\Component\Validator\Constraints\Uuid()
        );

        if (count($errors) > 0)
        {
            throw new Exceptions\InvalidUuid($string);
        }
    }

    public function jsonSerialize()
    {
        return $this->value();
    }
}
