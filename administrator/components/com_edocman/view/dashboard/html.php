<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class EDocmanViewDashboardHtml extends OSViewHtml
{

	public $hasModel = false;
	/**
	 * Display.
	 */
	public function display()
	{
		$model = OSModel::getInstance('Documents', 'EDocmanModel', array('ignore_request' => TRUE));
		$latestDocuments = $model->filter_state('P')
			->filter_order('created_time')
			->filter_order_Dir('DESC')
			->limit(5)
			->getData();
		
		$latestUpdatedDocuments = $model->reset()
			->filter_order('modified_time')
			->filter_order_Dir('DESC')
			->limit(5)
			->getData();
		
		$topHitDocuments = $model->reset()
			->filter_order('hits')
			->filter_order_Dir('DESC')
			->limit(5)
			->getData();
		
		$topDownloadDocuments = $model->reset()
			->filter_order('downloads')
			->filter_order_Dir('DESC')
			->limit(5)
			->getData();
		$this->user = JFactory::getUser();
		$this->latestDocuments = $latestDocuments;
		$this->latestUpdatedDocuments = $latestUpdatedDocuments;
		$this->topHitDocuments = $topHitDocuments;
		$this->topDownloadDocuments = $topDownloadDocuments;
		$this->addToolbar();
		EDocmanHelperHtml::renderSubmenu('dashboard');
		parent::display();
	}

	/**
	 * Add toolbar to the view
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title('EDOCMAN - '.JText::_('EDOCMAN_DASHBOARD'), 'dashboard.png');
		$canDo = EdocmanHelper::getActions();
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_edocman');
		}
	}

	/**
	 * Creates the buttons view.
	 * 
	 * @param string $link
	 *        	targeturl
	 * @param string $image
	 *        	path to image
	 * @param string $text
	 *        	image description
	 */
	protected function quickiconButton($link, $image, $text, $id = null)
	{
		// initialise variables
		$lang = JFactory::getLanguage();
        if($text == JText::_('EDOCMAN_HELP')){
            $target = "target='_blank'";
        }else{
            $target = "";
        }
	?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;" <?php if ($id) echo 'id="'.$id.'"'; ?>>
			<div class="icon">
				<a href="<?php echo $link; ?>" <?php echo $target; ?>>
					<?php echo JHtml::_('image', 'administrator/components/com_edocman/assets/images/' . $image, $text); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
	<?php
	}
}
