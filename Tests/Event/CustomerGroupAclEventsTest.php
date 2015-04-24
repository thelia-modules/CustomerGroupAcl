<?php

namespace CustomerGroupAcl\Tests\Event;

use CustomerGroup\Tests\Event\CustomerGroupEventsTest;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;

/**
 * Tests for CustomerGroupAclEvents.
 */
class CustomerGroupAclEventsTest extends CustomerGroupEventsTest
{
    /**
     * @covers CustomerGroupAclEvents
     */
    public function testDefinesAllModuleEvents()
    {
        $this->assertClassHasConstant("ACL_UPDATE", CustomerGroupAclEvents::class);
        $this->assertClassHasConstant("CUSTOMER_GROUP_ACL_UPDATE", CustomerGroupAclEvents::class);
    }
}
