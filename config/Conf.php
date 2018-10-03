<?php

namespace Skif;

class Conf
{
    public static function get()
    {
        $conf = \Skif\Conf\DefaultConf::get();

        // MySQL
        $conf['db'] = array(
            'host' => 'mysql',
            'db_name' => 'skif',
            'user' => 'root',
            'pass' => 'root'
        );

        // Memcache
        $conf['cache'] = array(
            'servers' => array(
                '0' => array('host' => 'memcached', 'port' => 11211),
            ),
            'expire' => 60
        );

        $conf['site_name'] = 'Скиф';
        $conf['site_url'] = '/skif';
        $conf['site_email'] = 'support@websk.ru';

        $conf['site_path'] = '/var/www/skif/public';
        $conf['log_path'] = '/var/www/log';
        $conf['tmp_path'] = '/var/www/tmp';
        $conf['static_data_path'] = '/var/www/skif/public/static';
        $conf['assets_version'] = 1;

        $conf['salt'] = 'irbis';

        $conf['content'] = array(
            'news' => array('limit_to_page' => 20, 'require_main_rubric' => 1, 'main_rubric_default_id' => 3),
        );

        $conf['comments'] = array(
            'message_to_page' => 20,
            'send_answer_to_email' => true,
        );

        $conf['kcfinder'] = [
            'uploadDir' => '../../../../../public/files'
        ];

        $conf['ckeditor'] = [
            'styles'=> ['/assets/' . $conf['assets_version'] . '/styles/common.css']
        ];

        $conf['skif_url_path'] = '/';

        return $conf;
    }
}
