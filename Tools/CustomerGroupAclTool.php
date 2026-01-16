<?php

namespace CustomerGroupAcl\Tools;

use CustomerGroup\CustomerGroup;
use CustomerGroupAcl\Event\CheckAclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Model\Base\AclQuery;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Exception;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;

/**
 * Tools for group ACL checking.
 *
 * @author Benjamin Perche <bperche@openstudio.fr>
 * @author Jérôme BILLIRAS <jbilliras@openstudio.fr>
 */
class CustomerGroupAclTool
{

    /**
     * Cache for ACL checking results: parameters hash => result.
     * @var array
     */
    protected array $runtimeCache = [];

    /**
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(protected Request $request, protected EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * Check if the current user is granted access to a ressource.
     * Also performs runtime cache management.
     *
     * @param array|string $resources     Resource name or resources list.
     * @param array|string $accesses      Access name or accesses list.
     * @param boolean $accessOr      Whether to return true if at least one resource/access couple is granted.
     * @param int|null $entityId
     *
     * @return bool Whether access is granted.
     * @throws Exception
     */
    public function checkAcl(array|string $resources, array|string $accesses, bool $accessOr = false, int $entityId = null): bool
    {
        if (!is_array($resources)) {
            $resources = (array)$resources;
        }

        if (!is_array($accesses)) {
            $accesses = (array)$accesses;
        }
        sort($accesses);

        $runtimeCacheKey = md5(json_encode([$resources, $accesses, $accessOr]));

        if (!array_key_exists($runtimeCacheKey, $this->runtimeCache)) {
            $this->runtimeCache[$runtimeCacheKey] = $this->performCheck($resources, $accesses, $accessOr);
        }

        if( !$this->runtimeCache[$runtimeCacheKey] ) return false;

        if( isset($entityId) && count($resources) > 1 ){
            throw new Exception(
                "Verification of ACLs cannot run over several resources if an entity ID is specified",
                "500"
            );
        }

        $resource = AclQuery::create()->findOneByCode($resources[0]);
        $className = $resource?->getEntityClass();
        $event = new CheckAclEvent();
        $event
            ->setResource($resources[0])
            ->setEntityId($entityId)
            ->setAccesses($accesses)
            ->setAccessesOr($accessOr)
            ->setClassNames($className)
        ;
        $eventName = CustomerGroupAclEvents::CHECK_ACL."_".$resources[0];
        $this->dispatcher->dispatch($event, $eventName);

        return $event->getResult();
    }

    /**
     * Check if the current user is granted access to a resource.
     *
     * @param array|string $resources Resource name or resources list.
     * @param array|string $accesses  Access name or accesses list.
     * @param boolean $accessOr  Whether to return true if at least one resource/access couple is granted.
     *
     * @return boolean Whether access is granted.
     */
    protected function performCheck(array|string $resources, array|string $accesses, bool $accessOr = false): bool
    {
        /** @var Session $session */
        $session = $this->request->getSession();

        if ($session->getCustomerUser() === null || $session->has(CustomerGroup::getModuleCode()) === false) {
            return false;
        }

        $accessIdsList = [];
        foreach ($accesses as $access) {
            $accessIdsList[] = CustomerGroupAclAccessManager::getAccessPowsValue(strtoupper(trim($access)));
        }
        $accessIdsList = array_unique($accessIdsList);

        $groupId = $this->request->getSession()->get(CustomerGroup::getModuleCode())['id'];

        // For each acl be sure that the current customer has the right access
        $query = CustomerGroupAclQuery::create()
            ->filterByActivate(1)
            ->filterByCustomerGroupId($groupId)
            ->filterByType($accessIdsList, Criteria::IN)
            ->useAclQuery()
            ->filterByCode($resources, Criteria::IN)
            ->endUse();

        $rights = $query->count();
        $askedRights = count($resources) * count($accessIdsList);

        return ($accessOr === true && $rights > 0) || ($rights === $askedRights);
    }
}
