<?php

namespace CustomerGroupAcl\Tests\EventListener;

use CustomerGroup\Model\CustomerGroup;
use CustomerGroupAcl\Event\CustomerGroupAclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\EventListener\CustomerGroupAclListener;
use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\CustomerGroupAcl;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;

/**
 * Tests for CustomerGroupAclListener
 */
class CustomerGroupAclListenerTest extends AbstractCustomerGroupAclTest
{
    public function setUp()
    {
        parent::setUp();

        $this->loadAclFixtures();

        // register the CustomerGroupAclListener under test
        $this->dispatcher->addSubscriber(new CustomerGroupAclListener());
    }

    /**
     * @covers CustomerGroupAclListener::customerGroupAclUpdate()
     */
    public function testDeactivateCustomerGroupAcl()
    {
        /** @var CustomerGroupAcl $testGroupAcl */
        $testGroupAcl = $this->testCustomerGroupAcls[0];

        $deactivateEvent = new CustomerGroupAclEvent(
            $testGroupAcl->getAclId(),
            $testGroupAcl->getCustomerGroupId(),
            $testGroupAcl->getType()
        );
        $this->dispatcher->dispatch(CustomerGroupAclEvents::CUSTOMER_GROUP_ACL_UPDATE, $deactivateEvent);

        $testGroupAcl->reload();
        $this->assertEquals(0, $testGroupAcl->getActivate());
    }

    /**
     * @depends testDeactivateCustomerGroupAcl
     * @covers CustomerGroupAclListener::customerGroupAclUpdate()
     */
    public function testActivateCustomerGroupAcl()
    {
        /** @var CustomerGroupAcl $testGroupAcl */
        $testGroupAcl = $this->testCustomerGroupAcls[0];

        $activateEvent = new CustomerGroupAclEvent(
            $testGroupAcl->getAclId(),
            $testGroupAcl->getCustomerGroupId(),
            $testGroupAcl->getType()
        );
        $this->dispatcher->dispatch(CustomerGroupAclEvents::CUSTOMER_GROUP_ACL_UPDATE, $activateEvent);

        $testGroupAcl->reload();
        $this->assertEquals(1, $testGroupAcl->getActivate());
    }

    public function testCreateCustomerGroupAcl()
    {
        /** @var Acl $testAcl */
        $testAcl = $this->testAcls[0];
        /** @var CustomerGroup $testGroup */
        $testGroup = self::$testCustomerGroups[2];
        $testAccessType = array_rand(CustomerGroupAclAccessManager::getAccessPows(), 1);

        $createEvent = new CustomerGroupAclEvent(
            $testAcl->getId(),
            $testGroup->getId(),
            $testAccessType
        );
        $this->dispatcher->dispatch(CustomerGroupAclEvents::CUSTOMER_GROUP_ACL_UPDATE, $createEvent);

        $groupAcl = CustomerGroupAclQuery::create()
            ->filterByAcl($testAcl)
            ->filterByCustomerGroup($testGroup)
            ->filterByType($testAccessType)
            ->findOne();

        $this->assertNotNull($groupAcl);
        $this->assertEquals(1, $groupAcl->getActivate());
    }
}
