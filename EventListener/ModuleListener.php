<?php

namespace CustomerGroupAcl\EventListener;

use CustomerGroupAcl\ACL\AclXmlFileLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Module\ModuleToggleActivationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;

/**
 * Listen to other module loading events to load the ACL configuration.
 */
class ModuleListener implements EventSubscriberInterface
{
    /**
     * ACL configuration file loader.
     */
    protected AclXmlFileLoader $aclXmlFileLoader;

    public function __construct(AclXmlFileLoader $aclXmlFileLoader)
    {
        $this->aclXmlFileLoader = $aclXmlFileLoader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TheliaEvents::MODULE_TOGGLE_ACTIVATION => ['manageAcl', 160]
        ];
    }

    /**
     * @throws \Exception
     */
    public function manageAcl(ModuleToggleActivationEvent $event): void
    {
        if (null === $module = ModuleQuery::create()->findPk($event->getModuleId())) {
            return;
        }

        //In case of deactivation, do nothing
        if ($module->getActivate() == BaseModule::IS_ACTIVATED) {
            return;
        }

        //In case of activation update acls
        $this->aclXmlFileLoader->load($module);
    }
}
