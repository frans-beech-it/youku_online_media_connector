<?php
defined('TYPO3_MODE') or ('Access denied.');

call_user_func(function($packageKey) {

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'] = [
        'youku' => \BeechIt\YoukuOnlineMediaConnector\Helper\YoukuHelper::class,
    ];

    // Custom mime type
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['FileInfo']['fileExtensionToMimeType']['youku'] = 'video/youku';

    // Add as allowed media extension
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',youku';

    // Renderers
    $rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
    $rendererRegistry->registerRendererClass(
        \BeechIt\YoukuOnlineMediaConnector\Renderer\YoukuRenderer::class
    );

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerFileExtension('youku', 'file-youku');
    $iconRegistry->registerIcon(
        'file-youku',
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        ['source' => 'EXT:youku_online_media_connector/ext_icon.png']
    );

}, $_EXTKEY);