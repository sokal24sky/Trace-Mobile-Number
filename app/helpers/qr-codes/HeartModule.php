<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

namespace Altum\QrCodes;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class HeartModule implements ModuleInterface, Singleton
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

    public function createPath(ByteMatrix $matrix) : Path
    {
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        $path = new Path();
        $halfSize = $this->size / 2;
        $margin = (1 - $this->size) / 2;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if(! $matrix->get($x, $y)) {
                    continue;
                }

                $pathX = $x + $margin;
                $pathY = $y + $margin;

                $path = $path
                    ->move($pathX + $this->size, $pathY + $halfSize)
                    ->ellipticArc(0, 0, 0, false, true, $pathX + $halfSize, $pathY + $this->size)
                    ->ellipticArc(0, 0, 0, false, true, $pathX, $pathY + $halfSize)
                    ->ellipticArc(0.1, 0.1, 0, false, true, $pathX + $halfSize, $pathY)
                    ->ellipticArc(0.1, 0.1, 0, false, true, $pathX + $this->size, $pathY + $halfSize)
                    ->close()
                ;
            }
        }

        return $path;
    }
}
