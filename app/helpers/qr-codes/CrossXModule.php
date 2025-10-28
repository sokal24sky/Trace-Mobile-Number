<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class CrossXModule implements ModuleInterface, Singleton
{
    private static $instance;
    private float $size;

    public function __construct(float $size = 1.0)
    {
        if($size <= 0 || $size > 1) {
            throw new InvalidArgumentException('Size must be between 0 and 1');
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

        $points = [
            [3,5.1], [3.9,6], [6,6], [6,3.9], [5.1,3],
            [6,2.1], [6,0], [3.9,0], [3,0.9], [2.1,0],
            [0,0], [0,2.1], [0.9,3], [0,3.9], [0,6],
            [2.1,6]
        ];

        $scale = $this->size / 6.0;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $offset_x = $x + (1 - $this->size) / 2;
                $offset_y = $y + (1 - $this->size) / 2;

                $first = true;
                foreach ($points as [$px, $py]) {
                    $fx = $offset_x + $px * $scale;
                    $fy = $offset_y + $py * $scale;

                    if($first) {
                        $path = $path->move($fx, $fy);
                        $first = false;
                    } else {
                        $path = $path->line($fx, $fy);
                    }
                }

                $path = $path->close();
            }
        }

        return $path;
    }
}
