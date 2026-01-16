<?php

namespace CustomerGroupAcl;

use CustomerGroupAcl\ACL\AclXmlFileLoader;
use CustomerGroupAcl\Model\AclQuery;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;

class CustomerGroupAcl extends BaseModule
{
    const string DOMAIN_MESSAGE = "customergroupacl";

    /**
     * @throws \Exception
     */
    public function postActivation(ConnectionInterface $con = null): void
    {
        parent::postActivation($con);

        if (!self::getConfigValue('is_initialized',null)){
            $database = new Database($con);
            $database->insertSql(null, [__DIR__ . "/Config/TheliaMain.sql"]);
            self::setConfigValue('is_initialized', 1);
        }

        $aclXmlFileLoader = new AclXmlFileLoader(Translator::getInstance());

        $modules = ModuleQuery::create()->findByActivate(BaseModule::IS_ACTIVATED);
        /** @var Module $module */
        foreach ($modules as $module) {
            $aclXmlFileLoader->load($module);
        }
    }

    /**
     * @return Module This module.
     */
    public static function getModule(): Module
    {
        return ModuleQuery::create()->findOneByCode(static::getModuleCode());
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
