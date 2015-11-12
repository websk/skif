<?php

namespace Skif\Content;


class RubricController
{
    public function listRubricsAction($content_type)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        $content_type_obj = \Skif\Content\ContentTypeFactory::loadContentTypeByType($content_type);

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            'rubrics_list.tpl.php',
            array($content_type_obj->getId())
        );

        $breadcrumbs_arr = array(
            $content_type_obj->getName() => '/admin/content/' . $content_type
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'content' => $content,
                'title' => 'Список рубрик',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function editRubricAction($content_type, $rubric_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Content',
            'rubric_form_edit.tpl.php',
            array('rubric_id' => $rubric_id)
        );

        $content_type_obj = \Skif\Content\ContentTypeFactory::loadContentTypeByType($content_type);

        $breadcrumbs_arr = array(
            $content_type_obj->getName() => '/admin/content/' . $content_type,
            'Рубрики' => '/admin/content/' . $content_type . '/rubrics',
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
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
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        $content_type_obj = \Skif\Content\ContentTypeFactory::loadContentTypeByType($content_type);

        if ($rubric_id == 'new') {
            $rubric_obj = new \Skif\Content\Rubric();
        } else {
            $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);
        }

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';
        $template_id = array_key_exists('template_id', $_REQUEST) ? $_REQUEST['template_id'] : '';

        $rubric_obj->setName($name);
        $rubric_obj->setComment($comment);
        $rubric_obj->setTemplateId($template_id);
        $rubric_obj->setContentTypeId($content_type_obj->getId());
        $rubric_obj->save();

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect('/admin/content/' . $content_type . '/rubrics');
    }

    public function deleteRubricAction($content_type, $rubric_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);

        $message = $rubric_obj->delete();

        if ($message === true) {
            \Skif\Messages::setMessage('Рубрика ' . $rubric_obj->getName() . ' была успешно удалена');
        } else {
            \Skif\Messages::setError($message);
        }

        \Skif\Http::redirect('/admin/content/' . $content_type . '/rubrics');
    }

}