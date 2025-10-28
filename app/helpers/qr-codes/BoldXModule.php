<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class BoldXModule implements ModuleInterface, Singleton
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
        $teeth = 3; /* number of zigs per edge */
        $margin = (1 - $this->size) / 2;
        $s = $this->size;
        $tooth_width = $s / ($teeth * 2); /* zig width */

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $ox = $x + $margin;
                $oy = $y + $margin;

                $points = [];

                /* Top edge (zig right) */
                for ($i = 0; $i < $teeth; $i++) {
                    $x1 = $ox + $i * 2 * $tooth_width;
                    $x2 = $x1 + $tooth_width;
                    $x3 = $x1 + 2 * $tooth_width;
                    $points[] = [$x1, $oy];
                    $points[] = [$x2, $oy + $tooth_width];
                    $points[] = [$x3, $oy];
                }

                /* Right edge (zig down) */
                for ($i = 0; $i < $teeth; $i++) {
                    $y1 = $oy + $i * 2 * $tooth_width;
                    $y2 = $y1 + $tooth_width;
                    $y3 = $y1 + 2 * $tooth_width;
                    $points[] = [$ox + $s, $y1];
                    $points[] = [$ox + $s - $tooth_width, $y2];
                    $points[] = [$ox + $s, $y3];
                }

                /* Bottom edge (zig left) */
                for ($i = $teeth - 1; $i >= 0; $i--) {
                    $x1 = $ox + $i * 2 * $tooth_width;
                    $x2 = $x1 + $tooth_width;
                    $x3 = $x1 + 2 * $tooth_width;
                    $points[] = [$x3, $oy + $s];
                    $points[] = [$x2, $oy + $s - $tooth_width];
                    $points[] = [$x1, $oy + $s];
                }

                /* Left edge (zig up) */
                for ($i = $teeth - 1; $i >= 0; $i--) {
                    $y1 = $oy + $i * 2 * $tooth_width;
                    $y2 = $y1 + $tooth_width;
                    $y3 = $y1 + 2 * $tooth_width;
                    $points[] = [$ox, $y3];
                    $points[] = [$ox + $tooth_width, $y2];
                    $points[] = [$ox, $y1];
                }

                /* draw path */
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
