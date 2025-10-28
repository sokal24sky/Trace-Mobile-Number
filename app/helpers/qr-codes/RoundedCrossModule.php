<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class RoundedCrossModule implements ModuleInterface, Singleton
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

        $scale = $this->size / 6.0;
        $offset = (1 - $this->size) / 2;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $ox = $x + $offset;
                $oy = $y + $offset;

                /* Match the exact SVG structure */
                $path = $path->move($ox + 4.5 * $scale, $oy + 1.5 * $scale);

                $path = $path->curve(
                    $ox + 4.5 * $scale, $oy + 0.7 * $scale,
                    $ox + 3.8 * $scale, $oy + 0.0 * $scale,
                    $ox + 3.0 * $scale, $oy + 0.0 * $scale
                );

                $path = $path->curve(
                    $ox + 2.2 * $scale, $oy + 0.0 * $scale,
                    $ox + 1.5 * $scale, $oy + 0.7 * $scale,
                    $ox + 1.5 * $scale, $oy + 1.5 * $scale
                );

                $path = $path->curve(
                    $ox + 0.7 * $scale, $oy + 1.5 * $scale,
                    $ox + 0.0 * $scale, $oy + 2.2 * $scale,
                    $ox + 0.0 * $scale, $oy + 3.0 * $scale
                );

                $path = $path->curve(
                    $ox + 0.0 * $scale, $oy + 3.8 * $scale,
                    $ox + 0.7 * $scale, $oy + 4.5 * $scale,
                    $ox + 1.5 * $scale, $oy + 4.5 * $scale
                );

                $path = $path->curve(
                    $ox + 1.5 * $scale, $oy + 5.3 * $scale,
                    $ox + 2.2 * $scale, $oy + 6.0 * $scale,
                    $ox + 3.0 * $scale, $oy + 6.0 * $scale
                );

                $path = $path->curve(
                    $ox + 3.8 * $scale, $oy + 6.0 * $scale,
                    $ox + 4.5 * $scale, $oy + 5.3 * $scale,
                    $ox + 4.5 * $scale, $oy + 4.5 * $scale
                );

                $path = $path->curve(
                    $ox + 5.3 * $scale, $oy + 4.5 * $scale,
                    $ox + 6.0 * $scale, $oy + 3.8 * $scale,
                    $ox + 6.0 * $scale, $oy + 3.0 * $scale
                );

                $path = $path->curve(
                    $ox + 6.0 * $scale, $oy + 2.2 * $scale,
                    $ox + 5.3 * $scale, $oy + 1.5 * $scale,
                    $ox + 4.5 * $scale, $oy + 1.5 * $scale
                );

                $path = $path->close();
            }
        }

        return $path;
    }
}
