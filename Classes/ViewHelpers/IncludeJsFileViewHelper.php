<?php

namespace Ecodev\Newsletter\ViewHelpers;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
class IncludeJsFileViewHelper extends AbstractViewHelper
{


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'the file to include', false, null);
        $this->registerArgument('extKey', 'string', 'the extension, where the file is located', false, null);
        $this->registerArgument('pathInsideExt', 'string', 'the path to the file relative to the ext-folder', false, 'Resources/Public/JavaScript/');
    }

    /**
     * Calls addJsFile on the Instance of TYPO3\CMS\Core\Page\PageRenderer.
     *
     */
    public function render()
    {
        $name = $this->arguments['name'];
        $extKey = $this->arguments['extKey'];
        $pathInsideExt = $this->arguments['pathInsideExt'];
        if ($extKey == null) {
            $extKey = $this->renderingContext->getControllerContext()->getRequest()->getControllerExtensionKey();
        }
        if (TYPO3_MODE === 'FE') {
            $extPath = ExtensionManagementUtility::extPath($extKey);
            $extRelPath = mb_substr($extPath, mb_strlen(PATH_site));
        } else {
            $extRelPath = ExtensionManagementUtility::extPath($extKey);
        }
        $this->pageRenderer->addJsFile($extRelPath . $pathInsideExt . $name, "text/javascript", false, false, false, true);
    }
}
