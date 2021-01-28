<?php

namespace Pure;

class HTML extends Component
{

    public function __construct()
    {
        parent::__construct('html');
    }

    public function __toString(): string
    {
        return '<!DOCTYPE html>' . parent::__toString();
    }
}
