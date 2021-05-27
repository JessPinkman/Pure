<?php

namespace Pure;

use Closure;
use Stringable;

class HTML extends Component
{

    public function __construct(Stringable|string|array|Closure|null ...$children)
    {
        parent::__construct('html');
        $this(...$children);
    }

    public function __toString(): string
    {
        return '<!DOCTYPE html>' . parent::__toString();
    }
}
