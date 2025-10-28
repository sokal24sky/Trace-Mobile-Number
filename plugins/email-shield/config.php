<?php
defined('ALTUMCODE') || die();

return (object) [
    'plugin_id' => 'email-shield',
    'name' => 'Email Shield',
    'description' => 'This plugin detects and blocks disposable email addresses to prevent spam & abuse on user signups.',
    'version' => '1.0.0',
    'url' => 'https://altumco.de/email-shield-plugin',
    'author' => 'AltumCode',
    'author_url' => 'https://altumcode.com/',
    'status' => 'inexistent',
    'actions' => true,
    'settings_url' => url('admin/settings/email_shield'),
    'avatar_style' => 'background-color: #FAD961;background-image: linear-gradient(90deg, #FAD961 0%, #F76B1C 100%);',
    'icon' => '🛡️',
];

