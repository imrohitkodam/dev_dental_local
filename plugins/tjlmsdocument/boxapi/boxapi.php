<?php
/*
 * @package Tjlms Document viewer
 * @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('DS')) {
	define('DS', '/');
}

jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');

$lang = JFactory::getLanguage();
$lang->load('boxapi', JPATH_ADMINISTRATOR);

require_once JPATH_SITE . DS . 'plugins' . DS . 'tjlmsdocument' . DS . 'boxapi' . DS . 'boxapi' . DS . 'lib' . DS . 'box-view-api.php';
require_once JPATH_SITE . DS . 'plugins' . DS . 'tjlmsdocument' . DS . 'boxapi' . DS . 'boxapi' . DS . 'lib' . DS . 'box-view-document.php';

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/crocodoc.viewer.css');

$document->addScript(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/js/crocodoc.viewer.min.js');
$document->addScript(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/js/track.js');
$document->addScript(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/js/realtime.js');

$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/pop.css');
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/fade.css');
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/slide.css');
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/spin.css');
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/pageflip.css');
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/carousel.css');
$document->addStyleSheet(JURI::base() . 'plugins/tjlmsdocument/boxapi/boxapi/assets/css/toolbar.css');

/**
 *
 *
 * @since 1.0.0
 */
class plgTjlmsdocumentBoxapi extends JPlugin {

	function plgTjlmsdocumentBoxapi(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	 * Function to get Sub Format options when creating / editing lesson format
	 * @since 1.0.0
	 */
	function getSubFormat_ContentInfo($config=array('boxapi'))
	{
		if(!in_array($this->_name,$config))
			return;
		$obj 			= array();
		$obj['name']	= $this->params->get( 'plugin_name', 'Box API');
		$obj['id']		= $this->_name;
		return $obj;
	}
	/**
	 * Function to get Sub Format HTML when creating / editing lesson format
	 * @since 1.0.0
	 */
	function getSubFormat_boxapiContentHTML($mod_id , $lesson_id, $lesson, $comp_params)
	{
		$result =array();
		$plugin_name = $this->_name;
/**
 * document format...used when lesson format is selected as document
 */

$source = (isset($lesson->format_details['source'])) ? $lesson->format_details['source'] :'' ;
		$html = '
					<div class="control-label">'.JText::_("COM_TJLMS_UPLOAD_FORMAT").'</div>

					<div  class="controls">
					<input type="hidden" id="lesson_format' . $plugin_name . 'document_source" name="lesson_format[' . $plugin_name . '][document_source]" value="upload"/>
						<div>
							<div class="fileupload fileupload-new pull-left" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input span4">
										<span class="fileupload-preview">
											'.JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION','pdf, doc, docx, ppt, pptx',$comp_params->get('lesson_upload_size','0','INT')).'
										</span>
									</div>
									<span class="btn btn-file">
										<span class="fileupload-new">'. JText::_("COM_TJLMS_BROWSE").'</span>
										<input type="file" id="document_upload" name="lesson_format['.$plugin_name.'][document]" onchange="validate_file(this,\''.$mod_id.'\',\''.$plugin_name.'\')">
									</span>
								</div>
							</div>
							<div style="clear:both"></div>
							<div class="format_upload_error alert alert-error" style="display:none" ></div>
							<div class="format_upload_success alert alert-info" style="display:none"></div>
						</div>
						<input type="hidden" class="valid_extensions" value="pdf,doc,docx,ppt,pptx"/>
					</div>';
		return $html;
	}

	/**
	 * Function to upload a file on cloud
	 * @return $document_id		This need to store in Media table params
	 *
	 * @since 1.0.0
	 */
	public function upload_files($filename = '', $filepath = '') {
		$api_key = $this->params->get('appkey', '', 'STRING');

		if (empty($api_key))
		{
			return false;
		}

		$box     = new Box_View_API($api_key);

		$doc = new Box_View_Document(array(
			'name'      => $filename,
			'file_path' => $filepath,
		));

		// Call box api to upload the file
		$upload_result = $box->upload($doc);

		return $upload_result;
	}

