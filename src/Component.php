<?php

namespace Pure;

use Closure;
use Error;

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

    public $pure_tag;

    protected $attributes = [];

    protected $children = [];

    public ?self $parent = null;

    protected ?string $drill_id = null;

    protected ?Component $drill_fallback = null;

    public function __construct(string $tag)
    {
        $this->setPureTag($tag);
    }

    /**
     *
     * @deprecated
     */
    public function append(...$children): self
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
                    $this->___($child);
                    return;
                }

                if (!$this->pureStringCheck($child)) {
                    throw new Error('Can only append strings / convertible to string');
                } else {
                    $child instanceof self && $child->parent = $this;
                    $this->children[] = $child;
                }
            }
        );
        return $this;
    }

    public function ___(...$children): self
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
                    $this->___($child);
                    return;
                }

                if (!$this->pureStringCheck($child)) {
                    throw new Error('Can only append strings / convertible to string');
                } else {
                    $child instanceof self && $child->parent = $this;
                    $this->children[] = $child;
                }
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

    public function setPureTag(string $tag): self
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

    public function __call($key, $args): self
    {

        $key = str_replace('_', '-', $key);

        if (strtoupper($key) == $key) {
            return $this->pureAccess(strtolower($key), ...$args);
        }

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

    public function setAttrs(array $attrs): self
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

    public function __invoke(...$children): self
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

    public static function render(...$args)
    {
        return new static(...$args);
    }

    protected function pureStringCheck($item): bool
    {
        return $item instanceof Component || is_string((string) $item);
    }

    public function pureAccess(string $object, $request): self
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

    public function setDrillID(string $id): self
    {
        $this->drill_id = $id;
        return $this;
    }

    public function endDrill(): Component
    {
        $fallback = $this->drill_fallback;
        $this->drill_fallback = null;
        return $fallback;
    }

    public function startDrill(string $id): ?self
    {
        $component = $this->getDrillChild($id);

        if ($component) {
            $component->drill_fallback = $this;
            return $component;
        } else {
            return null;
        }
    }

    protected function getDrillChild(string $id): ?self
    {
        $found = null;
        array_walk($this->children, function ($child) use ($id, &$found) {
            if ($child instanceof self && $child->drill_id === $id) {
                $found = $child;
            } elseif ($child instanceof self) {
                $found = $child->getDrillChild($id);
            }
        });
        return $found;
    }
}
