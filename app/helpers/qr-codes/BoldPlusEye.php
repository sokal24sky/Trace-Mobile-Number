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

use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class BoldPlusEye implements EyeInterface, Singleton
{
    private static $instance;

    private function __construct()
    {
    }

    public static function instance() : self
    {
        return self::$instance ?: self::$instance = new self();
    }

    public function getExternalPath() : Path
    {
    }

    public function getInternalPath() : Path
    {
        $path = new Path();

        /* SVG is 6x6 — center and scale it into QR eye space */
        $scale = 0.5;
        $offset = 3.0;

        $points = [
            [6,1.5],[4.5,1.5],[4.5,0],[1.5,0],
            [1.5,1.5],[0,1.5],[0,4.5],[1.5,4.5],
            [1.5,6],[4.5,6],[4.5,4.5],[6,4.5]
        ];

        $first = true;
        foreach ($points as [$x, $y]) {
            $nx = ($x - $offset) * $scale;
            $ny = ($y - $offset) * $scale;

            if($first) {
                $path = $path->move($nx, $ny);
                $first = false;
            } else {
                $path = $path->line($nx, $ny);
            }
        }

        $path = $path->close();

        return $path;
    }
}
