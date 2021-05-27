<?php

namespace Pure;

use Closure;
use Error;
use Stringable;

/**
 * Class to create markup elements that can be structured with nesting.
 * Combine, assemble and reassemble elements freely and seamlessly.
 * Easily add attributes to any element with the use of magic __call.
 *
 *
 * @author: Serge Goncalves
 */
class Component
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

    protected $attributes = [];

    protected $children = [];

    public function __construct(
        public string $pure_tag
    ) {
    }

    public function ___(Stringable|string|array|Closure|null ...$children): static
    {
        if ($this->self_closure) {
            $tag = $this->pure_tag;
            throw new Error("Cannot append child to self closing elements $tag");
        }

        \array_walk_recursive(
            $children,
            function ($child) {

                if ($child instanceof Closure) {
                    $child = \call_user_func($child);
                    $this($child);
                    return;
                }

                $this->children[] = $child instanceof self ? $child : htmlentities($child);
            }
        );
        return $this;
    }

    public function __toString(): string
    {
        $html = "<$this->pure_tag";

        foreach ($this->attributes as $key => $val) {
            if (empty($val)) {
                $html .= " $key ";
            } else {
                $values = \implode($this->getSeparator($key), $val);
                $values = htmlspecialchars($values, \ENT_QUOTES, 'UTF-8', false);
                $html .= " $key='$values' ";
            }
        }


        if ($this->self_closure) {
            $html .= "/>";
        } else {
            $html .= ">";
            foreach ($this->children as $element) {
                $html .=  $element;
            }
            $html .= "</$this->pure_tag>";
        }

        return $html;
    }

    public function setPureTag(string $tag): static
    {
        $this->pure_tag = $tag;

        if (in_array($tag, self::$self_closure_tags)) {
            $this->self_closure = true;
            if (!empty($this->children)) {
                throw new Error("Cannot append child to self closing elements $tag");
            }
        }

        return $this;
    }

    public function __call($key, $args): static
    {

        $key = str_replace('_', '-', $key);

        if (!isset($this->attributes[$key])) {
            $this->attributes[$key] = [];
        }

        \array_walk_recursive($args, function ($arg) use ($key) {
            if (!\is_string($arg) && !\is_bool($arg) && !is_null($arg) && !\is_numeric($arg)) {
                $type =  gettype($arg);
                throw new Error(
                    "Error on $key attribute: must be a string, an array, a bool, or null, received $type"
                );
            }
            if (false === $arg) {
                unset($this->attributes[$key]);
            } elseif (!is_null($arg)) {
                $this->attributes[$key][] = $arg;
            }
        });

        return $this;
    }

    public function setAttrs(array $attrs): static
    {
        foreach ($attrs as $attr => $val) {
            $this->$attr($val);
        }

        return $this;
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

    public function __invoke(Stringable|string|array|Closure|null ...$children): static
    {
        $this->___(...$children);
        return $this;
    }

    public function echo(): void
    {
        echo $this;
    }

    public function pureOpen(): string
    {
        $string = $this->__toString();
        $marker = strpos($string, '>') + 1;
        return \substr($string, 0, $marker);
    }

    public function pureClose(): string
    {
        return "</$this->pure_tag>";
    }

    /**
     * original: See context class for arguments
     */
    public static function render(...$args)
    {
        return new static(...$args);
    }

    public function pureAccess(string $object, $request): static
    {
        if (isset($this->$object) && $this->$object instanceof self) {
            $request instanceof Closure
                ? $request($this->$object)
                : $this->$object->___($request);
        } else {
            throw new Error("$object is not set or not a Component");
        }
        return $this;
    }
}
