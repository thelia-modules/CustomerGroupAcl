<?php

namespace CustomerGroupAcl\Tests\Loop;

use CustomerGroupAcl\Loop\AclLoop;
use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;
use Propel\Runtime\Util\PropelModelPager;

/**
 * Tests for the AclLoop.
 */
class AclLoopTest extends AbstractCustomerGroupAclTest
{
    /**
     * Expected possible values for the order argument.
     * @var array
     */
    protected static $VALID_ORDER = [
        "id",
        "module",
        "module_reverse",
    ];

    /**
     * The ACL loop under test.
     * @var AclLoop
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

        $this->loop = new AclLoop($this->container);

        /** @var Acl $testAcl */
        $testAcl = $this->testAcls[0];
        $this->testArguments = [
            "id" => $testAcl->getId(),
            "module" => $testAcl->getModuleId(),
            "code" => $testAcl->getCode(),
            "order" => "id",
            "lang" => "en_US",
        ];
    }

    /**
     * @covers AclLoop::initializeArgs()
     */
    public function testHasNoMandatoryArguments()
    {
        $this->loop->initializeArgs([]);
    }

    /**
     * @covers AclLoop::initializeArgs()
     */
    public function testAcceptsAllOrderArguments()
    {
        foreach (static::$VALID_ORDER as $order) {
            $this->loop->initializeArgs(["order" => $order]);
        }
    }

    /**
     * @covers AclLoop::initializeArgs()
     */
    public function testAcceptsAllArguments()
    {
        $this->loop->initializeArgs($this->testArguments);
    }

    /**
     * @covers AclLoop::buildModelCriteria()
     * @covers AclLoop::exec()
     * @covers AclLoop::parseResults()
     */
    public function testHasExpectedOutput()
    {
        /** @var Acl $testAcl */
        $testAcl = $this->testAcls[0];
        $testAcl->setLocale($this->testArguments["lang"]);

        $this->loop->initializeArgs($this->testArguments);

        $loopResult = $this->loop->exec(
            new PropelModelPager($this->loop->buildModelCriteria())
        );

        $this->assertEquals(1, $loopResult->getCount());

        $loopResult->rewind();
        $loopResultRow = $loopResult->current();
        $this->assertEquals($testAcl->getId(), $loopResultRow->get("ACL_ID"));
        $this->assertEquals($testAcl->getModuleId(), $loopResultRow->get("MODULE_ID"));
        $this->assertEquals($testAcl->getCode(), $loopResultRow->get("CODE"));
        $this->assertEquals($testAcl->getTitle(), $loopResultRow->get("TITLE"));
        $this->assertEquals($testAcl->getDescription(), $loopResultRow->get("DESCRIPTION"));
    }
}
