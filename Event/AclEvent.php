<?php

namespace CustomerGroupAcl\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Event object to dispatch to act on ACLs.
 */
class AclEvent extends ActionEvent
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $module_id;
    /** @var string */
    protected $code;
    /** @var string */
    protected $locale;
    /** @var string */
    protected $title;
    /** @var string */
    protected $description;

    public function __construct($code, $module_id, $locale = null, $title = null, $description = null, $id = null)
    {
        $this->code = $code;
        $this->module_id = $module_id;
        $this->locale = $locale;
        $this->title = $title;
        $this->description = $description;
        $this->id = $id;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param int $module_id
     */
    public function setModuleId($module_id)
    {
        $this->module_id = $module_id;
    }

    /**
     * @return int
     */
    public function getModuleId()
    {
        return $this->module_id;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
