<?php
/**
 * @version    SVN: <svn_id>
 * @package    JLike
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

// Load classification file
$brodfile        = JPATH_SITE . "/components/com_jlike/classification.ini";
$classifications = parse_ini_file($brodfile);

if (count($records) == 0)
{
	echo JText::_("APP_JLIKEMYLIKES_NO_DATA");

	return;
}

$checkAllowDislike = $jlikeParams->get('allow_dislike');
?>

<table class="table table-striped">
	<thead >
		<tr>
			<th class='left'>
				<?php echo JText::_("APP_JLIKEMYLIKES_TITLE"); ?>
			</th>
			<th class='left'>
				<?php echo JText::_("APP_JLIKEMYLIKES_ELEMENT"); ?>
			</th>
			<th class='left'>
				<?php echo JText::_("APP_JLIKEMYLIKES_TOTAL_LIKES"); ?>
			</th>
			<?php
			if ($checkAllowDislike == 1) {
			?>
			<th class='left'>
				<?php echo JText::_("APP_JLIKEMYLIKES_TOTAL_DISLIKE"); ?>
			</th>
			<?php } ?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="4">
				<?php
					require_once JPATH_SITE . '/components/com_jlike/helper.php';
					$comjlikeHelper = new comjlikeHelper;
					$itemid         = $comjlikeHelper->getItemId('index.php?option=com_jlike&view=likes&layout=my');
					$redirect       = JRoute::_('index.php?option=com_jlike&view=likes&layout=my&Itemid=' . $itemid, false);
				?>
				<strong>
					<a href="<?php echo $redirect; ?>" class="pull-right">
						<?php echo JText::_("APP_JLIKEMYLIKES_SHOW_ALL"); ?>
					</a>
				</strong>
			</td>
		</tr>
	</tfoot>

	<tbody>
		<?php
		foreach ($records as $i => $item)
		{
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="<?php echo $item->url; ?>" target="_blank">
						<?php echo $item->title; ?>
					</a>
				</td>
				<td>
				<?php
					$element = $item->element;

					foreach ($classifications as $v => $clssfcs)
					{
						if ($v == $item->element)
						{
							$element = $clssfcs;
							break;
						}
					}

					if (!$element)
					{
						$element = $item->element;
					}

					echo $element;
				?>
				</td>
				<td>
					<?php echo $item->like_cnt; ?>
				</td>
				<?php
				if ($checkAllowDislike == 1) {
				?>
				<td>
					<?php echo $item->dislike_cnt; ?>
				</td>
				<?php } ?>
			</tr>
		<?php
		} ?>
	</tbody>
</table>
