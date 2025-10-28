<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class TriangleModule implements ModuleInterface, Singleton
{
    private static $instance;
    private $size;

    public function __construct(float $size = 1.0)
    {
        if($size <= 0 || $size > 1) {
            throw new InvalidArgumentException('Size must be between 0 (exclusive) and 1 (inclusive)');
        }

        $this->size = $size;
    }

    public static function instance(): self
    {
        return self::$instance ?: self::$instance = new self();
    }

    public function createPath(ByteMatrix $matrix): Path
    {
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        $path = new Path();
        $half_size = $this->size / 2;
        $margin = (1 - $this->size) / 2;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $cx = $x + $margin + $half_size;
                $cy = $y + $margin + $half_size;

                $height_equilateral = sqrt(3) * $half_size;

                $x1 = $cx;
                $y1 = $cy - (2 / 3) * $height_equilateral;

                $x2 = $cx - $half_size;
                $y2 = $cy + (1 / 3) * $height_equilateral;

                $x3 = $cx + $half_size;
                $y3 = $cy + (1 / 3) * $height_equilateral;

                /* Draw triangle path */
                $path = $path->move($x1, $y1);
                $path = $path->line($x2, $y2);
                $path = $path->line($x3, $y3);
                $path = $path->close();
            }
        }

        return $path;
    }
}
