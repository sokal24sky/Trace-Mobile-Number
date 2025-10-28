<?php

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class SpacedSquareModule implements ModuleInterface, Singleton
{
    private static $instance;
    private $size;

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
        $offset = (1 - $this->size) / 2;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $x_start = $x + $offset;
                $y_start = $y + $offset;

                /* Draw square path */
                $path = $path->move($x_start, $y_start);
                $path = $path->line($x_start + $this->size, $y_start);
                $path = $path->line($x_start + $this->size, $y_start + $this->size);
                $path = $path->line($x_start, $y_start + $this->size);
                $path = $path->close();
            }
        }

        return $path;
    }
}
