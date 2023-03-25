<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentRubric;
use WebSK\Skif\Content\ContentRubricService;
use WebSK\Skif\Content\ContentService;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Utils\HTTP;
use WebSK\Utils\Messages;

class AdminContentSaveHandler extends BaseHandler
{
    protected ContentTypeService $content_type_service;

    protected ContentService $content_service;

    protected ContentRubricService $content_rubric_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $content_id): ResponseInterface
    {
        $this->content_type_service = ContentServiceProvider::getContentTypeService($this->container);
        $this->content_service = ContentServiceProvider::getContentService($this->container);

        $content_type_obj = $this->content_type_service->getByType($content_type);

        if (!$content_type_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $content_obj = $this->content_service->getById($content_id, false);
        if (!$content_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $redirect_url = Router::pathFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]);

        $title = $request->getParam('title', '');
        if (!$title) {
            Messages::setError('Отсутствует заголовок');
            return $response->withRedirect($redirect_url);
        }

        $annotation = $request->getParam('annotation', '');
        $body = $request->getParam('body', '');
        $url = $request->getParam('url', '');
        $published_at = $request->getParam('published_at') ?: null;
        $created_at = $request->getParam('created_at') ?: null;
        $unpublished_at = $request->getParam('unpublished_at') ?: null;
        $is_published = $request->getParam('is_published', false);
        $description = $request->getParam('description', '');
        $keywords = $request->getParam('keywords', '');
        $template_id = $request->getParam('template_id') ?: null;

        $content_obj->setTitle($title);
        $content_obj->setAnnotation($annotation);
        $content_obj->setBody($body);
        $content_obj->setContentTypeId($content_type_obj->getId());
        $content_obj->setCreatedAtTs((new \DateTime($created_at))->getTimestamp());
        $content_obj->setPublishedAt($published_at);
        $content_obj->setUnpublishedAt($unpublished_at);
        $content_obj->setDescription($description);
        $content_obj->setKeywords($keywords);
        $content_obj->setTemplateId($template_id);
        $content_obj->setIsPublished($is_published);

        if (!$is_published) {
            $content_obj->setUrl($url);
        }

        $this->content_service->save($content_obj);


        // Рубрики
        $require_main_rubric = ConfWrapper::value('content.' . $content_type_obj->getType() . '.require_main_rubric');
        $main_rubric_id = $this->saveRubrics($request, $content_type_obj, $content_obj);

        // Главная рубрика
        if ($require_main_rubric) {
            if (!$main_rubric_id) {
                $content_obj->setIsPublished(false);
                $this->content_service->save($content_obj);

                Messages::setError('Не указана главная рубрика');
                return $response->withRedirect($redirect_url);
            }
        }
        $content_obj->setMainRubricId($main_rubric_id);
        $this->content_service->save($content_obj);

        // Картинка
        $this->uploadImage($content_obj, $content_type_obj);

        Messages::setMessage('Изменения сохранены');

        return $response->withRedirect($redirect_url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ContentType $content_type_obj
     * @param Content $content_obj
     * @return null|int
     */
    protected function saveRubrics(ServerRequestInterface $request, ContentType $content_type_obj, Content $content_obj): ?int
    {
        $main_rubric_id = $request->getParam('main_rubric') ?: null;
        $rubrics_arr = $request->getParam('rubrics_arr', []) ?: [];

        $require_main_rubric = ConfWrapper::value('content.' . $content_type_obj->getType() . '.require_main_rubric');

        if (!$main_rubric_id && $require_main_rubric) {
            $main_rubric_id = ConfWrapper::value('content.' . $content_type_obj->getType() . '.main_rubric_default_id');

            if (!$rubrics_arr && $main_rubric_id) {
                $rubrics_arr = [$main_rubric_id];
            }
        }

        if (!$rubrics_arr) {
            return $main_rubric_id;
        }

        $this->content_rubric_service = ContentServiceProvider::getContentRubricService($this->container);
        $this->content_rubric_service->deleteByContentId($content_obj->getId());

        foreach ($rubrics_arr as $rubric_id) {
            $content_rubric_obj = new ContentRubric();
            $content_rubric_obj->setContentId($content_obj->getId());
            $content_rubric_obj->setRubricId($rubric_id);
            $this->content_rubric_service->save($content_rubric_obj);
        }

        if (!$main_rubric_id) {
            $main_rubric_id = $rubrics_arr[0];
        }

        return $main_rubric_id;
    }

    /**
     * @param Content $content_obj
     * @param ContentType $content_type_obj
     * @return void
     * @throws \Exception
     */
    protected function uploadImage(Content $content_obj, ContentType $content_type_obj)
    {
        if (!array_key_exists('image_file', $_FILES) || empty($_FILES['image_file']['name'])) {
            return;
        }

        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
        $file = $_FILES['image_file'];
        $target_folder = 'content/' . $content_type_obj->getType();
        $file_name = ImageController::processUpload($file, $target_folder, $root_images_folder);
        $content_obj->setImage($file_name);
        $this->content_service->save($content_obj);
    }
}