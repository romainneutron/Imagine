<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\ExactImage;

use Imagine\Image\AbstractImageTest;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;

class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!extension_loaded('ExactImage')) {
            $this->markTestSkipped('ExactImage is not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
