<?php
/*
	* @package Shika Video Player
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.folder' );
jimport('joomla.plugin.plugin');

$lang =  JFactory::getLanguage();
$lang->load('jwplayer', JPATH_ADMINISTRATOR);

/**
 *
 *
 * @since 1.0.0
 */
class plgTjlmsvideoJwplayer extends JPlugin
{

	function plgTjlmsvideoJwplayer(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	/**
	 * Function to get Sub Format options when creating / editing lesson format
	 * @since 1.0.0
	 */
	function getSubFormat_ContentInfo($config=array('jwplayer'))
	{
		if(!in_array($this->_name,$config))
			return;
		$obj 			= array();
		$obj['name']	= $this->params->get( 'plugin_name', 'jwplayer player');
		$obj['id']		= $this->_name;
		return $obj;
	}
	/**
	 * Function to get Sub Format HTML when creating / editing lesson format
	 * @since 1.0.0
	 */
	function getSubFormat_jwplayerContentHTML($mod_id , $lesson_id, $lesson, $comp_params)
	{
		$result =array();
		$plugin_name = $this->_name;
/**
 * video format...used when lesson format is selected as video
 */
$video_format=array();
//$video_format[] = JHTML::_('select.option','code', JText::_('Enter video code'));
$video_format[] = JHTML::_('select.option','url', JText::_('Enter Video / Audio URL'));
$video_format[] = JHTML::_('select.option','upload', JText::_('Upload Video / Audio'));
$source = (isset($lesson->format_details['source'])) ? $lesson->format_details['source'] :'' ;
		$html = '
		<script type="text/javascript">
		//repective input to show depending on video format if lesson format is video...
function getVideoFormat(subformat,thiselement)
{
	var format_lesson_form = techjoomla.jQuery(thiselement).closest(".lesson-format-form");
	var thiselementval = techjoomla.jQuery(thiselement).val();

	if(thiselementval != "upload")
	{
		techjoomla.jQuery(".video_subformat #video_package",format_lesson_form).hide();
		techjoomla.jQuery(".video_subformat #video_textarea",format_lesson_form).show();
	}
	else
	{
		techjoomla.jQuery(".video_subformat #video_package",format_lesson_form).show();
		techjoomla.jQuery(".video_subformat #video_textarea",format_lesson_form).hide();
	}
}
		</script>
					<div class="control-label">'.JText::_("COM_TJLMS_VIDEO_FORMAT_OPTIONS").'</div>

					<div  class="controls">
						<div class="lesson_video_format_container">
							'.JHTML::_('select.genericlist', $video_format, "lesson_format[".$plugin_name."][video_source]", 'class="class_video_format" onchange="getVideoFormat(\''.$plugin_name.'\',this);"', "value", "text",'upload').'
						</div>

						<div id="video_textarea" style="display:none">
							<textarea id="video_url" class="input-block-level"cols="50" rows="2" name="lesson_format['.$plugin_name.'][video_format_source]" >'.$source .'</textarea>
						</div>
						<div id="video_package">
							<div class="fileupload fileupload-new pull-left" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input span4">
										<span class="fileupload-preview">
											'.JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION','flv, mp4, mp3',$comp_params->get('lesson_upload_size','0','INT')).'
										</span>
									</div>
									<span class="btn btn-file">
										<span class="fileupload-new">'. JText::_("COM_TJLMS_BROWSE").'</span>
										<input type="file" id="video_upload" name="lesson_format['.$plugin_name.'][video]" onchange="validate_file(this,\''.$mod_id.'\',\''.$plugin_name.'\')">
									</span>
								</div>
							</div>
							<div style="clear:both"></div>
							<div class="format_upload_error alert alert-error" style="display:none" ></div>
							<div class="format_upload_success alert alert-info" style="display:none"></div>
						</div>
						<input type="hidden" class="valid_extensions" value="flv,mp4,mp3"/>
					</div>';
		return $html;
	}



