<?php

namespace ShopifyTemplate\Drops;

class Color extends \Liquid\Models\Drop
{

    function __construct($color)
    {
        $this->color = $color;
        $this->attrbites = [
            'red' => 0,
            'green' => 0,
            'blue' => 0,
        ];

        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return;
        }

        $this->setAttribute('red', hexdec($r));
        $this->setAttribute('green', hexdec($g));
        $this->setAttribute('blue', hexdec($b));
    }

    function __toString()
    {
        return $this->color;
    }
}
