<?php

namespace App\Helper;

use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette;
use Imagine\Image\Point;

trait ImageTrait {
    /**
     * @var Imagine
     */
    private $imagine;

    private function getImagine() {
        if (!isset($this->imagine)) {
            $this->imagine = new Imagine();
        }
        return $this->imagine;
    }

    /**
     * @param string $sourceFile
     * @return ImageInterface|Image
     */
    private function openImage($sourceFile) {
        return $this->getImagine()->open($sourceFile);
    }

    /**
     * @param ImageInterface|Image $image
     * @param int $side
     * @param string $bgColor in RGB format (#000000)
     * @return ImageInterface|Image
     */
    private function resizeImage($image, $side, $bgColor) {

        $size = $image->getSize();
        $scale = $side / max($size->getWidth(), $size->getHeight());
        $newSize = $size->scale($scale);

        $resized = $image->copy();
        $resized->usePalette($image->palette());
        $resized->strip();
        $resized->resize($newSize);

        $offset = new Point(abs($side - $newSize->getWidth()) / 2, abs($side - $newSize->getHeight()) / 2);
        $resultSize = new Box($side, $side);

        $palette = new Palette\RGB();
        $bgColor = $palette->color($bgColor, 0);

        $result = $this->getImagine()->create($resultSize, $bgColor);
        $result->paste($resized, $offset);

        return $result;
    }
}