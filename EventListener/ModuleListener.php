<?php

namespace CustomerGroupAcl\EventListener;

use CustomerGroupAcl\ACL\AclXmlFileloader;
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
     * @var AclXmlFileloader
     */
    protected $aclXmlFileloader;

    public function __construct(AclXmlFileloader $aclXmlFileloader)
    {
        $this->aclXmlFileloader = $aclXmlFileloader;
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::MODULE_TOGGLE_ACTIVATION => ['manageAcl', 160]
        ];
    }

    public function manageAcl(ModuleToggleActivationEvent $event)
    {
        if (null === $module = ModuleQuery::create()->findPk($event->getModuleId())) {
            return;
        }

        //In case of deactivation do nothing
        if ($module->getActivate() == BaseModule::IS_ACTIVATED) {
            return;
        }

        //In case of activation update acls
        $this->aclXmlFileloader->load($module);
    }
}
