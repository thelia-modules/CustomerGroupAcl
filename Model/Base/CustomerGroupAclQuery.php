<?php

namespace CustomerGroupAcl\Model\Base;

use \Exception;
use \PDO;
use CustomerGroupAcl\Model\CustomerGroupAcl as ChildCustomerGroupAcl;
use CustomerGroupAcl\Model\CustomerGroupAclQuery as ChildCustomerGroupAclQuery;
use CustomerGroupAcl\Model\Map\CustomerGroupAclTableMap;
use CustomerGroup\Model\CustomerGroup;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'customer_group_acl' table.
 *
 *
 *
 * @method     ChildCustomerGroupAclQuery orderByAclId($order = Criteria::ASC) Order by the acl_id column
 * @method     ChildCustomerGroupAclQuery orderByCustomerGroupId($order = Criteria::ASC) Order by the customer_group_id column
 * @method     ChildCustomerGroupAclQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildCustomerGroupAclQuery orderByActivate($order = Criteria::ASC) Order by the activate column
 * @method     ChildCustomerGroupAclQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCustomerGroupAclQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCustomerGroupAclQuery groupByAclId() Group by the acl_id column
 * @method     ChildCustomerGroupAclQuery groupByCustomerGroupId() Group by the customer_group_id column
 * @method     ChildCustomerGroupAclQuery groupByType() Group by the type column
 * @method     ChildCustomerGroupAclQuery groupByActivate() Group by the activate column
 * @method     ChildCustomerGroupAclQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCustomerGroupAclQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCustomerGroupAclQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCustomerGroupAclQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCustomerGroupAclQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCustomerGroupAclQuery leftJoinAcl($relationAlias = null) Adds a LEFT JOIN clause to the query using the Acl relation
 * @method     ChildCustomerGroupAclQuery rightJoinAcl($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Acl relation
 * @method     ChildCustomerGroupAclQuery innerJoinAcl($relationAlias = null) Adds a INNER JOIN clause to the query using the Acl relation
 *
 * @method     ChildCustomerGroupAclQuery leftJoinCustomerGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerGroup relation
 * @method     ChildCustomerGroupAclQuery rightJoinCustomerGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerGroup relation
 * @method     ChildCustomerGroupAclQuery innerJoinCustomerGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerGroup relation
 *
 * @method     ChildCustomerGroupAcl findOne(ConnectionInterface $con = null) Return the first ChildCustomerGroupAcl matching the query
 * @method     ChildCustomerGroupAcl findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCustomerGroupAcl matching the query, or a new ChildCustomerGroupAcl object populated from the query conditions when no match is found
 *
 * @method     ChildCustomerGroupAcl findOneByAclId(int $acl_id) Return the first ChildCustomerGroupAcl filtered by the acl_id column
 * @method     ChildCustomerGroupAcl findOneByCustomerGroupId(int $customer_group_id) Return the first ChildCustomerGroupAcl filtered by the customer_group_id column
 * @method     ChildCustomerGroupAcl findOneByType(int $type) Return the first ChildCustomerGroupAcl filtered by the type column
 * @method     ChildCustomerGroupAcl findOneByActivate(int $activate) Return the first ChildCustomerGroupAcl filtered by the activate column
 * @method     ChildCustomerGroupAcl findOneByCreatedAt(string $created_at) Return the first ChildCustomerGroupAcl filtered by the created_at column
 * @method     ChildCustomerGroupAcl findOneByUpdatedAt(string $updated_at) Return the first ChildCustomerGroupAcl filtered by the updated_at column
 *
 * @method     array findByAclId(int $acl_id) Return ChildCustomerGroupAcl objects filtered by the acl_id column
 * @method     array findByCustomerGroupId(int $customer_group_id) Return ChildCustomerGroupAcl objects filtered by the customer_group_id column
 * @method     array findByType(int $type) Return ChildCustomerGroupAcl objects filtered by the type column
 * @method     array findByActivate(int $activate) Return ChildCustomerGroupAcl objects filtered by the activate column
 * @method     array findByCreatedAt(string $created_at) Return ChildCustomerGroupAcl objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildCustomerGroupAcl objects filtered by the updated_at column
 *
 */
abstract class CustomerGroupAclQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerGroupAcl\Model\Base\CustomerGroupAclQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerGroupAcl\\Model\\CustomerGroupAcl', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCustomerGroupAclQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCustomerGroupAclQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerGroupAcl\Model\CustomerGroupAclQuery) {
            return $criteria;
        }
        $query = new \CustomerGroupAcl\Model\CustomerGroupAclQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34, 56), $con);
     * </code>
     *
     * @param array[$acl_id, $customer_group_id, $type] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCustomerGroupAcl|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CustomerGroupAclTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CustomerGroupAclTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildCustomerGroupAcl A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ACL_ID, CUSTOMER_GROUP_ID, TYPE, ACTIVATE, CREATED_AT, UPDATED_AT FROM customer_group_acl WHERE ACL_ID = :p0 AND CUSTOMER_GROUP_ID = :p1 AND TYPE = :p2';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->bindValue(':p2', $key[2], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildCustomerGroupAcl();
            $obj->hydrate($row);
            CustomerGroupAclTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildCustomerGroupAcl|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CustomerGroupAclTableMap::ACL_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(CustomerGroupAclTableMap::TYPE, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CustomerGroupAclTableMap::ACL_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(CustomerGroupAclTableMap::TYPE, $key[2], Criteria::EQUAL);
            $cton0->addAnd($cton2);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the acl_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAclId(1234); // WHERE acl_id = 1234
     * $query->filterByAclId(array(12, 34)); // WHERE acl_id IN (12, 34)
     * $query->filterByAclId(array('min' => 12)); // WHERE acl_id > 12
     * </code>
     *
     * @see       filterByAcl()
     *
     * @param     mixed $aclId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByAclId($aclId = null, $comparison = null)
    {
        if (is_array($aclId)) {
            $useMinMax = false;
            if (isset($aclId['min'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::ACL_ID, $aclId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($aclId['max'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::ACL_ID, $aclId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerGroupAclTableMap::ACL_ID, $aclId, $comparison);
    }

    /**
     * Filter the query on the customer_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerGroupId(1234); // WHERE customer_group_id = 1234
     * $query->filterByCustomerGroupId(array(12, 34)); // WHERE customer_group_id IN (12, 34)
     * $query->filterByCustomerGroupId(array('min' => 12)); // WHERE customer_group_id > 12
     * </code>
     *
     * @see       filterByCustomerGroup()
     *
     * @param     mixed $customerGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByCustomerGroupId($customerGroupId = null, $comparison = null)
    {
        if (is_array($customerGroupId)) {
            $useMinMax = false;
            if (isset($customerGroupId['min'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $customerGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerGroupId['max'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $customerGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $customerGroupId, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType(1234); // WHERE type = 1234
     * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
     * $query->filterByType(array('min' => 12)); // WHERE type > 12
     * </code>
     *
     * @param     mixed $type The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerGroupAclTableMap::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the activate column
     *
     * Example usage:
     * <code>
     * $query->filterByActivate(1234); // WHERE activate = 1234
     * $query->filterByActivate(array(12, 34)); // WHERE activate IN (12, 34)
     * $query->filterByActivate(array('min' => 12)); // WHERE activate > 12
     * </code>
     *
     * @param     mixed $activate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByActivate($activate = null, $comparison = null)
    {
        if (is_array($activate)) {
            $useMinMax = false;
            if (isset($activate['min'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::ACTIVATE, $activate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($activate['max'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::ACTIVATE, $activate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerGroupAclTableMap::ACTIVATE, $activate, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerGroupAclTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CustomerGroupAclTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerGroupAclTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \CustomerGroupAcl\Model\Acl object
     *
     * @param \CustomerGroupAcl\Model\Acl|ObjectCollection $acl The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByAcl($acl, $comparison = null)
    {
        if ($acl instanceof \CustomerGroupAcl\Model\Acl) {
            return $this
                ->addUsingAlias(CustomerGroupAclTableMap::ACL_ID, $acl->getId(), $comparison);
        } elseif ($acl instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerGroupAclTableMap::ACL_ID, $acl->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAcl() only accepts arguments of type \CustomerGroupAcl\Model\Acl or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Acl relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function joinAcl($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Acl');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Acl');
        }

        return $this;
    }

    /**
     * Use the Acl relation Acl object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerGroupAcl\Model\AclQuery A secondary query class using the current class as primary query
     */
    public function useAclQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAcl($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Acl', '\CustomerGroupAcl\Model\AclQuery');
    }

    /**
     * Filter the query by a related \CustomerGroup\Model\CustomerGroup object
     *
     * @param \CustomerGroup\Model\CustomerGroup|ObjectCollection $customerGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function filterByCustomerGroup($customerGroup, $comparison = null)
    {
        if ($customerGroup instanceof \CustomerGroup\Model\CustomerGroup) {
            return $this
                ->addUsingAlias(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $customerGroup->getId(), $comparison);
        } elseif ($customerGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID, $customerGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCustomerGroup() only accepts arguments of type \CustomerGroup\Model\CustomerGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CustomerGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function joinCustomerGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CustomerGroup');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CustomerGroup');
        }

        return $this;
    }

    /**
     * Use the CustomerGroup relation CustomerGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerGroup\Model\CustomerGroupQuery A secondary query class using the current class as primary query
     */
    public function useCustomerGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomerGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CustomerGroup', '\CustomerGroup\Model\CustomerGroupQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCustomerGroupAcl $customerGroupAcl Object to remove from the list of results
     *
     * @return ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function prune($customerGroupAcl = null)
    {
        if ($customerGroupAcl) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CustomerGroupAclTableMap::ACL_ID), $customerGroupAcl->getAclId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CustomerGroupAclTableMap::CUSTOMER_GROUP_ID), $customerGroupAcl->getCustomerGroupId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(CustomerGroupAclTableMap::TYPE), $customerGroupAcl->getType(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the customer_group_acl table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerGroupAclTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CustomerGroupAclTableMap::clearInstancePool();
            CustomerGroupAclTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCustomerGroupAcl or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCustomerGroupAcl object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerGroupAclTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CustomerGroupAclTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CustomerGroupAclTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CustomerGroupAclTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CustomerGroupAclTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CustomerGroupAclTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CustomerGroupAclTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CustomerGroupAclTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CustomerGroupAclTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildCustomerGroupAclQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CustomerGroupAclTableMap::CREATED_AT);
    }

} // CustomerGroupAclQuery
