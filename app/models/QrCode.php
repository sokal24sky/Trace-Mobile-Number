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

namespace Altum\Models;

use Altum\Uploads;

defined('ALTUMCODE') || die();

class QrCode extends Model {

    public function delete($qr_code_id) {

        if(!$qr_code = db()->where('qr_code_id', $qr_code_id)->getOne('qr_codes', ['user_id', 'qr_code_id', 'qr_code', 'qr_code_logo', 'qr_code_background'])) {
            return;
        }

        Uploads::delete_uploaded_file($qr_code->qr_code ?? '', 'qr_codes/logo');
        Uploads::delete_uploaded_file($qr_code->qr_code_logo ?? '', 'qr_codes/logo');
        Uploads::delete_uploaded_file($qr_code->qr_code_background ?? '', 'qr_code_background');
        Uploads::delete_uploaded_file($qr_code->qr_code_foreground ?? '', 'qr_code_foreground');

        /* Delete from database */
        db()->where('qr_code_id', $qr_code_id)->delete('qr_codes');

        /* Clear the cache */
        cache()->deleteItem('qr_codes_total?user_id=' . $qr_code->user_id);
        cache()->deleteItem('qr_codes_dashboard?user_id=' . $qr_code->user_id);

    }
}
