<?php

namespace Imagine\Effects;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\ImagineInterface;

abstract class AbstractEffectsTest extends \PHPUnit_Framework_TestCase
{

    public function testNegate()
    {
        $imagine = $this->getImagine();

        $image = $imagine->create(new Box(20, 20), new Color('ff0'));
        $image->effects()
            ->negative();

        $this->assertEquals('#0000ff', (string) $image->getColorAt(new Point(10, 10)));

        $image->effects()
            ->negative();

        $this->assertEquals('#ffff00', (string) $image->getColorAt(new Point(10, 10)));
    }

    public function testGamma()
    {
        $imagine = $this->getImagine();

        $r = 20;
        $g = 90;
        $b = 240;

        $image = $imagine->create(new Box(20, 20), new Color(array($r, $g, $b)));
        $image->effects()
            ->gamma(1.2);

        $pixel = $image->getColorAt(new Point(10, 10));

        $this->assertNotEquals($r, $pixel->getRed());
        $this->assertNotEquals($g, $pixel->getGreen());
        $this->assertNotEquals($b, $pixel->getBlue());
    }

    public function testGrayscale()
    {
        $imagine = $this->getImagine();

        $r = 20;
        $g = 90;
        $b = 240;

        $image = $imagine->create(new Box(20, 20), new Color(array($r, $g, $b)));
        $image->effects()
            ->grayscale();

        $pixel = $image->getColorAt(new Point(10, 10));

        $this->assertEquals('#565656', (string) $pixel);

        $greyR = (int)$pixel->getRed();
        $greyG = (int)$pixel->getGreen();
        $greyB = (int)$pixel->getBlue();

        $this->assertEquals($greyR, (int)86);
        $this->assertEquals($greyR, $greyG);
        $this->assertEquals($greyR, $greyB);
        $this->assertEquals($greyG, $greyB);
    }


    /**
     * @dataProvider getBrightnessValues
     */
    public function testBrightness($start, $adjustment, $end)
    {
        $imagine = $this->getImagine();

        $image = $imagine->create(new Box(20, 20), new Color($start));
        $image->effects()
            ->brightness($adjustment);

        $pixel = $image->getColorAt(new Point(10, 10));

        $this->assertEquals($end, (string) $pixel);
    }

    public function getBrightnessValues()
    {
        return array(
            array('111', 0, '#000000'),
            array('111', 1, '#111111'),
            array('111', 2, '#222222'),#
            array('222', 2, '#444444'),
            array('111', 3, '#333333'),#
            array('fff', 2, '#ffffff'),
            array('5E5F01', 1.8, '#5E5F01'),#
            array('fff', -2, '#dddddd'),#
        );
    }

    /**
     * @return ImagineInterface
     */
    abstract protected function getImagine();
}
