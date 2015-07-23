<?php

namespace CustomerGroupAcl\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use CustomerGroupAcl\Model\Acl as ChildAcl;
use CustomerGroupAcl\Model\AclI18n as ChildAclI18n;
use CustomerGroupAcl\Model\AclI18nQuery as ChildAclI18nQuery;
use CustomerGroupAcl\Model\AclQuery as ChildAclQuery;
use CustomerGroupAcl\Model\CustomerGroupAcl as ChildCustomerGroupAcl;
use CustomerGroupAcl\Model\CustomerGroupAclQuery as ChildCustomerGroupAclQuery;
use CustomerGroupAcl\Model\Map\AclTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use Thelia\Model\Module as ChildModule;
use Thelia\Model\ModuleQuery;

abstract class Acl implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CustomerGroupAcl\\Model\\Map\\AclTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the module_id field.
     * @var        int
     */
    protected $module_id;

    /**
     * The value for the code field.
     * @var        string
     */
    protected $code;

    /**
     * The value for the entity_class field.
     * @var        string
     */
    protected $entity_class;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        Module
     */
    protected $aModule;

    /**
     * @var        ObjectCollection|ChildCustomerGroupAcl[] Collection to store aggregation of ChildCustomerGroupAcl objects.
     */
    protected $collCustomerGroupAcls;
    protected $collCustomerGroupAclsPartial;

    /**
     * @var        ObjectCollection|ChildAclI18n[] Collection to store aggregation of ChildAclI18n objects.
     */
    protected $collAclI18ns;
    protected $collAclI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';

    /**
     * Current translation objects
     * @var        array[ChildAclI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $customerGroupAclsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $aclI18nsScheduledForDeletion = null;

    /**
     * Initializes internal state of CustomerGroupAcl\Model\Base\Acl object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Acl</code> instance.  If
     * <code>obj</code> is an instance of <code>Acl</code>, delegates to
     * <code>equals(Acl)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Acl The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Acl The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [module_id] column value.
     *
     * @return   int
     */
    public function getModuleId()
    {

        return $this->module_id;
    }

    /**
     * Get the [code] column value.
     *
     * @return   string
     */
    public function getCode()
    {

        return $this->code;
    }

    /**
     * Get the [entity_class] column value.
     *
     * @return   string
     */
    public function getEntityClass()
    {

        return $this->entity_class;
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [module_id] column.
     *
     * @param      int $v new value
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function setModuleId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->module_id !== $v) {
            $this->module_id = $v;
            $this->modifiedColumns[AclTableMap::MODULE_ID] = true;
        }

        if ($this->aModule !== null && $this->aModule->getId() !== $v) {
            $this->aModule = null;
        }


        return $this;
    } // setModuleId()

    /**
     * Set the value of [code] column.
     *
     * @param      string $v new value
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function setCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->code !== $v) {
            $this->code = $v;
            $this->modifiedColumns[AclTableMap::CODE] = true;
        }


        return $this;
    } // setCode()

    /**
     * Set the value of [entity_class] column.
     *
     * @param      string $v new value
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function setEntityClass($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->entity_class !== $v) {
            $this->entity_class = $v;
            $this->modifiedColumns[AclTableMap::ENTITY_CLASS] = true;
        }


        return $this;
    } // setEntityClass()

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[AclTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[AclTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[AclTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : AclTableMap::translateFieldName('ModuleId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->module_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : AclTableMap::translateFieldName('Code', TableMap::TYPE_PHPNAME, $indexType)];
            $this->code = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : AclTableMap::translateFieldName('EntityClass', TableMap::TYPE_PHPNAME, $indexType)];
            $this->entity_class = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : AclTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : AclTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : AclTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 6; // 6 = AclTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \CustomerGroupAcl\Model\Acl object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aModule !== null && $this->module_id !== $this->aModule->getId()) {
            $this->aModule = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AclTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildAclQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aModule = null;
            $this->collCustomerGroupAcls = null;

            $this->collAclI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Acl::setDeleted()
     * @see Acl::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(AclTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildAclQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(AclTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(AclTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(AclTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(AclTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                AclTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aModule !== null) {
                if ($this->aModule->isModified() || $this->aModule->isNew()) {
                    $affectedRows += $this->aModule->save($con);
                }
                $this->setModule($this->aModule);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->customerGroupAclsScheduledForDeletion !== null) {
                if (!$this->customerGroupAclsScheduledForDeletion->isEmpty()) {
                    \CustomerGroupAcl\Model\CustomerGroupAclQuery::create()
                        ->filterByPrimaryKeys($this->customerGroupAclsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->customerGroupAclsScheduledForDeletion = null;
                }
            }

                if ($this->collCustomerGroupAcls !== null) {
            foreach ($this->collCustomerGroupAcls as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->aclI18nsScheduledForDeletion !== null) {
                if (!$this->aclI18nsScheduledForDeletion->isEmpty()) {
                    \CustomerGroupAcl\Model\AclI18nQuery::create()
                        ->filterByPrimaryKeys($this->aclI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->aclI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collAclI18ns !== null) {
            foreach ($this->collAclI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[AclTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AclTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AclTableMap::MODULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'MODULE_ID';
        }
        if ($this->isColumnModified(AclTableMap::CODE)) {
            $modifiedColumns[':p' . $index++]  = 'CODE';
        }
        if ($this->isColumnModified(AclTableMap::ENTITY_CLASS)) {
            $modifiedColumns[':p' . $index++]  = 'ENTITY_CLASS';
        }
        if ($this->isColumnModified(AclTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(AclTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(AclTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO acl (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'MODULE_ID':
                        $stmt->bindValue($identifier, $this->module_id, PDO::PARAM_INT);
                        break;
                    case 'CODE':
                        $stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
                        break;
                    case 'ENTITY_CLASS':
                        $stmt->bindValue($identifier, $this->entity_class, PDO::PARAM_STR);
                        break;
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = AclTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getModuleId();
                break;
            case 1:
                return $this->getCode();
                break;
            case 2:
                return $this->getEntityClass();
                break;
            case 3:
                return $this->getId();
                break;
            case 4:
                return $this->getCreatedAt();
                break;
            case 5:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Acl'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Acl'][$this->getPrimaryKey()] = true;
        $keys = AclTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getModuleId(),
            $keys[1] => $this->getCode(),
            $keys[2] => $this->getEntityClass(),
            $keys[3] => $this->getId(),
            $keys[4] => $this->getCreatedAt(),
            $keys[5] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aModule) {
                $result['Module'] = $this->aModule->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCustomerGroupAcls) {
                $result['CustomerGroupAcls'] = $this->collCustomerGroupAcls->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAclI18ns) {
                $result['AclI18ns'] = $this->collAclI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = AclTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setModuleId($value);
                break;
            case 1:
                $this->setCode($value);
                break;
            case 2:
                $this->setEntityClass($value);
                break;
            case 3:
                $this->setId($value);
                break;
            case 4:
                $this->setCreatedAt($value);
                break;
            case 5:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = AclTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setModuleId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCode($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setEntityClass($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCreatedAt($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUpdatedAt($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AclTableMap::DATABASE_NAME);

        if ($this->isColumnModified(AclTableMap::MODULE_ID)) $criteria->add(AclTableMap::MODULE_ID, $this->module_id);
        if ($this->isColumnModified(AclTableMap::CODE)) $criteria->add(AclTableMap::CODE, $this->code);
        if ($this->isColumnModified(AclTableMap::ENTITY_CLASS)) $criteria->add(AclTableMap::ENTITY_CLASS, $this->entity_class);
        if ($this->isColumnModified(AclTableMap::ID)) $criteria->add(AclTableMap::ID, $this->id);
        if ($this->isColumnModified(AclTableMap::CREATED_AT)) $criteria->add(AclTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(AclTableMap::UPDATED_AT)) $criteria->add(AclTableMap::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(AclTableMap::DATABASE_NAME);
        $criteria->add(AclTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \CustomerGroupAcl\Model\Acl (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setModuleId($this->getModuleId());
        $copyObj->setCode($this->getCode());
        $copyObj->setEntityClass($this->getEntityClass());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCustomerGroupAcls() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCustomerGroupAcl($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAclI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAclI18n($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \CustomerGroupAcl\Model\Acl Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildModule object.
     *
     * @param                  ChildModule $v
     * @return                 \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     * @throws PropelException
     */
    public function setModule(ChildModule $v = null)
    {
        if ($v === null) {
            $this->setModuleId(NULL);
        } else {
            $this->setModuleId($v->getId());
        }

        $this->aModule = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildModule object, it will not be re-added.
        if ($v !== null) {
            $v->addAcl($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildModule object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildModule The associated ChildModule object.
     * @throws PropelException
     */
    public function getModule(ConnectionInterface $con = null)
    {
        if ($this->aModule === null && ($this->module_id !== null)) {
            $this->aModule = ModuleQuery::create()->findPk($this->module_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aModule->addAcls($this);
             */
        }

        return $this->aModule;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('CustomerGroupAcl' == $relationName) {
            return $this->initCustomerGroupAcls();
        }
        if ('AclI18n' == $relationName) {
            return $this->initAclI18ns();
        }
    }

    /**
     * Clears out the collCustomerGroupAcls collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCustomerGroupAcls()
     */
    public function clearCustomerGroupAcls()
    {
        $this->collCustomerGroupAcls = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCustomerGroupAcls collection loaded partially.
     */
    public function resetPartialCustomerGroupAcls($v = true)
    {
        $this->collCustomerGroupAclsPartial = $v;
    }

    /**
     * Initializes the collCustomerGroupAcls collection.
     *
     * By default this just sets the collCustomerGroupAcls collection to an empty array (like clearcollCustomerGroupAcls());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCustomerGroupAcls($overrideExisting = true)
    {
        if (null !== $this->collCustomerGroupAcls && !$overrideExisting) {
            return;
        }
        $this->collCustomerGroupAcls = new ObjectCollection();
        $this->collCustomerGroupAcls->setModel('\CustomerGroupAcl\Model\CustomerGroupAcl');
    }

    /**
     * Gets an array of ChildCustomerGroupAcl objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAcl is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCustomerGroupAcl[] List of ChildCustomerGroupAcl objects
     * @throws PropelException
     */
    public function getCustomerGroupAcls($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCustomerGroupAclsPartial && !$this->isNew();
        if (null === $this->collCustomerGroupAcls || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCustomerGroupAcls) {
                // return empty collection
                $this->initCustomerGroupAcls();
            } else {
                $collCustomerGroupAcls = ChildCustomerGroupAclQuery::create(null, $criteria)
                    ->filterByAcl($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCustomerGroupAclsPartial && count($collCustomerGroupAcls)) {
                        $this->initCustomerGroupAcls(false);

                        foreach ($collCustomerGroupAcls as $obj) {
                            if (false == $this->collCustomerGroupAcls->contains($obj)) {
                                $this->collCustomerGroupAcls->append($obj);
                            }
                        }

                        $this->collCustomerGroupAclsPartial = true;
                    }

                    reset($collCustomerGroupAcls);

                    return $collCustomerGroupAcls;
                }

                if ($partial && $this->collCustomerGroupAcls) {
                    foreach ($this->collCustomerGroupAcls as $obj) {
                        if ($obj->isNew()) {
                            $collCustomerGroupAcls[] = $obj;
                        }
                    }
                }

                $this->collCustomerGroupAcls = $collCustomerGroupAcls;
                $this->collCustomerGroupAclsPartial = false;
            }
        }

        return $this->collCustomerGroupAcls;
    }

    /**
     * Sets a collection of CustomerGroupAcl objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $customerGroupAcls A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAcl The current object (for fluent API support)
     */
    public function setCustomerGroupAcls(Collection $customerGroupAcls, ConnectionInterface $con = null)
    {
        $customerGroupAclsToDelete = $this->getCustomerGroupAcls(new Criteria(), $con)->diff($customerGroupAcls);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->customerGroupAclsScheduledForDeletion = clone $customerGroupAclsToDelete;

        foreach ($customerGroupAclsToDelete as $customerGroupAclRemoved) {
            $customerGroupAclRemoved->setAcl(null);
        }

        $this->collCustomerGroupAcls = null;
        foreach ($customerGroupAcls as $customerGroupAcl) {
            $this->addCustomerGroupAcl($customerGroupAcl);
        }

        $this->collCustomerGroupAcls = $customerGroupAcls;
        $this->collCustomerGroupAclsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CustomerGroupAcl objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CustomerGroupAcl objects.
     * @throws PropelException
     */
    public function countCustomerGroupAcls(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCustomerGroupAclsPartial && !$this->isNew();
        if (null === $this->collCustomerGroupAcls || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCustomerGroupAcls) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCustomerGroupAcls());
            }

            $query = ChildCustomerGroupAclQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAcl($this)
                ->count($con);
        }

        return count($this->collCustomerGroupAcls);
    }

    /**
     * Method called to associate a ChildCustomerGroupAcl object to this object
     * through the ChildCustomerGroupAcl foreign key attribute.
     *
     * @param    ChildCustomerGroupAcl $l ChildCustomerGroupAcl
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function addCustomerGroupAcl(ChildCustomerGroupAcl $l)
    {
        if ($this->collCustomerGroupAcls === null) {
            $this->initCustomerGroupAcls();
            $this->collCustomerGroupAclsPartial = true;
        }

        if (!in_array($l, $this->collCustomerGroupAcls->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCustomerGroupAcl($l);
        }

        return $this;
    }

    /**
     * @param CustomerGroupAcl $customerGroupAcl The customerGroupAcl object to add.
     */
    protected function doAddCustomerGroupAcl($customerGroupAcl)
    {
        $this->collCustomerGroupAcls[]= $customerGroupAcl;
        $customerGroupAcl->setAcl($this);
    }

    /**
     * @param  CustomerGroupAcl $customerGroupAcl The customerGroupAcl object to remove.
     * @return ChildAcl The current object (for fluent API support)
     */
    public function removeCustomerGroupAcl($customerGroupAcl)
    {
        if ($this->getCustomerGroupAcls()->contains($customerGroupAcl)) {
            $this->collCustomerGroupAcls->remove($this->collCustomerGroupAcls->search($customerGroupAcl));
            if (null === $this->customerGroupAclsScheduledForDeletion) {
                $this->customerGroupAclsScheduledForDeletion = clone $this->collCustomerGroupAcls;
                $this->customerGroupAclsScheduledForDeletion->clear();
            }
            $this->customerGroupAclsScheduledForDeletion[]= clone $customerGroupAcl;
            $customerGroupAcl->setAcl(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Acl is new, it will return
     * an empty collection; or if this Acl has previously
     * been saved, it will retrieve related CustomerGroupAcls from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Acl.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCustomerGroupAcl[] List of ChildCustomerGroupAcl objects
     */
    public function getCustomerGroupAclsJoinCustomerGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCustomerGroupAclQuery::create(null, $criteria);
        $query->joinWith('CustomerGroup', $joinBehavior);

        return $this->getCustomerGroupAcls($query, $con);
    }

    /**
     * Clears out the collAclI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAclI18ns()
     */
    public function clearAclI18ns()
    {
        $this->collAclI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAclI18ns collection loaded partially.
     */
    public function resetPartialAclI18ns($v = true)
    {
        $this->collAclI18nsPartial = $v;
    }

    /**
     * Initializes the collAclI18ns collection.
     *
     * By default this just sets the collAclI18ns collection to an empty array (like clearcollAclI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAclI18ns($overrideExisting = true)
    {
        if (null !== $this->collAclI18ns && !$overrideExisting) {
            return;
        }
        $this->collAclI18ns = new ObjectCollection();
        $this->collAclI18ns->setModel('\CustomerGroupAcl\Model\AclI18n');
    }

    /**
     * Gets an array of ChildAclI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAcl is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildAclI18n[] List of ChildAclI18n objects
     * @throws PropelException
     */
    public function getAclI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAclI18nsPartial && !$this->isNew();
        if (null === $this->collAclI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAclI18ns) {
                // return empty collection
                $this->initAclI18ns();
            } else {
                $collAclI18ns = ChildAclI18nQuery::create(null, $criteria)
                    ->filterByAcl($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAclI18nsPartial && count($collAclI18ns)) {
                        $this->initAclI18ns(false);

                        foreach ($collAclI18ns as $obj) {
                            if (false == $this->collAclI18ns->contains($obj)) {
                                $this->collAclI18ns->append($obj);
                            }
                        }

                        $this->collAclI18nsPartial = true;
                    }

                    reset($collAclI18ns);

                    return $collAclI18ns;
                }

                if ($partial && $this->collAclI18ns) {
                    foreach ($this->collAclI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collAclI18ns[] = $obj;
                        }
                    }
                }

                $this->collAclI18ns = $collAclI18ns;
                $this->collAclI18nsPartial = false;
            }
        }

        return $this->collAclI18ns;
    }

    /**
     * Sets a collection of AclI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $aclI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAcl The current object (for fluent API support)
     */
    public function setAclI18ns(Collection $aclI18ns, ConnectionInterface $con = null)
    {
        $aclI18nsToDelete = $this->getAclI18ns(new Criteria(), $con)->diff($aclI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->aclI18nsScheduledForDeletion = clone $aclI18nsToDelete;

        foreach ($aclI18nsToDelete as $aclI18nRemoved) {
            $aclI18nRemoved->setAcl(null);
        }

        $this->collAclI18ns = null;
        foreach ($aclI18ns as $aclI18n) {
            $this->addAclI18n($aclI18n);
        }

        $this->collAclI18ns = $aclI18ns;
        $this->collAclI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AclI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related AclI18n objects.
     * @throws PropelException
     */
    public function countAclI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAclI18nsPartial && !$this->isNew();
        if (null === $this->collAclI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAclI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAclI18ns());
            }

            $query = ChildAclI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAcl($this)
                ->count($con);
        }

        return count($this->collAclI18ns);
    }

    /**
     * Method called to associate a ChildAclI18n object to this object
     * through the ChildAclI18n foreign key attribute.
     *
     * @param    ChildAclI18n $l ChildAclI18n
     * @return   \CustomerGroupAcl\Model\Acl The current object (for fluent API support)
     */
    public function addAclI18n(ChildAclI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collAclI18ns === null) {
            $this->initAclI18ns();
            $this->collAclI18nsPartial = true;
        }

        if (!in_array($l, $this->collAclI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAclI18n($l);
        }

        return $this;
    }

    /**
     * @param AclI18n $aclI18n The aclI18n object to add.
     */
    protected function doAddAclI18n($aclI18n)
    {
        $this->collAclI18ns[]= $aclI18n;
        $aclI18n->setAcl($this);
    }

    /**
     * @param  AclI18n $aclI18n The aclI18n object to remove.
     * @return ChildAcl The current object (for fluent API support)
     */
    public function removeAclI18n($aclI18n)
    {
        if ($this->getAclI18ns()->contains($aclI18n)) {
            $this->collAclI18ns->remove($this->collAclI18ns->search($aclI18n));
            if (null === $this->aclI18nsScheduledForDeletion) {
                $this->aclI18nsScheduledForDeletion = clone $this->collAclI18ns;
                $this->aclI18nsScheduledForDeletion->clear();
            }
            $this->aclI18nsScheduledForDeletion[]= clone $aclI18n;
            $aclI18n->setAcl(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->module_id = null;
        $this->code = null;
        $this->entity_class = null;
        $this->id = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collCustomerGroupAcls) {
                foreach ($this->collCustomerGroupAcls as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAclI18ns) {
                foreach ($this->collAclI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collCustomerGroupAcls = null;
        $this->collAclI18ns = null;
        $this->aModule = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AclTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildAcl The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[AclTableMap::UPDATED_AT] = true;

        return $this;
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildAcl The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildAclI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collAclI18ns) {
                foreach ($this->collAclI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildAclI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildAclI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addAclI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildAcl The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildAclI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collAclI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collAclI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildAclI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return   string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param      string $v new value
         * @return   \CustomerGroupAcl\Model\AclI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [description] column value.
         *
         * @return   string
         */
        public function getDescription()
        {
        return $this->getCurrentTranslation()->getDescription();
    }


        /**
         * Set the value of [description] column.
         *
         * @param      string $v new value
         * @return   \CustomerGroupAcl\Model\AclI18n The current object (for fluent API support)
         */
        public function setDescription($v)
        {    $this->getCurrentTranslation()->setDescription($v);

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
