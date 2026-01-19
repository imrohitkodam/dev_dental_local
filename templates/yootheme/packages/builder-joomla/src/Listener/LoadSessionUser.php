<?php

namespace YOOtheme\Builder\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\User;
use YOOtheme\Builder\Joomla\ArticleHelper;
use YOOtheme\Config;

class LoadSessionUser
{
    public ?User $user = null;
    public Config $config;
    public Session $session;
    public CMSApplication $joomla;

    public function __construct(Config $config, Session $session, CMSApplication $joomla)
    {
        $this->joomla = $joomla;
        $this->config = $config;
        $this->session = $session;
    }

    public function handle(): void
    {
        if (
            ArticleHelper::isArticleView() &&
            $this->config->get('req.customizer.admin') &&
            ($user_id = $this->config->get('req.customizer.user_id'))
        ) {
            $this->user = Factory::getUser();
            $this->setCurrentUser(Factory::getUser($user_id));
        }
    }

    public function reset(): void
    {
        if ($this->user) {
            $this->setCurrentUser($this->user);
        }
    }

    protected function setCurrentUser($user)
    {
        // If user with given id can't be found, Joomla will throw exception
        if (!isset($user->id)) {
            return;
        }

        $this->session->set('user', $user);

        if (method_exists($this->joomla, 'loadIdentity')) {
            $this->joomla->loadIdentity($user);

            // Set the flag indicating that MFA is already checked.
            $this->session->set('com_users.mfa_checked', 1);
        }
    }
}
