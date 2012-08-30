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

use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\ImagineInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

class Imagine implements ImagineInterface
{

    /**
     * @throws RuntimeException
     */
    public function __construct()
    {
        if ( ! extension_loaded('ExactImage')) {
            throw new RuntimeException('ExactImage not installed');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function open($path)
    {
        $handle = @fopen($path, 'r');

        if (false === $handle) {
            throw new InvalidArgumentException(sprintf(
                    'File %s doesn\'t exist', $path
            ));
        }

        $image = $this->read($handle);
        fclose($handle);

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function create(BoxInterface $size, Color $color = null)
    {
        $color = null !== $color ? $color : new Color('fff');

        $image = newImageWithTypeAndSize(4, 8, $size->getWidth(), $size->getHeight());

        setbackgroundcolor((float) $color->getRed(), (float) $color->getGreen(), (float) $color->getBlue(), (float) $color->getAlpha());

        return new Image($image);
    }

    /**
     * {@inheritdoc}
     */
    public function load($string)
    {
        $image = newimage();
        decodeImage($image, $string);

        return new Image($image);
    }

    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if ( ! is_resource($resource)) {
            throw new InvalidArgumentException('Variable does not contain a stream resource');
        }

        $content = stream_get_contents($resource);

        if (false === $content) {
            throw new InvalidArgumentException('Cannot read resource content');
        }

        return $this->load($content);
    }

    /**
     * {@inheritdoc}
     */
    public function font($file, $size, Color $color)
    {
        throw new RuntimeException('Not yet implemented');
//        $gmagick = new \Gmagick();
//
//        $gmagick->newimage(1, 1, 'transparent');
//
//        return new Font($gmagick, $file, $size, $color);
    }
}
