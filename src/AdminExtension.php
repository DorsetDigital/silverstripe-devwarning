<?php

namespace DorsetDigital\DevWarn;

use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;


class AdminExtension extends Extension
{
    public function onAfterInit()
    {

        $messages = [];

        $scriptTpl = <<<EOT
jQuery.noticeAdd({
  text: '%s',
  stay: false, 
  type: '%s'
});
EOT;

        if (Director::isDev()) {
            $messages[] = [
                'level' => 'warn',
                'message' => _t(__CLASS__ . '.DevModeWarning',
                    'Notice: This site is currently in development mode.  Please ensure that the correct security precautions are in place to protect it.')
            ];
        }

        if (Director::isLive() && (Environment::getEnv('SS_DEFAULT_ADMIN_PASSWORD') != '')) {
            $messages[] = [
                'level' => 'warn',
                'message' => _t(__CLASS__ . '.DefaultAdminWarning',
                    'Notice: A default administrator password is set in the site config.')
            ];
        }

        if (!empty($messages)) {
            $script = null;
            foreach ($messages as $message) {
                $script .= sprintf($scriptTpl, $message['message'], $message['level']);
            }
            Requirements::customScript($script);
        }
    }
}