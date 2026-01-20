<?php

namespace CustomerGroupAcl\Event;

/**
 * Events for the CustomerGroupAcl module.
 */
class CustomerGroupAclEvents
{
    const ACL_UPDATE = "action.admin.acl.update";
    const CUSTOMER_GROUP_ACL_UPDATE = "action.admin.customer.group.acl.update";
    const CHECK_ACL = "action.check.acl";
}
