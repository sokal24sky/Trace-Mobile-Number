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

class AiQrCode extends Model {

    public function delete($ai_qr_code_id) {

        if(!$ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->getOne('ai_qr_codes', ['user_id', 'ai_qr_code_id', 'ai_qr_code'])) {
            return;
        }

        Uploads::delete_uploaded_file($ai_qr_code->ai_qr_code, 'ai_qr_codes');

        /* Delete from database */
        db()->where('ai_qr_code_id', $ai_qr_code_id)->delete('ai_qr_codes');

        /* Clear the cache */
        cache()->deleteItem('ai_qr_codes_total?user_id=' . $ai_qr_code->user_id);
        cache()->deleteItem('ai_qr_codes_dashboard?user_id=' . $ai_qr_code->user_id);

    }
}
