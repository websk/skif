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
     * @param int $id
     * @return array
     */
    public function getChildrenIdsArr(int $id)
    {
        return $this->repository->findIdsByParentId($id);
    }

    /**
     * @param InterfaceEntity|Comment $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj)
    {
        $entity_obj->setUserId(Auth::getCurrentUserId());

        $url = $entity_obj->getUrl();
        $entity_obj->setUrlMd5(md5($url));

        parent::beforeSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|Comment $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        if ($entity_obj->getParentId()) {
            if (ConfWrapper::value('comments.send_answer_to_email')) {
                $parent_comment_obj = $this->getById($entity_obj->getParentId());

                if ($parent_comment_obj->getUserEmail()) {
                    $site_email = ConfWrapper::value('site_email');
                    $site_domain = ConfWrapper::value('site_domain');
                    $site_name = ConfWrapper::value('site_name');

                    $mail_message = 'Здравствуйте, ' . $parent_comment_obj->getUserEmail() . '!<br />';
                    $mail_message .= 'Получен ответ на ваше сообщение:<br />';
                    $mail_message .= $parent_comment_obj->getComment() . '<br />';
                    $mail_message .= 'Ответ: ' . $entity_obj->getComment() . '<br />';
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

        parent::afterSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|Comment $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        $children_ids_arr = $this->getChildrenIdsArr($entity_obj->getId());

        foreach ($children_ids_arr as $children_comment_id) {
            $children_comment_obj = $this->getById($children_comment_id);
            $this->delete($children_comment_obj);
        }

        self::removeObjFromCacheById($entity_obj->getParentId());

        self::removeObjFromCacheById($entity_obj->getId());
    }
}
