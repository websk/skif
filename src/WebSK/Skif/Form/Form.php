<?php

namespace WebSK\Skif\Form;

use WebSK\Entity\Entity;

/**
 * Class Form
 * @package WebSK\Skif\Form
 */
class Form extends Entity
{
    const string DB_TABLE_NAME = 'form';

    const string _TITLE = 'title';
    protected string $title;

    const string _COMMENT = 'comment';
    protected string $comment = '';

    const string _BUTTON_LABEL = 'button_label';
    protected string $button_label = '';

    const string _EMAIL = 'email';
    protected string $email = '';

    const string _EMAIL_COPY = 'email_copy';
    protected string $email_copy = '';

    const string _RESPONSE_MAIL_MESSAGE = 'response_mail_message';
    protected string $response_mail_message = '';

    const string _URL = 'url';
    protected ?string $url = null;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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

    /**
     * @return string
     */
    public function getButtonLabel(): string
    {
        return $this->button_label;
    }

    /**
     * @param string $button_label
     */
    public function setButtonLabel(string $button_label): void
    {
        $this->button_label = $button_label;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmailCopy(): string
    {
        return $this->email_copy;
    }

    /**
     * @param string $email_copy
     */
    public function setEmailCopy(string $email_copy): void
    {
        $this->email_copy = $email_copy;
    }

    /**
     * @return string
     */
    public function getResponseMailMessage(): string
    {
        return $this->response_mail_message;
    }

    /**
     * @param string $response_mail_message
     */
    public function setResponseMailMessage(string $response_mail_message): void
    {
        $this->response_mail_message = $response_mail_message;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
