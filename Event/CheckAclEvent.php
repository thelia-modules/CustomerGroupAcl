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
    protected string $resource;
    protected string $classNames;
    protected int $entityId;
    protected array $accesses;
    protected bool $accessesOr;
    protected bool $result = true;

    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @param string $resource
     *
     * @return CheckAclEvent
     */
    public function setResource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassNames(): string
    {
        return $this->classNames;
    }

    /**
     * @param string $classNames
     *
     * @return CheckAclEvent
     */
    public function setClassNames(string $classNames): static
    {
        $this->classNames = $classNames;

        return $this;
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param mixed $entityId
     *
     * @return CheckAclEvent
     */
    public function setEntityId(mixed $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * @return array
     */
    public function getAccesses(): array
    {
        return $this->accesses;
    }

    /**
     * @param array $accesses
     *
     * @return CheckAclEvent
     */
    public function setAccesses(array $accesses): static
    {
        $this->accesses = $accesses;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAccessesOr(): bool
    {
        return $this->accessesOr;
    }

    /**
     * @param bool $accessesOr
     *
     * @return CheckAclEvent
     */
    public function setAccessesOr(bool $accessesOr): static
    {
        $this->accessesOr = $accessesOr;

        return $this;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }

    /**
     * @param bool $result
     *
     * @return CheckAclEvent
     */
    public function setResult(bool $result): static
    {
        $this->result = $result;

        return $this;
    }

}
