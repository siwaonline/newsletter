<?php

namespace Ecodev\Newsletter\ViewHelpers;

use Ecodev\Newsletter\MVC\ExtDirect\Api;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * View helper which allows
 *
 * = Examples =
 *
 * <newsletter:be.moduleContainer pageTitle="foo">
 *    <newsletter:includeDirectApi />
 * </newsletter:be.moduleContainer>
 */
class ExtDirectProviderViewHelper extends AbstractViewHelper
{
    /**
     * @var Api
     */
    protected $apiService;

    /**
     * @see Classes/Core/ViewHelper/\TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper#initializeArguments()
     */
    public function initializeArguments()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->apiService = $objectManager->get(Api::class);
        $this->registerArgument('name', 'string', 'the file to include', false, 'remoteDescriptor');
        $this->registerArgument('namespace', 'string', 'the namespace the variable is placed', false, 'Ext.ux.Ecodev.Newsletter.Remote');
        $this->registerArgument('routeUrl', 'string', 'you can specify a URL that acts as router', false, null);
    }

    /**
     * Generates a Ext.Direct API descriptor and adds it to the pagerenderer.
     * Also calls Ext.Direct.addProvider() on itself (at js side).
     * The remote API is directly useable.
     *
     */
    public function render()
    {
        $routeUrl = $this->arguments['routeUrl'];
        $namespace = $this->arguments['namespace'];
        $name = $this->arguments['name'];
        if ($routeUrl === null) {
            $routeUrl = $this->renderingContext->getControllerContext()->getUriBuilder()->reset()->build() . '&Ecodev\\Newsletter\\ExtDirectRequest=1';
        }

        $api = $this->apiService->createApi($routeUrl, $namespace);

        // prepare output variable
        $jsCode = '';
        $descriptor = $namespace . '.' . $name;
        // build up the output
        $jsCode .= 'Ext.ns(\'' . $namespace . '\'); ' . "\n";
        $jsCode .= $descriptor . ' = ';
        $jsCode .= json_encode($api);
        $jsCode .= ";\n";
        $jsCode .= 'Ext.Direct.addProvider(' . $descriptor . ');' . "\n";
        // add the output to the pageRenderer
        $this->pageRenderer->addJsInlineCode('newsletterExtOnReady', $jsCode, false, false);
    }
}
