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

namespace Altum;

defined('ALTUMCODE') || die();

class CustomHooks {

    public static function user_initiate_registration($data = []) {

    }

    public static function user_finished_registration($data = []) {

    }

    public static function user_delete($data = []) {

        /* Delete the potentially uploaded files on preference settings */
        if($data['user']->preferences->white_label_logo_light) {
            Uploads::delete_uploaded_file($data['user']->preferences->white_label_logo_light, 'users');
        }

        if($data['user']->preferences->white_label_logo_dark) {
            Uploads::delete_uploaded_file($data['user']->preferences->white_label_logo_dark, 'users');
        }

        if($data['user']->preferences->white_label_favicon) {
            Uploads::delete_uploaded_file($data['user']->preferences->white_label_favicon, 'users');
        }

        /* Delete everything related to the qr codes that the user owns */
        $result = database()->query("SELECT `qr_code_id` FROM `qr_codes` WHERE `user_id` = {$data['user']->user_id}");

        while($qr_code = $result->fetch_object()) {
            (new \Altum\Models\QrCode())->delete($qr_code->qr_code_id);
        }

    }

    public static function user_payment_finished($data = []) {
        extract($data);

        db()->where('user_id', $user->user_id)->update('users', [
            'qrcode_ai_qr_codes_current_month' => 0,
        ]);

    }

    public static function generate_language_prefixes_to_skip($data = []) {

        $prefixes = [];

        /* Base features */
        if(!empty(settings()->main->index_url)) {
            $prefixes = array_merge($prefixes, ['index.']);
        }

        if(!settings()->email_notifications->contact) {
            $prefixes = array_merge($prefixes, ['contact.']);
        }

        if(!settings()->main->api_is_enabled) {
            $prefixes = array_merge($prefixes, ['api.', 'api_documentation.', 'account_api.']);
        }

        if(!settings()->internal_notifications->admins_is_enabled) {
            $prefixes = array_merge($prefixes, ['global.notifications.']);
        }

        if(!settings()->cookie_consent->is_enabled) {
            $prefixes = array_merge($prefixes, ['global.cookie_consent.']);
        }

        if(!settings()->ads->ad_blocker_detector_is_enabled){
            $prefixes = array_merge($prefixes, ['ad_blocker_detector_modal.']);
        }

        if(!settings()->content->blog_is_enabled) {
            $prefixes = array_merge($prefixes, ['blog.']);
        }

        if(!settings()->content->pages_is_enabled) {
            $prefixes = array_merge($prefixes, ['page.', 'pages.']);
        }

        if(!settings()->users->register_is_enabled) {
            $prefixes = array_merge($prefixes, ['register.']);
        }

        /* Extended license */
        if(!settings()->payment->is_enabled) {
            $prefixes = array_merge($prefixes, ['plan.', 'pay.', 'pay_thank_you.', 'account_payments.']);
        }

        if(!settings()->payment->is_enabled || !settings()->payment->taxes_and_billing_is_enabled) {
            $prefixes = array_merge($prefixes, ['pay_billing.']);
        }

        if(!settings()->payment->is_enabled || !settings()->payment->codes_is_enabled) {
            $prefixes = array_merge($prefixes, ['account_redeem_code.']);
        }

        if(!settings()->payment->is_enabled || !settings()->payment->invoice_is_enabled) {
            $prefixes = array_merge($prefixes, ['invoice.']);
        }


        /* Plugins */
        if(!\Altum\Plugin::is_active('pwa') || !settings()->pwa->is_enabled) {
            $prefixes = array_merge($prefixes, ['pwa_install.']);
        }

        if(!\Altum\Plugin::is_active('push-notifications') || !settings()->push_notifications->is_enabled) {
            $prefixes = array_merge($prefixes, ['push_notifications_modal.']);
        }

        if(!\Altum\Plugin::is_active('teams')) {
            $prefixes = array_merge($prefixes, ['teams.', 'team.', 'team_create.', 'team_update.', 'team_members.', 'team_member_create.', 'team_member_update.', 'teams_member.', 'teams_member_delete_modal.', 'teams_member_join_modal.', 'teams_member_login_modal.']);
        }

        if(!\Altum\Plugin::is_active('affiliate') || (\Altum\Plugin::is_active('affiliate') && !settings()->affiliate->is_enabled)) {
            $prefixes = array_merge($prefixes, ['referrals.', 'affiliate.']);
        }

        /* Per product features */
        if(!settings()->codes->ai_qr_codes_is_enabled) {
            $prefixes = array_merge($prefixes, ['ai_qr_codes.', 'ai_qr_code_update.', 'ai_qr_code_create.']);
        }

        if(!settings()->codes->qr_codes_is_enabled) {
            $prefixes = array_merge($prefixes, ['qr_codes.', 'qr_code_update.', 'qr_code_create.', 'qr.']);
        }

        if(!settings()->codes->barcodes_is_enabled) {
            $prefixes = array_merge($prefixes, ['barcodes.', 'barcode_update.', 'barcode_create.']);
        }

        if(!settings()->codes->qr_reader_is_enabled) {
            $prefixes = array_merge($prefixes, ['qr_reader.',]);
        }

        if(!settings()->codes->barcode_reader_is_enabled) {
            $prefixes = array_merge($prefixes, ['barcode_reader.',]);
        }

        if(!settings()->links->domains_is_enabled) {
            $prefixes = array_merge($prefixes, ['domains.', 'domain_create.', 'domain_update.', 'domain_delete_modal.']);
        }

        if(!settings()->links->pixels_is_enabled) {
            $prefixes = array_merge($prefixes, ['pixels.', 'pixel_create.', 'pixel_update.']);
        }

        if(!settings()->links->projects_is_enabled) {
            $prefixes = array_merge($prefixes, ['projects.', 'project_create.', 'project_update.']);
        }

        return $prefixes;

    }

}
