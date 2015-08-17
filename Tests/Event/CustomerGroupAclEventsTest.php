<?php

namespace CustomerGroupAcl\Tests\Event;

use CustomerGroup\Tests\Event\CustomerGroupEventsTest;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;

/**
 * Tests for CustomerGroupAclEvents.
 */
class CustomerGroupAclEventsTest extends CustomerGroupEventsTest
{
    const MODULE_EVENTS_CLASS = 'CustomerGroupAcl\Event\CustomerGroupAclEvents';

    /**
     * @covers CustomerGroupAclEvents
     */
    public function testDefinesAllModuleEvents()
    {
        $this->assertClassHasConstant("ACL_UPDATE", static::MODULE_EVENTS_CLASS);
        $this->assertClassHasConstant("CUSTOMER_GROUP_ACL_UPDATE", static::MODULE_EVENTS_CLASS);
    }
}
