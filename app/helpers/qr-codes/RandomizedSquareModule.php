<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class RandomizedSquareModule implements ModuleInterface, Singleton
{
    public const LARGE = 1;
    public const MEDIUM = .8;
    public const SMALL = .6;

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
        $max_offset = (1 - $this->size) * 0.4;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $seed = crc32("{$x}_{$y}");
                mt_srand($seed);
                $offset_x = ((mt_rand() / mt_getrandmax()) * 2 - 1) * $max_offset;
                $offset_y = ((mt_rand() / mt_getrandmax()) * 2 - 1) * $max_offset;

                $origin_x = $x + ((1 - $this->size) / 2) + $offset_x;
                $origin_y = $y + ((1 - $this->size) / 2) + $offset_y;

                $center_x = $origin_x + $this->size / 2;
                $center_y = $origin_y + $this->size / 2;

                /* randomly decide to rotate this square */
                $should_rotate = (mt_rand() / mt_getrandmax()) < 0.3;

                $rotation_angle = 0;
                if($should_rotate) {
                    $rotation_angle = deg2rad(((mt_rand() / mt_getrandmax()) * 10) - 5); /* -5° to +5° */
                }

                /* define square corners */
                $corners = [
                    [$origin_x, $origin_y],
                    [$origin_x + $this->size, $origin_y],
                    [$origin_x + $this->size, $origin_y + $this->size],
                    [$origin_x, $origin_y + $this->size],
                ];

                /* rotate if needed */
                if($should_rotate) {
                    foreach ($corners as &$point) {
                        $dx = $point[0] - $center_x;
                        $dy = $point[1] - $center_y;

                        $rotated_x = $center_x + ($dx * cos($rotation_angle) - $dy * sin($rotation_angle));
                        $rotated_y = $center_y + ($dx * sin($rotation_angle) + $dy * cos($rotation_angle));

                        $point[0] = $rotated_x;
                        $point[1] = $rotated_y;
                    }
                }

                /* draw square path */
                $path = $path->move($corners[0][0], $corners[0][1]);
                for ($i = 1; $i < 4; $i++) {
                    $path = $path->line($corners[$i][0], $corners[$i][1]);
                }
                $path = $path->close();
            }
        }

        return $path;
    }
}
