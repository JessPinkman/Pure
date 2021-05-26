<?php

namespace Pure;

use Error;
use Exception;
use Stringable;

/**
 * @method static Component a
 * @method static Component abbr
 * @method static Component address
 * @method static Component area
 * @method static Component article
 * @method static Component aside
 * @method static Component audio
 * @method static Component b
 * @method static Component base
 * @method static Component bdi
 * @method static Component bdo
 * @method static Component blockquote
 * @method static Component body
 * @method static Component br
 * @method static Component button
 * @method static Component canvas
 * @method static Component caption
 * @method static Component cite
 * @method static Component code
 * @method static Component col
 * @method static Component colgroup
 * @method static Component data
 * @method static Component colgroup
 * @method static Component data
 * @method static Component datalist
 * @method static Component dd
 * @method static Component del
 * @method static Component details
 * @method static Component dfn
 * @method static Component dialog
 * @method static Component div
 * @method static Component dl
 * @method static Component dt
 * @method static Component em
 * @method static Component embed
 * @method static Component fieldset
 * @method static Component figcaption
 * @method static Component figure
 * @method static Component footer
 * @method static Component form
 * @method static Component h1
 * @method static Component h2
 * @method static Component h3
 * @method static Component h4
 * @method static Component h5
 * @method static Component h6
 * @method static Component head
 * @method static Component header
 * @method static Component hr
 * @method static Component html
 * @method static Component i
 * @method static Component iframe
 * @method static Component img
 * @method static Component input
 * @method static Component ins
 * @method static Component kbd
 * @method static Component label
 * @method static Component legend
 * @method static Component li
 * @method static Component link
 * @method static Component main
 * @method static Component map
 * @method static Component mark
 * @method static Component meta
 * @method static Component meter
 * @method static Component nav
 * @method static Component noscript
 * @method static Component object
 * @method static Component ol
 * @method static Component optgroup
 * @method static Component option
 * @method static Component output
 * @method static Component p
 * @method static Component param
 * @method static Component picture
 * @method static Component pre
 * @method static Component progress
 * @method static Component q
 * @method static Component rp
 * @method static Component rt
 * @method static Component ruby
 * @method static Component s
 * @method static Component samp
 * @method static Component script
 * @method static Component section
 * @method static Component select
 * @method static Component sidebar
 * @method static Component small
 * @method static Component source
 * @method static Component span
 * @method static Component strong
 * @method static Component style
 * @method static Component sub
 * @method static Component svg
 * @method static Component table
 * @method static Component tbody
 * @method static Component td
 * @method static Component template
 * @method static Component textarea
 * @method static Component tfoot
 * @method static Component th
 * @method static Component thead
 * @method static Component time
 * @method static Component title
 * @method static Component tr
 * @method static Component track
 * @method static Component u
 * @method static Component ul
 * @method static Component var
 * @method static Component video
 * @method static Component wbr
 */
class Pure
{

    protected static string $view_folder;

    public static function __callStatic(string $tag, array $children): Component
    {
        $component = new Component($tag);
        if (count($children)) {
            $component($children);
        }
        return $component;
    }

    public static function pureBuffer(callable $func, ...$args): string
    {
        ob_start();
        \call_user_func($func, ...$args);
        return \ob_get_clean();
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

    public static function html(Stringable | string| array | null ...$children): HTML
    {
        return new HTML(...$children);
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
        if (!file_exists($path)) {
            throw new Exception("file $path not found");
        }
        $args && extract($args);
        return require $path;
    }
}
