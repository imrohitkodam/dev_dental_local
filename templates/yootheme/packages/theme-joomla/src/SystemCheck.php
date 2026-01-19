<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use function YOOtheme\trans;

class SystemCheck extends BaseSystemCheck
{
    protected ApiKey $apiKey;
    protected DatabaseDriver $db;

    /**
     * Constructor.
     */
    public function __construct(DatabaseDriver $db, ApiKey $apiKey)
    {
        $this->db = $db;
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritdoc
     */
    public function getRequirements()
    {
        $res = [];

        // Check for debug mode
        if (constant('JDEBUG')) {
            $res[] = trans(
                'The System debug mode generates too much session data which can lead to unexpected behavior. Disable the debug mode.',
            );
        }

        // Check for SEBLOD Plugin and setting
        $components = ComponentHelper::getComponents();
        $cck = $components['com_cck'] ?? false;
        if ($cck && $cck->enabled == 1) {
            if ($cck->getParams()->get('hide_edit_icon')) {
                $res[] = trans(
                    'The SEBLOD plugin causes the builder to be unavailable. Disable the feature <em>Hide Edit Icon</em> in the <a href="index.php?option=com_config&view=component&component=com_cck" target="_blank">SEBLOD configuration</a>.',
                );
            }
        }

        try {
            // Check for RSFirewall settings @TODO check if enabled?
            $rsfw = $this->db
                ->setQuery(
                    sprintf(
                        'SELECT value FROM #__rsfirewall_configuration WHERE name = %s',
                        $this->db->quote('verify_emails'),
                    ),
                )
                ->loadResult();

            if ($rsfw == 1) {
                $res[] = trans(
                    'The RSFirewall plugin corrupts the builder content. Disable the feature <em>Convert email addresses from plain text to images</em> in the <a href="index.php?option=com_rsfirewall&view=configuration" target="_blank">RSFirewall configuration</a>.',
                );
            }
        } catch (\Exception $e) {
        }

        return array_merge($res, parent::getRequirements());
    }

    protected function hasApiKey()
    {
        return $this->apiKey->get();
    }
}
