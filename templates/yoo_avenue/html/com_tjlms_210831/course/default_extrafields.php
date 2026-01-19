<?php
 /**
  * @version     1.5
  * @package     com_jticketing
  * @copyright   Copyright (C) 2014. All rights reserved.
  * @license     GNU General Public License version 2 or later; see LICENSE.txt
  * @author      Techjoomla <extensions@techjoomla.com> - http://techjoomla.com
  */
// no direct access
defined('_JEXEC') or die;

// Get Course's custom fields
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$customFields = FieldsHelper::getFields('com_tjlms.course', $this->course_info, true);

if(!empty($customFields[0]->value))
{
	foreach ($customFields as $field)
	{
		$val = is_numeric($field->value);

		if ($field->type == 'user' && !empty($field->value))
		{
			$users[] = $field;
		}

		switch ($field->name)
		{
			case 'course-type';
			$tjf_ctype = $field;
			break;

			case 'based-on';
			$tjf_based_on = $field;
			break;

			case 'cpd-hours-guide';
			$tjf_cpd_hr = $field;
			break;

			case 'development-outcomes';
			$tjf_gdc_pr = $field;
			break;

			case 'gdc-highly-recommended-topic';
			$tjf_gdc_hrt = $field;
			break;

			case 'target-audience';
			$tjf_tar_aud = $field;
			break;

			case 'aims';
			$tjf_aims = $field;
			break;

			case 'gdc-recommended-topic';
			$tjf_gdc_grt = $field;
			break;

			case 'objectives';
			$tjf_obj = $field;
			break;

			case 'outcomes';
			$tjf_outcome = $field;
			break;

			case 'reflection';
			$tjf_reflection = $field;
			break;

			case 'date';
			$tjf_date = $field;
			break;

			case 'time';
			$tjf_time = $field;
			break;

			case 'contents';
			$tjf_content = $field;
			break;
		}
	}

	?>
	<div class="tjlms-course-additionaldata">
		<div class="row-fluid center">
			<div class="col-md-4 col-sm-4 dc-grey m-5">
				<div>
					<?php $img_name = 'Live_wht.png';
					if (strtolower($tjf_ctype->rawvalue[0])== 'on_demand')
					{
						$img_name = 'OnDemand_wht.png';
					} ?>
					<img class="dc-img-coursetype" src="<?php echo JUri::root() . 'images/lms/'.$img_name?>">
				</div>
				<div class="dc-coursetype ">
					<?php echo $tjf_ctype->value; ?>
				</div>

				<?php
				if (strtolower($tjf_ctype->rawvalue[0])== 'on_demand' && isset($tjf_based_on))
				{
					echo $tjf_based_on->label; ?>
					<b><?php echo $tjf_based_on->value; ?></b>
				<?php
				}

				if (strtolower($tjf_ctype->rawvalue[0]) == strtolower('Live_webinar'))
				{
					$time=strtotime($tjf_date->value);

					$dat=date("d",$time);
					$month=date("F",$time);
					$year=date("Y",$time);

					echo '<b>' . $dat. ' ' .$month . ' ' .$year . '  '. $tjf_time->value . '</b>';
				}
				?>
			</div>

			<div class="col-md-4 col-sm-4 m-5 dc-grey">
				<div>
					<?php echo $tjf_cpd_hr->label; ?>
				</div>
				<div class="dc-big">
					<?php echo $tjf_cpd_hr->value;?>
				</div>

				<?php
				if($tjf_gdc_hrt->rawvalue[0] != 'not_applicable')
				{ ?>
					<?php echo $tjf_gdc_hrt->label; ?>
					<br/>
					<b><?php echo $tjf_gdc_hrt->value; ?></b>
					<br/>
				<?php }
				if($tjf_gdc_grt->rawvalue[0] != 'not_applicable')
				{ ?>
					<?php echo $tjf_gdc_grt->label; ?>
					<br/>
					<b><?php echo $tjf_gdc_grt->value; ?></b>
				<?php } ?>

				<div class="btns-cover mt-5 m-b5 pt-10 pb-10">
					<?php
						if (count($tjf_gdc_pr))
						{?>
							<div class="course-extra-block">
								<a href="#gdc-pr" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
									<span class='tjf-modal-btn  dc-btn'>
										<?php echo $tjf_gdc_pr->label; ?>
									</span>
								</a>
								<div style="display:none;">
									<div class="modal-body dc-modal " id="gdc-pr">
										<?php $tjf_gdc_pr_list = explode(',', $tjf_gdc_pr->value);
											foreach ($tjf_gdc_pr_list as $list) {
												echo $list . ",<br/>";
											}?>
									</div>
								</div>
							</div>
						<?php }


					if (count($tjf_tar_aud))
					{
						?>
						<div class="course-extra-block">
							<a href="#tar-aud" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
								<span class='tjf-modal-btn  dc-btn'>
									<?php echo $tjf_tar_aud->label;?>
								</span>
							</a>
							<div style="display:none;">
								<div class="modal-body dc-modal " id="tar-aud">
									<?php echo $tjf_tar_aud->value . ", "; ?>
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
				$offset = 'view-3 course-pre-3';
				break;

				case 4:
				$offset = 'span6 view-3 course-pre-4';
				break;

			}
	if (!empty($users))
	{
?>
		<div class="col-md-4 col-sm-4 m-5 dc-grey center">
<?php
			require_once JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			foreach ($users as $presenter)
			{
				$mod_data = new stdclass;
				$mod_data->getCreatedInfo = $model->getCreatedInfo($presenter->rawvalue);
				$userData = JFactory::getUser($mod_data->getCreatedInfo->id);

				if ($userData->block == 0 && $userData->id)
				{
					$job_title = '';
					$my = ES::user($userData->id);

					/* For Dentist Professionals - Profile type only */
					if($my->profile_id == 1)
					{
						$db = JFactory::getDbo();

						$query = $db->getQuery(true);
						$query->select(array($db->quoteName('sf.unique_key'),$db->quoteName('sfd.data')));
						$query->from($db->quoteName('#__social_fields_data', 'sfd'));
						$query->join(
						'LEFT', $db->quoteName('#__social_fields', 'sf') . 'ON' . $db->quoteName('sf.id') . '=' . $db->quoteName('sfd.field_id')
						);
						$query->where($db->quoteName('sfd.uid') . " = " . (int) $userData->id);

						$db->setQuery($query);
						$socialData = $db->loadObjectlist();

						foreach($socialData as $data)
						{
							if($data->unique_key === 'tdces_job-title')
							{
								$job_title = $data->data;
							}
							if($data->unique_key === 'tdces_qulfctns')
							{
								$qualification = $data->data;
							}
							if($data->unique_key === 'tdces_organisation')
							{
								$organisation = $data->data;
							}
						}

						$tdces_biography ='';

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
						<a href="#presneter-<?php echo $presenter->rawvalue; ?>" class="modal link-tjf" rel="{ ajaxOptions: {method: &quot;get&quot;}}">
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
							<div class="modal-body dc-modal center" id="presneter-<?php echo $presenter->rawvalue; ?>">
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
										<?php echo $qualification;?>
									</span>
									<br/><br/>
									<strong><em><?php echo $job_title; ?></em></strong>
									<br/>
									<strong><em><?php echo $organisation; ?></em></strong>
										<br/>
									<strong><em><?php echo $tdces_biography; ?></em></strong>
										<br/>
								</div>
							</div>
						</div>
				</div>
			<?php } ?> <!-- end of if-->
		<?php
		} ?> <!-- end of foreach-->
		</div>
	<?php
	}
	?>
	</div>
</div>
<?php
}
