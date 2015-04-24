<?php

namespace CustomerGroupAcl\Tests\ACL;

use CustomerGroup\Model\CustomerGroup;
use CustomerGroupAcl\ACL\AclXmlFileloader;
use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\AclQuery;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;
use Propel\Runtime\Exception\PropelException;
use Thelia\Core\Translation\Translator;

/**
 * Tests for AclXmlFileloader.
 */
class AclXmlFileloaderTest extends AbstractCustomerGroupAclTest
{
    /**
     * The XML file loader under test.
     * @var AclXmlFileloader
     */
    protected $aclXmlFileloader;

    public function setUp()
    {
        parent::setUp();

        $this->aclXmlFileloader = new AclXmlFileloader(new Translator($this->container));
    }

    /**
     * Assert that a CustomerGroupAcl exists, is unique and is activated.
     * @param Acl $expectedAcl Expected Acl.
     * @param CustomerGroup $expectedCustomerGroup Expected CustomerGroup.
     * @param int $expectedType Expected access resource.
     * @throws PropelException
     */
    protected function assertCustomerGroupAclExistsAndUnique(
        Acl $expectedAcl,
        CustomerGroup $expectedCustomerGroup,
        $expectedType
    ) {
        $customerGroupAcls = CustomerGroupAclQuery::create()
            ->filterByAcl($expectedAcl)
            ->filterByCustomerGroup($expectedCustomerGroup)
            ->filterByType($expectedType)
            ->find();

        $this->assertNotEmpty($customerGroupAcls);
        $this->assertEquals(1, $customerGroupAcls->count());

        $customerGroupAcl = $customerGroupAcls->current();
        $this->assertEquals(1, $customerGroupAcl->getActivate());
    }

    /**
     * @covers AclXmlFileloader::load()
     */
    public function testLoadModuleWithNoConfigFile()
    {
        // get the acl and group acl state
        $initialAcls = AclQuery::create()->find();
        $initialCustomerGroupAcls = CustomerGroupAclQuery::create()->find();

        // load a module with no ACL configuration
        $this->aclXmlFileloader->load(
            $this->getStubModule("ModuleNoConfigFile")
        );

        // assert that acl and group acl are unchanged
        $finalAcls = AclQuery::create()->find();
        $this->assertEquals($initialAcls, $finalAcls);

        $finalCustomerGroupAcls = CustomerGroupAclQuery::create()->find();
        $this->assertEquals($initialCustomerGroupAcls, $finalCustomerGroupAcls);
    }

    /**
     * @covers AclXmlFileloader::load()
     * @expectedException \Exception
     */
    public function testLoadModuleWithInvalidConfigFile()
    {
        // load a test module with an invalid ACL configuration
        $this->aclXmlFileloader->load(
            $this->getStubModule("ModuleInvalidConfigFile")
        );
    }

    /**
     * @covers AclXmlFileloader::load()
     */
    public function testLoadModuleWithValidConfigFile()
    {
        // get the acl and group acl state
        $initialAcls = AclQuery::create()->find();
        $initialCustomerGroupAcls = CustomerGroupAclQuery::create()->find();

        // load a test module with a valid ACL configuration
        $this->aclXmlFileloader->load(
            $this->getStubModule("ModuleValidConfigFile")
        );

        // assert that the initial acl and group acl are still here
        $finalAcls = AclQuery::create()->find();
        foreach ($initialAcls as $acl) {
            $this->assertTrue($finalAcls->contains($acl));
        }

        $finalCustomerGroupAcls = CustomerGroupAclQuery::create()->find();
        foreach ($initialCustomerGroupAcls as $customerGroupAcl) {
            $this->assertTrue($finalCustomerGroupAcls->contains($customerGroupAcl));
        }

        // ensure that the new ACL were created
        $this->assertEquals($initialAcls->count() + 2, $finalAcls->count());

        $aclA = AclQuery::create()->findOneByCode("-customer-group-acl-unit-test-acl-a-");
        $this->assertNotNull($aclA);
        $aclA->setLocale("en_US");
        $this->assertEquals("Test ACL A", $aclA->getTitle());
        $this->assertEquals("Test ACL A description.", $aclA->getDescription());
        $aclA->setLocale("fr_FR");
        $this->assertEquals("ACL de test A", $aclA->getTitle());
        $this->assertEquals("Description de l'ACL de test A.", $aclA->getDescription());

        $aclB = AclQuery::create()->findOneByCode("-customer-group-acl-unit-test-acl-b-");
        $this->assertNotNull($aclB);
        $aclB->setLocale("en_US");
        $this->assertEquals("Test ACL B", $aclB->getTitle());
        $this->assertEquals("Test ACL B description.", $aclB->getDescription());
        $aclB->setLocale("fr_FR");
        $this->assertEquals("ACL de test B", $aclB->getTitle());
        $this->assertEquals("Description de l'ACL de test B.", $aclB->getDescription());

        // ensure that the group ACL were created
        $this->assertEquals($initialCustomerGroupAcls->count() + 7, $finalCustomerGroupAcls->count());

        $this->assertCustomerGroupAclExistsAndUnique(
            $aclA,
            self::$testCustomerGroups[0],
            CustomerGroupAclAccessManager::getAccessPowsValue("VIEW")
        );
        $this->assertCustomerGroupAclExistsAndUnique(
            $aclA,
            self::$testCustomerGroups[0],
            CustomerGroupAclAccessManager::getAccessPowsValue("CREATE")
        );

        $this->assertCustomerGroupAclExistsAndUnique(
            $aclB,
            self::$testCustomerGroups[0],
            CustomerGroupAclAccessManager::getAccessPowsValue("VIEW")
        );

        $this->assertCustomerGroupAclExistsAndUnique(
            $aclB,
            self::$testCustomerGroups[1],
            CustomerGroupAclAccessManager::getAccessPowsValue("VIEW")
        );
        $this->assertCustomerGroupAclExistsAndUnique(
            $aclB,
            self::$testCustomerGroups[1],
            CustomerGroupAclAccessManager::getAccessPowsValue("CREATE")
        );
        $this->assertCustomerGroupAclExistsAndUnique(
            $aclB,
            self::$testCustomerGroups[1],
            CustomerGroupAclAccessManager::getAccessPowsValue("UPDATE")
        );
        $this->assertCustomerGroupAclExistsAndUnique(
            $aclB,
            self::$testCustomerGroups[1],
            CustomerGroupAclAccessManager::getAccessPowsValue("DELETE")
        );
    }
}
