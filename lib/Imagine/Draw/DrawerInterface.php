<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Draw;

use Imagine\Exception\RuntimeException;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\FontInterface;
use Imagine\Image\PointInterface;

/**
 * Interface for the drawer
 */
interface DrawerInterface
{
    /**
     * Draws an arc on a starting at a given x, y coordinates under a given
     * start and end angles
     *
     * @param PointInterface $center
     * @param BoxInterface   $size
     * @param integer        $start
     * @param integer        $end
     * @param Color          $color
     * @param integer        $thickness
     *
     * @throws RuntimeException
     *
     * @return DrawerInterface
     */
    public function arc(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $thickness = 1);

    /**
     * Same as arc, but also connects end points with a straight line
     *
     * @param PointInterface $center
     * @param BoxInterface   $size
     * @param integer        $start
     * @param integer        $end
     * @param Color          $color
     * @param Boolean        $fill
     * @param integer        $thickness
     *
     * @throws RuntimeException
     *
     * @return DrawerInterface
     */
    public function chord(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false, $thickness = 1);

    /**
     * Draws and ellipse with center at the given x, y coordinates, and given
     * width and height
     *
     * @param PointInterface $center
     * @param BoxInterface   $size
     * @param Color          $color
     * @param Boolean        $fill
     * @param integer        $thickness
     *
     * @throws RuntimeException
     *
     * @return DrawerInterface
     */
    public function ellipse(PointInterface $center, BoxInterface $size, Color $color, $fill = false, $thickness = 1);

    /**
     * Draws a line from start(x, y) to end(x, y) coordinates
     *
     * @param PointInterface $start
     * @param PointInterface $end
     * @param Color          $outline
     * @param integer        $thickness
     *
     * @return DrawerInterface
     */
    public function line(PointInterface $start, PointInterface $end, Color $outline, $thickness = 1);

    /**
     * Same as arc, but connects end points and the center
     *
     * @param PointInterface $center
     * @param BoxInterface   $size
     * @param integer        $start
     * @param integer        $end
     * @param Color          $color
     * @param Boolean        $fill
     * @param integer        $thickness
     *
     * @throws RuntimeException
     *
     * @return DrawerInterface
     */
    public function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false, $thickness = 1);

    /**
     * Places a one pixel point at specific coordinates and fills it with
     * specified color
     *
     * @param PointInterface $position
     * @param Color          $color
     *
     * @throws RuntimeException
     *
     * @return DrawerInterface
     */
    public function dot(PointInterface $position, Color $color);

    /**
     * Draws a polygon using array of x, y coordinates. Must contain at least
     * three coordinates
     *
     * @param array   $coordinates
     * @param Color   $color
     * @param Boolean $fill
     * @param integer $thickness
     *
     * @throws RuntimeException
     *
     * @return DrawerInterface
     */
    public function polygon(array $coordinates, Color $color, $fill = false, $thickness = 1);

    /**
     * Annotates image with specified text at a given position starting on the
     * top left of the final text box
     *
     * The rotation is done CW
     *
     * @param string         $string
     * @param FontInterface  $font
     * @param PointInterface $position
     * @param integer        $angle
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return DrawerInterface
     */
    public function text($string, FontInterface $font, PointInterface $position, $angle = 0);
}
