<?php

namespace Pure;

use Stringable;

class HTML extends Component
{

    public function __construct(Stringable | string| array ...$children)
    {
        parent::__construct('html')(...$children);
    }

    public function __toString(): string
    {
        return '<!DOCTYPE html>' . parent::__toString();
    }
}
