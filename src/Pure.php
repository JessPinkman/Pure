<?php

namespace pure\src;

use Closure;
use Error;
use pure\src\generic\Fragment;

/**
 * Class to create markup elements that can be structured with nesting.
 * Combine, assemble and reassemble elements freely and seamlessly.
 * Easily add attributes to any element with the use of magic __call.
 *
 *
 * @author: Serge Goncalves
 * @version 1.0.0
 */
class Pure
{

    protected static $self_closure_tags = [
        'area',
        'base',
        'br',
        'embed',
        'hr',
        'iframe',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
    ];

    private $self_closure = false;

    private $rob_tag;

    protected $attributes = [];

    protected $children = [];


    public function __construct(string $tag = 'div')
    {
        if (in_array($tag, self::$self_closure_tags)) {
            $this->self_closure = true;
        }

        $this->rob_tag = $tag;
    }

    public function append(...$children)
    {
        if ($this->self_closure) {
            $tag = $this->rob_tag;
            throw new Error("Cannot append child to self closing elements $tag");
        }

        \array_walk_recursive(
            $children,
            function ($child) {

                if ($child instanceof Closure) {
                    $child = \call_user_func($child);
                    $this->append($child);
                    return;
                }

                if (!is_string((string) $child)) {
                    throw new Error('Can only append strings / convertible to string');
                } else {
                    $this->children[] = $child;
                }
            }
        );
        return $this;
    }

    public function robBuffer(callable $func)
    {
        \ob_start();
        \call_user_func($func);
        $this->append(\ob_get_clean());
    }

    public function __toString()
    {
        $tag = $this->rob_tag;
        $html = "<$tag";

        if (!empty($this->attributes)) {
            foreach ($this->attributes as $key => $val) {
                if (true === $val) {
                    $html .= " $key ";
                } else {
                    $val = htmlspecialchars($val, \ENT_QUOTES, 'UTF-8', false);
                    $html .= " $key='$val' ";
                }
            }
        }

        if ($this->self_closure) {
            $html .= "/>";
        } else {
            $html .= ">";
            foreach ($this->children as $element) {
                $html .=  $element;
            }
            $html .= "</$tag>";
        }

        return $html;
    }

    public function __call($key, $args): self
    {
        $key = str_replace('_', '-', $key);

        foreach ($args as $arg) {
            if (!\is_string($arg) && !\is_array($arg) && !\is_bool($arg) && !is_null($arg) && !\is_numeric($arg)) {
                $type =  gettype($arg);
                throw new Error(
                    "Error on $key attribute: must be a string, an array, a bool, or null, received $type"
                );
            }
            if (is_array($arg)) {
                $arg = explode($this->getSeparator($key), $arg);
            }
            if (false === $arg) {
                if (isset($this->attributes[$key])) {
                    unset($this->attributes[$key]);
                }
            } else {
                if (!isset($this->attributes[$key])) {
                    $this->attributes[$key] = $arg;
                } else {
                    $this->attributes[$key] .= $this->getSeparator($key) . $arg;
                }
            }
        }

        return $this;
    }

    final public static function __callStatic($tag, $args): self
    {
        return new self($tag);
    }

    private function getSeparator($key): string
    {
        switch ($key) {
            case 'style':
                return ';';
            case 'class':
                return ' ';
            default:
                return ',';
        }
    }

    public static function getBuffer(callable $func, ...$args): string
    {
        ob_start();
        \call_user_func($func, ...$args);
        return \ob_get_clean();
    }

    public static function create(...$args): self
    {
        return new static(...$args);
    }

    public static function fragment(...$children): Fragment
    {
        return new Fragment(...$children);
    }

    public function echo()
    {
        echo $this;
    }
}
