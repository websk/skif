<?php

namespace Skif\Redirect;


class ControllerRedirect {

	public function listAction()
	{
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

		$redirect_objs_arr = \Skif\DB\DBWrapper::readObjects(
			"SELECT * FROM redirect_rewrites ORDER BY id"
		);

		$html = \Skif\PhpTemplate::renderTemplateBySkifModule('Redirect', 'list.tpl.php', array(
			'redirect_objs_arr' => $redirect_objs_arr
			)
		);

		echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
			'title' => "Список Redirect",
			'content' => $html
			)
		);
	}

	public function addAction()
	{
		// Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());
		
		$html = \Skif\PhpTemplate::renderTemplateBySkifModule('Redirect', 'add.tpl.php', array());

		$html .= \Skif\PhpTemplate::renderTemplateBySkifModule('Redirect', 'helper_text.tpl.php', array());
		
		echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
			'title' => "Добавить Redirect",
			'content' => $html,
			'breadcrumbs_arr' => array('Redirect' => '/admin/redirect/list')
			)
		);
	}

	public function editAction()
	{
		// Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

		$redirect_id = \Skif\Utils::url_arg(3);

		if($redirect_id == '')
		{
			\Skif\Http::exit404();
		}

		$redirect_objs_arr = \Skif\DB\DBWrapper::readObject(
			"SELECT * FROM redirect_rewrites WHERE id = ?",
            array($redirect_id)
		);

		$html = \Skif\PhpTemplate::renderTemplateBySkifModule('Redirect', 'edit.tpl.php', array(
			'redirect_objs_arr' => $redirect_objs_arr
			)
		);
		
		$html .= \Skif\PhpTemplate::renderTemplateBySkifModule('Redirect', 'helper_text.tpl.php', array());
		
		echo \Skif\PhpTemplate::renderTemplate(\Skif\Conf\ConfWrapper::value('layout.admin'), array(
			'title' => 'Редактирование <small>redirect #' . $redirect_id . '</small>',
			'content' => $html,
			'breadcrumbs_arr' => array('Redirect' => '/admin/redirect/list')
			)
		);
	}

	public function saveAction()
	{
		if(isset($_POST['action']) && $_POST['action']=='addredirect')
		{
			$kind = $_POST['kind'];
			$code = $_POST['code'];
			$src = $_POST['src'];

            $dst = '';
            if(array_key_exists('dst', $_POST)){
			    $dst = $_POST['dst'];
            }

			if($kind == '' || $code == '' || $src == '')
			{
				throw new \Exception('redirect add error');
			}

			\Skif\DB\DBWrapper::query(
				"INSERT INTO redirect_rewrites (src, dst, code, kind) values (?, ?, ?, ?)",
				array($src, $dst, $code, $kind)
			);

            if ($kind == 2) {
                $cache_key = $this->getCacheKeyRegexpRedirectArr();
                \Skif\Cache\CacheWrapper::delete($cache_key);
            }
		}
		
		else if(isset($_POST['action']) && $_POST['action']=='saveredirect')
		{
			$id = $_POST['id'];
			$kind = $_POST['kind'];
			$code = $_POST['code'];
			$src = $_POST['src'];

            $dst = '';
            if(array_key_exists('dst', $_POST)){
                $dst = $_POST['dst'];
            }

			if($id == '' || $kind == '' || $code == '' || $src == '')
			{
				throw new \Exception('redirect save error');
			}

			\Skif\DB\DBWrapper::query(
				"UPDATE redirect_rewrites SET src=?, dst=?, code=?, kind=? WHERE id=?",
				array($src, $dst, $code, $kind, $id)
			);

            if ($kind == 2) {
                $cache_key = $this->getCacheKeyRegexpRedirectArr();
                \Skif\Cache\CacheWrapper::delete($cache_key);
            }
		}

		else
		{
			throw new \Exception('redirect save error');
		}

		\Skif\Http::redirect('/admin/redirect/list');
	}

	public function deleteAction()
	{
		// Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

		if(isset($_REQUEST['action']) && $_REQUEST['action']=='deleteredirect')
		{
			$id = $_REQUEST['redirect_id'];

			if($id == '') 
			{
				throw new \Exception('redirect add error');
			}
	
			\Skif\DB\DBWrapper::query(
				"DELETE FROM redirect_rewrites WHERE id=(?)",
				array($id)
			);

            $cache_key = $this->getCacheKeyRegexpRedirectArr();
            \Skif\Cache\CacheWrapper::delete($cache_key);
		}

		\Skif\Http::redirect('/admin/redirect/list');
	}

    public function redirectAction()
    {
        $uri = rawurldecode($_SERVER['REQUEST_URI']);
        $exact_uri = $uri;

        //
        // CHECK FOR "STRING" REDIRECT PRESENCE
        //
        $exact_redirect_stdobj_arr = \Skif\DB\DBWrapper::readObjects(
            "SELECT dst, code FROM redirect_rewrites WHERE src = ? AND kind = 1",
            array($exact_uri)
        );

        if (!empty($exact_redirect_stdobj_arr)) {
            $exact_redirect_stdobj = array_shift($exact_redirect_stdobj_arr);
            $http_response_code = $exact_redirect_stdobj->code ? $exact_redirect_stdobj->code : 301;
            header('Location: ' .\Skif\UrlManager::appendLeadingSlash($exact_redirect_stdobj->dst) , true, intval($http_response_code));
            exit();
        }

        //
        // CHECK FOR "REGEXP" REDIRECT PRESENCE
        //
        $cache_key = $this->getCacheKeyRegexpRedirectArr();

        $regexp_redirect_stdobj_arr = \Skif\Cache\CacheWrapper::get($cache_key);

        if ($regexp_redirect_stdobj_arr == false) {
            $regexp_redirect_stdobj_arr = \Skif\DB\DBWrapper::readObjects(
                "SELECT src, dst, code FROM redirect_rewrites WHERE kind = 2 ORDER BY id"
            );
            \Skif\Cache\CacheWrapper::set($cache_key, $regexp_redirect_stdobj_arr, 3600);
        }

        foreach ($regexp_redirect_stdobj_arr as $regexp_redirect_stdobj) {
            $matches = array();

            if (preg_match($regexp_redirect_stdobj->src, $uri, $matches)) {

                $dst = $regexp_redirect_stdobj->dst;
                foreach ($matches as $match_k => $match_val) {
                    $dst = str_replace('$' . $match_k, $match_val, $dst);
                }

                if ($regexp_redirect_stdobj->code != "") {
                    header('Location: '. \Skif\UrlManager::appendLeadingSlash($dst), true, intval($regexp_redirect_stdobj->code));
                    exit();
                }
            }
        }

        return \Skif\UrlManager::CONTINUE_ROUTING;
    }

    private function getCacheKeyRegexpRedirectArr()
    {
        return "regexp_redirect_stdobj_arr";
    }
}
