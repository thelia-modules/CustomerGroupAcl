<?php

namespace CustomerGroupAcl\Loop;

use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\AclQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\AlphaNumStringListType;
use Thelia\Type\TypeCollection;

/**
 * Loop on ACLs.
 */
class AclLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createAnyTypeArgument('module'),
            new Argument(
                'code',
                new TypeCollection(
                    new AlphaNumStringListType()
                )
            ),
            Argument::createEnumListTypeArgument(
                'order',
                [
                    'id',
                    'module',
                    'module_reverse',
                ],
                'id'
            )
        );
    }

    public function buildModelCriteria()
    {
        $search = new AclQuery();

        /* manage translations */
        $this->configureI18nProcessing(
            $search,
            [
                'TITLE',
                'DESCRIPTION',
            ]
        );

        $ids = $this->getArgValue('id');
        if ($ids !== null) {
            $search->filterById($ids, Criteria::IN);
        }

        $modules = $this->getArgValue('module');
        if ($modules !== null) {
            $search->filterByModuleId($modules, Criteria::IN);
        }

        $codes = $this->getArgValue('code');
        if ($codes !== null) {
            $search->filterByCode($codes, Criteria::IN);
        }

        $orders = $this->getArgValue('order');
        foreach ($orders as $order) {
            switch ($order) {
                case 'module':
                    $search->orderByModuleId(Criteria::ASC);
                    break;
                case 'module_reverse':
                    $search->orderByModuleId(Criteria::DESC);
                    break;
                case 'id':
                default:
                    $search->orderById(Criteria::ASC);
                    break;
            }
        }

        return $search;
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var Acl $acl */
        foreach ($loopResult->getResultDataCollection() as $acl) {
            $row = new LoopResultRow($acl);
            $row
                ->set('ACL_ID', $acl->getId())
                ->set('MODULE_ID', $acl->getModuleId())
                ->set('CODE', $acl->getCode())
                ->set('TITLE', $acl->getVirtualColumn('i18n_TITLE'))
                ->set('DESCRIPTION', $acl->getVirtualColumn('i18n_DESCRIPTION'));

            $loopResult->addRow($row);
        }

        return $loopResult;
    }
}
