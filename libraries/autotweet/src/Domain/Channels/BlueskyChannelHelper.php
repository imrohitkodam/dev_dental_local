<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

final class BlueskyChannelHelper extends ChannelHelper
{
    private $blueskyHelper;

    /**
     * sendMessage.
     *
     * @param string $message Param
     * @param object $data    Params
     *
     * @return bool
     */
    public function sendMessage($message, $data)
    {
        $imageFile = null;

        try {
            $imageUrl = $data->image_url;

            if (($this->isMediaModeTextOnlyPost()) || (empty($imageUrl))) {
                return $this->publishPost($message);
            }

            $imageFile = ImageUtil::getInstance()->downloadImage($imageUrl);

            return $this->publishPost(
                $message,
                $imageFile,
                $data
            );
        } catch (Exception $exception) {
            $result = [
                false,
                $exception->getMessage(),
            ];
        }

        if ($imageFile) {
            ImageUtil::getInstance()->releaseImage($imageFile);
        }

        return $result;
    }

    /**
     * hasWeight.
     *
     * @return bool
     */
    public function hasWeight()
    {
        return true;
    }

    /**
     * includeHashTags.
     *
     * @return bool
     */
    public function includeHashTags()
    {
        return $this->channel->params->get('hashtags', true);
    }

    private function getApiInstance()
    {
        $this->blueskyHelper = new BlueskyHelper(
            $this->get('identifier'),
            $this->get('password'),
        );

        return $this->blueskyHelper;
    }

    /**
     * publishPost.
     *
     * @param string             $statusMessage Param
     * @param string             $imagefile     Param
     * @param AutotweetTablePost $data          Param
     *
     * @return array
     */
    private function publishPost($statusMessage, $imagefile = null, $data = null)
    {
        try {
            $logger = AutotweetLogger::getInstance();
            $logger->log(\Joomla\CMS\Log\Log::INFO, 'publishPost: '.$statusMessage.' - '.$imagefile);

            $this->getApiInstance();
            $this->blueskyHelper->login();
            $accountDid = $this->blueskyHelper->getAccountDid();

            $defaultLanguage = \JFactory::getApplication()->getLanguage();
            $languages = \JLanguageHelper::getLanguages('lang_code');
            $sefLanguage = $languages[$defaultLanguage->getTag()];

            $facets = array_merge(
                $this->extractLinkFacets($statusMessage),
                $this->extractHashtagFacets($statusMessage)
            );

            $parameters = [
                'collection' => 'app.bsky.feed.post',
                'repo' => $accountDid,
                'record' => [
                    'text' => $statusMessage,
                    'langs' => [$sefLanguage->sef],
                    'createdAt' => date('c'),
                    '$type' => 'app.bsky.feed.post',
                    'facets' => $facets
                ],
            ];

            if ($imagefile) {
                $logger->log(\Joomla\CMS\Log\Log::INFO, 'publishPost uploadBlob '.$imagefile);
                $image = $this->blueskyHelper->uploadBlob($imagefile);

                $parameters['record']['embed'] = [
                    '$type' => 'app.bsky.embed.images',
                    'images' => [
                        [
                            'alt' => PostHelper::getAltText($statusMessage, $data),
                            'image' => $image,
                        ],
                    ],
                ];
            }

            $logger->log(\Joomla\CMS\Log\Log::INFO, 'publishPost createRecord', $parameters);
            $response = $this->blueskyHelper->createRecord($parameters);

            if (isset($response->error)) {
                return [
                    false,
                    $response->message,
                ];
            }

            $parts = explode('app.bsky.feed.', $response->uri);
            $link = 'https://bsky.app/profile/'.$this->get('identifier').'/'.$parts[1];

            return [
                true,
                $link
            ];
        } catch (\Exception $exception) {
            $message = [
                false,
                $exception->getMessage(),
            ];

            return $message;
        }
    }

    private function extractLinkFacets($text)
    {
        $pattern = '/(https?:\/\/\S+)/i';
        preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE);

        $result = [];

        foreach ($matches[0] as $match) {
            $url = $match[0];
            $start = $match[1];
            $end = $start + strlen($url) - 1;

            $result[] = [
                '$type' => 'app.bsky.richtext.facet',
                'index' => [
                    'byteStart' => $start,
                    'byteEnd' => $end+1
                ],
                'features' => [
                    [
                        '$type' => 'app.bsky.richtext.facet#link',
                        'uri' => $url
                    ]
                ]
            ];
        }

        return $result;
    }

    private function extractHashtagFacets($text)
    {
        $pattern = '/#\w+/i';
        preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE);

        $result = [];

        foreach ($matches[0] as $match) {
            $hashtag = $match[0];
            $start = $match[1];
            $end = $start + strlen($hashtag) - 1;

            $result[] = [
                '$type' => 'app.bsky.richtext.facet',
                'index' => [
                    'byteStart' => $start,
                    'byteEnd' => $end+1
                ],
                'features' => [
                    [
                        '$type' => 'app.bsky.richtext.facet#tag',
                        'tag' => str_replace('#', '', $hashtag)
                    ]
                ]
            ];
        }

        return $result;
    }
}
