<?php

namespace Ecodev\Newsletter\ViewHelpers;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View helper which allows you to include a JS File.
 * Note: This feature is experimental!
 * Note: You MUST wrap this Helper with <newsletter:Be.moduleContainer>-Tags
 *
 * = Examples =
 *
 * <newsletter:be.moduleContainer pageTitle="foo">
 *    <newsletter:includeJsFile file="foo.js" extKey="blog_example" pathInsideExt="Resources/Public/JavaScript" />
 * </newsletter:be.moduleContainer>
 */
class IncludeJsFolderViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'the file to include', false, null);
        $this->registerArgument('extKey', 'string', 'the extension, where the file is located', false, null);
        $this->registerArgument('pathInsideExt', 'string', 'the path to the file relative to the ext-folder', false, 'Resources/Public/JavaScript/');
        $this->registerArgument('recursive', 'boolean', 'include folder and subfolders', false, false);
    }

    /**
     * Calls addJsFile for each file in the given folder on the Instance of TYPO3\CMS\Core\Page\PageRenderer.
     *
     */
    public function render()
    {
        $name = $this->arguments['name'];
        $extKey = $this->arguments['extKey'];
        $pathInsideExt = $this->arguments['pathInsideExt'];
        $recursive = $this->arguments['recursive'];
        if ($extKey == null) {
            $extKey = $this->renderingContext->getControllerContext()->getRequest()->getControllerExtensionKey();
        }
        $extPath = ExtensionManagementUtility::extPath($extKey);
        if (TYPO3_MODE === 'FE') {
            $extRelPath = mb_substr($extPath, mb_strlen(PATH_site));
        } else {
            $extRelPath = ExtensionManagementUtility::extPath($extKey);
        }
        $absFolderPath = $extPath . $pathInsideExt . $name;
        // $files will include all files relative to $pathInsideExt
        if ($recursive === false) {
            $files = GeneralUtility::getFilesInDir($absFolderPath);
            foreach ($files as $hash => $filename) {
                $files[$hash] = $name . $filename;
            }
        } else {
            $files = GeneralUtility::getAllFilesAndFoldersInPath([], $absFolderPath, '', 0, 99, '\\.svn');
            foreach ($files as $hash => $absPath) {
                $files[$hash] = str_replace($extPath . $pathInsideExt, '', $absPath);
            }
        }
        foreach ($files as $name) {
            $this->pageRenderer->addJsFile($extRelPath . $pathInsideExt . $name, "text/javascript", false, false, false, true);
        }
    }
}
