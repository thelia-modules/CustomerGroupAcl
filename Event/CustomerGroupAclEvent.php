<?php

namespace CustomerGroupAcl\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Event object to dispatch to act on customer group ACLs.
 */
class CustomerGroupAclEvent extends ActionEvent
{
    protected int $acl_id;
    protected int $customer_group_id;
    protected int $type;
    protected bool $activate;

    public function __construct(int $acl_id, int $customer_group_id, int $type)
    {
        $this->acl_id = $acl_id;
        $this->customer_group_id = $customer_group_id;
        $this->type = $type;
    }

    /**
     * @param int $acl_id
     */
    public function setAclId(int $acl_id): void
    {
        $this->acl_id = $acl_id;
    }

    /**
     * @return int
     */
    public function getAclId(): int
    {
        return $this->acl_id;
    }

    /**
     * @param boolean $activate
     */
    public function setActivate(bool $activate): void
    {
        $this->activate = $activate;
    }

    /**
     * @return boolean
     */
    public function getActivate(): bool
    {
        return $this->activate;
    }

    /**
     * @param int $customer_group_id
     */
    public function setCustomerGroupId(int $customer_group_id): void
    {
        $this->customer_group_id = $customer_group_id;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId(): int
    {
        return $this->customer_group_id;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}
