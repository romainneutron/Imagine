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

use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

final class Image implements ImageInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * Constructs a new Image instance
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Makes sure the current image resource is destroyed
     */
    public function __destruct()
    {
        deleteImage($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    final public function copy()
    {
        if (false === $copy = copyImage($this->resource)) {
            throw new RuntimeException('Image copy operation failed');
        }

        return new Image($copy);
    }

    /**
     * {@inheritdoc}
     */
    final public function crop(PointInterface $start, BoxInterface $size)
    {

        if ( ! $start->in($this->getSize())) {
            throw new OutOfBoundsException(
                'Crop coordinates must start at minimum 0, 0 position from ' .
                'top  left corner, crop height and width must be positive ' .
                'integers and must not exceed the current image borders'
            );
        }

        $image = copyImage($this->resource);

        if (false === imageCrop($image, $start->getX(), $start->getY(), $size->getWidth(), $size->getHeight())) {
            throw new RuntimeException('Image crop operation failed');
        }

        deleteImage($this->resource);

        $this->resource = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function paste(ImageInterface $image, PointInterface $start)
    {
        if ( ! $image instanceof self) {
            throw new InvalidArgumentException(sprintf(
                    'ExactImage\Image can only paste() ExactImage\Image instances, %s given', get_class($image)
            ));
        }

        $size = $image->getSize();
        if ( ! $this->getSize()->contains($size, $start)) {
            throw new OutOfBoundsException(
                'Cannot paste image of the given size at the specified ' .
                'position, as it moves outside of the current image\'s box'
            );
        }

        $startX = $start->getX();
        $startY = $start->getY();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x ++ ) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y ++ ) {
                $color = $image->getColorAt(new Point($x, $y));

                if (false === set($this->resource, (int) ($x + $startX), (int)($y + $startY)
                        , (float) ($color->getRed() / 255)
                        , (float) ($color->getGreen() / 255)
                        , (float) ($color->getBlue() / 255)
                        , (float) ($color->getAlpha()))) {
                    throw new RuntimeException('Fill operation failed');
                }
                unset($color);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function resize(BoxInterface $size)
    {
        $image = copyImage($this->resource);

        if (false === imageResize($image, $size->getWidth(), $size->getHeight())) {
            throw new RuntimeException('Image crop operation failed');
        }

        deleteImage($this->resource);

        $this->resource = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function rotate($angle, Color $background = null)
    {
        $image = copyImage($this->resource);


        if ($background) {
            setbackgroundcolor((float) $background->getRed(), (float) $background->getGreen(), (float) $background->getBlue(), (float) $background->getAlpha());
        }

        if (false === imageRotate($image, $angle)) {
            throw new RuntimeException('Image rotate operation failed');
        }

        deleteImage($this->resource);

        $this->resource = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function save($path, array $options = array())
    {
        $quality = isset($options['quality']) ? $options['quality'] : 75;

        if (false === encodeImageFile($this->resource, $path, $quality)) {
            throw new RuntimeException('Unable to save imahe');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function show($format, array $options = array())
    {
        header('Content-type: ' . $this->getMimeType($format));

        $this->saveOrOutput($format, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($format, array $options = array())
    {
        // todo : implement resolution
        $quality = isset($options['quality']) ? $options['quality'] : 75;

        return encodeImage($this->resource, $format, $quality);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->get('png');
    }

    /**
     * {@inheritdoc}
     */
    final public function flipHorizontally()
    {
        $image = copyImage($this->resource);

        if (false === imageFlipX($image)) {
            throw new RuntimeException('Image crop operation failed');
        }

        deleteImage($this->resource);

        $this->resource = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function flipVertically()
    {
        $image = copyImage($this->resource);

        if (false === imageFlipY($image)) {
            throw new RuntimeException('Image crop operation failed');
        }

        deleteImage($this->resource);

        $this->resource = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function strip()
    {
        throw new RuntimeException('ExactImage does not support image strip');
    }

    /**
     * {@inheritdoc}
     */
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $width = $size->getWidth();
        $height = $size->getHeight();

        $ratios = array(
            $width / imageWidth($this->resource),
            $height / imageHeight($this->resource)
        );

        $thumbnail = $this->copy();

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        $thumbnailSize = $thumbnail->getSize()->scale($ratio);
        $thumbnail->resize($thumbnailSize);

        if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            $thumbnail->crop(new Point(
                    max(0, round(($thumbnailSize->getWidth() - $width) / 2)),
                    max(0, round(($thumbnailSize->getHeight() - $height) / 2))
                ), $size);
        }

        return $thumbnail;
    }

    /**
     * {@inheritdoc}
     */
    public function draw()
    {
        return new Drawer($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function effects()
    {
        return new Effects($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return new Box(imageWidth($this->resource), imageHeight($this->resource));
    }

    /**
     * {@inheritdoc}
     */
    public function applyMask(ImageInterface $mask)
    {
        if ( ! $mask instanceof self) {
            throw new InvalidArgumentException('Cannot mask non-exactimage images');
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf(
                    'The given mask doesn\'t match current image\'s size, Current ' .
                    'mask\'s dimensions are %s, while image\'s dimensions are %s', $maskSize, $size
            ));
        }

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x ++ ) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y ++ ) {
                $position = new Point($x, $y);
                $color = $this->getColorAt($position);
                $maskColor = $mask->getColorAt($position);
                $round = (int) round(max($color->getAlpha(), (100 - $color->getAlpha()) * $maskColor->getRed() / 255));

                $newColor = $color->dissolve($round - $color->getAlpha());
                if (false === set($this->resource, $x, $y
                        , (float) $newColor->getRed() / 255
                        , (float) $newColor->getGreen() / 255
                        , (float) $newColor->getBlue() / 255
                        , (float) $newColor->getAlpha())) {
                    throw new RuntimeException('Fill operation failed');
                }
                unset($color);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fill(FillInterface $fill)
    {
        $size = $this->getSize();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x ++ ) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y ++ ) {
                $color = $fill->getColor(new Point($x, $y));
                if (false === set($this->resource, $x, $y
                        , (float) $color->getRed() / 255
                        , (float) $color->getGreen() / 255
                        , (float) $color->getBlue() / 255
                        , (float) $color->getAlpha())) {
                    throw new RuntimeException('Fill operation failed');
                }
                unset($color);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function mask()
    {
        $mask = $this->copy();

        if (false === imageHueSaturationLightness($this->resource, 100, 0, 100)) {
            throw new RuntimeException('Mask operation failed');
        }

        return $mask;
    }

    /**
     * {@inheritdoc}
     */
    public function histogram()
    {
        $size   = $this->getSize();
        $colors = array();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $colors[] = $this->getColorAt(new Point($x, $y));
            }
        }

        return array_unique($colors);
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAt(PointInterface $point)
    {
        $infos = get($this->resource, $point->getX(), $point->getY());

        return new Color(array(
                round($infos[0] * 255),
                round($infos[1] * 255),
                round($infos[2] * 255),
                ),
                (int)$infos[3] * 100
        );
    }

    private function getMimeType($format)
    {
        static $mimeTypes = array(
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'png'  => 'image/png',
        'wbmp' => 'image/vnd.wap.wbmp',
        'xbm'  => 'image/xbm',
        );

        if ( ! isset($mimeTypes[$format])) {
            throw new RuntimeException(sprintf(
                    'Unsupported format given. Only %s are supported, %s given', implode(", ", array_keys($mimeTypes)), $format
            ));
        }

        return $mimeTypes[$format];
    }
}
