<?php

namespace CustomerGroupAcl\EventListener;

use CustomerGroupAcl\Event\CustomerGroupAclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\Model\CustomerGroupAcl;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener for customer group ACLs related events.
 */
class CustomerGroupAclListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
              CustomerGroupAclEvents::CUSTOMER_GROUP_ACL_UPDATE => ["customerGroupAclUpdate", 128],
        ];
    }

    /**
     * Create or toggle a customer group ACL.
     *
     * @param CustomerGroupAclEvent $event Customer group ACL event.
     *
     * @throws PropelException
     *
     * @todo Clarify what this should be doing.
     */
    public function customerGroupAclUpdate(CustomerGroupAclEvent $event): void
    {
        $customerGroupAcl = CustomerGroupAclQuery::create()
            ->filterByAclId($event->getAclId())
            ->filterByCustomerGroupId($event->getCustomerGroupId())
            ->filterByType($event->getType())
            ->findOne();

        if (null === $customerGroupAcl) {
            $customerGroupAcl = new CustomerGroupAcl();
            $customerGroupAcl
                ->setAclId($event->getAclId())
                ->setCustomerGroupId($event->getCustomerGroupId())
                ->setType($event->getType())
                ->setActivate(1)
                ->save()
            ;
        } else {
            if ($customerGroupAcl->getActivate() == 1) {
                $customerGroupAcl->setActivate(0);
            } else {
                $customerGroupAcl->setActivate(1);
            }

            $customerGroupAcl->save();
        }
    }
}