	/**
	 * Create a session for viewing a document
	 *
	 * @since 1.0.0
	 */
	public function getSessionForDocument($document_id) {

		$doc = new Box_View_Document(array(
			'id' => $document_id,
		));

		$api_key = $this->params->get('appkey', '', 'STRING');

		if (empty($api_key))
		{
			return false;
		}

		$box     = new Box_View_API($api_key);

		// check if the status of the file is 'Done'
		$checkStatusForDoc = $box->getMetaData($doc);

		if ($checkStatusForDoc->status !== 'done') {
			//return a error message for the viewer
			return false;
		}

		// As we got the status as 'done' we can proceed to get the session for viewing the doc
		$getSession = $box->view($doc);

		return $getSession;
	}

	/**
	 * Function to render the document
	 * @return 		$html		complete html along with script is return.
	 *
	 * @since 1.0.0
	 */
	function renderPluginHTML($data) {

		$html             = '';
		$document_id      = $data['document_id'];
		$checksessionUrl  = '';
		$getSession       = '';
		$loadingImagePath = JUri::root() . 'components/com_tjlms/assets/images/ajax.gif';

		$SelectedLayout      = $this->params->get('doc_layout', '', 'STRING');
		$layoutOptionForUser = $this->params->get('doc_layout_ft_option', '', 'STRING');

		// Check if session already present. So need to create a new seesion.
		$checksessionUrl = $this->checkSessionForDocument($data['lesson_id']);

		if (empty($checksessionUrl)) {
			// Create a session for viewing a document
			$getSession = $this->getSessionForDocument($document_id);

			// Store session ID and expire at in tjlms_media table
			$storeSessionData = $this->storeSessionData($getSession, $data['lesson_id']);
		}


		// check if there was a error while creating seesion.
		if (!$getSession && empty($checksessionUrl)) {
			$html = '<div class="alert alert-danger">' . JText::_('PLG_BOX_FILE_NOT_YE_AVAILABLE_TO_VIEW') . '</div>';
		} else {
			if (!empty($checksessionUrl)) {
				$url_to_use   = $checksessionUrl->assets_url;
				$realtime_url = $checksessionUrl->realtime_url;
			} else {
				$url_to_use   = $getSession->urls->assets;
				$realtime_url = $getSession->urls->realtime_url;
			}

			$html = '

					<div id="viewer_container" class="viewer_container"  >

						<!-- DIV WHICH RENDER DOCUMENT-->
						<div class="viewer" ></div>

						<!--TOOLBAR FOR THE DOCUMENT-->
						<div class="viewer_toolbar row-fluid" style="display:none">
							<div class="viewer_controls span4 view-left viewer_layouts_container">';
			if ($layoutOptionForUser == 1) {

				$html .= '
											<select  class="viewer_layouts" onchange="setMode(this.value)" >
												<option value="plain">Normal</option>
												<option value="pop">Pop</option>
												<option value="fade">Fade</option>
												<option value="spin">Spin</option>
												<option value="slide">Slide</option>
												<option value="carousel">Carousel</option>
												<option value="book">Book</option>
											</select>
										';
			}

			$html .= '
							</div>
							<div class="viewer_controls span4 view-center viewer_nav_conatainer">
								<button class="btn btn-small" onclick="previouspage()"><i class="icon-chevron-left"></i></button>
									<button class="pagedetails" onclick="enable_gotopage()">
										<span class="currentpage blackcolor" ></span>
										<span class="totalPagesSpan blackcolor" ></span>
									</button>
									<input type="text"  class="input-small viewer_gotopage" style="display:none"  onblur="gotopage()">
								<button class="btn btn-small" onclick="nextpage()"><i class="icon-chevron-right"></i></button>
							</div>
							<div class="viewer_controls span4 view-right viewer_zoom_conatainer">
								<button class="btn btn-small" onclick="zoomin()"><i class="icon-zoom-in"></i></button>
								<button class="btn btn-small" onclick="zoomout()"><i class="icon-zoom-out"></i></button>
							</div>
						</div><!--TOOLBAR ENDS-->


					</div><!--VIEWER CONTAINER ENDS-->



					<script type="text/javascript">

						techjoomla.jQuery(window).load(function () {

								var player_height = techjoomla.jQuery(".tjlms_lesson_screen", top.document).height();
								if(!player_height)
									player_height = techjoomla.jQuery(this).height();

								techjoomla.jQuery(".viewer_container").height(player_height-100);
								techjoomla.jQuery(".viewer_layouts").val("'.$SelectedLayout.'");

								loadingImage();
						});

						techjoomla.jQuery(document).keypress(function(e) {
							if(e.which == 13) {
								gotopage();
							}
						});

						/* Function to load the loading image. */
						function loadingImage()
						{
							techjoomla.jQuery("<div id=\'appsloading\'></div>")
							.css("background", "white url(' . $loadingImagePath . ') 50% 15% no-repeat")
							.css("top", techjoomla.jQuery("#main_doc_container").position().top - techjoomla.jQuery(window).scrollTop())
							.css("width", "100%")
							.css("height", "100%")
							.css("background-position","center")
							.css("position", "fixed")
							.css("z-index", "1000")
							.css("opacity", "1")
							.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
							.css("filter", "alpha(opacity = 80)")
							.appendTo("#main_doc_container");
						}


						/* Function to close the loading image. */
						function hideImage()
						{
							techjoomla.jQuery("#appsloading").remove();
							techjoomla.jQuery(".viewer_toolbar").show();
						}

						var url = "' . $url_to_use . '";

						var doc_object = [];
						doc_object["current_position"] = ' . $data['current'] . ';
						doc_object["total_time"] = 0;
						doc_object["user_id"] = ' . $data['user_id'] . ';
						doc_object["lesson_id"] = ' . $data['lesson_id'] . ';

						/* Function to allow user to enter the number of the page he wants to visit */
						function enable_gotopage()
						{
							techjoomla.jQuery(".pagedetails").hide();
							techjoomla.jQuery(".viewer_gotopage").show();
							techjoomla.jQuery(".viewer_gotopage").focus();
						}

						/* Function to go to page directly */
						function gotopage()
						{
							techjoomla.jQuery(".pagedetails").show();
							techjoomla.jQuery(".viewer_gotopage").hide();
							var pagenumberToVisit = techjoomla.jQuery(".viewer_gotopage").val();

							if (pagenumberToVisit)
							viewer.scrollTo(pagenumberToVisit);
						}

						/* Function to go next page */
						function nextpage()
						{
							viewer.scrollTo(Crocodoc.SCROLL_NEXT);
						}

						/* Function to go to previous directly */
						function previouspage()
						{
							viewer.scrollTo(Crocodoc.SCROLL_PREVIOUS);
						}

						/* Function to zoom in */
						function zoomin()
						{
							viewer.zoom(Crocodoc.ZOOM_IN);
						}

						/* Function to zoom out */
						function zoomout()
						{
							viewer.zoom(Crocodoc.ZOOM_OUT);
						}

						/* Function to set mode as per preference */
						function setMode(mode)
						{
							techjoomla.jQuery(".controls button").removeClass("selected");
							techjoomla.jQuery(".controls button." + mode).addClass("selected");

							switch (mode)
							{
								case "pop":
									viewer.setLayout(Crocodoc.LAYOUT_PRESENTATION);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									techjoomla.jQuery("body").removeClass().addClass("crocodoc-presentation-pop");
									break;

								case "fade":
									viewer.setLayout(Crocodoc.LAYOUT_PRESENTATION);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									techjoomla.jQuery("body").removeClass().addClass("crocodoc-presentation-fade");
									break;

								case "spin":
									viewer.setLayout(Crocodoc.LAYOUT_PRESENTATION);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									techjoomla.jQuery("body").removeClass().addClass("crocodoc-presentation-spin");
									break;

								case "slide":
									viewer.setLayout(Crocodoc.LAYOUT_PRESENTATION);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									techjoomla.jQuery("body").removeClass().addClass("crocodoc-presentation-slide");
									break;

								case "carousel":
									viewer.setLayout(Crocodoc.LAYOUT_PRESENTATION);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									viewer.zoom(Crocodoc.ZOOM_OUT);
									techjoomla.jQuery("body").removeClass().addClass("crocodoc-presentation-carousel");
									break;

								case "book":
									viewer.setLayout(Crocodoc.LAYOUT_PRESENTATION_TWO_PAGE);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									viewer.zoom(Crocodoc.ZOOM_OUT);
									techjoomla.jQuery("body").removeClass().addClass("crocodoc-pageflip");
									break;
								case "plain":
									viewer.setLayout(Crocodoc.LAYOUT_VERTICAL);
									viewer.zoom(Crocodoc.ZOOM_AUTO);
									techjoomla.jQuery("body").removeClass();
									break;
							}
						}


						var viewer = Crocodoc.createViewer(".viewer", {
							url: url,
							plugins: {
								// config for the analytics plugin
								analytics: {
									ontrack: function (page, seconds) {

										/* Get the time spent on the page */
										doc_object["total_time"] = seconds;
									}
								},
								realtime: {
									url: "' . $realtime_url . '"
								}

							}
						});


						viewer.on("ready", function (event) {

							setTimeout(hideImage, 5000);
							techjoomla.jQuery(".currentpage").html(' . $data['current'] . ');


							/*viewer.on("realtimecomplete", function () {
								setTimeout(hideImage, 2500);
							});*/



							var wheight = techjoomla.jQuery("#main_doc_container").height();
							var wwidth = techjoomla.jQuery("#main_doc_container").width();

							/*techjoomla.jQuery(".crocodoc-doc").height(wheight);
							techjoomla.jQuery(".crocodoc-doc").width(wwidth);*/

							/*showloading(0);*/


							/* Get total number of pages of the document */
							doc_object["total_content"] = event.data.numPages;

							/* Set total pages in toolbar */
							techjoomla.jQuery(".totalPagesSpan").html("/" +event.data.numPages);

							/* Page change on next and previous keyboard buttons */
							techjoomla.jQuery(window).on("keydown", function (ev)
							{
								if (ev.keyCode === 37) {
									viewer.scrollTo(Crocodoc.SCROLL_PREVIOUS);
								} else if (ev.keyCode === 39) {
									viewer.scrollTo(Crocodoc.SCROLL_NEXT);
								} else {
									return;
								}
								ev.preventDefault();
							});

							/*
							if ("' . $SelectedLayout . '" !== "plain")
							{
								techjoomla.jQuery(window).bind("mousewheel", function(e){
									if(e.originalEvent.wheelDelta /120 > 0) {
										viewer.zoom(Crocodoc.ZOOM_IN);
									}
									else{
										viewer.zoom(Crocodoc.ZOOM_OUT);
									}
								});
							}*/

							/* Id totla number of pages is 1 .. then save the status as completetd */
							if (event.data.numPages == 1)
							{
								doc_object["current_position"] = 1;
								techjoomla.jQuery.ajax({
										type: "POST",
										async:false,
										url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsdocument&plgtask=updateData",
										data: {
											user_id: doc_object["user_id"],
											lesson_id: doc_object["lesson_id"],
											current_position: doc_object["current_position"],
											total_time: doc_object["total_time"],
											total_content: doc_object["total_content"],
											attempt : ' . $data['attempt'] . '
										},
										dataType: "JSON",
										success: function(data) {

										}
								});
							}


							setMode("' . $SelectedLayout . '");


							/* On continuing old attempt scroll directly to last visited page */
							viewer.scrollTo(' . $data['current'] . ');

							/* Save data of the user. time spent and current position */
							viewer.on("pagefocus", function (ev) {

								techjoomla.jQuery(".currentpage").html(ev.data.page);
								doc_object["current_position"] = ev.data.page;

								techjoomla.jQuery.ajax({
											type: "POST",
											async:false,
											url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsdocument&plgtask=updateData",
											data: {
												user_id: doc_object["user_id"],
												lesson_id: doc_object["lesson_id"],
												current_position: doc_object["current_position"],
												total_time: doc_object["total_time"],
												total_content: doc_object["total_content"],
												attempt : ' . $data['attempt'] . '
											},
											dataType: "JSON",
											success: function(data) {

											}
										});

							});

							viewer.on("pageload",function(ev){
								techjoomla.jQuery.ajax({
									type: "POST",
									async:false,
									url: "index.php?option=com_tjlms&task=callSysPlgin&plgType=tjlmsdocument&plgtask=updateData",
									data: {
										user_id: doc_object["user_id"],
										lesson_id: doc_object["lesson_id"],
										current_position: doc_object["current_position"],
										total_time: doc_object["total_time"],
										total_content: doc_object["total_content"],
										attempt : ' . $data['attempt'] . '
									},
									dataType: "JSON",
									success: function(data) {

									}
								});
							});

						});

						/* Load the viewer */
						viewer.load();

					</script>

					<style>
						.viewer {
							height: 90%;
						}

					</style>




			';
		}

		return $html;
	}

