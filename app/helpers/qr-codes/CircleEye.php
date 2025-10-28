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

final class CircleEye implements EyeInterface, Singleton
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
        return (new Path())
            ->move(3.5, 0)
            ->ellipticArc(3.5, 3.5, 0., false, true, 0., 3.5)
            ->ellipticArc(3.5, 3.5, 0., false, true, -3.5, 0.)
            ->ellipticArc(3.5, 3.5, 0., false, true, 0., -3.5)
            ->ellipticArc(3.5, 3.5, 0., false, true, 3.5, 0.)
            ->close()
            ->move(2.5, 0)
            ->ellipticArc(2.5, 2.5, 0., false, true, 0., 2.5)
            ->ellipticArc(2.5, 2.5, 0., false, true, -2.5, 0.)
            ->ellipticArc(2.5, 2.5, 0., false, true, 0., -2.5)
            ->ellipticArc(2.5, 2.5, 0., false, true, 2.5, 0.)
            ->close()
            ;
    }

    public function getInternalPath() : Path
    {
        return (new Path())
            ->move(1.5, 0)
            ->ellipticArc(1.5, 1.5, 0., false, true, 0., 1.5)
            ->ellipticArc(1.5, 1.5, 0., false, true, -1.5, 0.)
            ->ellipticArc(1.5, 1.5, 0., false, true, 0., -1.5)
            ->ellipticArc(1.5, 1.5, 0., false, true, 1.5, 0.)
            ->close()
            ;
    }
}
