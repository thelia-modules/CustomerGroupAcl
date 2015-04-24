<?php

namespace CustomerGroupAcl\Tests\Loop;

use CustomerGroupAcl\Loop\CustomerGroupAclLoop;
use CustomerGroupAcl\Model\CustomerGroupAcl;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;
use Propel\Runtime\Util\PropelModelPager;

/**
 * Tests for the CustomerGroupAclLoop.
 */
class CustomerGroupAclLoopTest extends AbstractCustomerGroupAclTest
{
    /**
     * The customer group ACL loop under test.
     * @var CustomerGroupAclLoop
     */
    protected $loop;

    /**
     * Test arguments.
     * @var array
     */
    protected $testArguments = [];

    public function setUp()
    {
        parent::setUp();

        $this->loadAclFixtures();

        $this->loop = new CustomerGroupAclLoop($this->container);

        /** @var CustomerGroupAcl $testGroupAcl */
        $testGroupAcl = $this->testCustomerGroupAcls[0];
        $this->testArguments = [
            "acl" => $testGroupAcl->getAclId(),
            "customer_group" => $testGroupAcl->getCustomerGroupId(),
            "acl_type" => $testGroupAcl->getType(),
            "activate" => $testGroupAcl->getActivate(),
        ];
    }

    /**
     * @covers CustomerGroupAclLoop::initializeArgs()
     */
    public function testHasNoMandatoryArguments()
    {
        $this->loop->initializeArgs([]);
    }

    /**
     * @covers CustomerGroupAclLoop::initializeArgs()
     */
    public function testAcceptsAllArguments()
    {
        $this->loop->initializeArgs($this->testArguments);
    }

    /**
     * @covers CustomerGroupAclLoop::buildModelCriteria()
     * @covers CustomerGroupAclLoop::exec()
     * @covers CustomerGroupAclLoop::parseResults()
     */
    public function testHasExpectedOutput()
    {
        /** @var CustomerGroupAcl $testGroupAcl */
        $testGroupAcl = $this->testCustomerGroupAcls[0];
        $this->loop->initializeArgs($this->testArguments);

        $loopResult = $this->loop->exec(
            new PropelModelPager($this->loop->buildModelCriteria())
        );

        $this->assertEquals(1, $loopResult->getCount());

        $loopResult->rewind();
        $loopResultRow = $loopResult->current();
        $this->assertEquals($testGroupAcl->getAclId(), $loopResultRow->get("ACL_ID"));
        $this->assertEquals($testGroupAcl->getCustomerGroupId(), $loopResultRow->get("CUSTOMER_GROUP_ID"));
        $this->assertEquals($testGroupAcl->getActivate(), $loopResultRow->get("ACTIVATE"));
        $this->assertEquals($testGroupAcl->getType(), $loopResultRow->get("TYPE"));
    }
}
