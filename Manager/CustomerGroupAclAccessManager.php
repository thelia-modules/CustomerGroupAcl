<?php

namespace CustomerGroupAcl\Manager;

use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\AclQuery;
use Thelia\Core\Security\AccessManager;

/**
 * Extension of the Thelia access manager.
 * Exposes the access types.
 * Provides methods on ACLs.
 */
class CustomerGroupAclAccessManager extends AccessManager
{
    /**
     * Get an ACL by its code.
     * @param string $code ACL code.
     * @return Acl
     */
    public static function getAclByCode($code)
    {
        return AclQuery::create()->findOneByCode($code);
    }

    /**
     * Get the code for an access type
     * @param string $key The access type.
     * @return int Numeric code for the type.
     */
    public static function getAccessPowsValue($key)
    {
        if (array_key_exists($key, self::$accessPows)) {
            return self::$accessPows[$key];
        }

        return null;
    }

    /**
     * @return array A map of the possible ACL access type => access type numeric code.
     */
    public static function getAccessPows()
    {
        return self::$accessPows;
    }
}
