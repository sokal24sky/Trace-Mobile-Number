<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class CornerCutModule implements ModuleInterface, Singleton
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
        $cut_ratio = 0.4; /* Controls how deep corners are cut */
        $margin = (1 - $this->size) / 2;
        $end = $this->size;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $origin_x = $x + $margin;
                $origin_y = $y + $margin;
                $cut = $this->size * $cut_ratio;

                /* Coordinates: top-left corner to bottom-right (clockwise) */
                $path = $path->move($origin_x + $cut, $origin_y); /* Cut top-left corner */
                $path = $path->line($origin_x + $end, $origin_y);
                $path = $path->line($origin_x + $end, $origin_y + $end - $cut); /* bottom-right cut */
                $path = $path->line($origin_x + $end - $cut, $origin_y + $end);
                $path = $path->line($origin_x, $origin_y + $end);
                $path = $path->line($origin_x, $origin_y + $cut);
                $path = $path->close();
            }
        }

        return $path;
    }
}
