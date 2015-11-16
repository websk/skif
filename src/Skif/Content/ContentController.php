<?php

namespace Skif\Content;

class ContentController extends \Skif\BaseController
{

    /**
     * @var string
     */
    protected $url_table = \Skif\Content\Content::DB_TABLE_NAME;

    public function viewAction()
    {
        $requested_id = $this->getRequestedId();

        if (!$requested_id) {
            return \Skif\UrlManager::CONTINUE_ROUTING;
        }

        $content_id = $requested_id;

        $content_obj = \Skif\Content\Content::factory($content_id);
        if (!$content_obj) {
            \Skif\Http::exit404();
        }

        if (!$content_obj->isPublished()) {
            \Skif\Http::exit404If(!\Skif\Users\AuthUtils::currentUserIsAdmin());
        }

        $content_type_id = $content_obj->getContentTypeId();

        \Skif\Http::exit404If(!$content_type_id);

        $content_type_obj = \Skif\Content\ContentType::factory($content_type_id);
        $content_type = $content_type_obj->getType();


        $content = '';

        $admin_nav_arr = array();
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            $admin_nav_arr = array($content_obj->getEditorUrl() => 'Редактировать');
        }

        $breadcrumbs_arr = array();


        $template_file = 'content_view.tpl.php';

        if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', 'content_' . $content_type . '_view.tpl.php')) {
            $template_file = 'content_' . $content_type . '_view.tpl.php';
        }

        if ($content_obj->getCountRubricIdsArr()) {
            $main_rubric_id = $content_obj->getMainRubricId();

            if ($main_rubric_id) {
                $rubric_obj = \Skif\Content\Rubric::factory($main_rubric_id);

                $breadcrumbs_arr = array($rubric_obj->getName() => $rubric_obj->getUrl() );
            }

            if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', 'content_by_rubric_' . $main_rubric_id .'_view.tpl.php')) {
                $template_file = 'content_by_rubric_' . $main_rubric_id .'_view.tpl.php';
            } else if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', 'content_by_rubric_view.tpl.php')) {
                $template_file = 'content_by_rubric_view.tpl.php';
            }
        }

        $content .= \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            $template_file,
            array('content_id' => $content_id)
        );

        $template_id = $content_obj->getTemplateId();

        $template_obj = \Skif\Content\Template::factory($template_id);
        $layout_template_file = $template_obj->getLayoutTemplateFilePath();

        echo \Skif\PhpTemplate::renderTemplate(
            $layout_template_file,
            array(
                'content' => $content,
                'admin_nav_arr' => $admin_nav_arr,
                'title' => $content_obj->getTitle(),
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Список материалов
     * @param $content_type
     * @return string
     */
    public function listAction($content_type)
    {
        if (!\Skif\Conf\ConfWrapper::value('content.' . $content_type )) {
            return \Skif\UrlManager::CONTINUE_ROUTING;
        }

        $template_file = 'content_list.tpl.php';

        if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', 'content_' . $content_type. '_list.tpl.php')) {
            $template_file = 'content_' . $content_type. '_list.tpl.php';
        }

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            $template_file,
            array('content_type' => $content_type)
        );

        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        $template_id = $content_type_obj->getTemplateId();

        $template_obj = \Skif\Content\Template::factory($template_id);
        $layout_template_file = $template_obj->getLayoutTemplateFilePath();

        echo \Skif\PhpTemplate::renderTemplate(
            $layout_template_file,
            array(
                'content' => $content,
                'title' => $content_type_obj->getName(),
                'keywords' => '',
                'description' => ''
            )
        );
    }

    /**
     * Редактирование материала
     * @param $content_type
     * @param $content_id
     */
    public function editAdminAction($content_type, $content_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            'admin_content_form_edit.tpl.php',
            array('content_id' => $content_id, 'content_type' => $content_type)
        );

        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => 'Редактирование материала',
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array(
                    $content_type_obj->getName() => '/admin/content/' . $content_type
                )
            )
        );
    }

    public function listAdminAction($content_type)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            'admin_content_list.tpl.php',
            array('content_type' => $content_type)
        );

        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => $content_type_obj->getName(),
                'content' => $html,
                'page_title_extra' => '',
                'breadcrumbs_arr' => array()
            )
        );
    }

    public function saveAdminAction($content_type, $content_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        if ($content_id == 'new') {
            $content_obj = new \Skif\Content\Content();
        } else {
            $content_obj = \Skif\Content\Content::factory($content_id);
        }

        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        $title = array_key_exists('title', $_REQUEST) ? $_REQUEST['title'] : '';

        if (!$title){
            \Skif\Messages::setError('Отсутствует заголовок');
            \Skif\Http::redirect('/admin/content/' . $content_type . '/edit/' . $content_id);
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
                $url = $content_obj->generateUrl();
            }

            $url = '/' . ltrim($url, '/');

            $content_type_url_length = strlen($content_type_obj->getUrl());
            if (substr($url, 0, $content_type_url_length+1) != $content_type_obj->getUrl() . '/') {
                $url = $content_type_obj->getUrl() . $url;
            }

            $url = '/' . ltrim($url, '/');

            $content_obj->setUrl($url);
        }

        $content_obj->setIsPublished($is_published);

        $content_obj->save();


        // Рубрики
        $main_rubric_id = !empty($_REQUEST['main_rubric']) ? $_REQUEST['main_rubric'] : null;
        $rubrics_arr = !empty($_REQUEST['rubrics_arr']) ? $_REQUEST['rubrics_arr'] : array();
        $require_main_rubric = \Skif\Conf\ConfWrapper::value('content.' . $content_type_obj->getType() . '.require_main_rubric');

        if (!$main_rubric_id && $require_main_rubric) {
            $main_rubric_id = \Skif\Conf\ConfWrapper::value('content.' . $content_type_obj->getType() . '.main_rubric_default_id');

            if (!$rubrics_arr) {
                $rubrics_arr = array($main_rubric_id);
            }
        }

        if ($rubrics_arr) {
            $content_obj->deleteContentRubrics();

            foreach ($rubrics_arr as $rubric_id) {
                $content_rubrics_obj = new \Skif\Content\ContentRubrics();
                $content_rubrics_obj->setContentId($content_obj->getId());
                $content_rubrics_obj->setRubricId($rubric_id);
                $content_rubrics_obj->save();
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

                \Skif\Messages::setError('Не указана главная рубрика');
                \Skif\Http::redirect('/admin/content/' . $content_type . '/edit/' . $content_obj->getId());
            }
        }

        $content_obj->setMainRubricId($main_rubric_id);
        $content_obj->save();


        // Картинка
        if (array_key_exists('image_file', $_FILES) && !empty($_FILES['image_file']['name'])) {
            $root_images_folder = \Skif\Image\ImageConstants::IMG_ROOT_FOLDER;
            $file = $_FILES['image_file'];
            $file_name = \Skif\Image\ImageController::processUpload($file, 'content/' . $content_type, $root_images_folder);
            $content_obj->setImage($file_name);
            $content_obj->save();
        }


        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect('/admin/content/' . $content_type_obj->getType() . '/edit/' . $content_obj->getId());
    }

    /**
     * Удаление изображения
     * @param $content_id
     * @throws \Exception
     */
    public function deleteImageAction($content_type, $content_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        \Skif\Content\ContentController::deleteImageByContentId($content_id);

        echo 'OK';
    }

    protected static function deleteImageByContentId($content_id)
    {
        $content_obj = \Skif\Content\Content::factory($content_id);
        \Skif\Utils::assert($content_obj);

        $image_manager = new \Skif\Image\ImageManager();
        $image_manager->removeImageFile($content_obj->getImagePath());

        $content_obj->setImage('');
        $content_obj->save();
    }

    public function deleteAction($content_type, $content_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $content_obj = \Skif\Content\Content::factory($content_id);

        \Skif\Content\ContentController::deleteImageByContentId($content_id);

        $content_obj->delete();

        $content_type_obj = \Skif\Content\ContentType::factory($content_obj->getContentTypeId());

        \Skif\Http::redirect('/admin/content/' . $content_type_obj->getType());
    }

    /**
     * Автокомплит для выбора материала
     */
    public static function autoCompleteContentAction()
    {
        $term = array_key_exists('term', $_REQUEST) ? trim($_REQUEST['term']) : '';

        $query_param_arr = array($term .'%');

        $query = "SELECT id FROM content WHERE title LIKE ? LIMIT 20";
        $content_ids_arr = \Skif\DB\DBWrapper::readColumn($query, $query_param_arr);

        $output_arr = array();
        foreach ($content_ids_arr as $content_id) {
            $content_obj = \Skif\Content\Content::factory($content_id);

            $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_obj->getType()));

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