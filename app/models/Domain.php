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

defined('ALTUMCODE') || die();

class Domain extends Model {

    public function get_available_domains_by_user($user) {
        if(!settings()->links->domains_is_enabled) return [];

        /* Get the domains */
        $domains = [];

        /* Try to check if the domain posts exists via the cache */
        $cache_instance = cache()->getItem('domains?user_id=' . $user->user_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Where */
            if(settings()->links->additional_domains_is_enabled) {
                $where = "(`user_id` = {$user->user_id} OR `type` = 1)";
            } else {
                $where = "`user_id` = {$user->user_id}";
            }

            $where .= " AND `is_enabled` = 1";

            /* Get data from the database */
            $domains_result = database()->query("SELECT * FROM `domains` WHERE {$where}");
            while($row = $domains_result->fetch_object()) {
                if($row->type == 1 && !in_array($row->domain_id, $user->plan_settings->additional_domains ?? [])) continue;

                /* Build the url */
                $row->url = $row->scheme . $row->host . '/';

                $domains[$row->domain_id] = $row;
            }

            /* Properly tag the cache */
            $cache_instance->set($domains)->expiresAfter(CACHE_DEFAULT_SECONDS);

            cache()->save($cache_instance);

        } else {

            /* Get cache */
            $domains = $cache_instance->get();

        }

        return $domains;

    }

    public function get_available_additional_domains() {
        if(!settings()->links->additional_domains_is_enabled) return [];

        /* Get the domains */
        $domains = [];

        /* Try to check if the user posts exists via the cache */
        $cache_instance = cache()->getItem('available_additional_domains');

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $domains_result = database()->query("SELECT * FROM `domains` WHERE `is_enabled` = 1 AND `type` = 1");
            while($row = $domains_result->fetch_object()) {

                /* Build the url */
                $row->url = $row->scheme . $row->host . '/';

                $domains[$row->domain_id] = $row;
            }

            cache()->save(
                $cache_instance->set($domains)->expiresAfter(CACHE_DEFAULT_SECONDS)
            );

        } else {

            /* Get cache */
            $domains = $cache_instance->get();

        }

        return $domains;
    }

    public function get_domain_by_host($host) {
        if(!settings()->links->domains_is_enabled) return null;

        /* Get the domain */
        $domain = null;

        /* Try to check if the domain posts exists via the cache */
        $cache_instance = cache()->getItem('domain?host=' . md5($host));

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $domain = db()->where('host', $host)->getOne('domains');

            if($domain) {
                /* Build the url */
                $domain->url = $domain->scheme . $domain->host . '/';

                cache()->save(
                    $cache_instance->set($domain)->expiresAfter(CACHE_DEFAULT_SECONDS)
                );
            }

        } else {

            /* Get cache */
            $domain = $cache_instance->get();

        }

        return $domain;

    }

    public function delete($domain_id) {
        /* Delete everything related to the links that the user owns */
        $result = database()->query("SELECT `link_id` FROM `links` WHERE `domain_id` = {$domain_id}");

        while($link = $result->fetch_object()) {
            (new \Altum\Models\Link())->delete($link->link_id);
        }

        /* Get the resource */
        $domain = db()->where('domain_id', $domain_id)->getOne('domains');

        /* Delete the resource */
        db()->where('domain_id', $domain_id)->delete('domains');

        /* Clear the cache */
        cache()->deleteItems(['domain?domain_id=' . $domain_id, 'domains?user_id=' . $domain->user_id, 'domains_total?user_id=' . $domain->user_id]);
        cache()->deleteItem('available_additional_domains');

    }

}
