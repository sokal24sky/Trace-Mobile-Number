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
use BaconQrCode\Renderer\Eye\SquareEye;
use BaconQrCode\Renderer\Path\Path;
use SimpleSoftwareIO\QrCode\Singleton;

final class EyeCombiner implements EyeInterface, Singleton
{
    /**
     * @var self|null
     */
    private static $instance;

    private static $inner_eyes = [
        'square' => SquareEye::class,
        'dot' => CircleEye::class,
        'rounded' => RoundedEye::class,
        'diamond' => DiamondEye::class,
        'flower' => FlowerEye::class,
        'leaf' => LeafEye::class,
        'sun' => SunEye::class,
        'heart' => HeartEye::class,
        'bold_plus' => BoldPlusEye::class,
        'star' => StarEye::class,
        'shine' => ShineEye::class,
        'rounded_cross' => RoundedCrossEye::class,
        'cross_x' => CrossXEye::class,
        'curvy_x' => CurvyXEye::class,
        'ninja' => NinjaEye::class,
        'elastic_square' => ElasticSquareEye::class,
        'inverted_squircle' => InvertedSquircleEye::class,
        'hexagon' => HexagonEye::class,
        'octagon' => OctagonEye::class,
        'shield' => ShieldEye::class,
        'thick_star' => ThickStarEye::class,
    ];

    private static $outer_eyes = [
        'square' => SquareEye::class,
        'circle' => CircleEye::class,
        'rounded' => RoundedEye::class,
        'flower' => FlowerEye::class,
        'leaf' => LeafEye::class,
        'ninja' => NinjaEye::class,
        'hexagon' => HexagonEye::class,
        'octagon' => OctagonEye::class,
    ];

    private static $outer_eye;

    private static $inner_eye;

    private function __construct()
    {
    }

    public static function instance($inner_eye = null, $outer_eye = null) : self
    {
        self::$inner_eye = array_key_exists($inner_eye, self::$inner_eyes) ? self::$inner_eyes[$inner_eye] : self::$inner_eyes['square'];

        self::$outer_eye = array_key_exists($outer_eye, self::$outer_eyes) ? self::$outer_eyes[$outer_eye] : self::$outer_eyes['square'];

        return self::$instance ?: self::$instance = new self();
    }

    public function getExternalPath() : Path
    {
        return (\call_user_func([self::$outer_eye, 'instance']))->getExternalPath();
    }

    public function getInternalPath() : Path
    {
        return (\call_user_func([self::$inner_eye, 'instance']))->getInternalPath();
    }
}


