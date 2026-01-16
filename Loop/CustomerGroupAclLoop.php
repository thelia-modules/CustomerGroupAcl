<?php

namespace CustomerGroupAcl\Loop;

use CustomerGroupAcl\Model\Base\CustomerGroupAcl;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;

/**
 * Loop on customer group ACLs.
 */
class CustomerGroupAclLoop extends BaseLoop implements PropelSearchLoopInterface
{
    public function parseResults(LoopResult $loopResult): LoopResult
    {
        /** @var CustomerGroupAcl $customerGroupAcl */
        foreach ($loopResult->getResultDataCollection() as $customerGroupAcl) {
            $row = new LoopResultRow($customerGroupAcl);
            $row
                ->set('ACL_ID', $customerGroupAcl->getAclId())
                ->set('CUSTOMER_GROUP_ID', $customerGroupAcl->getCustomerGroupId())
                ->set('ACTIVATE', $customerGroupAcl->getActivate())
                ->set('TYPE', $customerGroupAcl->getType());

            $loopResult->addRow($row);
        }

        return $loopResult;
    }

    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('acl'),
            Argument::createIntListTypeArgument('customer_group'),
            Argument::createIntListTypeArgument('acl_type'),
            Argument::createBooleanOrBothTypeArgument('activate', BooleanOrBothType::ANY)
        );
    }

    public function buildModelCriteria(): CustomerGroupAclQuery
    {
        $search = new CustomerGroupAclQuery();

        $aclIds = $this->getArgValue('acl');
        if ($aclIds !== null) {
            $search->filterByAclId($aclIds, Criteria::IN);
        }

        $customerGroupIds = $this->getArgValue('customer_group');
        if ($customerGroupIds !== null) {
            $search->filterByCustomerGroupId($customerGroupIds, Criteria::IN);
        }

        $types = $this->getArgValue('acl_type');
        if ($types !== null) {
            $search->filterByType($types, Criteria::IN);
        }

        $activate = $this->getArgValue('activate');
        if ($activate !== BooleanOrBothType::ANY) {
            $search->filterByActivate($activate ? 1 : 0, Criteria::EQUAL);
        }

        return $search;
    }
}
