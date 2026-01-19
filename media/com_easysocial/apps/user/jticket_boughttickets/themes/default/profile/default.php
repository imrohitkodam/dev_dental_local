<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

$lang = Factory::getLanguage();
$extension                = 'com_jticketing';
$base_dir                 = JPATH_SITE;
$lang->load($extension, $base_dir);
$payment_statuses = array(
	'P' => Text::_('JT_PSTATUS_PENDING'),
	'C' => Text::_('JT_PSTATUS_COMPLETED'),
	'D' => Text::_('JT_PSTATUS_DECLINED'),
	'E' => Text::_('JT_PSTATUS_FAILED'),
	'UR' => Text::_('JT_PSTATUS_UNDERREVIW'),
	'RF' => Text::_('JT_PSTATUS_REFUNDED'),
	'CRV' => Text::_('JT_PSTATUS_CANCEL_REVERSED'),
	'RV' => Text::_('JT_PSTATUS_REVERSED'),
	'I' => Text::_('JT_PSTATUS_INITIATED')
);

$jticketingfrontendhelper = JPATH_ROOT . '/components/com_jticketing/helpers/frontendhelper.php';

if (!class_exists('jticketingfrontendhelper'))
{
	JLoader::register('jticketingfrontendhelper', $jticketingfrontendhelper);
	JLoader::load('jticketingfrontendhelper');
}

// Load assets
JT::utilities()->loadjticketingAssetFiles();
$utilities = JT::utilities();
?>

<div class="es-filterbar">
	<div class="filterbar-title h5 pull-left">
		<?php	echo Text::_('APP_JTICKET_BOUGHTTICKETS_USER_TITLE');?>
	</div>
	<div class="clearfix"> &nbsp;</div>
</div>
<div class="clearfix"> &nbsp;</div>

<div class="<?php echo JTICKETING_WRAPPER_CLASS; ?>">
<?php

if (empty($target_data))
{
	$divhtml = '<div class="empty"><i class="ies-droplet"></i>';

	if ($no_authorize === 'no')
	{
		// Not authorized
		$divhtml .= Text::_('APP_JTICKET_BOUGHTTICKETS_NOT_AUTHORIZED');
	}
	else
	{
		// Nothing found
		$divhtml .= Text::_('APP_JTICKET_BOUGHTTICKETS_NO_TICKETS');
	}

	$divhtml .= '</div>';
	echo $divhtml;
}
else
{
?>
<div class='row-fluid'>
	<div id="jticket_es_app_boughttickets">
		<table  class="table table-striped table-hover " width="100%">
			<tr>
				<th align="center"><?php	echo Text::_('APP_JTICKET_BOUGHTTICKETS_EVENT');?></th>
				<th align="center"><?php	echo Text::_('Ticket No');?></th>
				<th align="center"><?php	echo Text::_('TICKET_RATE');?></th>
				<th align="center"><?php	echo Text::_('PAYMENT_STATUS');?></th>
				<th align="center"><?php	echo Text::_('VIEW_TICKET');?></th>
			</tr>
		<?php

		foreach ($target_data as $data)
		{
			$integration = JT::getIntegration();
			$event = JT::event($data->eventid, $integration);
			$startdate = Factory::getDate($data->startdate)->Format(Text::_('COM_JTICKETING_DATE_FORMAT_SHOW_AMPM'));
			$enddate   = Factory::getDate($data->enddate)->Format(Text::_('COM_JTICKETING_DATE_FORMAT_SHOW_AMPM'));
			$ticketid = Text::_("TICKET_PREFIX") . $data->id . '-' . $data->order_items_id;

			/*if ($data->startdate == $data->enddate)
			{
				$datetoshow = Text::sprintf('EVENTS_DURATION_ONE', $startdate);
			}
			else
			{
				$datetoshow = Text::sprintf('EVENTS_DURATION', $startdate, $enddate);
			}*/

			?>
			<tr>
				<td>
				<a href="<?php echo $event->getUrl(); ?>">
				<?php    echo $data->title;?></a>
				</td>
				<td>
				<?php    echo $ticketid;?>
				</td>
				<td align="center">
				<?php
				echo $utilities->getFormattedPrice($data->price);
				?>
				</td>
				<td align="center">
				<?php	echo $payment_statuses[$data->STATUS];?></td>
				<td	align="center">
				<?php
				if ($data->STATUS == 'C')
				{
					$link_o = '';
					$link_o = 'index.php?option=com_jticketing&view=mytickets&tmpl=component&layout=ticketprint&$jticketing_usesess=0&attendee_id=';
					$link_o .= $data->attendee_id . '&jticketing_userid=' . $data->user_id . '&jticketing_ticketid=';
					$link_o .= $data->id . '&jticketing_order_items_id=' . $data->order_items_id;
					$link = Route::_($link_o);

					$modalConfig = array('width' => '800px', 'height' => '300px', 'modalWidth' => 80, 'bodyHeight' => 70);
					$modalConfig['url'] = $link;
					$modalConfig['title'] = Text::_('PREVIEW');
					echo HTMLHelper::_('bootstrap.renderModal', 'jtProfilePreview' . $data->attendee_id, $modalConfig);
					
					if (JVERSION < '4.0.0')
					{
						?>
						<a data-target="#jtProfilePreview<?php echo $data->attendee_id;?>" data-toggle="modal">
						<span class="editlinktip hasTip" title="<?php  echo Text::_('PREVIEW_DES');?>" >
						<?php        echo Text::_('PREVIEW');?></span>
						</a>
						<?php
					}
					else
					{
						?>
						<a data-bs-target="#jtProfilePreview<?php echo $data->attendee_id;?>" data-bs-toggle="modal">
						<span class="editlinktip hasTip" title="<?php  echo Text::_('PREVIEW_DES');?>" >
						<?php        echo Text::_('PREVIEW');?></span>
						</a>
						<?php
					}
				}
				else
				{
				echo '-';
				}
				?>
				</td>
			</tr>
			<?php
		}?>
</table>
<?php
}
?>
</div>
</div>

