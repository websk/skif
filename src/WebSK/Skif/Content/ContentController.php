<?php

namespace WebSK\Skif\Content;

use WebSK\Config\ConfWrapper;
use WebSK\DB\DBWrapper;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use WebSK\Image\ImageManager;
use WebSK\Skif\BaseController;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Container;
use WebSK\Slim\Router;
use WebSK\Utils\Messages;
use WebSK\SimpleRouter\Sitemap\InterfaceSitemapController;
use WebSK\Auth\Auth;
use WebSK\Utils\Assert;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;

/**
 * Class ContentController
 * @package WebSK\Skif\Content
 */
class ContentController extends BaseController implements InterfaceSitemapController
{

    /**
     * @var string
     */
    protected $url_table = Content::DB_TABLE_NAME;

    /**
     * @return array
     */
    public function getUrlsForSitemap()
    {
        $current_domain = ConfWrapper::value('site_domain');

        $urls = [];

        $content_service = ContentServiceProvider::getContentService(Container::self());
        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());

        $content_type_ids_arr = $content_type_service->getAllIdsArrByIdAsc();

        foreach ($content_type_ids_arr as $content_type_id) {
            $content_type_obj = $content_type_service->getById($content_type_id);

            $content_ids_arr = $content_service->getPublishedIdsArrByType($content_type_obj->getType());

            foreach ($content_ids_arr as $content_id) {
                $content_obj = $content_service->getById($content_id);

                $urls[] = ['url' => $current_domain . Url::appendLeadingSlash($content_obj->getUrl())];
            }
        }

