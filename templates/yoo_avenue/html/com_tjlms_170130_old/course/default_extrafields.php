<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_tjlms/defines.php';
require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php');


JHTML::_('behavior.modal');
$str1 = $str2 = $str3 = $str1_val = '';

if (count($this->extraData))
{
	foreach ($this->extraData as $f)
	{
		$val = is_numeric($f->value);

		if ($f->type == 'user' && $f->value != 0 )
		{
			$users[] = $f;
		}

		switch ($f->field_id)
		{
			case TJFIELD_COURSTYPE;
			$tjf_ctype = $f;
			break;

			case TJFIELD_BASED_ON;
			$tjf_based_on = $f;
			break;

			case TJFIELD_CPD_HOURS;
			$tjf_cpd_hr = $f;
			break;

			case TJFIELD_GDC_PRINCIPLE;
			$tjf_gdc_pr = $f;
			break;

			case TJFIELD_GDC_HIGH_RECO_TOPIC;
			$tjf_gdc_hrt = $f;
			break;

			case TJFIELD_TARGET_AUD;
			$tjf_tar_aud = $f;
			break;

			case TJFIELD_AIMS;
			$tjf_aims = $f;
			break;

			case TJFIELD_GDC_RECOMMEND_TOPIC;
			$tjf_gdc_grt = $f;
			break;

			case TJFIELD_GDC_OBJECTIVE;
			$tjf_obj = $f;
			break;

			case TJFIELD_OUTCOME;
			$tjf_outcome = $f;
			break;

			case TJFIELD_REFLECTION;
			$tjf_reflection = $f;
			break;

			case TJFIELD_DATE;
			$tjf_date = $f;
			break;

			case TJFIELD_TIME;
			$tjf_time = $f;
			break;

			case TJFIELD_CONTENT;
			$tjf_content = $f;
			break;
		}
	}?>
<div class="tjlms-course-additionaldata">
	<div class="row-fluid center">

		<div class="dc-grey">
			<div>
				<?php $img_name = 'Live_wht.png';

				if ($tjf_ctype->value[0]->value== 'on_demand')
				{
					$img_name = 'OnDemand_wht.png';
				} ?>
				<img class="dc-img-coursetype" src="<?php echo JUri::root() . 'images/lms/'.$img_name?>">
			</div>
			<div class="dc-coursetype ">
				<?php echo $tjf_ctype->value[0]->options; ?>
			</div>
			
			<?php 
			if ($tjf_ctype->value[0]->value== 'on_demand')
			{
				echo $tjf_based_on->label; ?>
				<b><?php echo $tjf_based_on->value; ?></b>
			<?php
			}
			
			if ($tjf_ctype->value[0]->value== 'live_webinar')
			{
				$time=strtotime($tjf_date->value);

				$dat=date("d",$time);
				$month=date("F",$time);
				$year=date("Y",$time);

				echo '<b>' . $dat. ' ' .$month . ' ' .$year . ' '. $tjf_time->value . '</b>';  

			}
			?>
			</div>

		<div class="dc-grey">
			<div>
				<?php echo $tjf_cpd_hr->label; ?>
			</div>
			<div class="dc-big">
				<?php echo $tjf_cpd_hr->value;?>
			</div>

			<?php
			if($tjf_gdc_hrt->value[0]->value != 'not_applicable')
			{ ?>
				<?php echo $tjf_gdc_hrt->label; ?>
				<br/>
				<b><?php echo $tjf_gdc_hrt->value[0]->options; ?></b>
				<br/>
			<?php }
			if($tjf_gdc_grt->value[0]->value != 'not_applicable')
			{ ?>
				<?php echo $tjf_gdc_grt->label; ?>
				<br/>
				<b><?php echo $tjf_gdc_grt->value[0]->options; ?></b>
			<?php } ?>

			<div class="btns-cover">
				<?php
				 if (count($tjf_gdc_pr->value))
					{ ?>
					<div class="course-extra-block">
						<?php
						foreach ($tjf_gdc_pr->value as $option1)
						{
							$str1 .= $option1->options . '<br/>';
							$str1_val .= $option1->value . ', ';
						}?>
						<a href="#gdc-pr" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
							<span class='tjf-modal-btn  dc-btn'>
								<?php echo $tjf_gdc_pr->label; ?>
							</span>
						</a>
						<div style="display:none;">
							<div class="modal-body dc-modal " id="gdc-pr">
								<?php echo trim($str1, "<br/> "); ?>
							</div>
						</div>
					</div>
				<?php }


				 if (count($tjf_tar_aud->value))
					{ ?>
					<div class="course-extra-block">
						<a href="#tar-aud" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
							<span class='tjf-modal-btn  dc-btn'>
								<?php echo $tjf_tar_aud->label;?>
							</span>
						</a>
						<?php
						foreach ($tjf_tar_aud->value as $option2)
						{
							$str2 .= $option2->options . ', ';
						}?>
						<div style="display:none;">
							<div class="modal-body dc-modal " id="tar-aud">
								<?php echo trim($str2, ", "); ?>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="course-extra-block">
						<a href="#lrn-smr" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
							<span class='tjf-modal-btn dc-btn'>
								<?php echo JText::_('COM_TJLMS_TJF_LEARNING_SUMMARY');?>
							</span>
						</a>

						<div style="display:none;">
						<div class="modal-body dc-modal" id="lrn-smr">
							<div class="tjf-field-title">
								<b><?php echo $tjf_aims->label;?></b>
							</div>
							<div class="tjf-field-title">
								<?php echo $tjf_aims->value;?>
							</div>
							<div class="tjf-field-title">
								<b><?php echo $tjf_obj->label;?></b>
							</div>
							<div class="tjf-field-title">
								<?php echo $tjf_obj->value;?>
							</div>

							<div class="tjf-field-title">
								<b><?php echo $tjf_outcome->label;?></b>
							</div>
							<div class="tjf-field-title">
								<?php echo $tjf_outcome->value;?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--div class="span3 dc-grey">


				<?php /* if (count($tjf_content->value))
				{ ?>
				<div class="course-extra-block">
					<div>
						<b><?php echo $tjf_content->label; ?></b>
					</div>
						<?php
						foreach ($tjf_content->value as $option3)
						{
							$str3 .= $option3->options . ', ';
						}?>
						<div>
							<?php echo trim($str3, ", "); ?>
						</div>
				</div>
				<?php } */ ?>

		</div-->
		<div class="dc-grey center">
			<?php
			if (!class_exists('TjlmsModelcourses'))
			{
				$path = JPATH_SITE . '/components/com_tjlms/models/course.php';
				JLoader::register('TjlmsModelcourse', $path);
			}
			$model = new TjlmsModelcourse;
			switch (count($users))
			{
				case 1:
				$offset = 'span12 course-pre-1';
				break;

				case 2:
				$offset = 'span6 course-pre-2';
				break;

				case 3:
				$offset = 'span6 view-3 course-pre-3';
				break;

				case 4:
				$offset = 'span6 view-3 course-pre-4';
				break;

			}
			//~ if((count($users)<= 1) )
			//~ {
				//~ $offset = "span12";
			//~ }
			//~ elseif(count($users) == 2){
				//~ $offset = "span6";
			//~ }
			//~ else{
				//~ $offset = "span6 hide-desc";
			//~ }
			require_once JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			foreach ($users as $presenter)
			{
				$mod_data = new stdclass;
				$mod_data->getCreatedInfo = $model->getCreatedInfo($presenter->value);
				$userData = JFactory::getUser($mod_data->getCreatedInfo->id);

				if ($userData->block == 0 && $userData->id)
				{


					$job_title = '';
					$my = ES::user($userData->id);

					/* For Dentist Professionals - Profile type only */
					if($my->profile_id == 1)
					{
						$job_title = $my->getFieldValue(ES_JOB_FIELD_NAME)->data;

						 //~ $stepsModel = FD::model('Steps');
						 //~ $steps = $stepsModel->getSteps(1, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_DISPLAY);

						//~ $fieldsModel = FD::model('Fields');
						//~ $fields = $fieldsModel->getCustomFields(array('step_id' => 25, 'data' => true, 'dataId' => $userData->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => 'edit'));
//~
						//~ $tdces_biography = $fields[1]->data;

						//~ require_once(JPATH_ROOT . '/components/com_easyblog/helpers/helper.php');
						//~ $eb = EasyBlogHelper::getTable('Profile');
						//~ $eb->load($user->id);

						$tdces_biography ='';

						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select($db->quoteName('biography'));
						$query->from($db->quoteName('#__easyblog_users'));
						$query->where('id = ' . $userData->id);
						$db->setQuery($query);
						$tdces_biography = $db->loadResult();
					}
					/* For Dentist Professionals only */

					 ?>
					<div class=" <?php echo $offset;?>">
						<a href="#presneter-<?php echo $presenter->value; ?>" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
							<div class="user-image"><?php
								if (empty($mod_data->getCreatedInfo->avatar)) :
									$mod_data->getCreatedInfo->avatar = JUri::root(true).'/media/com_tjlms/images/default/user.png';
								endif; ?>

								<img src="<?php echo $mod_data->getCreatedInfo->avatar; ?>" alt="<?php echo $mod_data->getCreatedInfo->name; ?>" class="dc-user-img">
							</div>

							<div class="user-info-block dc-user-name" title="<?php echo $mod_data->getCreatedInfo->name; ?>">
								<span>
									<strong><?php echo $mod_data->getCreatedInfo->name; ?></strong>
								</span>
							</div>
						</a>
						<br/>
						<?php echo $job_title; ?>
						<div class="user-desc">
							<?php //echo $job_title; ?>
						</div>
						<div style="display:none;">
							<div class="modal-body dc-modal center" id="presneter-<?php echo $presenter->value; ?>">

								<div class="user-image"><?php
									if (empty($mod_data->getCreatedInfo->avatar)) :
										$mod_data->getCreatedInfo->avatar = JUri::root(true).'/media/com_tjlms/images/default/user.png';
									endif; ?>
									<img src="<?php echo $mod_data->getCreatedInfo->avatar; ?>" alt="<?php echo $mod_data->getCreatedInfo->name; ?>" class="">
								</div>

								<div class="user-info-block">
									<span>
										<strong><em><?php echo $mod_data->getCreatedInfo->name; ?></em></strong>
										<br/>
										<?php echo $my->getFieldValue(tdces_qulfctns)->data; ?>
									</span>
									<br/><br/>
									<strong><em><?php echo $job_title; ?></em></strong>
									<br/>
									<strong><em><?php echo $my->getFieldValue(tdces_organisation)->data; ?></em></strong>
										<br/>
									<strong><em><?php echo $tdces_biography; ?></em></strong>
										<br/>
								</div>
							</div>
						</div>
					</div>
			<?php } ?> <!-- end of if-->
		<?php } ?> <!-- end of foreach-->
		</div>
	</div>
</div>
<?php
}
