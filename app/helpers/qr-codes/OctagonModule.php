<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class OctagonModule implements ModuleInterface, Singleton
{
    public const LARGE = 1;
    public const MEDIUM = 0.8;
    public const SMALL = 0.6;

    private static $instance;
    private $size;

    public function __construct(float $size = self::MEDIUM)
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

        /* Cos(π/8) ≈ 0.92388 to get regular octagon radius to vertex */
        $radius = $half_size / cos(pi() / 8);

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $center_x = $x + $margin + $half_size;
                $center_y = $y + $margin + $half_size;

                /* Calculate 8 points of a regular octagon */
                $points = [];
                for ($i = 0; $i < 8; $i++) {
                    $angle = pi() / 4 * $i + pi() / 8; /* Start at 22.5° for vertical symmetry */
                    $point_x = $center_x + $radius * cos($angle);
                    $point_y = $center_y + $radius * sin($angle);
                    $points[] = [$point_x, $point_y];
                }

                $path = $path->move($points[0][0], $points[0][1]);
                for ($i = 1; $i < count($points); $i++) {
                    $path = $path->line($points[$i][0], $points[$i][1]);
                }
                $path = $path->close();
            }
        }

        return $path;
    }
}
