<?php

namespace Puzzle\Configuration;

use Puzzle\Configuration;

class ArrayMemory extends AbstractConfiguration
{
    private
        $values;

    public function __construct(array $values)
    {
        parent::__construct();

        $this->values = $values;
    }

    public function exists($fqn)
    {
        return $this->getValue($fqn) !== null;
    }

    protected function getValue($fqn)
    {
        $keys = $this->parseDsn($fqn);
        $config = $this->values;

        while(! empty($keys))
        {
            $key = array_shift($keys);

            if(!isset($config[$key]))
            {
                return null;
            }

            $config = $config[$key];
        }

        return $config;
    }
}
