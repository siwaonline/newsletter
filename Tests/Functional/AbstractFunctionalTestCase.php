<?php

namespace Ecodev\Newsletter\Tests\Functional;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class AbstractFunctionalTestCase extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    /** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager */
    protected $objectManager;
    protected $testExtensionsToLoad = ['typo3conf/ext/newsletter'];
    protected $coreExtensionsToLoad = ['extbase', 'fluid'];

    /**
     * Auth code for recipient 2
     *
     * @var string
     */
    protected $authCode;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->importDataSet(__DIR__ . '/Fixtures/fixtures.xml');
        $this->authCode = md5(302 . 'recipient2@example.com');
    }

    /**
     * Assert that there is exactly 1 record in sys_log table containing
     * the exact text given in $details
     *
     * @param string $details
     */
    protected function assertRecipientListCallbackWasCalled($details)
    {
        $db = $this->getDatabaseConnection();
        $count = $db->exec_SELECTcountRows('*', 'sys_log', 'details = ' . $db->fullQuoteStr($details, 'sys_log'));
        $this->assertSame(1, $count, 'could not find exactly 1 log record containing "' . $details . '"');
    }
}
