<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentRubric;
use WebSK\Skif\Content\ContentRubricService;
use WebSK\Skif\Content\ContentService;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Router;
use WebSK\Utils\Messages;

class AdminContentSaveHandler extends BaseHandler
{
    #[\DI\Attribute\Inject]
    protected ContentTypeService $content_type_service;

    #[\DI\Attribute\Inject]
    protected ContentService $content_service;

    #[\DI\Attribute\Inject]
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
        $content_type_obj = $this->content_type_service->getByType($content_type);

        if (!$content_type_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $content_obj = $this->content_service->getById($content_id, false);
        if (!$content_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $redirect_url = Router::urlFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]);

        $title = \WebSK\CRUD\RequestParams::parsedBody($request, 'title', '');
        if (!$title) {
            Messages::setError('Отсутствует заголовок');
            return $response->withHeader('Location', $redirect_url)
                ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
        }

        $annotation = \WebSK\CRUD\RequestParams::parsedBody($request, 'annotation', '');
        $body = \WebSK\CRUD\RequestParams::parsedBody($request, 'body', '');
        $url = \WebSK\CRUD\RequestParams::parsedBody($request, 'url', '');
        $published_at = \WebSK\CRUD\RequestParams::parsedBody($request, 'published_at') ?: null;
        $created_at = \WebSK\CRUD\RequestParams::parsedBody($request, 'created_at') ?: null;
        $unpublished_at = \WebSK\CRUD\RequestParams::parsedBody($request, 'unpublished_at') ?: null;
        $is_published = \WebSK\CRUD\RequestParams::parsedBody($request, 'is_published', false);
        $description = \WebSK\CRUD\RequestParams::parsedBody($request, 'description', '');
        $keywords = \WebSK\CRUD\RequestParams::parsedBody($request, 'keywords', '');
        $template_id = \WebSK\CRUD\RequestParams::parsedBody($request, 'template_id') ?: null;

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
                return $response->withHeader('Location', $redirect_url)
                    ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
            }
        }
        $content_obj->setMainRubricId($main_rubric_id);
        $this->content_service->save($content_obj);

        // Картинка
        $this->uploadImage($content_obj, $content_type_obj);

        Messages::setMessage('Изменения сохранены');

        return $response->withHeader('Location', $redirect_url)
            ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ContentType $content_type_obj
     * @param Content $content_obj
     * @return null|int
     */
    protected function saveRubrics(ServerRequestInterface $request, ContentType $content_type_obj, Content $content_obj): ?int
    {
        $main_rubric_id = \WebSK\CRUD\RequestParams::parsedBody($request, 'main_rubric') ?: null;
        $rubrics_arr = \WebSK\CRUD\RequestParams::parsedBody($request, 'rubrics_arr', []) ?: [];

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
    protected function uploadImage(Content $content_obj, ContentType $content_type_obj): void
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