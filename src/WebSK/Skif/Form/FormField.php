<?php

namespace WebSK\Skif\Form;

use WebSK\Entity\Entity;

/**
 * Class FormField
 * @package WebSK\Skif\Form
 */
class FormField extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'skif.form_field__service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.form_field_repository';
    const DB_TABLE_NAME = 'form_field';

    const FIELD_TYPE_STRING = 1;
    const FIELD_TYPE_TEXTAREA = 2;
    const FIELD_TYPE_COMMENT = 3;
    const FIELD_TYPE_CHECKBOX = 4;

    const FIELD_TYPES_ARR = [
        self::FIELD_TYPE_STRING => 'Строка',
        self::FIELD_TYPE_TEXTAREA => 'Текст',
        self::FIELD_TYPE_COMMENT => 'Комментарий',
        self::FIELD_TYPE_CHECKBOX => 'Галочка',
    ];

    const _FORM_ID = 'form_id';
    /** @var int */
    protected $form_id;

    const _NAME = 'name';
    /** @var string */
    protected $name = '';

    const _TYPE = 'type';
    /** @var null|int */
    protected $type;

    const _REQUIRED = 'required';
    /** @var int */
    protected $required = 0;

    const _WEIGHT = 'weight';
    /** @var null|int */
    protected $weight = 0;

    const _SIZE = 'size';
    /** @var null|int */
    protected $size = 50;

    /**
     * @return int
     */
    public function getFormId(): int
    {
        return $this->form_id;
    }

    /**
     * @param int $form_id
     */
    public function setFormId(int $form_id): void
    {
        $this->form_id = $form_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getRequired(): int
    {
        return $this->required;
    }

    /**
     * @param int $required
     */
    public function setRequired(int $required): void
    {
        $this->required = $required;
    }

    /**
     * @return int|null
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param int|null $weight
     */
    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     */
    public function setSize(?int $size): void
    {
        $this->size = $size;
    }
}
