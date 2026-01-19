<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @extension Edocman
 * @copyright Copyright (C) Ossolution https://www.joomdonation.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
// USER RIGHT - Access of categories (if file is included in some not accessed category) - - - - -
// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
if ($this->t['html5_play'] == 1 && $this->t['filetype'] != 'flv') {
	?>
	<div style="text-align:center;verticle-align:middle;">
		<?php
		if ($this->t['filetype'] == 'mp3') {
			echo '<audio width="'.$this->t['playerwidth'].'" height="'.$this->t['playerheight'].'" style="margin-top: 10px;" controls>';
			echo '<source src="'.$this->t['playfilewithpath'].'" type="video/mp4">';
			echo JText::_('EDOCMAN_BROWSER_DOES_NOT_SUPPORT_AUDIO_VIDEO_TAG');
			echo '</audio>'. "\n";
		} else if ($this->t['filetype'] == 'mp4') {
			echo '<video width="'.$this->t['playerwidth'].'" height="'.$this->t['playerheight'].'" style="margin-top: 10px;" controls>';
			echo '<source src="'.$this->t['playfilewithpath'].'" type="video/mp4">';
			echo JText::_('EDOCMAN_BROWSER_DOES_NOT_SUPPORT_AUDIO_VIDEO_TAG');
			echo '</video>'. "\n";
		} else if ($this->t['filetype'] == 'avi') {
			echo '<video width="'.$this->t['playerwidth'].'" height="'.$this->t['playerheight'].'" style="margin-top: 10px;" controls>';
			echo '<source src="'.$this->t['playfilewithpath'].'" type="video/avi">';
			echo JText::_('EDOCMAN_BROWSER_DOES_NOT_SUPPORT_AUDIO_VIDEO_TAG');
			echo '</video>'. "\n";
		} else if ($this->t['filetype'] == 'ogg') {
			echo '<audio width="'.$this->t['playerwidth'].'" height="'.$this->t['playerheight'].'" style="margin-top: 10px;" controls>';
			echo '<source src="'.$this->t['playfilewithpath'].'" type="audio/ogg">';
			echo JText::_('EDOCMAN_BROWSER_DOES_NOT_SUPPORT_AUDIO_VIDEO_TAG');
			echo '</audio>'. "\n";
		} else if ($this->t['filetype'] == 'ogv') {
			echo '<video width="'.$this->t['playerwidth'].'" height="'.$this->t['playerheight'].'" style="margin-top: 10px;" controls>';
			echo '<source src="'.$this->t['playfilewithpath'].'" type="video/ogg">';
			echo JText::_('EDOCMAN_BROWSER_DOES_NOT_SUPPORT_AUDIO_VIDEO_TAG');
			echo '</video>'. "\n";
		}
	?>
	</div>
	<?php
} else {

	//Flow Player
	$versionFLP 	= '3.2.2';
	$versionFLPJS 	= '3.2.2';
	$document = JFactory::getDocument();
	$document->addScript(JUri::root().'components/com_edocman/assets/flowplayer/flowplayer-'.$versionFLPJS.'.min.js');
	?>
	<div style="text-align:center;">
	<div style="margin: 10px auto;text-align:center;width:328px"><a href="<?php echo $this->t['playfilewithpath']; ?>"  style="display:block;width:328px;height:200px" id="player"></a><?php
		if ($this->t['filetype'] == 'mp3') {
			?><script>
				flowplayer("player", "<?php echo $this->t['playerpath']; ?>flowplayer-<?php echo $versionFLP ?>.swf",
					{
						plugins: {
							controls: {
								fullscreen: false,
								height: <?php echo $this->t['playerheight']; ?>
							}
						}
					}
				);</script><?php
		} else {
			?><script>flowplayer("player", "<?php echo JUri::root().'components/com_edocman/assets/flowplayer/'; ?>flowplayer-<?php echo $versionFLP ?>.swf");</script><?php
		}
		?></div></div><?php
}

