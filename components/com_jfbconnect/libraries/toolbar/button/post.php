<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectToolbarButtonPost extends JFBConnectToolbarButton
{
    var $order = '30';
    var $displayName = "Create Post";
    var $systemName = "post";

    public function html()
    {
        return '<div id="social-post-container" style="display:none"></div>';
    }

    protected function generateJavascript()
    {
        $query = 'option=com_jfbconnect&task=ajax.fetch&library=toolbar.button.autopost&subtask=getHtml&refresh=1';
        $input = JFactory::getApplication()->input;
        $option = $input->get('option');
        $query .= '&ext='.$option;

        $view = $input->get('view');
        if($option == 'com_easyblog' && $view == 'entry')
            $view = 'post';

        $query .= '&extview='.$view;

        $id = $input->get('id');
        if($option == 'com_community')
        {
            if($view == 'groups')
                $id = $input->get('groupid');
            else if($view == 'events')
                $id = $input->get('eventid');
        }

        $query .= '&extid='.$id;

        $uri = JURI::getInstance();
        $uri = JFBConnectUtilities::stripQuery($uri);
        $currentUrl = $uri->toString();
        $root = JURI::root();
        $currentUrl = str_replace($root, "", $currentUrl);
        $query .= '&url='.base64_encode($currentUrl);

        $js = "display: function ()
                {
                    jfbcJQuery('#social-post-container').html('')
                        .append(jfbcJQuery('<div />').attr('id', 'progressbar').progressbar({value: false}))
                        .css('display', 'block')
                        .dialog({title: 'Loading...'});

                    var query = 'option=com_jfbconnect&task=ajax.fetch&library=toolbar.button.post&subtask=getHtml';
                    var promise = jfbc.util.ajax(query);
                    promise.done(jfbc.popup.display).done(function ()
                    {
                        jfbcJQuery('#social-post-message').keyup(function ()
                        {
                            jfbcJQuery('.social-post-counter').each(function(i)
                            {
                                var el = jfbcJQuery(this);
                                var max = el.data('max');
                                var urlLength = el.data('urlLength');
                                var remaining = max - urlLength - jfbcJQuery('#social-post-message').val().length;
                                jfbcJQuery(this).text('(' + remaining + ' characters remaining)');
                            });

                        });
                    });
                },
                submit: function ()
                {
                    jfbcJQuery('#social-post-container').dialog({buttons: null });
                    var form = jfbcJQuery('#social-post-form');
                    var url = form . attr('action') + '&' + jfbc . token + '=1';
                    var data = form . serializeArray();
                    data . push({name: 'link', value: window.location.href});
                    jfbcJQuery.post(url, data).done(function (ret)
                    {
                        var query = '".$query."';
						jfbc.util.ajax(query).done(function( ret ) {
							var isOpen = jfbcJQuery('#social-autopost').css('display') == 'none' ? false : true;
							jfbcJQuery('#social-autopost').replaceWith(ret);
							jfbcJQuery('#social-autopost').accordion();

							if(isOpen) {
								jfbcJQuery('#social-autopost').css('display', 'block');
								jfbcJQuery('#social-autopost').accordion('refresh');
							}
						});

                        jfbcJQuery('#social-post-popup').html(ret);
                        jfbcJQuery('#social-post-container').dialog({buttons: {
                          Close:
                            function ()
                            {
                              jfbcJQuery(this).dialog('close');
                            }
                          }
                      });
                    });
                }";

        return $js;
    }

    public function ajaxGetHtml()
    {

        $return = new stdClass();
        $return->target = '#social-post-container';
        $return->title = JText::_('COM_JFBCONNECT_CHANNELS_POST_TITLE');

        $channels = JFBCFactory::model('channel')->getChannels();
        $checkboxes = array();
        foreach ($channels as $c)
        {
            $options = new JRegistry();
            $options->loadObject($c);
            $data = JFBCFactory::provider($c->provider)->channel($c->type, $options);

            if ($data->postCharacterMax > 0)
                $charCounter = ' <span class="social-post-counter" data-max="' . $data->postCharacterMax . '" data-url-length="' . $data->urlLength . '"></span>';
            else
                $charCounter = "";

            if($data->canPostToNetwork())
            {
                $checkboxes[] = '<input type="checkbox" name="cids[]" class="' . $c->provider . '-' . $c->type . '" value="' . $c->id . '" id="cid_' . $c->id . '" />
                <label class="channel-name" for="cid_' . $c->id . '">
                    <img src="' . JURI::root() . 'media/sourcecoast/images/provider/' . $c->provider . '/icon.png" />' .
                    $c->title .
                    '</label>' .
                    $charCounter;
            }
        }

        if (count($checkboxes) > 0)
        {
            $return->html = '<div id="social-post-popup">
            <form id="social-post-form" action="'.JRoute::_('index.php?option=com_jfbconnect&task=ajax.fetch&library=toolbar.button.post&subtask=createPost').'">
          <p>' . JText::_('COM_JFBCONNECT_CHANNELS_POST_INSTRUCTION_LABEL') . '</p>
          <textarea id="social-post-message" name="message" style="width:90%" rows="5"></textarea>
          <br/><input type="checkbox" id="channels_selectall" onclick="
          jfbcJQuery(\'#social-post-popup :checkbox\').each(function() {
             if(this.id != \'channels_selectall\')
                this.checked = jfbcJQuery(\'#channels_selectall\').is(\':checked\');
             });
          "/><label for="channels_selectall">Select All</label><br/>
          ' . implode("<br/>", $checkboxes) .
                JHTML::_('form.token') .
                '</form>
              </div>';
            $return->buttons = array();
            $button = array('name' => "Post", 'id' => 'submit-social-post', 'action' => 'jfbc.toolbar.post.submit');
            $button['name'] = "Post";
            $return->buttons[] = $button;
        }
        else
            $return->html = JText::_('COM_JFBCONNECT_CHANNELS_POST_ERROR_LABEL');

        echo json_encode($return);
    }

    public function ajaxCreatePost()
    {
        if (JFactory::getUser()->authorise('jfbconnect.channels.post', 'com_jfbconnect'))
        {
            $response = array();

            $input = JFactory::getApplication()->input;
            $message = $input->post->getString('message');
            $link = $input->post->getString('link');

            $cids = $input->post->get('cids', array(), 'array');
            if (empty($cids))
            {
                $response[] = JText::_('COM_JFBCONNECT_CHANNELS_SELECT_CHANNEL_LABEL');
            }

            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables/');
            $row = JTable::getInstance('Channel', 'Table');
            foreach ($cids as $cid)
            {
                $row->load($cid);
                $options = new JRegistry();
                $options->loadObject($row->attribs);
                $channel = JFBCFactory::provider($row->provider)->channel($row->type, $options);

                $post = new JRegistry();
                $post->set('message', $message);
                $post->set('link', $link);

                try
                {
                    $return = $channel->post($post, AP_TYPE_MANUAL);
                }
                catch (Exception $e)
                {
                    $return = false;
                }

                if (!$return)
                    $response[] = JText::sprintf('COM_JFBCONNECT_CHANNELS_POST_FAILED_LABEL', $row->provider, $row->type);
                else
                    $response[] = $return;
            }
            echo implode("<br/>", $response);
        }
        exit;
    }

}