<?php

namespace CustomerGroupAcl\EventListener;

use CustomerGroupAcl\Event\AclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\AclQuery;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener for ACLs related events.
 */
class AclListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CustomerGroupAclEvents::ACL_UPDATE => ["aclUpdate", 128]
        ];
    }

    /**
     * Update (or create) an ACL.
     *
     * @param AclEvent $event ACL event.
     *
     * @throws PropelException
     */
    public function aclUpdate(AclEvent $event)
    {
        // create the ACL if it does not exists
        if (null == $acl = AclQuery::create()->findPk($event->getId())) {
            $acl = new Acl();
        }

        $acl
            ->setCode($event->getCode())
            ->setModuleId($event->getModuleId())
            ->setLocale($event->getLocale())
            ->setTitle($event->getTitle())
            ->setDescription($event->getDescription())
            ->save();
    }
}
