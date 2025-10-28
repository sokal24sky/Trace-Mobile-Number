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

final class ShieldEye implements EyeInterface, Singleton
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

        /* centre‑and‑scale helpers */
        $scale_factor  = 0.6; /* tweak weight / size here */
        $center_offset = 3.0;

        $convert = static function (float $x_coordinate, float $y_coordinate) use ($scale_factor, $center_offset): array {
            return [
                ($x_coordinate - $center_offset) * $scale_factor,
                ($y_coordinate - $center_offset) * $scale_factor,
            ];
        };

        /* start: top‑left corner a little below the border for subtle rounding */
        [$start_x, $start_y] = $convert(1.0, 0.8);
        $path = $path->move($start_x, $start_y);

        /* gentle crown‑curve across the top (left → right) */
        /*   control points give just a hint of arc – less “blob”, more “shield”  */
        [$ctrl1_x, $ctrl1_y] = $convert(2.3, 0.3);
        [$ctrl2_x, $ctrl2_y] = $convert(3.7, 0.3);
        [$top_right_x, $top_right_y] = $convert(5.0, 0.8);
        $path = $path->curve($ctrl1_x, $ctrl1_y, $ctrl2_x, $ctrl2_y, $top_right_x, $top_right_y);

        /* crisp right flank down to mid‑height */
        [$right_mid_x, $right_mid_y] = $convert(5.4, 3.1);
        $path = $path->line($right_mid_x, $right_mid_y);

        /* diagonal to bottom tip */
        [$bottom_tip_x, $bottom_tip_y] = $convert(3.0, 5.4);
        $path = $path->line($bottom_tip_x, $bottom_tip_y);

        /* left flank up */
        [$left_mid_x, $left_mid_y] = $convert(0.6, 3.1);
        $path = $path->line($left_mid_x, $left_mid_y);

        /* close back to start */
        return $path->line($start_x, $start_y)->close();
    }
}
