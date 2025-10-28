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

namespace Altum\Controllers;

use Altum\Models\Plan;

defined('ALTUMCODE') || die();

class Affiliate extends Controller {

    public function index() {

        if(!\Altum\Plugin::is_active('affiliate') || (\Altum\Plugin::is_active('affiliate') && !settings()->affiliate->is_enabled)) {
            redirect('not-found');
        }

        /* Get the min & max of commission for affiliates */
        $plans = (new \Altum\Models\Plan())->get_plans();
        $plans['free'] = (new Plan())->get_plan_by_id('free');
        $plans['custom'] = (new Plan())->get_plan_by_id('custom');
        unset($plans['guest']);

        $minimum_commission = 100;
        $maximum_commission = 0;

        foreach($plans as $plan) {
            if($plan->settings->affiliate_commission_percentage > $maximum_commission) $maximum_commission = $plan->settings->affiliate_commission_percentage;
            if($plan->settings->affiliate_commission_percentage < $minimum_commission) $minimum_commission = $plan->settings->affiliate_commission_percentage;
        }

        

        /* Prepare the view */
        $data = [
            'minimum_commission' => $minimum_commission,
            'maximum_commission' => $maximum_commission,
        ];

        $view = new \Altum\View('affiliate/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}


