<?php

namespace CustomerGroupAcl\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Event object to dispatch to act on ACLs.
 */
class AclEvent extends ActionEvent
{
    protected string $code;
    protected int $module_id;
    protected string|null $locale;
    protected string|null $title;
    protected string|null $description;
    protected int|null $id;

    public function __construct(string $code, int $module_id, string $locale = null, string $title = null, string $description = null, int $id = null)
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
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param int $module_id
     */
    public function setModuleId(int $module_id): void
    {
        $this->module_id = $module_id;
    }

    /**
     * @return int
     */
    public function getModuleId(): int
    {
        return $this->module_id;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
}