        return $urls;
    }

    /**
     * Добавление материала
     * @param $content_type
     */
    public function newAdminAction($content_type)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $html = PhpRender::renderTemplateInViewsDir(
            'admin_content_form_edit.tpl.php',
            array('content_id' => 'new', 'content_type' => $content_type)
        );

        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());
        $content_type_obj = $content_type_service->getByType($content_type);

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => 'Редактирование материала',
                'content' => $html,
                'breadcrumbs_arr' => array(
                    $content_type_obj->getName() => '/admin/content/' . $content_type
                )
            )
        );
    }

    public function listAdminAction($content_type)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $html = PhpRender::renderTemplateInViewsDir(
            'admin_content_list.tpl.php',
            array('content_type' => $content_type)
        );

        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());
        $content_type_obj = $content_type_service->getByType($content_type);

        echo PhpRender::renderTemplate(
            SkifPath::getLayout(),
            array(
                'title' => $content_type_obj->getName(),
                'content' => $html,
                'breadcrumbs_arr' => array()
            )
        );
    }

    public function saveAdminAction($content_type, $content_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $content_service = ContentServiceProvider::getContentService(Container::self());

        if ($content_id == 'new') {
            $content_obj = new Content();
        } else {
            $content_obj = $content_service->getById($content_id);
        }

        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());
        $content_type_obj = $content_type_service->getByType($content_type);

        $title = array_key_exists('title', $_REQUEST) ? $_REQUEST['title'] : '';

        if (!$title) {
            Messages::setError('Отсутствует заголовок');
            Redirects::redirect(Router::pathFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]));
        }

        $annotation = array_key_exists('annotation', $_REQUEST) ? $_REQUEST['annotation'] : '';
        $body = array_key_exists('body', $_REQUEST) ? $_REQUEST['body'] : '';
        $url = array_key_exists('url', $_REQUEST) ? $_REQUEST['url'] : '';

        $published_at = array_key_exists('published_at', $_REQUEST) ? $_REQUEST['published_at'] : null;
        if (empty($published_at)) {
            $published_at = null;
        }

        $unpublished_at = array_key_exists('unpublished_at', $_REQUEST) ? $_REQUEST['unpublished_at'] : null;
        if (empty($unpublished_at)) {
            $unpublished_at = null;
        }

        $is_published = array_key_exists('is_published', $_REQUEST) ? $_REQUEST['is_published'] : 0;
        $created_at = array_key_exists('created_at', $_REQUEST) ? $_REQUEST['created_at'] : date('Y-m-d H:i:s');
        $description = array_key_exists('description', $_REQUEST) ? $_REQUEST['description'] : '';
        $keywords = array_key_exists('keywords', $_REQUEST) ? $_REQUEST['keywords'] : '';
        $template_id = array_key_exists('template_id', $_REQUEST) ? $_REQUEST['template_id'] : null;

        if ($is_published && empty($published_at)) {
            $published_at = $created_at;
        }

        $content_obj->setTitle($title);
        $content_obj->setAnnotation($annotation);
        $content_obj->setBody($body);
        $content_obj->setContentTypeId($content_type_obj->getId());
        $content_obj->setPublishedAt($published_at);
        $content_obj->setUnpublishedAt($unpublished_at);
        $content_obj->setCreatedAt($created_at);
        $content_obj->setDescription($description);
        $content_obj->setKeywords($keywords);
        $content_obj->setTemplateId($template_id);
        $content_obj->setLastModifiedAt(date('Y-m-d H:i:s'));


        // URL
        if (!$content_obj->isPublished()) {
            if (!$url) {
                $url = $content_service->generateUrl($content_obj);
            }

            $url = '/' . ltrim($url, '/');

            $content_type_url_length = strlen($content_type_obj->getUrl());
            if (substr($url, 0, $content_type_url_length + 1) != $content_type_obj->getUrl() . '/') {
                $url = $content_type_obj->getUrl() . $url;
            }

            $url = '/' . ltrim($url, '/');

            $content_obj->setUrl($url);
        }

        $content_obj->setIsPublished($is_published);

        $content_service->save($content_obj);


        // Рубрики*
        $main_rubric_id = !empty($_REQUEST['main_rubric']) ? $_REQUEST['main_rubric'] : null;
        $rubrics_arr = !empty($_REQUEST['rubrics_arr']) ? $_REQUEST['rubrics_arr'] : array();
        $require_main_rubric = ConfWrapper::value('content.' . $content_type_obj->getType() . '.require_main_rubric');

        if (!$main_rubric_id && $require_main_rubric) {
            $main_rubric_id = ConfWrapper::value('content.' . $content_type_obj->getType() . '.main_rubric_default_id');

            if (!$rubrics_arr) {
                $rubrics_arr = array($main_rubric_id);
            }
        }

        if ($rubrics_arr) {
            $content_rubric_service = ContentServiceProvider::getContentRubricService(Container::self());
            $content_rubric_service->deleteByContentId($content_obj->getId());

            foreach ($rubrics_arr as $rubric_id) {
                $content_rubric_obj = new ContentRubric();
                $content_rubric_obj->setContentId($content_obj->getId());
                $content_rubric_obj->setRubricId($rubric_id);
                $content_rubric_service->save($content_rubric_obj);
            }

            if (!$main_rubric_id) {
                $main_rubric_id = $rubrics_arr[0];
            }
        }

        // Главная рубрика

        if ($require_main_rubric) {
            if (!$main_rubric_id) {
                $content_obj->setIsPublished(0);
                $content_obj->save();

                Messages::setError('Не указана главная рубрика');
                Redirects::redirect(Router::pathFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]));
            }
        }

        $content_obj->setMainRubricId($main_rubric_id);
        $content_obj->save();

        $content_id = $content_obj->getId();

        // Картинка
        if (array_key_exists('image_file', $_FILES) && !empty($_FILES['image_file']['name'])) {
            $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
            $file = $_FILES['image_file'];
            $file_name = ImageController::processUpload($file, 'content/' . $content_type, $root_images_folder);
            $content_obj->setImage($file_name);
            $content_obj->save();
        }


        Messages::setMessage('Изменения сохранены');

        Redirects::redirect(Router::pathFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]));
    }

    /**
     * Удаление изображения
     * @param $content_id
     * @throws \Exception
     */
    public function deleteImageAction($content_type, $content_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        self::deleteImageByContentId($content_id);

        echo 'OK';
    }

    /**
     * @param $content_id
     */
    protected static function deleteImageByContentId($content_id)
    {
        $content_service = ContentServiceProvider::getContentService(Container::self());

        $content_obj = $content_service->getById($content_id);
        Assert::assert($content_obj);

        if (!$content_obj->getImage()) {
            return;
        }



        $image_manager = new ImageManager();
        $image_manager->removeImageFile($content_service->getImagePath($content_obj));

        $content_obj->setImage('');
        $content_service->save($content_obj);
    }

    public function deleteAction($content_type, $content_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $content_service = ContentServiceProvider::getContentService(Container::self());
        $content_obj = $content_service->getById($content_id);

        self::deleteImageByContentId($content_id);

        $content_service->delete($content_obj);

        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());
        $content_type_obj = $content_type_service->getById($content_obj->getContentTypeId());

        Redirects::redirect('/admin/content/' . $content_type_obj->getType());
    }

    /**
     * Автокомплит для выбора материала
     */
    public static function autoCompleteContentAction()
    {
        $term = array_key_exists('term', $_REQUEST) ? trim($_REQUEST['term']) : '';

        $query_param_arr = array($term . '%');

        $query = "SELECT id FROM " . Content::DB_TABLE_NAME . " WHERE title LIKE ? LIMIT 20";
        $content_ids_arr = DBWrapper::readColumn($query, $query_param_arr);

        $content_service = ContentServiceProvider::getContentService(Container::self());
        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());

        $output_arr = array();
        foreach ($content_ids_arr as $content_id) {
            $content_obj = $content_service->getById($content_id);

            $content_type_obj = $content_type_service->getByType($content_obj->getContentTypeId());

            $output_arr[] = array(
                'id' => $content_id,
                'label' => $content_obj->getTitle(),
                'value' => $content_obj->getTitle(),
                'type' => $content_type_obj->getName(),
                'url' => $content_obj->getUrl(),
            );
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output_arr);
    }
}
