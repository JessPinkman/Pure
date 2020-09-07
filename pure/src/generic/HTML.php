<?php

namespace pure\src\generic;

use pure\src\Pure;

class HTML extends Pure
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
