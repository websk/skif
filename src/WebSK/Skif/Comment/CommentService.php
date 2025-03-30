<?php

namespace WebSK\Skif\Comment;

use PHPMailer\PHPMailer\PHPMailer;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserService;
use WebSK\Cache\CacheService;
use WebSK\Config\ConfWrapper;
use WebSK\Entity\EntityRepository;
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
    const string CONFIG_MESSAGE_TO_PAGE = 'comments.message_to_page';
    const string CONFIG_NO_ADD_COMMENTS_FOR_UNREGISTERED_USERS = 'comments.no_add_comments_for_unregistered_users';
    const string CONFIG_ENABLE_EMAIL_NOTIFICATION = 'comments.enable_email_notification';
    const string CONFIG_SEND_ANSWER_TO_EMAIL = 'comments.send_answer_to_email';

    const int DEFAULT_MESSAGE_TO_PAGE = 20;

    /** @var CommentRepository */
    protected $repository;

    protected UserService $user_service;

    /**
     * @return int
     */
    protected function getCacheTtlSeconds(): int
    {
        return 60 * 60 * 24 * 30 - 1; // 1 месяц
    }

    /**
     * CommentService constructor.
     * @param string $entity_class_name
     * @param EntityRepository $repository
     * @param CacheService $cache_service
     * @param UserService $user_service
     */
    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        UserService $user_service
    ) {
        $this->user_service = $user_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getChildrenIdsArr(int $id): array
    {
        return $this->repository->findIdsByParentId($id);
    }

    /**
     * @param string $url
     * @param int $parent_id
     * @param int $page
     * @return array
     */
    public function getCommentsIdsArrByUrl(string $url, int $page = 1): array
    {
        $page_size = ConfWrapper::value(self::CONFIG_MESSAGE_TO_PAGE, self::DEFAULT_MESSAGE_TO_PAGE);
        $offset = ($page - 1) * $page_size;

        return $this->repository->findIdsByUrl($url, $offset, $page_size);
    }

    /**
     * @param string $url
     * @return int
     */
    public function getCountCommentsByUrl(string $url): int
    {
        return $this->repository->findCountCommentsByUrl($url);
    }

    /**
     * @param InterfaceEntity|Comment $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        $entity_obj->setUserId(Auth::getCurrentUserId());

        $url = $entity_obj->getUrl();
        $entity_obj->setUrlMd5(md5($url));

        parent::beforeSave($entity_obj);
    }

    /**
     * @param Comment $comment_obj
     * @return string
     */
    public function getUserEmail(Comment $comment_obj): string
    {
        if (!$comment_obj->getUserId()) {
            return $comment_obj->getUserEmail() ?: '';
        }

        $user_obj = $this->user_service->getById($comment_obj->getUserId());

        return $user_obj->getEmail();
    }

    /**
     * @param Comment $comment_obj
     * @return string
     */
    public function getUserName(Comment $comment_obj): string
    {
        if (!$comment_obj->getUserId()) {
            return $comment_obj->getUserName() ?: '';
        }

        $user_obj = $this->user_service->getById($comment_obj->getUserId());

        return $user_obj->getName();
    }

    /**
     * @param InterfaceEntity|Comment $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        $children_ids_arr = $this->getChildrenIdsArr($entity_obj->getId());

        foreach ($children_ids_arr as $children_comment_id) {
            $children_comment_obj = $this->getById($children_comment_id);
            $this->delete($children_comment_obj);
        }

        if ($entity_obj->getParentId()) {
            $this->removeObjFromCacheById($entity_obj->getParentId());
        }

        $this->removeObjFromCacheById($entity_obj->getId());
    }

    public function sendEmailNotificationForComment(Comment $comment_obj): void
    {
        if (!ConfWrapper::value(CommentService::CONFIG_ENABLE_EMAIL_NOTIFICATION))
        {
            return;
        }

        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');
        $site_name = ConfWrapper::value('site_name');

        $user_email = $this->getUserEmail($comment_obj);
        $user_name = $this->getUserName($comment_obj);

        $mail_message = 'Пользователь ' . $user_name;
        if ($user_email) {
            $mail_message .= ' (' . $user_email . ')';
        }
        $mail_message .= ' оставил новое сообщение на странице ' . $comment_obj->getUrl() . '.<br />';
        $mail_message .= $comment_obj->getComment() . '<br />';
        $mail_message .= $site_name . ', ' . $site_domain;

        $subject = 'Новое сообщение от пользователя ' . $user_name;

        $mail = new PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addReplyTo($site_email);
        $mail->addAddress($site_email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $mail_message;
        $mail->AltBody = Filters::checkPlain($mail_message);
        $mail->send();

        if ($comment_obj->getParentId()) {
            if (ConfWrapper::value(self::CONFIG_SEND_ANSWER_TO_EMAIL)) {
                $parent_comment_obj = $this->getById($comment_obj->getParentId());

                $parent_comment_user_email = $this->getUserEmail($parent_comment_obj);

                if ($parent_comment_user_email) {
                    $mail_message = 'Здравствуйте, ' . $this->getUserName($parent_comment_obj) . '!<br />';
                    $mail_message .= 'Получен ответ на ваше сообщение:<br />';
                    $mail_message .= $parent_comment_obj->getComment() . '<br />';
                    $mail_message .= 'Ответ: ' . $comment_obj->getComment() . '<br />';
                    $mail_message .= $site_name . ', ' . $site_domain;

                    $subject = 'Ответ на сообщение на сайте' . $site_name;

                    $mail = new PHPMailer;
                    $mail->CharSet = "utf-8";
                    $mail->setFrom($site_email, $site_name);
                    $mail->addReplyTo($site_email);
                    $mail->addAddress($parent_comment_user_email);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $mail_message;
                    $mail->AltBody = Filters::checkPlain($mail_message);
                    $mail->send();
                }
            }
        }
    }
}
