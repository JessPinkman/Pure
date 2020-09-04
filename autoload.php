<?php

namespace pure;

use Error;

\spl_autoload_register(
    function (string $class) {
        $prefix = __NAMESPACE__;

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);

        $string = \str_replace('\\', '/', $relative_class);
        $string = \str_replace('_', '-', $string);
        $string = __DIR__ . $string . '.php';


        if (file_exists($string)) {
            require $string;
        } else {
            throw new Error("File $string not found");
        }
    }
);
