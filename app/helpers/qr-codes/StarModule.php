<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class StarModule implements ModuleInterface, Singleton
{
    public const LARGE = 1;
    public const MEDIUM = .8;
    public const SMALL = .6;

    private static $instance;
    private $size;

    public function __construct(float $size)
    {
        if($size <= 0 || $size > 1) {
            throw new InvalidArgumentException('Size must between 0 (exclusive) and 1 (inclusive)');
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

                $path_x = $x + $margin;
                $path_y = $y + $margin;

                /* Calculate star points (5-point star) */
                $cx = $path_x + $half_size;
                $cy = $path_y + $half_size;
                $radius = $half_size;
                $angle = deg2rad(-90);

                $points = [];
                for ($i = 0; $i < 5; $i++) {
                    $outer_x = $cx + cos($angle) * $radius;
                    $outer_y = $cy + sin($angle) * $radius;
                    $angle += deg2rad(72);

                    $inner_x = $cx + cos($angle - deg2rad(36)) * ($radius * 0.5);
                    $inner_y = $cy + sin($angle - deg2rad(36)) * ($radius * 0.5);

                    $points[] = [$outer_x, $outer_y];
                    $points[] = [$inner_x, $inner_y];
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
