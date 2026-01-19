<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Session\Session;
use YOOtheme\Arr;
use YOOtheme\Config;

class LoadCustomizerSession
{
    public Config $config;
    public Session $session;
    public CMSApplication $joomla;

    public function __construct(Config $config, CMSApplication $joomla, Session $session)
    {
        $this->config = $config;
        $this->joomla = $joomla;
        $this->session = $session;
    }

    public function handle(): void
    {
        $cookie = hash_hmac(
            'md5',
            $this->config->get('theme.template'),
            $this->config->get('app.secret'),
        );

        // If not customizer route
        if ($this->joomla->input->get('p') !== 'customizer') {
            // Is frontend request and has customizer cookie
            if (!$this->config->get('app.isSite') || !$this->joomla->input->cookie->get($cookie)) {
                return;
            }

            // Get params from frontend session
            $params = $this->session->get($cookie) ?: [];

            // Get customizer config from request
            if ($custom = $this->joomla->input->getBase64('customizer')) {
                $params = array_replace($params, json_decode(base64_decode($custom), true));
                $this->session->set($cookie, Arr::pick($params, ['config', 'admin', 'user_id']));
            }

            // Override theme config
            if (isset($params['config'])) {
                $this->config->set('~theme', $params['config']);
            }

            // Pass through e.g. page, modules and template params
            $this->config->add('req.customizer', $params);
        }

        $this->joomla->set('caching', 0);
        $this->config->set('app.isCustomizer', true);
        $this->config->set('theme.cookie', $cookie);
        $this->config->set('customizer.id', $this->config->get('theme.id'));
    }
}
