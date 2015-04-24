<?php

namespace CustomerGroupAcl\Tests\EventListener;

use CustomerGroup\CustomerGroup;
use CustomerGroupAcl\Event\AclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\EventListener\AclListener;
use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\AclQuery;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;
use Thelia\Model\ModuleQuery;

/**
 * Tests for AclListener.
 */
class AclListenerTest extends AbstractCustomerGroupAclTest
{
    public function setUp()
    {
        parent::setUp();

        $this->loadAclFixtures();

        // register the AclListener under test
        $this->dispatcher->addSubscriber(new AclListener());
    }

    /**
     * @covers AclListener::aclUpdate()
     */
    public function testUpdateAcl()
    {
        /** @var Acl $initialAcl */
        $initialAcl = $this->testAcls[0];
        $initialAclId = $initialAcl->getId();

        $testAclCode = $this->makeUniqueAclCode("-customer-group-acl-unit-test-updated-acl-code-");

        $anotherModuleId = ModuleQuery::create()->findOneByCode(CustomerGroup::getModuleCode())->getId();

        $updateEvent = new AclEvent(
            $testAclCode,
            $anotherModuleId,
            "en_US",
            "New title",
            "New description",
            $initialAclId
        );
        $this->dispatcher->dispatch(CustomerGroupAclEvents::ACL_UPDATE, $updateEvent);

        $finalAcl = AclQuery::create()->findPk($initialAclId);

        $this->assertNotNull($finalAcl);
        $this->assertEquals($finalAcl->getCode(), $testAclCode);
        $this->assertEquals($finalAcl->getModuleId(), $anotherModuleId);
        $finalAcl->setLocale("en_US");
        $this->assertEquals($finalAcl->getTitle(), "New title");
        $this->assertEquals($finalAcl->getDescription(), "New description");
    }

    /**
     * @covers AclListener::aclUpdate()
     */
    public function testUpdateNonExistingAcl()
    {
        // get an ACL id not yet used
        $initialAclId = 1;
        while (null !== AclQuery::create()->findPk($initialAclId)) {
            ++$initialAclId;
        }

        $testAclCode = $this->makeUniqueAclCode("-customer-group-acl-unit-test-new-acl-code-");

        $anotherModuleId = ModuleQuery::create()->findOneByCode(CustomerGroup::getModuleCode())->getId();

        $updateEvent = new AclEvent(
            $testAclCode,
            $anotherModuleId,
            "en_US",
            "New title",
            "New description",
            $initialAclId
        );
        $this->dispatcher->dispatch(CustomerGroupAclEvents::ACL_UPDATE, $updateEvent);

        $finalAcl = AclQuery::create()->findOneByCode($testAclCode);

        $this->assertNotNull($finalAcl);
        $this->assertEquals($finalAcl->getModuleId(), $anotherModuleId);
        $finalAcl->setLocale("en_US");
        $this->assertEquals($finalAcl->getTitle(), "New title");
        $this->assertEquals($finalAcl->getDescription(), "New description");
    }
}