	/**
	 * update the appemt data
	 *
	 * @since 1.0.0
	 */
	public function updateData() {

		header('Content-type: application/json');
		$input = JFactory::getApplication()->input;

		$post             = $input->post;
		$lesson_id        = $post->get('lesson_id', '', 'INT');
		$current_position = $post->get('current_position', '', 'INT');
		$total_content    = $post->get('total_content', '', 'INT');
		$time_spent       = $post->get('total_time', '', 'FLOAT');
		$user_id          = $post->get('user_id', '', 'INT');
		$attempt          = $post->get('attempt', '', 'INT');
		$score            = 0;
		$lesson_status    = 'incomplete';

		if ($current_position == $total_content) {
			$lesson_status = 'completed';
		}

		require_once JPATH_SITE . '/components/com_tjlms/helpers/tracking.php';

		$comtjlmstrackingHelper = new comtjlmstrackingHelper();
		$trackingid             = $comtjlmstrackingHelper->update_lesson_track($lesson_id, $attempt, $score, $lesson_status, $user_id, $total_content, $current_position, $time_spent);
		$trackingid             = json_encode($trackingid);
		echo $trackingid;
		jexit();
	}

	/**
	 * Check session ID for a document whether expired or not
	 *
	 * @since 1.0.0
	 */
	public function checkSessionForDocument($lesson_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('m.params');
		$query->from('#__tjlms_media as m');
		$query->join('LEFT', '#__tjlms_lessons as l ON l.media_id=m.id');
		$query->where('l.id=' . $lesson_id);
		$db->setQuery($query);
		$params = $db->loadresult();

		$jsonDecodedParams = json_decode($params);

		if (isset($jsonDecodedParams->expire_at)) {
			$expire_at = $jsonDecodedParams->expire_at;

			// Convert the time in Y-m-d H:i:s format
			$expire_at = date("Y-m-d H:i:s", strtotime($expire_at));

			// Get current time to compare
			$current_time = date('Y-m-d H:i:s');

			$date = new DateTime($expire_at);
			$now  = new DateTime();

			// Compare the two timings
			$time_diff = $date->diff($now)->invert;

			// if session is present and not expired return the params
			if ($time_diff == 1) {
				return $jsonDecodedParams;
			}
		}
		return false;
	}

	/**
	 * Store session ID for a document in tjlms_media table
	 *
	 * @since 1.0.0
	 */
	public function storeSessionData($sessionData, $lesson_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('m.id, m.params');
		$query->from('#__tjlms_media as m');
		$query->join('LEFT', '#__tjlms_lessons as l ON l.media_id=m.id');
		$query->where('l.id=' . $lesson_id);
		$db->setQuery($query);
		$mediaData = $db->loadObject();

		$jsonDecodedParams               = json_decode($mediaData->params);
		$jsonDecodedParams->session_id   = $sessionData->id;
		$jsonDecodedParams->expire_at    = $sessionData->expires_at;
		$jsonDecodedParams->assets_url   = $sessionData->urls->assets;
		$jsonDecodedParams->realtime_url = $sessionData->urls->realtime;

		$jsonEncodedParam = json_encode($jsonDecodedParams);

		$object         = new stdClass();
		$object->params = $jsonEncodedParam;
		$object->id     = $mediaData->id;

		if (!$db->updateObject('#__tjlms_media', $object, 'id')) {
			echo $db->stderr();
			return false;
		}
		return true;

	}

}//end class
