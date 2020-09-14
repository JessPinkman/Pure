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

    public static function buffer(callable $func, ...$args): string
    {
        ob_start();
        \call_user_func($func, ...$args);
        return \ob_get_clean();
    }

    public static function fromTag(string $tag, ?array $children = null): Component
    {
        $component = new Component($tag);
        if (count($children)) {
            $component->append($children);
        }
        return $component;
    }

    public static function openTag($tag): string
    {
        return "<$tag>";
    }

    public static function closeTag($tag): string
    {
        return "</$tag>";
    }

    public static function addComment($comment): string
    {
        return "<!-- $comment -->";
    }
}
