<?php

namespace Altum\QrCodes;

use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class ElasticSquareModule implements ModuleInterface, Singleton
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

    public function createPath(\BaconQrCode\Encoder\ByteMatrix $matrix): Path
    {
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        $path = new Path();

        $scale = $this->size / 6.0;
        $margin = (1 - $this->size) / 2;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $ox = $x + $margin;
                $oy = $y + $margin;

                $path = $path->move($ox + 6.0 * $scale, $oy + 6.0 * $scale);

                $path = $path
                    ->curve(
                        $ox + 4.1 * $scale, $oy + 5.4 * $scale,
                        $ox + 1.9 * $scale, $oy + 5.4 * $scale,
                        $ox + 0.0 * $scale, $oy + 6.0 * $scale
                    )
                    ->curve(
                        $ox + 0.6 * $scale, $oy + 4.1 * $scale,
                        $ox + 0.6 * $scale, $oy + 1.9 * $scale,
                        $ox + 0.0 * $scale, $oy + 0.0 * $scale
                    )
                    ->curve(
                        $ox + 1.9 * $scale, $oy + 0.6 * $scale,
                        $ox + 4.1 * $scale, $oy + 0.6 * $scale,
                        $ox + 6.0 * $scale, $oy + 0.0 * $scale
                    )
                    ->curve(
                        $ox + 5.4 * $scale, $oy + 1.9 * $scale,
                        $ox + 5.4 * $scale, $oy + 4.1 * $scale,
                        $ox + 6.0 * $scale, $oy + 6.0 * $scale
                    )
                    ->close();
            }
        }

        return $path;
    }
}
