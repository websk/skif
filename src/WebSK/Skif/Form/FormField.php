<?php

namespace WebSK\Skif\Form;

use WebSK\Entity\Entity;

/**
 * Class FormField
 * @package WebSK\Skif\Form
 */
class FormField extends Entity
{
    const string DB_TABLE_NAME = 'form_field';

    const int FIELD_TYPE_STRING = 1;
    const int FIELD_TYPE_TEXTAREA = 2;
    const int FIELD_TYPE_COMMENT = 3;
    const int FIELD_TYPE_CHECKBOX = 4;

    const array FIELD_TYPES_ARR = [
        self::FIELD_TYPE_STRING => 'Строка',
        self::FIELD_TYPE_TEXTAREA => 'Текст',
        self::FIELD_TYPE_COMMENT => 'Комментарий',
        self::FIELD_TYPE_CHECKBOX => 'Галочка',
    ];

    const string _FORM_ID = 'form_id';
    protected int $form_id;

    const string _NAME = 'name';
    protected string $name = '';

    const string _TYPE = 'type';
    protected int $type = self::FIELD_TYPE_STRING;

    const string _REQUIRED = 'required';
    protected bool $required = false;

    const string _WEIGHT = 'weight';
    protected int $weight = 0;

    const string _SIZE = 'size';
    protected ?int $size = null;

    const string _COMMENT = 'comment';
    protected string $comment = '';

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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
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

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
}
