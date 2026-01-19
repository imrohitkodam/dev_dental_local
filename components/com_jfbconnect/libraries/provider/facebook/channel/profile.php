<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

class JFBConnectProviderFacebookChannelProfile extends JFBConnectChannel
{
    var $name = "Profile";

    public function setup()
    {
        $this->name = "Profile";
        $this->outbound = true;
        $this->inbound = true;
        $this->requiredScope[] = 'user_posts';
        $this->postSuccessMessage = 'COM_JFBCONNECT_CHANNELS_FACEBOOK_PROFILE_POST_SUCCESS';
    }

    public function canPostToNetwork()
    {
        return false;
    }

    public function canPublish($data)
    {
        return true;
    }

    public function getStream($stream)
    {
        $jid = $this->options->get('user_id');
        $uid = JFBCFactory::usermap()->getProviderUserId($jid, 'facebook');
        $feed = JFBCFactory::cache()->get('facebook.profile.feed.' . $uid);
        if ($feed === false)
        {
            $params = array();
            $params['access_token'] = JFBCFactory::usermap()->getUserAccessToken($this->options->get('user_id'), 'facebook');
            $feed = $this->provider->api( $uid . '/feed?fields=message,from,updated_time,name,link,caption,description,comments,picture,full_picture,privacy', $params, true, 'GET' );
            JFBCFactory::cache()->store($feed, 'facebook.profile.feed.' . $uid);
        }

        if($feed['data'])
        {
            //check to either show thumnail or full image of the preview
            $size_image = $this->options->get('size_image');
            $key = $size_image == 'full' ? 'full_picture' : 'picture';

            foreach($feed['data'] as $data)
            {
//                if(array_key_exists('message', $data))
//                {
                if($data['privacy']['value'] != 'EVERYONE') continue;

                    $post = new JFBConnectPost();

                    if(isset($data['actions'][0]))
                    {
                        $post->link = $data['actions'][0]['link'];
                    }
                    else
                    {
                        $ids = explode("_", $data['id']);
                        $idIndex = count($ids) - 1;
                        $post->link = 'https://www.facebook.com/'.$uid.'/posts/'.$ids[$idIndex];
                    }

                    $post->message = (array_key_exists('message', $data)?$data['message']:"");
                    $post->authorID = $data['from']['id'];
                    $post->authorScreenName = $data['from']['name'];
                    $post->updatedTime = (array_key_exists('updated_time', $data)?$data['updated_time']:"");
                    $post->thumbTitle = (array_key_exists('name', $data)?$data['name']:"");
                    $post->thumbLink = (array_key_exists('link', $data)?$data['link']:"");
                    $post->thumbPicture = (array_key_exists($key, $data)?$data[$key]:"");
                    $post->thumbCaption = (array_key_exists('caption', $data)?$data['caption']:"");
                    $post->thumbDescription = (array_key_exists('description', $data)?$data['description']:"");
                    $post->comments = (array_key_exists('comments', $data)?$data['comments']:"");

                    $stream->addPost($post);
//                }
            }
        }
    }
}