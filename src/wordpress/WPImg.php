<?php

namespace pure\src\wordpress;

use pure\src\Pure;

class WPImg extends Pure
{

    public $default = "300px";
    const DEFAULT_SIZES = [
        '30rem'     => '480px',
        '60rem'     => '960px',
        '90rem'     => '720px',
        '120rem'    => '640px',
    ];

    public function __construct(int $id, ?string $alt = null, ?array $sizes = null)
    {
        parent::__construct('img');
        $this->srcset(\wp_get_attachment_image_srcset($id));
        $this->src(\wp_get_attachment_image_url($id, 'full'));

        $this->addSizes($sizes ?? self::DEFAULT_SIZES);

        $this->alt($alt ?? \get_the_title($id));
    }

    private function addSizes(array $sizes)
    {
        foreach ($sizes as $max => $size) {
            $this->addSize($max, $size);
        }
    }

    public function addSize(string $max, string $size)
    {
        $this->sizes("(max-width: $max) $size");
    }

    public function __toString(): string
    {
        $this->sizes($this->default);
        return parent::__toString();
    }
}
