<?php
/**
 * @package     LMS_Shika
 * @subpackage  mod_lms_course_progress
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true) . '/modules/mod_tjfield_presenter/assets/style.css');

/* Course presenter users */
if (!empty($mod_data->course_extrainfo))
{
	?><div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?> ">
		<div class="courseTaughtBy couserBlock coursePresenter">
			<div class="panel-heading row-fluid">
				<i class="fa fa-user fa-2x"></i>
				<span class="course_block_title"><?php echo JText::_('MOD_LMS_TJFIELD_PRESENTER_BY')?></span>
			</div>
			<div class="panel-content row-fluid taughtBy"><?php
				$showMsg = 0;

				if ($mod_data->count == 1)
				{
					$offset = 'span12';
				}
				elseif ($mod_data->count == 2)
				{
					$offset = 'span6';
				}
				else
				{
					$offset = 'span4';
				}

				foreach ($mod_data->course_extrainfo as $extrafields)
				{
					if ($extrafields->type == 'user')
					{
						$mod_data->getCreatedInfo = $model->getCreatedInfo($extrafields->value);
						$userData = JFactory::getUser($mod_data->getCreatedInfo->id);

						if ($userData->block == 0 && $userData->id)
						{
							if ($extrafields->value)
							{
								$showMsg ++;
							}
							?>
							<div class="center <?php echo $offset;?>">

								<?php if (!empty($mod_data->getCreatedInfo->profileurl)) : ?>
									<a class="" target="_blank" href="<?php echo $mod_data->getCreatedInfo->profileurl?>" >
								<?php endif; ?>

								<div class="user-image"><?php
									if (empty($mod_data->getCreatedInfo->avatar)) :
										$mod_data->getCreatedInfo->avatar = JUri::root(true).'/media/com_tjlms/images/default/user.png';
									endif; ?>

									<img src="<?php echo $mod_data->getCreatedInfo->avatar; ?>" alt="<?php echo $mod_data->getCreatedInfo->name; ?>" class="img-circle">
								</div>

								<div class="user-info-block">
									<span>
										<strong><em><?php echo $mod_data->getCreatedInfo->name; ?></em></strong>
									</span>
								</div>

								<?php if (!empty($mod_data->getCreatedInfo->profileurl)) : ?>
									</a>
								<?php endif; ?>

											<?php if ($mod_data->tjlmsparams->get('social_integration') == 'easysocial') { ?>
										<div class="panel-heading-right-content">
											<?php
												require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/document/document.php' );
												$socialDocument = new SocialDocument;
												// Render css codes
												// Foundry::document()->init();

												// This will render the necessary javascripts on the page header.
												$socialDocument->processScripts();
												?>

											<a class="btn btn-small btn-primary tjlms-btn-flat MsgBtnDivContainner" href="javascript:void(0);"
												data-es-conversations-compose
												data-es-conversations-id="<?php echo $mod_data->getCreatedInfo->id;?>">
												<?php echo JText::_("MOD_LMS_TJFIELD_PRESENTER_BY_SEND_MESSAGE");?></a>

										</div>
										<br/>
									<?php } ?>
							</div><?php
						}
					}
				}

				if ($showMsg == 0)
				{
					echo "<div class='center'>" . JText::_('MOD_LMS_TJFIELD_PRESENTER_BY_EMPTY')."</div>";
				}
				?>
			</div>
		</div>
	</div><?php
}
?>
