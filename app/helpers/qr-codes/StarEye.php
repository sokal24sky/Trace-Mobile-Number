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

final class StarEye implements EyeInterface, Singleton
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

    public function getInternalPath(): Path
    {
        $path = new Path();

        $scale = 0.5;
        $offset = 3.0;

        $commands = [
            ['move', [3.2, 0.3]],
            ['line', [3.8, 1.6]],
            ['curve', [4.0, 1.8, 4.1, 1.9, 4.3, 1.9]],
            ['line', [5.7, 2.1]],
            ['curve', [5.9, 2.1, 6.0, 2.4, 5.9, 2.6]],
            ['line', [4.9, 3.6]],
            ['curve', [4.7, 3.8, 4.7, 4.0, 4.7, 4.2]],
            ['line', [5.0, 5.5]],
            ['curve', [5.0, 5.7, 4.8, 5.9, 4.6, 5.8]],
            ['line', [3.3, 5.2]],
            ['curve', [3.1, 5.1, 2.9, 5.1, 2.7, 5.2]],
            ['line', [1.4, 5.8]],
            ['curve', [1.2, 5.9, 1.0, 5.8, 1.0, 5.5]],
            ['line', [1.2, 4.1]],
            ['curve', [1.2, 3.9, 1.2, 3.7, 1.1, 3.6]],
            ['line', [0.1, 2.6]],
            ['curve', [-0.1, 2.4, 0.0, 2.2, 0.2, 2.1]],
            ['line', [1.6, 1.9]],
            ['curve', [1.8, 1.9, 2.0, 1.8, 2.1, 1.6]],
            ['line', [2.7, 0.3]],
            ['curve', [2.9, 0.1, 3.1, 0.1, 3.2, 0.3]],
        ];

        foreach ($commands as [$command, $coords]) {
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
