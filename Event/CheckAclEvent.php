<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerGroupAcl\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Class CheckAclEvent
 * @package CustomerGroupAcl\Event
 * @author  David Gros <dgros@openstudio.fr>
 */
class CheckAclEvent extends ActionEvent
{
    /** @var  string */
    protected $resource;
    /** @var  string */
    protected $classNames;
    /** @var  integer */
    protected $entityId;
    /** @var  array */
    protected $accesses;
    /** @var  bool */
    protected $accessesOr;
    /** @var  bool  */
    protected $result;

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param mixed $resource
     *
     * @return CheckAclEvent
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassNames()
    {
        return $this->classNames;
    }

    /**
     * @param mixed $classNames
     *
     * @return CheckAclEvent
     */
    public function setClassNames($classNames)
    {
        $this->classNames = $classNames;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param mixed $entityId
     *
     * @return CheckAclEvent
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccesses()
    {
        return $this->accesses;
    }

    /**
     * @param mixed $accesses
     *
     * @return CheckAclEvent
     */
    public function setAccesses($accesses)
    {
        $this->accesses = $accesses;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccessesOr()
    {
        return $this->accessesOr;
    }

    /**
     * @param mixed $accessesOr
     *
     * @return CheckAclEvent
     */
    public function setAccessesOr($accessesOr)
    {
        $this->accessesOr = $accessesOr;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     *
     * @return CheckAclEvent
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

}
