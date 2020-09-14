<?php

namespace Pure;

class Pure
{
    public static function __callStatic(string $tag, array $children): Component
    {
        $component = new Component($tag);
        if (count($children)) {
            $component->append($children);
        }
        return $component;
    }

    public static function pureBuffer(callable $func, ...$args): string
    {
        ob_start();
        \call_user_func($func, ...$args);
        return \ob_get_clean();
    }

    public static function pureFrom(string $tag, ?array $children = null): Component
    {
        $component = new Component($tag);
        if (count($children)) {
            $component->append($children);
        }
        return $component;
    }

    public static function pureOpen($tag): string
    {
        return "<$tag>";
    }

    public static function pureClose($tag): string
    {
        return "</$tag>";
    }

    public static function pureComment($comment): string
    {
        return "<!-- $comment -->";
    }
}
