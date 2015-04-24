<?php

namespace CustomerGroupAcl\Tests\EventListener;

use CustomerGroupAcl\ACL\AclXmlFileloader;
use CustomerGroupAcl\CustomerGroupAcl;
use CustomerGroupAcl\EventListener\ModuleListener;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;
use Thelia\Core\Event\Module\ModuleToggleActivationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ModuleQuery;

/**
 * Tests for ModuleListener.
 */
class ModuleListenerTest extends AbstractCustomerGroupAclTest
{
    /**
     * Mock XML file loader.
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclXmlFileloader;

    public function setUp()
    {
        parent::setUp();

        // get a mock XML file loader
        $this->aclXmlFileloader = $this
            ->getMockBuilder(AclXmlFileloader::class)
            ->setConstructorArgs([
                new Translator($this->container)
            ])
            ->setMethods([
                "load",
            ])
            ->getMock();

        // register the ModuleListener under test
        $this->dispatcher->addSubscriber(new ModuleListener($this->aclXmlFileloader));
    }

    /**
     * @covers ModuleListener::load()
     */
    public function testModuleConfigurationIsLoadedOnActivation()
    {
        // use this module for testing
        $testModule = ModuleQuery::create()->findOneByCode(CustomerGroupAcl::getModuleCode());
        // deactivate it
        $testModule->reload();
        $testModule->setActivate(false)->save();

        // we expect the ACL configuration for our module to be loaded
        $this->aclXmlFileloader
            ->expects($this->once())
            ->method("load")
            ->with($this->equalTo($testModule));

        // toggle the module
        $activationEvent = new ModuleToggleActivationEvent($testModule->getId());
        $this->dispatcher->dispatch(TheliaEvents::MODULE_TOGGLE_ACTIVATION, $activationEvent);
    }

    /**
     * @covers ModuleListener::load()
     */
    public function testModuleConfigurationIsNotLoadedOnDeactivation()
    {
        // use this module for testing
        $testModule = ModuleQuery::create()->findOneByCode(CustomerGroupAcl::getModuleCode());
        // activate it
        $testModule->reload();
        $testModule->setActivate(true)->save();

        // we expect the ACL configuration for our module to NOT be loaded
        $this->aclXmlFileloader
            ->expects($this->never())
            ->method("load")
            ->with($this->equalTo($testModule));

        // toggle the module
        $activationEvent = new ModuleToggleActivationEvent($testModule->getId());
        $this->dispatcher->dispatch(TheliaEvents::MODULE_TOGGLE_ACTIVATION, $activationEvent);
    }
}