	/**
	 * Function to get needed data for this API
	 * param $data
	 * @since 1.0.0
	 */
	function getData($data)
	{
		// $data will be contain some useful data which is require to get futher data from the api
		// YOUR CODE TO GET DATA
		$input=JFactory::getApplication()->input;
		// YOUR CODE ENDS
		$re = '';
		$lesson_id	= $input->get('lesson_id','','INT');
		$attempt	= $input->get('last_attempt','','INT');
		$type	= $input->get('type','','STRING');
		$score = 0;
		$oluser_id = 	JFactory::getUser()->id;
		$db=JFactory::getDBO();
		require_once JPATH_SITE.'/components/com_tjlms/helpers/tracking.php';

		$comtjlmstrackingHelper = new comtjlmstrackingHelper();

		if($type =='update'){
			$lesson_status = 'started';
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id,$attempt,$score,$lesson_status,$oluser_id);
		}
		elseif($type =='update_current'){
			$duration	= round($input->get('duration','','FLOAT'),2);
			$spent	= round($input->get('spent','','FLOAT'),2);
			$lesson_status = 'incomplete';
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id,$attempt,$score,$lesson_status,$oluser_id,'',$duration,$spent);
		}
		else if($type == 'update_total'){ // update the total content of video
			$total_content	= round($input->get('duration','','FLOAT'),2);
			$lesson_status = 'incomplete';
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id,$attempt,$score,$lesson_status,$oluser_id,$total_content,'','');
		}
		else if($type == 'update_pause'){ //update current_position of video
			$duration	= round($input->get('duration','','FLOAT'),2);
			$spent	= round($input->get('spent','','FLOAT'),2);
			$lesson_status = 'incomplete';
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id,$attempt,$score,$lesson_status,$oluser_id,'',$duration,$spent);
		}
		else if($type == 'update_spent'){ //update current_position of video & total spent
			$duration	= round($input->get('duration','','FLOAT'),2);

			$current	= round($input->get('current',0,'FLOAT'),2);
			$lesson_status = 'completed';
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id,$attempt,$score,$lesson_status,$oluser_id,'',$current,$duration);
		}
		return $re;
	}

	/**
	 * Function to render the document
	 *
	 * @since 1.0.0
	 */
	function renderPluginHTML($config)
	{
		$api_key	= $this->params->get('appkey','','STRING');


		// YOUR CODE TO RENDER HTML
/*@TODO take jwpsrv.com/library on local rather than using the live file */
		$html =
		'
		<script src="http://jwpsrv.com/library/'.$api_key.'.js"></script>
		<div id="shika_jwplayer">Loading the player...</div>
		<script type="text/javascript">
		var wheight	= techjoomla.jQuery(window).height();
		if(wheight == 0)
			wheight	= techjoomla.jQuery(window.parent).height();

		wheight	=	wheight-80;

		var jwplayer_lesson_id = '.$config['lesson_id'].';
		var jwplayer_attempt = '.$config['attempt'].';
		var jwplayer_flag = "0";
		var jwplayer_lastpause = "0";
		var newtime = "0";
		var jwplayercounter = 0;
		var jwplayerisPaused = false;
		var jwplayermyInterval = setInterval(function () {
				if(!jwplayerisPaused) {
					jwplayer_lastcounter = jwplayercounter;
					++jwplayercounter;
					newtime = jwplayercounter - jwplayer_lastcounter;
				}
		}, 1000);
		jwplayer("shika_jwplayer").setup({
			file: "'.$config['file'].'",
			width: "100%",
			height: wheight ,
			autostart:true
		});
	jwplayer().onTime( function(event){
		console.log("onTime"+jwplayercounter);

		techjoomla.jQuery.ajax({
			url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsvideo&plgtask=getData&type=update_current&lesson_id='.$config['lesson_id'].'&last_attempt='.$config['attempt'].'&duration="+event.position+"&spent="+newtime,
			dataType: "json",
			success: function(response)
			{
				attempt = response;
			}
		});
		if(jwplayer_flag == "0"){
			console.log("total"+event.duration);
			jwplayer_flag = 1;
			techjoomla.jQuery.ajax({
				url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsvideo&plgtask=getData&type=update_total&lesson_id='.$config['lesson_id'].'&last_attempt='.$config['attempt'].'&duration="+event.duration,
				dataType: "json",
				success: function(response)
				{
					attempt = response;
				}
			});
		}
		if (event.position == event.duration) {
			jwplayerisPaused = true;
			/*		if(jwplayer_lastpause != "0"){
						newtime =  event.duration - (jwplayer_lastpause );
						console.log("newtime"+newtime);
					}else{
						newtime = event.duration;
					}
			*/
			console.log("Im done");
			techjoomla.jQuery.ajax({
				url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsvideo&plgtask=getData&type=update_spent&lesson_id='.$config['lesson_id'].'&last_attempt='.$config['attempt'].'&duration="+newtime+"&current="+event.duration,
				dataType: "json",
				success: function(response)
				{
				}
			});
			/*
					var r = confirm("Do you want to again play the video ??");
					if (r == true) {
						jwplayer().seek(0);
						newtime = 0;
					} else {
						jwplayer("shika_jwplayer").remove();
					}
			*/
		}

	});
	/*Do this when $current is passed to RenderHTML*/
	jwplayer().onPlay( function(event){
		techjoomla.jQuery.ajax({
				url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsvideo&plgtask=getData&type=update&lesson_id='.$config['lesson_id'].'&last_attempt='.$config['attempt'].'",
				dataType: "json",
				async:false,
				success: function(response)
				{
				}
			});

		jwplayerisPaused = false;
		if('.$config['current'].' == jwplayer().getDuration() ){
			jwplayer().seek(0);
		}else{
			if(jwplayer_flag == "0"){
				console.log("onplay");
				jwplayer().seek("'.$config['current'].'");
			}
		}
	});



/*When the user Pauses you want to send the current position to Database & status Incomplete*/
jwplayer().onPause( function(event){
	jwplayerisPaused = true;
/*	if(jwplayer_lastpause != "0"){
	newtime = jwplayer().getPosition() - (jwplayer_lastpause );
	console.log("newtime"+newtime);
	}else{
		newtime = jwplayer().getPosition();
	}
*/
  console.log("onPause Current"+jwplayer().getPosition());
	techjoomla.jQuery.ajax({
		url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsvideo&plgtask=getData&type=update_pause&lesson_id='.$config['lesson_id'].'&last_attempt='.$config['attempt'].'&duration="+jwplayer().getPosition()+"&spent="+newtime,
		dataType: "json",
		success: function(response)
		{
			attempt = response;
		}
	});
	/*if(jwplayer_lastpause == "0")
	{
		jwplayer_lastpause =  jwplayer().getPosition();
		console.log("jwplayer_lastpause"+jwplayer_lastpause);
	}
	*/
});

			</script>
		';

		// YOUR CODE ENDS
		// This may be an iframe directlys
		return $html;
	}
}//end class
