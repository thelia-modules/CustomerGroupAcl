<?php

namespace CustomerGroupAcl\Model\Base;

use \Exception;
use \PDO;
use CustomerGroupAcl\Model\Acl as ChildAcl;
use CustomerGroupAcl\Model\AclI18nQuery as ChildAclI18nQuery;
use CustomerGroupAcl\Model\AclQuery as ChildAclQuery;
use CustomerGroupAcl\Model\Map\AclTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Module;

/**
 * Base class that represents a query for the 'acl' table.
 *
 *
 *
 * @method     ChildAclQuery orderByModuleId($order = Criteria::ASC) Order by the module_id column
 * @method     ChildAclQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildAclQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildAclQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildAclQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildAclQuery groupByModuleId() Group by the module_id column
 * @method     ChildAclQuery groupByCode() Group by the code column
 * @method     ChildAclQuery groupById() Group by the id column
 * @method     ChildAclQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildAclQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildAclQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAclQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAclQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAclQuery leftJoinModule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Module relation
 * @method     ChildAclQuery rightJoinModule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Module relation
 * @method     ChildAclQuery innerJoinModule($relationAlias = null) Adds a INNER JOIN clause to the query using the Module relation
 *
 * @method     ChildAclQuery leftJoinCustomerGroupAcl($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerGroupAcl relation
 * @method     ChildAclQuery rightJoinCustomerGroupAcl($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerGroupAcl relation
 * @method     ChildAclQuery innerJoinCustomerGroupAcl($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerGroupAcl relation
 *
 * @method     ChildAclQuery leftJoinAclI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the AclI18n relation
 * @method     ChildAclQuery rightJoinAclI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AclI18n relation
 * @method     ChildAclQuery innerJoinAclI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the AclI18n relation
 *
 * @method     ChildAcl findOne(ConnectionInterface $con = null) Return the first ChildAcl matching the query
 * @method     ChildAcl findOneOrCreate(ConnectionInterface $con = null) Return the first ChildAcl matching the query, or a new ChildAcl object populated from the query conditions when no match is found
 *
 * @method     ChildAcl findOneByModuleId(int $module_id) Return the first ChildAcl filtered by the module_id column
 * @method     ChildAcl findOneByCode(string $code) Return the first ChildAcl filtered by the code column
 * @method     ChildAcl findOneById(int $id) Return the first ChildAcl filtered by the id column
 * @method     ChildAcl findOneByCreatedAt(string $created_at) Return the first ChildAcl filtered by the created_at column
 * @method     ChildAcl findOneByUpdatedAt(string $updated_at) Return the first ChildAcl filtered by the updated_at column
 *
 * @method     array findByModuleId(int $module_id) Return ChildAcl objects filtered by the module_id column
 * @method     array findByCode(string $code) Return ChildAcl objects filtered by the code column
 * @method     array findById(int $id) Return ChildAcl objects filtered by the id column
 * @method     array findByCreatedAt(string $created_at) Return ChildAcl objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildAcl objects filtered by the updated_at column
 *
 */
