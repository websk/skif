<?php

namespace Skif\Content;


use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;
use WebSK\Utils\Url;

class RubricController extends \Skif\BaseController
{
    protected $url_table = "rubrics";

    public static function getRubricsListUrlByContentType($content_type)
    {
        return '/admin/content/' . $content_type . '/rubrics';
    }

    /**
     * Список материалов в рубрике
     * @return string
     * @throws \Exception
     */
    public function listAction()
    {
        $requested_id = $this->getRequestedId();

        if (!$requested_id) {
            return \Skif\UrlManager::CONTINUE_ROUTING;
        }

        $rubric_id = $requested_id;

        $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);

        $content_type_obj = \Skif\Content\ContentType::factory($rubric_obj->getContentTypeId());

        $template_file = 'content_by_rubric_' . $rubric_id . '_list.tpl.php';
        if (!\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', $template_file)) {
            $template_file = 'content_' . $content_type_obj->getType() . '_by_rubric_list.tpl.php';
        }
        if (!\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', $template_file)) {
            $template_file = 'content_by_rubric_list.tpl.php';
        }

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            $template_file,
            array(
                'rubric_id' => $rubric_id
            )
        );

        $template_id = $rubric_obj->getRelativeTemplateId();

        $layout_template_file = \Skif\Content\TemplateUtils::getLayoutFileByTemplateId($template_id);

        echo \Skif\PhpTemplate::renderTemplate(
            $layout_template_file,
            array(
                'content' => $content,
                'title' => $rubric_obj->getName(),
                'keywords' => '',
                'description' => ''
            )
        );
    }

    public function listAdminRubricsAction($content_type)
    {
        Exits::exit403if(!\WebSK\Skif\Auth\Auth::currentUserIsAdmin());


        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            'rubrics_list.tpl.php',
            array('content_type_id' => $content_type_obj->getId())
        );

        $breadcrumbs_arr = array(
            $content_type_obj->getName() => '/admin/content/' . $content_type
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \WebSK\Slim\ConfWrapper::value('layout.admin'),
            array(
                'content' => $content,
                'title' => 'Рубрики',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function editRubricAction($content_type, $rubric_id)
    {
        Exits::exit403if(!\WebSK\Skif\Auth\Auth::currentUserIsAdmin());


        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            'rubric_form_edit.tpl.php',
            array('content_type_id' => $content_type_obj->getId(), 'rubric_id' => $rubric_id)
        );

        $breadcrumbs_arr = array(
            $content_type_obj->getName() => '/admin/content/' . $content_type,
            'Рубрики' => \Skif\Content\RubricController::getRubricsListUrlByContentType($content_type),
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \WebSK\Slim\ConfWrapper::value('layout.admin'),
            array(
                'content' => $content,
                'title' => 'Редактирование рубрики',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function saveRubricAction($content_type, $rubric_id)
    {
        Exits::exit403if(!\WebSK\Skif\Auth\Auth::currentUserIsAdmin());


        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        if ($rubric_id == 'new') {
            $rubric_obj = new \Skif\Content\Rubric();
        } else {
            $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);
        }

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';
        $template_id = array_key_exists('template_id', $_REQUEST) ? $_REQUEST['template_id'] : '';
        $url = array_key_exists('url', $_REQUEST) ? $_REQUEST['url'] : '';

        $rubric_obj->setName($name);
        $rubric_obj->setComment($comment);
        $rubric_obj->setTemplateId($template_id);
        $rubric_obj->setContentTypeId($content_type_obj->getId());

        if ($url) {
            $url = '/' . ltrim($url, '/');

            if ($url != $rubric_obj->getUrl()) {
                Url::getUniqueUrl($url);
            }
        } else {
            $url = $rubric_obj->generateUrl();
            $url = '/' . ltrim($url, '/');
        }

        $rubric_obj->setUrl($url);

        $rubric_obj->save();

        \Websk\Skif\Messages::setMessage('Изменения сохранены');

        Redirects::redirect($rubric_obj->getEditorUrl());
    }

    public function deleteRubricAction($content_type, $rubric_id)
    {
        Exits::exit403if(!\WebSK\Skif\Auth\Auth::currentUserIsAdmin());


        $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);

        $message = $rubric_obj->delete();

        if ($message === true) {
            \Websk\Skif\Messages::setMessage('Рубрика ' . $rubric_obj->getName() . ' была успешно удалена');
        } else {
            \Websk\Skif\Messages::setError($message);
        }

        Redirects::redirect(\Skif\Content\RubricController::getRubricsListUrlByContentType($content_type));
    }

}