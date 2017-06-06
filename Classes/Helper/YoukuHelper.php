<?php

namespace BeechIt\YoukuOnlineMediaConnector\Helper;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class YoukuHelper extends AbstractOnlineMediaHelper
{
    /**
     * @var array
     */
    static protected $metaDataCache = [];

    /**
     * @param string $url
     * @param Folder $targetFolder
     * @return null|File
     */
    public function transformUrlToFile($url, Folder $targetFolder)
    {
        $videoId = null;
        // Try to get the Youku video ID from given url.
        // These formats are supported with and without http(s)://
        // - v.youku.com/v_show/id_XNjgzNDM4MzIw.html # Normal web link
        // - player.youku.com/player.php/sid/XMzgxNzY3NTU2/v.swf # flash embed
        // - player.youku.com/embed/XMzgxNzY3NTU2 # URL form iframe embed code, can also get code from full iframe snippet
        if (preg_match('/youku\.com\/v_show\/id_([a-z0-9\-_=]+)\.html/i', $url, $match)) {
            $videoId = $match[1];
        } elseif (preg_match('/youku\.com\/player\.php\/sid\/([a-z0-9\-_=]+)/i', $url, $match)) {
            $videoId = $match[1];
        } elseif (preg_match('/youku\.com\/embed\/([a-z0-9\-_=]+)/i', $url, $match)) {
            $videoId = $match[1];
        }
        if (empty($videoId)) {
            return null;
        }

        $file = $this->findExistingFileByOnlineMediaId($videoId, $targetFolder, $this->extension);

        // no existing file create new
        if ($file === null) {
            $data = $this->fetchMetaData($videoId);
            if (!empty($data)) {
                $fileName = $data['title'] . '.' . $this->extension;
            } else {
                $fileName = $videoId . '.' . $this->extension;
            }
            $file = $this->createNewFile($targetFolder, $fileName, $videoId);
        }
        return $file;
    }

    /**
     * @param $videoId
     * @return array
     */
    protected function fetchMetaData($videoId)
    {
        if (!isset(self::$metaDataCache[$videoId])) {
            $data = file_get_contents(sprintf('https://v.youku.com/v_show/id_%s.html', $videoId));
            $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;

            self::$metaDataCache[$videoId] = [
                'title' => $title
            ];
        }
        return self::$metaDataCache[$videoId];
    }

    /**
     * @param File $file
     * @param bool $relativeToCurrentScript
     * @return string
     */
    public function getPublicUrl(File $file, $relativeToCurrentScript = false)
    {
        $videoId = $this->getOnlineMediaId($file);
        return sprintf('https://v.youku.com/v_show/id_%s.html', $videoId);
    }

    /**
     * @param File $file
     * @return string
     */
    public function getPreviewImage(File $file)
    {
        // No public api found currently to fetch preview image
        // For now we use the extension icon
        return ExtensionManagementUtility::extPath('youku_online_media_connector', 'ext_icon.png');
    }

    /**
     * @param File $file
     * @return array
     */
    public function getMetaData(File $file)
    {
        $metadata = [];

        $rawMetadata = $this->fetchMetaData($this->getOnlineMediaId($file));

        // todo: check if this can be different, but looks all videos have same dimensions
        $metadata['width'] = 498;
        $metadata['height'] = 510;
        if (empty($file->getProperty('title')) && !empty($rawMetadata['title'])) {
            $metadata['title'] = strip_tags($rawMetadata['title']);
        }

        return $metadata;
    }

}