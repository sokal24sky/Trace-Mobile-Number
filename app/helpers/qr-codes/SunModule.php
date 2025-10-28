<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class SunModule implements ModuleInterface, Singleton
{
    private static $instance;
    private float $size;

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

        $points = [
            [3,0],[3.4,0.7],[4,0.2],[4.1,0.9],[4.9,0.7],[4.8,1.5],[5.6,1.5],[5.2,2.2],[5.9,2.5],
            [5.3,3],[5.9,3.5],[5.2,3.8],[5.6,4.5],[4.8,4.5],[4.9,5.3],[4.1,5.1],[4,5.8],[3.4,5.3],
            [3,6],[2.5,5.3],[1.9,5.8],[1.8,5.1],[1,5.3],[1.1,4.5],[0.4,4.5],[0.7,3.8],[0,3.5],
            [0.6,3],[0,2.5],[0.7,2.2],[0.4,1.5],[1.1,1.5],[1,0.7],[1.8,0.9],[1.9,0.2],[2.5,0.7]
        ];

        $scale = $this->size / 6.0; // Since viewBox is 6x6

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