abstract class AclQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerGroupAcl\Model\Base\AclQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerGroupAcl\\Model\\Acl', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAclQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildAclQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerGroupAcl\Model\AclQuery) {
            return $criteria;
        }
        $query = new \CustomerGroupAcl\Model\AclQuery();
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
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildAcl|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AclTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AclTableMap::DATABASE_NAME);
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
     * @return   ChildAcl A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT MODULE_ID, CODE, ID, CREATED_AT, UPDATED_AT FROM acl WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildAcl();
            $obj->hydrate($row);
            AclTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildAcl|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(12, 56, 832), $con);
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
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AclTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AclTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the module_id column
     *
     * Example usage:
     * <code>
     * $query->filterByModuleId(1234); // WHERE module_id = 1234
     * $query->filterByModuleId(array(12, 34)); // WHERE module_id IN (12, 34)
     * $query->filterByModuleId(array('min' => 12)); // WHERE module_id > 12
     * </code>
     *
     * @see       filterByModule()
     *
     * @param     mixed $moduleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByModuleId($moduleId = null, $comparison = null)
    {
        if (is_array($moduleId)) {
            $useMinMax = false;
            if (isset($moduleId['min'])) {
                $this->addUsingAlias(AclTableMap::MODULE_ID, $moduleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($moduleId['max'])) {
                $this->addUsingAlias(AclTableMap::MODULE_ID, $moduleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclTableMap::MODULE_ID, $moduleId, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AclTableMap::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(AclTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AclTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclTableMap::ID, $id, $comparison);
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
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(AclTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(AclTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(AclTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(AclTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Thelia\Model\Module object
     *
     * @param \Thelia\Model\Module|ObjectCollection $module The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByModule($module, $comparison = null)
    {
        if ($module instanceof \Thelia\Model\Module) {
            return $this
                ->addUsingAlias(AclTableMap::MODULE_ID, $module->getId(), $comparison);
        } elseif ($module instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AclTableMap::MODULE_ID, $module->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByModule() only accepts arguments of type \Thelia\Model\Module or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Module relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function joinModule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Module');

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
            $this->addJoinObject($join, 'Module');
        }

        return $this;
    }

    /**
     * Use the Module relation Module object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\ModuleQuery A secondary query class using the current class as primary query
     */
    public function useModuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinModule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Module', '\Thelia\Model\ModuleQuery');
    }

    /**
     * Filter the query by a related \CustomerGroupAcl\Model\CustomerGroupAcl object
     *
     * @param \CustomerGroupAcl\Model\CustomerGroupAcl|ObjectCollection $customerGroupAcl  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByCustomerGroupAcl($customerGroupAcl, $comparison = null)
    {
        if ($customerGroupAcl instanceof \CustomerGroupAcl\Model\CustomerGroupAcl) {
            return $this
                ->addUsingAlias(AclTableMap::ID, $customerGroupAcl->getAclId(), $comparison);
        } elseif ($customerGroupAcl instanceof ObjectCollection) {
            return $this
                ->useCustomerGroupAclQuery()
                ->filterByPrimaryKeys($customerGroupAcl->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCustomerGroupAcl() only accepts arguments of type \CustomerGroupAcl\Model\CustomerGroupAcl or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CustomerGroupAcl relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function joinCustomerGroupAcl($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CustomerGroupAcl');

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
            $this->addJoinObject($join, 'CustomerGroupAcl');
        }

        return $this;
    }

    /**
     * Use the CustomerGroupAcl relation CustomerGroupAcl object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerGroupAcl\Model\CustomerGroupAclQuery A secondary query class using the current class as primary query
     */
    public function useCustomerGroupAclQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomerGroupAcl($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CustomerGroupAcl', '\CustomerGroupAcl\Model\CustomerGroupAclQuery');
    }

    /**
     * Filter the query by a related \CustomerGroupAcl\Model\AclI18n object
     *
     * @param \CustomerGroupAcl\Model\AclI18n|ObjectCollection $aclI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function filterByAclI18n($aclI18n, $comparison = null)
    {
        if ($aclI18n instanceof \CustomerGroupAcl\Model\AclI18n) {
            return $this
                ->addUsingAlias(AclTableMap::ID, $aclI18n->getId(), $comparison);
        } elseif ($aclI18n instanceof ObjectCollection) {
            return $this
                ->useAclI18nQuery()
                ->filterByPrimaryKeys($aclI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAclI18n() only accepts arguments of type \CustomerGroupAcl\Model\AclI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AclI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function joinAclI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AclI18n');

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
            $this->addJoinObject($join, 'AclI18n');
        }

        return $this;
    }

    /**
     * Use the AclI18n relation AclI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerGroupAcl\Model\AclI18nQuery A secondary query class using the current class as primary query
     */
    public function useAclI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinAclI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AclI18n', '\CustomerGroupAcl\Model\AclI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildAcl $acl Object to remove from the list of results
     *
     * @return ChildAclQuery The current query, for fluid interface
     */
    public function prune($acl = null)
    {
        if ($acl) {
            $this->addUsingAlias(AclTableMap::ID, $acl->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the acl table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AclTableMap::DATABASE_NAME);
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
            AclTableMap::clearInstancePool();
            AclTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildAcl or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildAcl object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(AclTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AclTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        AclTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            AclTableMap::clearRelatedInstancePool();
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
     * @return     ChildAclQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(AclTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildAclQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(AclTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildAclQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(AclTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildAclQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(AclTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildAclQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(AclTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildAclQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(AclTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAclQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'AclI18n';

        return $this
            ->joinAclI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAclQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('AclI18n');
        $this->with['AclI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAclI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AclI18n', '\CustomerGroupAcl\Model\AclI18nQuery');
    }

} // AclQuery
