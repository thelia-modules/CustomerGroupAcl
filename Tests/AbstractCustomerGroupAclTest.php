<?php

namespace CustomerGroupAcl\Tests;

use CustomerGroup\Tests\AbstractCustomerGroupTest;
use CustomerGroupAcl\ACL\AclXmlFileloader;
use CustomerGroupAcl\CustomerGroupAcl;
use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Model\AclQuery;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Propel\Runtime\Propel;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ModuleQuery;

/**
 * Base class for CustomerGroupAcl tests.
 */
abstract class AbstractCustomerGroupAclTest extends AbstractCustomerGroupTest
{
    protected static $TEST_CUSTOMER_GROUP_CODES = [
        "-customer-group-unit-test-group-a-",
        "-customer-group-unit-test-group-b-",
        "-customer-group-unit-test-group-c-",
    ];

    /**
     * Codes for the test ACLs.
     * Make sure these and the fixture file are in sync.
     * @todo Ensure uniqueness.
     * @var array
     */
    protected static $TEST_ACL_CODES = [
        "-customer-group-acl-unit-test-acl-a-",
        "-customer-group-acl-unit-test-acl-b-",
    ];

    /**
     * Map groupCode => [aclCode => [accessCode, ...], ...] of the expected group acl accesses in the test fixtures.
     * @var array
     */
    protected static $expectedAclFixturesAccesses = [];

    /**
     * Whether the acl fixtures were loaded and need to be rollback.
     * @var boolean
     */
    protected $aclFixturesLoaded = false;

    /**
     * ACLs to be used for tests.
     * Available only after calling loadAclFixtures().
     * @var array
     */
    protected $testAcls = [];

    /**
     * Customer group ACLs to be used for tests.
     * Available only after calling loadAclFixtures().
     * @var array
     */
    protected $testCustomerGroupAcls = [];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make sure these and the fixture file are in sync
        self::$expectedAclFixturesAccesses = [
            self::$TEST_CUSTOMER_GROUP_CODES[0] => [
                self::$TEST_ACL_CODES[0] => [
                    "VIEW",
                    "CREATE",
                ],
                self::$TEST_ACL_CODES[1] => [
                    "VIEW",
                ],
            ],
            self::$TEST_CUSTOMER_GROUP_CODES[1] => [
                self::$TEST_ACL_CODES[1] => [
                    "VIEW",
                    "CREATE",
                    "UPDATE",
                    "DELETE",
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        self::$testModulesPath
            = __DIR__
            . DIRECTORY_SEPARATOR . "fixtures"
            . DIRECTORY_SEPARATOR . "modules";
    }

    public function tearDown()
    {
        parent::tearDown();

        if ($this->aclFixturesLoaded) {
            Propel::getConnection()->rollBack();
        }
    }

    /**
     * Get the module id for this module.
     * @return int
     */
    protected function getThisModuleId()
    {
        return ModuleQuery::create()->findOneByCode(CustomerGroupAcl::getModuleCode())->getId();
    }

    protected function getStubModule($moduleName)
    {
        $stubModule = parent::getStubModule($moduleName);

        $stubModule->method("getId")->willReturn($this->getThisModuleId());

        return $stubModule;
    }

    /**
     * Load acl and customer group acl fixtures, in a new transaction level.
     * They will be rollback on tear-down.
     */
    protected function loadAclFixtures()
    {
        Propel::getConnection()->beginTransaction();

        $aclXmlFileLoader = new AclXmlFileloader(new Translator($this->container));
        $aclXmlFileLoader->load($this->getStubModule("ModuleValidConfigFile"));

        foreach (static::$TEST_ACL_CODES as $aclCode) {
            $this->testAcls[] = AclQuery::create()->findOneByCode($aclCode);
        }

        foreach (static::$expectedAclFixturesAccesses as $customerGroupCode => $acls) {
            foreach ($acls as $aclCode => $accesses) {
                foreach ($accesses as $access) {
                    /** @var CustomerGroupAclQuery $query */
                    $query = CustomerGroupAclQuery::create();

                    $query
                        ->useCustomerGroupQuery()
                        ->filterByCode($customerGroupCode)
                        ->endUse();

                    $query
                        ->useAclQuery()
                        ->filterByCode($aclCode)
                        ->endUse();

                    $query->filterByType(CustomerGroupAclAccessManager::getAccessPowsValue($access));

                    $this->testCustomerGroupAcls[] = $query->findOne();
                }
            }
        }

        $this->aclFixturesLoaded = true;
    }

    /**
     * Return the given acl code, or change it so that it is unique.
     * @param string $aclCode Acl code to make unique.
     * @return string
     */
    protected function makeUniqueAclCode($aclCode)
    {
        while (null !== AclQuery::create()->findOneByCode($aclCode)) {
            $aclCode .= rand(0, 9);
        }

        return $aclCode;
    }
}
