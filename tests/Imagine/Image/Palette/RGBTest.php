<?php

namespace Imagine\Image\Palette;

use Imagine\Image\Palette\RGBTest;
use Imagine\Image\Palette\Color\RGB as RGBColor;

class RGBTest extends AbstractPaletteTest
{
    public function provideColorAndAlphaTuples()
    {
        $palette = $this->getPalette();

        return array(
            array(new RGBColor($palette, array(23, 24, 0), 0), array(23, 24, 0), null),
            array(new RGBColor($palette, array(23, 24, 0), 0), array(23, 24, 0), 0),
            array(new RGBColor($palette, array(23, 24, 0), 3), array(23, 24, 0), 3),
            array(new RGBColor($palette, array(129, 127, 168), 3), array(23, 24, 0, 34), 3),
            array(new RGBColor($palette, array(255, 255, 255), 0), array(0, 0, 0, 0), null),
            array(new RGBColor($palette, array(0, 0, 0), 0), array(0, 0, 0, 100), null),
        );
    }

    public function provideColorsForBlending()
    {
        $palette = $this->getPalette();

        return array(
            array(
                new RGBColor($palette, array(240, 0, 0), 0),
                new RGBColor($palette, array(230, 0, 0), 0),
                new RGBColor($palette, array(128, 0, 0), 0),
                1.1,
            ),
            array(
                new RGBColor($palette, array(21, 11, 15), 0),
                new RGBColor($palette, array(1, 2, 3), 0),
                new RGBColor($palette, array(50, 25, 32), 0),
                0.4,
            ),
        );
    }

    protected function getPalette()
    {
        return new RGB();
    }
}
