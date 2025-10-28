<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class RoundedModule implements ModuleInterface, Singleton
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
        $unit = $this->size;
        $half_unit = $unit / 2;
        $margin = (1 - $unit) / 2;

        $corner_radius = $unit * 0.25;
        $segments = 5; /* segments per quarter circle */

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $x0 = $x + $margin;
                $y0 = $y + $margin;
                $x1 = $x0 + $unit;
                $y1 = $y0 + $unit;

                /* Start at top-left corner after rounding */
                $path = $path->move($x0 + $corner_radius, $y0);

                /* Top edge */
                $path = $path->line($x1 - $corner_radius, $y0);

                /* Top-right corner (simulate quarter arc) */
                for ($i = 0; $i <= $segments; $i++) {
                    $angle = deg2rad(270 + ($i * 90 / $segments));
                    $path = $path->line(
                        $x1 - $corner_radius + cos($angle) * $corner_radius,
                        $y0 + $corner_radius + sin($angle) * $corner_radius
                    );
                }

                /* Right edge */
                $path = $path->line($x1, $y1 - $corner_radius);

                /* Bottom-right corner */
                for ($i = 0; $i <= $segments; $i++) {
                    $angle = deg2rad(0 + ($i * 90 / $segments));
                    $path = $path->line(
                        $x1 - $corner_radius + cos($angle) * $corner_radius,
                        $y1 - $corner_radius + sin($angle) * $corner_radius
                    );
                }

                /* Bottom edge */
                $path = $path->line($x0 + $corner_radius, $y1);

                /* Bottom-left corner */
                for ($i = 0; $i <= $segments; $i++) {
                    $angle = deg2rad(90 + ($i * 90 / $segments));
                    $path = $path->line(
                        $x0 + $corner_radius + cos($angle) * $corner_radius,
                        $y1 - $corner_radius + sin($angle) * $corner_radius
                    );
                }

                /* Left edge */
                $path = $path->line($x0, $y0 + $corner_radius);

                /* Top-left corner */
                for ($i = 0; $i <= $segments; $i++) {
                    $angle = deg2rad(180 + ($i * 90 / $segments));
                    $path = $path->line(
                        $x0 + $corner_radius + cos($angle) * $corner_radius,
                        $y0 + $corner_radius + sin($angle) * $corner_radius
                    );
                }

                $path = $path->close();
            }
        }

        return $path;
    }
}
