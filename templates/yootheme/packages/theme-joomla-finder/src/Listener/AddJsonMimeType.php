<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;

class AddJsonMimeType
{
    public CMSApplication $joomla;

    public function __construct(CMSApplication $joomla)
    {
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        if (
            version_compare(JVERSION, '4.0', '>') ||
            $this->joomla->input->getMethod() !== 'POST' ||
            $this->joomla->input->getCmd('option') !== 'com_media' ||
            !str_contains(
                $this->joomla->input->server->getString('HTTP_ACCEPT', ''),
                'application/json',
            ) ||
            headers_sent()
        ) {
            return;
        }

        $this->joomla->clearHeaders();
        $this->joomla->setHeader('Status', '200');
        $this->joomla->mimeType = 'application/json';
        $this->joomla->setBody(json_encode($this->joomla->getMessageQueue(true)));
        $this->joomla->set('gzip', false);
        $this->joomla->getSession()->set('application.queue', []);
    }
}
