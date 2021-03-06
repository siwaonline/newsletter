<?php

namespace Ecodev\Newsletter\ViewHelpers;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Make localization files available in JavaScript
 */
class LocalizationViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'the file to include', false, "locallang.xlf");
        $this->registerArgument('extKey', 'string', 'the extension, where the file is located', false, null);
        $this->registerArgument('pathInsideExt', 'string', 'the path to the file relative to the ext-folder', false, 'Resources/Private/Language/');
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
        $names = explode(',', $name);

        if ($extKey == null) {
            $extKey = $this->renderingContext->getControllerContext()->getRequest()->getControllerExtensionKey();
        }
        $extPath = ExtensionManagementUtility::extPath($extKey);

        $localizations = [];
        foreach ($names as $name) {
            $filePath = $extPath . $pathInsideExt . $name;
            $localizations = array_merge($localizations, $this->getLocalizations($filePath));
        }

        $localizations = json_encode($localizations);
        $javascript = "Ext.ux.Ecodev.Newsletter.Language = $localizations;";

        $this->pageRenderer->addJsInlineCode($filePath, $javascript, false, false);
    }

    /**
     * Returns localization variables within an array
     *
     * @param string $filePath
     *
     * @throws Exception
     * @return array
     */
    protected function getLocalizations($filePath)
    {
        global $LANG;
        global $LOCAL_LANG;

        // Language inclusion
        $LANG->includeLLFile($filePath);
        if (!isset($LOCAL_LANG[$LANG->lang]) || empty($LOCAL_LANG[$LANG->lang])) {
            $lang = 'default';
        } else {
            $lang = $LANG->lang;
        }

        $result = [];
        foreach ($LOCAL_LANG[$lang] as $key => $value) {
            $target = $value[0]['target'];

            // Replace '.' in key because it would break JSON
            $key = str_replace('.', '_', $key);
            $result[$key] = $target;
        }

        return $result;
    }
}
