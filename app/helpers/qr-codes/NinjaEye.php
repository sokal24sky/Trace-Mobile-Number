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

final class NinjaEye implements EyeInterface, Singleton
{
    private static $instance;

    private function __construct()
    {
    }

    public static function instance() : self
    {
        return self::$instance ?: self::$instance = new self();
    }

    public function getExternalPath(): Path
    {
        $path = new Path();

        $scale = 0.5;
        $offset = 7.0; // center SVG (14x14) to (0,0)

        $commands = [
            // Outer contour (main organic loop)
            ['move',  [13.8, 1.1]],
            ['curve', [10.3, 5.0, 10.0, -1.0, 1.0, 0.1]],
            ['curve', [4.9, 3.6, -1.0, 4.0, 0.1, 13.0]],
            ['curve', [3.6, 9.1, 4.0, 15.0, 13.0, 13.9]],
            ['curve', [9.1, 10.4, 15.0, 10.0, 13.8, 1.1]],
            ['close', []],

            // Inner ring (cutout)
            ['move',  [9.4, 13.2]],
            ['curve', [2.9, 11.6, 4.2, 7.6, 0.8, 9.4]],
            ['curve', [2.4, 2.9, 7.6, 4.2, 5.8, 0.8]],
            ['curve', [12.3, 2.4, 11.0, 7.4, 14.4, 5.6]],
            ['curve', [11.7, 11.1, 7.6, 9.8, 9.4, 13.2]],
            ['close', []],
        ];

        foreach ($commands as [$cmd, $coords]) {
            if($cmd === 'move') {
                [$x, $y] = $coords;
                $path = $path->move(($x - $offset) * $scale, ($y - $offset) * $scale);
            }
            if($cmd === 'line') {
                [$x, $y] = $coords;
                $path = $path->line(($x - $offset) * $scale, ($y - $offset) * $scale);
            }
            if($cmd === 'curve') {
                [$x1, $y1, $x2, $y2, $x, $y] = $coords;
                $path = $path->curve(
                    ($x1 - $offset) * $scale, ($y1 - $offset) * $scale,
                    ($x2 - $offset) * $scale, ($y2 - $offset) * $scale,
                    ($x - $offset) * $scale, ($y - $offset) * $scale
                );
            }
            if($cmd === 'close') {
                $path = $path->close();
            }
        }

        return $path;
    }

    public function getInternalPath() : Path
    {
        $path = new Path();

        $scale = 0.5;
        $offset = 3.0;

        $commands = [
            ['move',  [3.5, 6.0]],
            ['curve', [0.7, 4.7, 1.7, 3.0, 0.0, 3.5]],
            ['curve', [1.3, 0.7, 3.0, 1.7, 2.5, 0.0]],
            ['curve', [5.3, 1.3, 4.3, 3.0, 6.0, 2.5]],
            ['curve', [4.7, 5.3, 3.0, 4.3, 3.5, 6.0]],
        ];

        foreach ($commands as [$command, $coords]) {
            $x1 = $x2 = $x = $y1 = $y2 = $y = null;

            if($command === 'move') {
                [$x, $y] = $coords;
                $x = ($x - $offset) * $scale;
                $y = ($y - $offset) * $scale;
                $path = $path->move($x, $y);
            }

            if($command === 'curve') {
                [$x1, $y1, $x2, $y2, $x, $y] = $coords;

                $x1 = ($x1 - $offset) * $scale;
                $y1 = ($y1 - $offset) * $scale;
                $x2 = ($x2 - $offset) * $scale;
                $y2 = ($y2 - $offset) * $scale;
                $x  = ($x  - $offset) * $scale;
                $y  = ($y  - $offset) * $scale;

                $path = $path->curve($x1, $y1, $x2, $y2, $x, $y);
            }
        }

        $path = $path->close();

        return $path;
    }
}
