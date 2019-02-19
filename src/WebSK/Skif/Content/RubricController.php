<?php

namespace WebSK\Skif\Content;

use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\BaseController;
use WebSK\Auth\Auth;
use WebSK\Skif\UniqueUrl;
use WebSK\Utils\Messages;
use WebSK\Skif\SkifPhpRender;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;

/**
 * Class RubricController
 * @package WebSK\Skif\Content
 */
class RubricController extends BaseController
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
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $rubric_id = $requested_id;

        $rubric_obj = Rubric::factory($rubric_id);

        $content_type_obj = ContentType::factory($rubric_obj->getContentTypeId());

        $template_file = 'content_by_rubric_' . $rubric_id . '_list.tpl.php';
        if (!SkifPhpRender::existsTemplateBySkifModuleRelativeToRootSitePath('Content', $template_file)) {
            $template_file = 'content_' . $content_type_obj->getType() . '_by_rubric_list.tpl.php';
        }
        if (!SkifPhpRender::existsTemplateBySkifModuleRelativeToRootSitePath('Content', $template_file)) {
            $template_file = 'content_by_rubric_list.tpl.php';
        }

        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Content',
            $template_file,
            array(
                'rubric_id' => $rubric_id
            )
        );

        $template_id = $rubric_obj->getRelativeTemplateId();

        $layout_template_file = \WebSK\Skif\Content\TemplateUtils::getLayoutFileByTemplateId($template_id);

        echo SkifPhpRender::renderTemplate(
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
        Exits::exit403if(!Auth::currentUserIsAdmin());


        $content_type_obj = ContentType::factoryByFieldsArr(array('type' => $content_type));

        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Content',
            'rubrics_list.tpl.php',
            array('content_type_id' => $content_type_obj->getId())
        );

        $breadcrumbs_arr = array(
            $content_type_obj->getName() => '/admin/content/' . $content_type
        );

        echo SkifPhpRender::renderTemplate(
            ConfWrapper::value('layout.admin'),
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
        Exits::exit403if(!Auth::currentUserIsAdmin());

        $content_type_obj = ContentType::factoryByFieldsArr(array('type' => $content_type));

        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Content',
            'rubric_form_edit.tpl.php',
            array('content_type_id' => $content_type_obj->getId(), 'rubric_id' => $rubric_id)
        );

        $breadcrumbs_arr = array(
            $content_type_obj->getName() => '/admin/content/' . $content_type,
            'Рубрики' => self::getRubricsListUrlByContentType($content_type),
        );

        echo SkifPhpRender::renderTemplate(
            ConfWrapper::value('layout.admin'),
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
        Exits::exit403if(!Auth::currentUserIsAdmin());

        $content_type_obj = ContentType::factoryByFieldsArr(array('type' => $content_type));

        if ($rubric_id == 'new') {
            $rubric_obj = new Rubric();
        } else {
            $rubric_obj = Rubric::factory($rubric_id);
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
                UniqueUrl::getUniqueUrl($url);
            }
        } else {
            $url = $rubric_obj->generateUrl();
            $url = '/' . ltrim($url, '/');
        }

        $rubric_obj->setUrl($url);

        $rubric_obj->save();

        Messages::setMessage('Изменения сохранены');

        Redirects::redirect($rubric_obj->getEditorUrl());
    }

    public function deleteRubricAction($content_type, $rubric_id)
    {
        Exits::exit403if(!Auth::currentUserIsAdmin());

        $rubric_obj = Rubric::factory($rubric_id);

        $message = $rubric_obj->delete();

        if ($message === true) {
            Messages::setMessage('Рубрика ' . $rubric_obj->getName() . ' была успешно удалена');
        } else {
            Messages::setError($message);
        }

        Redirects::redirect(self::getRubricsListUrlByContentType($content_type));
    }
}
