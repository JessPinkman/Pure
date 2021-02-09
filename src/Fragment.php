<?php

namespace Pure;

use Error;

class Fragment extends Component
{

    public function __construct(...$children)
    {
        foreach ($children as $child) {
            $this->append($child);
        }
    }

    public function __toString(): string
    {
        $html = '';
        foreach ($this->children as $element) {
            $html .=  $element;
        }

        return $html;
    }

    public function __call($key, $args): self
    {
        throw new Error("method $key not found, cannot set attributes for Fragments");
        return $this;
    }
}
