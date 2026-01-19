<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

class JFBConnectProviderFacebookChannelGroup extends JFBConnectChannel
{
    var $name = "Group";

    public function setup()
    {
        $this->name = "Group";
        $this->outbound = true;
        $this->inbound = true;
        $this->requiredScope[] = 'groups_access_member_info';
        $this->postSuccessMessage = 'COM_JFBCONNECT_CHANNELS_FACEBOOK_GROUP_POST_SUCCESS';
    }

    public function onAfterSave($newData, $oldData)
    {
        if($newData['attribs']['allow_posts'])
        {
            $this->requiredScope[] = 'publish_to_groups';
        }

        parent::onAfterSave($newData, $oldData);
    }

    public function canPublish($data)
    {
        $canPublish = false;

        $jid = $data['attribs']['user_id'];
        if ($jid)
        {
            $uid = JFBCFactory::usermap()->getProviderUserId($jid, 'facebook');
            if ($uid && isset($data['attribs']['group_id']) && ($data['attribs']['group_id'] != '--') &&
                JFBCFactory::provider('facebook')->hasScope($uid, 'groups_access_member_info') &&
                (JFBCFactory::provider('facebook')->hasScope($uid, 'publish_to_groups') || !$data['attribs']['allow_posts'])
            )
            {
                $canPublish = true;
            }
        }

        return $canPublish;
    }

    public function getStream($stream)
    {
        $groupId = $this->options->get('group_id');
        if (!$groupId || $groupId == '--')
            return;

        $feed = JFBCFactory::cache()->get('facebook.page.group.' . $groupId);
        if ($feed === false)
        {
            $params = array();
            $params['access_token'] = JFBCFactory::usermap()->getUserAccessToken($this->options->get('user_id'), 'facebook');
            $feed = $this->provider->api($groupId . '/feed?fields=message,from,updated_time,name,link,caption,description,comments,picture,full_picture', $params, true, 'GET');
            JFBCFactory::cache()->store($feed, 'facebook.page.group.' . $groupId);
        }

        if($feed['data'])
        {
            //check to either show thumnail or full image of the preview
            $size_image = $this->options->get('size_image');
            $key = $size_image == 'full' ? 'full_picture' : 'picture';

            foreach($feed['data'] as $data)
            {
                if(array_key_exists('message', $data))
                {
                    $post = new JFBConnectPost();

                    if(isset($data['actions'][0]))
                    {
                        $post->link = $data['actions'][0]['link'];
                    }
                    else
                    {
                        $ids = explode("_", $data['id']);
                        $idIndex = count($ids) - 1;
                        $post->link = 'https://www.facebook.com/'.$groupId.'/posts/'.$ids[$idIndex];
                    }

                    $post->message = (array_key_exists('message', $data)?$data['message']:"");
                    if(isset($data['from']))
                    {
                        $post->authorID = $data['from']['id'];
                        $post->authorScreenName = $data['from']['name'];
                    }
                    $post->updatedTime = (array_key_exists('updated_time', $data)?$data['updated_time']:"");
                    $post->thumbTitle = (array_key_exists('name', $data)?$data['name']:"");
                    $post->thumbLink = (array_key_exists('link', $data)?$data['link']:"");
                    $post->thumbPicture = (array_key_exists($key, $data)?$data[$key]:"");
                    $post->thumbCaption = (array_key_exists('caption', $data)?$data['caption']:"");
                    $post->thumbDescription = (array_key_exists('description', $data)?$data['description']:"");
                    $post->comments = (array_key_exists('comments', $data)?$data['comments']:"");

                    $stream->addPost($post);
                }
            }
        }
    }

    public function performPost(JRegistry $data)
    {
        $groupId = $this->options->get('group_id');
        $message = $data->get('message', '');
        $link = $data->get('link', '');

        $params = array();
        $params['access_token'] = JFBCFactory::usermap()->getUserAccessToken($this->options->get('user_id'), 'facebook');
        $params['message'] = $message;
        $params['link'] = $link;

        $return = $this->provider->api($groupId . '/feed', $params);

        return $return;
    }

    /***
     * Get a list of scope requested from users for this object and details about why
     * @return array
     */
    public function getScopeUsed()
    {
        $scopes = $this->requiredScope;

        $attribs = new JRegistry;
        $attribs->loadString($this->options->get('attribs'));
        if($attribs->get('allow_posts') )
            $scopes[] = 'publish_to_groups';

        return $scopes;
    }
}