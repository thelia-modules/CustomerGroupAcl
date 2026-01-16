<?php

namespace CustomerGroupAcl\Event;

/**
 * Events for the CustomerGroupAcl module.
 */
class CustomerGroupAclEvents
{
    const string ACL_UPDATE = "action.admin.acl.update";
    const string CUSTOMER_GROUP_ACL_UPDATE = "action.admin.customer.group.acl.update";
    const string CHECK_ACL = "action.check.acl";
}
