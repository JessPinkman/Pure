<?php

namespace Pure;

use Error;

/**
 * @method static Component div
 * @method static Component p
 * @method static Component span
 * @method static Component ul
 * @method static Component ol
 * @method static Component li
 * @method static Component a
 * @method static Component html
 * @method static Component h1
 * @method static Component h2
 * @method static Component h3
 * @method static Component h4
 * @method static Component h5
 * @method static Component h6
 * @method static Component head
 * @method static Component title
 * @method static Component meta
 * @method static Component body
 */
class Pure
{

    protected static string $view_folder;

    public static function __callStatic(string $tag, array $children): Component
    {
        $component = new Component($tag);
        if (count($children)) {
            $component->___($children);
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
            $component->___($children);
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

    public static function html(): HTML
    {
        return new HTML;
    }

    public static function setViewFolderRoot(string $path)
    {
        self::$view_folder = rtrim($path, '/\\');
    }

    public static function getView(string $path, ?array $args = null): Component
    {
        if (!self::$view_folder) {
            throw new Error("Pure View path is missing");
        }
        $path = self::$view_folder . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $path) . '.php';
        $args && extract($args);
        return require $path;
    }
}
