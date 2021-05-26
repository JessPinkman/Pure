<?php

namespace Pure;

use Error;
use Stringable;

class Fragment extends Component
{

    public function __construct(Stringable| string | array ...$children)
    {
        $this(...$children);
    }

    public function __toString(): string
    {
        $html = '';
        foreach ($this->children as $element) {
            $html .=  $element;
        }

        return $html;
    }

    public function __call($key, $args): static
    {
        throw new Error("method $key not found, cannot set attributes for Fragments");
        return $this;
    }
}
