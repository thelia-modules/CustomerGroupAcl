<?php

namespace CustomerGroupAcl\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Event object to dispatch to act on customer group ACLs.
 */
class CustomerGroupAclEvent extends ActionEvent
{
    /** @var int */
    protected $acl_id;
    /** @var int */
    protected $customer_group_id;
    /** @var int */
    protected $type;
    /** @var boolean */
    protected $activate;

    public function __construct($acl_id, $customer_group_id, $type)
    {
        $this->acl_id = $acl_id;
        $this->customer_group_id = $customer_group_id;
        $this->type = $type;
    }

    /**
     * @param int $acl_id
     */
    public function setAclId($acl_id)
    {
        $this->acl_id = $acl_id;
    }

    /**
     * @return int
     */
    public function getAclId()
    {
        return $this->acl_id;
    }

    /**
     * @param boolean $activate
     */
    public function setActivate($activate)
    {
        $this->activate = $activate;
    }

    /**
     * @return boolean
     */
    public function getActivate()
    {
        return $this->activate;
    }

    /**
     * @param int $customer_group_id
     */
    public function setCustomerGroupId($customer_group_id)
    {
        $this->customer_group_id = $customer_group_id;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->customer_group_id;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
}
