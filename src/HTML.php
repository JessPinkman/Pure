<?php

namespace Pure;

class HTML extends Component
{
    public function __toString(): string
    {
        return '<!DOCTYPE html>' . parent::__toString();
    }
}
