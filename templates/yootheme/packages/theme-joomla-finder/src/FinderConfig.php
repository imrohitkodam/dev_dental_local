<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\User\User;
use YOOtheme\ConfigObject;
use YOOtheme\Joomla\Media;

class FinderConfig extends ConfigObject
{
    /**
     * Constructor.
     */
    public function __construct(User $user)
    {
        $params = ComponentHelper::getParams('com_media');

        // allow all allowable file extensions and MIME types in input field
        $accept = [];

        if ($params->get('restrict_uploads', true)) {
            foreach (explode(',', $params->get('upload_extensions', '')) as $extension) {
                $accept[] = '.' . trim($extension);
            }

            if ($params->get('check_mime', true)) {
                foreach (explode(',', $params->get('upload_mime', '')) as $mime) {
                    $accept[] = trim($mime);
                }
            }
        }

        parent::__construct([
            'accept' => implode(',', $accept),
            'roots' => Media::getRootPaths(),
            'legacy' => version_compare(JVERSION, '4.0', '<'),
            'canCreate' =>
                $user->authorise('core.manage', 'com_media') ||
                $user->authorise('core.create', 'com_media'),
            'canDelete' =>
                $user->authorise('core.manage', 'com_media') ||
                $user->authorise('core.delete', 'com_media'),
        ]);
    }
}
