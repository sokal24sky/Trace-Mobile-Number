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

class Barcode extends Model {

    public function delete($barcode_id) {

        if(!$barcode = db()->where('barcode_id', $barcode_id)->getOne('barcodes', ['user_id', 'barcode_id', 'barcode'])) {
            return;
        }

        Uploads::delete_uploaded_file($barcode->barcode, 'barcodes/logo');

        /* Delete from database */
        db()->where('barcode_id', $barcode_id)->delete('barcodes');

        /* Clear the cache */
        cache()->deleteItem('barcodes_total?user_id=' . $barcode->user_id);
        cache()->deleteItem('barcodes_dashboard?user_id=' . $barcode->user_id);
    }
}
