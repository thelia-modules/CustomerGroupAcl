<?php

namespace CustomerGroupAcl\Tests\Tools;

use CustomerGroup\Event\AddCustomerToCustomerGroupEvent;
use CustomerGroup\Event\CustomerGroupEvents;
use CustomerGroup\Model\CustomerGroup;
use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Tests\AbstractCustomerGroupAclTest;
use CustomerGroupAcl\Tools\CustomerGroupAclTool;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Customer;

/**
 * Tests for CustomerGroupAclTool.
 */
class CustomerGroupAclToolTest extends AbstractCustomerGroupAclTest
{
    /**
     * The CustomerGroupAclTool under test.
     * @var CustomerGroupAclTool
     */
    protected $aclTool;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function setUp()
    {
        parent::setUp();

        /** @var Request $request */
        $request = $this->container->get("request");
        $this->aclTool = new CustomerGroupAclTool($request);

        $this->loadAclFixtures();
    }

    /**
     * Assert that the group of the currently logged in customer only has or not some accesses to some resources.
     * @param array $expectedRessourceAccesses A map of [expected resource => [expected accesses...], ...].
     * @param boolean $expectedGrant Whether the accesses are expected to be granted or not.
     *
     * @todo Check every possible permutation of ressource(s) and access(es) in the expected set ?
     */
    protected function assertGroupAccesses(array $expectedRessourceAccesses, $expectedGrant)
    {
        foreach ($expectedRessourceAccesses as $ressourceCode => $accessesCodes) {
            // assert all accesses at once
            $this->assertEquals(
                $expectedGrant,
                $this->aclTool->checkAcl($ressourceCode, $accessesCodes)
            );

            // as well as individually
            foreach ($accessesCodes as $accessCode) {
                $this->assertEquals(
                    $expectedGrant,
                    $this->aclTool->checkAcl($ressourceCode, $accessCode)
                );
            }
        }
    }

    /**
     * Assert that the group of the currently logged in customer only has some accesses to some resources
     * (ACLs), and not any other.
     * @param array $expectedRessourceAccesses A map of [expected resource => [expected accesses...], ...].
     */
    protected function assertGroupOnlyHasTheseAccesses(array $expectedRessourceAccesses)
    {
        $unexpectedRessourceAccesses = [];
        foreach (self::$TEST_ACL_CODES as $aclCode) {
            foreach (CustomerGroupAclAccessManager::getAccessPows() as $accessCode => $accessCodeValue) {
                if (!isset($expectedRessourceAccesses[$aclCode])
                    || !in_array($accessCode, $expectedRessourceAccesses[$aclCode])
                ) {
                    $unexpectedRessourceAccesses[$aclCode][] = $accessCode;
                }
            }
        }

        $this->assertGroupAccesses($expectedRessourceAccesses, true);
        $this->assertGroupAccesses($unexpectedRessourceAccesses, false);
    }

    /**
     * @covers CustomerGroupAclTool::checkAcl()
     */
    public function testCheckAclNoCustomer()
    {
        // assert that no accesses are granted
        $this->assertGroupOnlyHasTheseAccesses([]);
        // assert it twice to also test the ACL checking cache
        $this->assertGroupOnlyHasTheseAccesses([]);
    }

    /**
     * @covers CustomerGroupAclTool::checkAcl()
     */
    public function testCheckAclCustomerNoGroup()
    {
        /** @var Customer $customer */
        $customer = self::$testCustomers[0];

        // login the customer
        $this->dispatcher->dispatch(
            TheliaEvents::CUSTOMER_LOGIN,
            new CustomerLoginEvent($customer)
        );

        // assert that ne accesses are granted
        $this->assertGroupOnlyHasTheseAccesses([]);
        // assert it twice to also test the ACL checking cache
        $this->assertGroupOnlyHasTheseAccesses([]);
    }

    /**
     * @covers CustomerGroupAclTool::checkAcl()
     *
     * @param CustomerGroup $customerGroup Group to test.
     * @param Customer $customer Customer to use for the test.
     * @param array $expectedRessourceAccesses A map of [expected resource => [expected accesses...], ...].
     */
    protected function doTestCheckAclCustomerWithGroup(
        CustomerGroup $customerGroup,
        Customer $customer,
        $expectedRessourceAccesses
    ) {
        // add the customer to the test group
        $addCustomerToGroupEvent = new AddCustomerToCustomerGroupEvent();
        $addCustomerToGroupEvent
            ->setCustomerId($customer->getId())
            ->setCustomerGroupId($customerGroup->getId());
        $this->dispatcher->dispatch(
            CustomerGroupEvents::ADD_CUSTOMER_TO_CUSTOMER_GROUP,
            $addCustomerToGroupEvent
        );

        // login the customer
        $this->dispatcher->dispatch(
            TheliaEvents::CUSTOMER_LOGIN,
            new CustomerLoginEvent($customer)
        );

        // assert its accesses
        $this->assertGroupOnlyHasTheseAccesses($expectedRessourceAccesses);
        // assert it twice to also test the ACL checking cache
        $this->assertGroupOnlyHasTheseAccesses($expectedRessourceAccesses);
    }

    /**
     * @covers CustomerGroupAclTool::checkAcl()
     */
    public function testCheckAclCustomerWithFirstGroup()
    {
        /** @var CustomerGroup $firstGroup */
        $firstGroup = self::$testCustomerGroups[0];

        $this->doTestCheckAclCustomerWithGroup(
            $firstGroup,
            self::$testCustomers[0],
            self::$expectedAclFixturesAccesses[$firstGroup->getCode()]
        );
    }

    /**
     * @covers CustomerGroupAclTool::checkAcl()
     */
    public function testCheckAclCustomerWithSecondGroup()
    {
        /** @var CustomerGroup $secondGroup */
        $secondGroup = self::$testCustomerGroups[1];

        $this->doTestCheckAclCustomerWithGroup(
            $secondGroup,
            self::$testCustomers[1],
            self::$expectedAclFixturesAccesses[$secondGroup->getCode()]
        );
    }
}
