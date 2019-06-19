<?php

namespace WebSK\Skif\Comment;

use WebSK\Auth\Auth;
use WebSK\Config\ConfWrapper;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Filters;

/**
 * Class CommentService
 * @method Comment getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Comment
 */
class CommentService extends EntityService
{
    /** @var CommentRepository */
    protected $repository;

    /**
     * Ветка с ответами
     * @return array
     */
    public function getChildrenIdsArr()
    {
        return $this->children_ids_arr;
    }

    /**
     * @param InterfaceEntity|Comment $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj)
    {
        $entity_obj->setUserId(Auth::getCurrentUserId());
        $this->date_time = date('Y-m-d H:i:s');

        parent::beforeSave($entity_obj);
    }

    public function afterUpdate(InterfaceEntity $entity_obj)
    {
        $comment_obj = $($comment_id);

        if ($comment_obj->getParentId()) {
            self::removeObjFromCacheById($comment_obj->getParentId());

            if (ConfWrapper::value('comments.send_answer_to_email')) {
                $parent_comment_obj = self::factory($comment_obj->getParentId());
                if ($parent_comment_obj->getUserEmail()) {
                    $site_email = ConfWrapper::value('site_email');
                    $site_domain = ConfWrapper::value('site_domain');
                    $site_name = ConfWrapper::value('site_name');

                    $mail_message = 'Здравствуйте, ' . $parent_comment_obj->getUserEmail() . '!<br />';
                    $mail_message .= 'Получен ответ на ваше сообщение:<br />';
                    $mail_message .= $parent_comment_obj->getComment() . '<br />';
                    $mail_message .= 'Ответ: ' . $comment_obj->getComment() . '<br />';
                    $mail_message .= $site_name . ', ' . $site_domain;

                    $subject = 'Ответ на сообщение на сайте' . $site_name;

                    $mail = new \PHPMailer;
                    $mail->CharSet = "utf-8";
                    $mail->setFrom($site_email, $site_name);
                    $mail->addReplyTo($site_email);
                    $mail->addAddress($parent_comment_obj->getUserEmail());
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $mail_message;
                    $mail->AltBody = Filters::checkPlain($mail_message);
                    $mail->send();
                }
            }
        }

        self::removeObjFromCacheById($comment_id);
    }

    public function afterDelete()
    {
        $children_ids_arr = $this->getChildrenIdsArr();

        foreach ($children_ids_arr as $children_comment_id) {
            $children_comment_obj = self::factory($children_comment_id);
            $children_comment_obj->delete();
        }

        self::removeObjFromCacheById($this->getParentId());

        self::removeObjFromCacheById($this->getId());
    }
}
