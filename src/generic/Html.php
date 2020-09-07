<?php

namespace Pure\Generic;

use Pure\generic\Pure;

class Html extends Pure
{

    public function __construct()
    {
        parent::__construct('html');
    }

    public function __toString(): string
    {
        return "<!DOCTYPE html>" . parent::__toString();
    }
}
