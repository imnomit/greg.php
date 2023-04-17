<?php

namespace nomit\Console\Provider\Table;

class TableRows implements TableRowsInterface
{

    protected \Closure $generator;

    public function __construct(\Closure $generator)
    {
        $this->generator = $generator;
    }

    public function getIterator()
    {
        return ($this->generator)();
    }

}