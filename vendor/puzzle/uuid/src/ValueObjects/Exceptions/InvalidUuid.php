<?php

declare(strict_types = 1);

namespace Puzzle\ValueObjects\Exceptions;

class InvalidUuid extends \InvalidArgumentException
{
    public function __construct($uuid)
    {
        parent::__construct(sprintf(
            'Invalid uuid "%s"',
            $uuid
        ));
    }
}
