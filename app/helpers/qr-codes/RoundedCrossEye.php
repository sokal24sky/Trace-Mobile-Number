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

final class RoundedCrossEye implements EyeInterface, Singleton
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

        $scale = 0.5;
        $offset = 3.0;

        $points = [
            ['move', [3.0, 5.1]],
            ['curve', [3.4, 5.5, 3.7, 5.8, 4.5, 6.0]],
            ['curve', [5.3, 6.0, 6.0, 5.3, 6.0, 4.5]],
            ['curve', [6.0, 4.1, 5.8, 3.7, 5.6, 3.4]],
            ['line',  [5.1, 3.0]],
            ['line',  [5.6, 2.6]],
            ['curve', [5.8, 2.3, 6.0, 1.9, 6.0, 1.5]],
            ['curve', [6.0, 0.7, 5.3, 0.0, 4.5, 0.0]],
            ['curve', [4.1, 0.0, 3.7, 0.2, 3.4, 0.4]],
            ['line',  [3.0, 0.9]],
            ['line',  [2.6, 0.4]],
            ['curve', [2.3, 0.2, 1.9, 0.0, 1.5, 0.0]],
            ['curve', [0.7, 0.0, 0.0, 0.7, 0.0, 1.5]],
            ['curve', [0.0, 1.9, 0.2, 2.3, 0.4, 2.6]],
            ['line',  [0.9, 3.0]],
            ['line',  [0.4, 3.4]],
            ['curve', [0.2, 3.7, 0.0, 4.1, 0.0, 4.5]],
            ['curve', [0.0, 5.3, 0.7, 6.0, 1.5, 6.0]],
            ['curve', [1.9, 6.0, 2.3, 5.8, 2.6, 5.6]],
            ['line',  [3.0, 5.1]],
        ];

        foreach ($points as [$command, $coords]) {
            $x1 = $x2 = $x = $y1 = $y2 = $y = null;

            if($command === 'move' || $command === 'line') {
                [$x, $y] = $coords;
                $x = ($x - $offset) * $scale;
                $y = ($y - $offset) * $scale;

                $path = ($command === 'move') ? $path->move($x, $y) : $path->line($x, $y);
            }

            if($command === 'curve') {
                [$x1, $y1, $x2, $y2, $x, $y] = $coords;

                $x1 = ($x1 - $offset) * $scale;
                $y1 = ($y1 - $offset) * $scale;
                $x2 = ($x2 - $offset) * $scale;
                $y2 = ($y2 - $offset) * $scale;
                $x = ($x - $offset) * $scale;
                $y = ($y - $offset) * $scale;

                $path = $path->curve($x1, $y1, $x2, $y2, $x, $y);
            }
        }

        $path = $path->close();

        return $path;
    }
}
