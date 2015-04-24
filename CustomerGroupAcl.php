<?php

namespace CustomerGroupAcl;

use CustomerGroupAcl\ACL\AclXmlFileloader;
use CustomerGroupAcl\Model\AclQuery;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;

class CustomerGroupAcl extends BaseModule
{
    const DOMAIN_MESSAGE = "customergroupacl";

    public function preActivation(ConnectionInterface $con = null)
    {
        try {
            // Try find Acl DB Model
            AclQuery::create()->findOne();
            CustomerGroupAclQuery::create()->findOne();
        } catch (\Exception $e) {
            $database = new Database($con);
            $database->insertSql(null, [__DIR__ . DS . 'Config' . DS . 'thelia.sql']);
        }

        return true;
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        $aclXmlFileloader = new AclXmlFileloader(Translator::getInstance());

        $modules = ModuleQuery::create()->findByActivate(BaseModule::IS_ACTIVATED);
        /** @var Module $module */
        foreach ($modules as $module) {
            $aclXmlFileloader->load($module);
        }
    }

    /**
     * @return Module This module.
     */
    public static function getModule()
    {
        return ModuleQuery::create()->findOneByCode(static::getModuleCode());
    }
}
