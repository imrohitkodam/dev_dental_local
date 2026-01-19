<?php
// no direct access
defined('_JEXEC') or die;
class plgButtonEdocman extends JPlugin
{
	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	function onDisplay($name)
	{		
		
		$linkType = $this->params->get('link_type', 0);
		if ($linkType == 0){
			$js = "
			function jSelectEdocman(id, title, object) {
				var tag = '<a href='+'\"index.php?option=com_edocman&amp;view=document&amp;id='+id+'\">'+title+'</a>';
				jInsertEditorText(tag);
				SqueezeBox.close();
			}";
		}elseif ($linkType == 1){
			$js = "
			function jSelectEdocman(id, title, object) {
			var tag = '<a href='+'\"index.php?option=com_edocman&amp;task=document.download&amp;id='+id+'\">'+title+'</a>';
			jInsertEditorText(tag);
			SqueezeBox.close();
			}";
		}elseif ($linkType == 2){
			$js = "
			function jSelectEdocman(id, title, object) {
			var tag = '<a href='+'\"index.php?option=com_edocman&amp;task=document.viewDoc&amp;id='+id+'\">'+title+'</a>';
			jInsertEditorText(tag);
			SqueezeBox.close();
			}";
		}
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		$doc->addStyleSheet(JURI::root().'plugins/editors-xtd/edocman/css/style.css');
		JHtml::_('behavior.modal');
		$link = 'index.php?option=com_edocman&amp;view=documents&amp;layout=modal&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('Edocman'));
		$button->set('name', 'edocman');
		$button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");
        if (version_compare(JVERSION, '3.0', 'ge'))
        {
            $button->set('class', 'btn modal-button');
        }

		return $button;
	}
}
