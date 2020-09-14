<?php

namespace Pure;

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
}
