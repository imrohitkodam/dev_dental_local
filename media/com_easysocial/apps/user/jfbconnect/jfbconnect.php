<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2018 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2018/04/20
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// We want to import our app library
Foundry::import( 'admin:/includes/apps/apps' );

/**
 * JFBConnect application for EasySocial. Take note that all classes must be derived from the `SocialAppItem` class
 *
 */
class SocialUserAppJfbconnect extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
	}

    public function onAfterStreamSave($args)
    {
        if($args->context_type == 'photos')
        {
            $ogActionModel = JFBCFactory::model('OpenGraphAction');
            $actions = $ogActionModel->getActionsOfType('easysocial', "image_upload");
            foreach ($actions as $action)
            {
                $id = $args->context_id;
                $photo = Foundry::photo($id);

                $alias = $photo->data->getAlias();

                $user = Foundry::user($photo->data->uid);
                $userAlias = $user->getAlias(true);

                $url 	= FRoute::photos( array( 'layout' => 'item', 'id' => $alias, 'userid' => $userAlias ) );
                $url 	= '/' . ltrim( $url , '/' );
                $url 	= str_replace('/administrator/', '/', $url );
                $uri = JURI::getInstance();
                $url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port')) . $url;

                $ogActionModel->triggerAction($action, $url, null, $id);
            }
        }
    }
	
}
