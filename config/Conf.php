<?php

namespace Skif;

class Conf
{
    // Базы данных
    const READERS_BASE = 'RDR'; // Читатели
    const ORDERS_BASE = 'RQST'; // БД заказанной литературы
    const TRAINING_BASE = 'BOOK'; // Учебная литература
    const DEFAULT_BASE = 'CATAL'; // Каталог
    const DISSERTATION_BASE = 'DISS'; // Диссертации
    const PERIODIC_BASE = 'PER'; // Периодика    const GOST_BASE = 'GOST'; // Госты
    const PROCEEDINGS_BASE = 'TRUD'; // Госты
    const GRADUATE_WORKS_BASE = 'GRWORKS'; // Выпускные квалификационные работы
    const BOOK_PROVIDING_BASE = 'OBESP'; // Книгообеспеченность

    public static function get()
    {
        $conf = \Skif\Conf\DefaultConf::get();

        // MySQL
        $conf['db'] = array(
            'host' => 'mysql',
            'db_name' => 'cil',
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

        $conf['site_name'] = 'Информационно-библиотечный центр РХТУ им. Д.И. Менделеева';
        $conf['site_url'] = 'lib.muctr.kss';
        $conf['site_domain'] = 'http://lib.muctr.kss';
        $conf['site_email'] = 'abonement@muctr.ru';

        $conf['site_dir'] = '../'; // Путь к файлам сайта от папки admin
        $conf['site_path'] = '/var/www/lib.muctr.ru/public';
        $conf['log_path'] = '/var/www/log';
        $conf['tmp_path'] = '/var/www/tmp';
        $conf['static_data_path'] = '/var/www/lib.muctr.ru/public/static';
        $conf['lib_resources_domain'] = 'http://host.docker.internal:8080';
        $conf['assets_version'] = 105;

        $conf['salt'] = 'irbis';

        $conf['libris'] = array(
            'irbis64' => array(
                'host' => 'host.docker.internal',
                'port' => 5555,
                'login' => 'reader',
                'password' => 'reader',
            ),
            'reader' => array(
                'photo_dir' => '/var/www/lib.muctr.ru/public/files/images/readers',
                'photo_path' => '/files/images/readers',
                'photo_width' => 400,
            ),
            'orders' => array(
                'separate_orders' => true,
                'multiple_orders' => false,
            ),
            'digital_library' => array(
                'source_dir' => '/Users/skulkov/www/elib', // Путь к файлам ЭБ
                'secret_salt' => 'n7D1rUweZeDG4HG8hSji',
                'permission_code_arr' => array(
                    1 => 'только в ИБЦ',
                    2 => 'только с IP адресов РХТУ',
                    3 => 'авторизованным пользователям ЛКЧ, везде',
                    4 => 'в свободном доступе',
                ),
                'allowed_library_ip_mask_arr' => array(
                    '193.232.56.168/29',
                    '192.168.116.0/24',
                    '192.168.66.0/24',
                    '192.168.97.0/24',
                ),
                'allowed_university_ip_mask_arr' => array(
                    '193.232.56.0/23',
                    '192.168.0.0/16',
                    '172.17.5.0/24',
                    '172.16.5.0/24',
                )
            ),
            'delivery_desks' => array(
                'default' => 'Отд. хран.',
                'list_arr' => array(
                    'АУЛ',
                    'Отд. хран.',
                    'АУЛКСК',
                    'ЧЗУЛ',
                    'ЧЗПИ',
                    'ИБЦ ТК'
                ),
                'configs_arr' => array(
                    'АУЛ' => array(
                        'title' => 'Абонемент учебной литературы',
                        'url' => '/abonement-uchebnoy-literatury-ibc-aul-ibc',
                        'max_user_count' => 200, // Количество обслуживаемых читателей по заказам в сутки
                        'books_count' => 7, // Максимальное количество книг на одного читателя в сутки
                        'term' => array('RI', 'RB', 'RS', 'ZD'),
                        'min_book_mhr' => 1,
                        'catalog' => self::DEFAULT_BASE,
                    ),
                    'Отд. хран.' => array(
                        'title' => 'Абонемент научной литературы',
                        'url' => '/abonement-nauchnoy-literatury-anl',
                        'max_user_count' => 80,
                        'books_count' => 10,
                        'term' => array('CI', 'CB', 'CS'),
                        'min_book_mhr' => 1,
                        'min_book_title' => 'Выдается в Читальном зале',
                        'catalog' => self::DEFAULT_BASE,
                    ),
                    'АУЛКСК' => array(
                        'title' => 'Абонемент учебной литературы КСК',
                        'url' => '/abonement-uchebnoy-literatury-ksk-aul-ksk',
                        'max_user_count' => 250,
                        'books_count' => 10,
                        'term' => array('KI', 'KB', 'KS'),
                        'min_book_mhr' => 1,
                        'catalog' => self::DEFAULT_BASE,
                    ),
                    'ЧЗУЛ' => array(
                        'title' => 'Читальный зал учебной литературы',
                        'url' => '/chitalnyy-zal-uchebnoy-literatury',
                        'max_user_count' => 100,
                        'books_count' => 10,
                        'min_book_mhr' => 0,
                    ),
                    'ЧЗПИ' => array(
                        'title' => 'Читальный зал периодических изданий',
                        'url' => '/chitalnyy-zal-spravochno-informacionnyh-izdaniy',
                        'max_user_count' => 100,
                        'books_count' => 10,
                        'min_book_mhr' => 0,
                    ),
                    'ИБЦ ТК' => array(
                        'title' => 'ИБЦ Тушинский  комплекс',
                        'url' => '/chitalnyy-zal-ibc-tushinskogo-kompleksa',
                        'max_user_count' => 100,
                        'books_count' => 10,
                        'min_book_mhr' => 0,
                    )
                ),
            ),
            'databases' => array(
                'default' => self::DEFAULT_BASE,
                'training' => self::TRAINING_BASE,
                'readers' => self::READERS_BASE,
                'orders' => self::ORDERS_BASE,
                'proceedings' => self::PROCEEDINGS_BASE,
                'graduate_works' => self::GRADUATE_WORKS_BASE,
                'configs_arr' => array(
                    'BOOK' => array(
                        'name' => 'Учебная литература',
                        'title' => 'Поиск и заказ по каталогу учебной литературы',
                        'zakaz' => 1,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                            3 => 'NM',
                            10 => 'DISC',
                            12 => 'IN',
                        ),
                        'access' => 3,
                    ),
                    'CATAL' => array(
                        'name' => 'Научный фонд',
                        'title' => 'Поиск и заказ литературы по каталогу научного фонда',
                        'zakaz' => 1,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                            3 => 'NM',
                            10 => 'DISC',
                            12 => 'IN',
                        ),
                        'access' => 3,
                    ),
                    'DISS' => array(
                        'name' => 'Диссертации',
                        'title' => 'Поиск по каталогу диссертаций',
                        'zakaz' => 0,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                            12 => 'IN',
                        ),
                        'access' => 4,
                    ),
                    self::PROCEEDINGS_BASE => array(
                        'name' => 'Труды сотрудников РХТУ',
                        'title' => 'Поиск по БД &laquo;Труды сотрудников РХТУ&raquo;',
                        'zakaz' => 0,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                        ),
                    ),
                    self::GRADUATE_WORKS_BASE => array(
                        'name' => 'Выпускные квалификационные работы',
                        'title' => 'Поиск по БД &laquo;Выпускные квалификационные работы РХТУ&raquo;',
                        'zakaz' => 0,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                            12 => 'IN',
                        ),
                        'access' => 4,
                    ),
                    'ISTOR' => array(
                        'name' => 'История РХТУ',
                        'title' => 'Поиск по БД &laquo;История РХТУ&raquo;',
                        'zakaz' => 0,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                        ),
                    ),
                    'GOST' => array(
                        'name' => 'ГОСТы',
                        'title' => 'Поиск по каталогу ГОСТ',
                        'zakaz' => 0,
                    ),
                    'PER' => array(
                        'name' => 'Периодика',
                        'title' => 'Поиск по каталогу периодики',
                        'zakaz' => 0,
                        'search_type' => array(
                            4 => 'TJ',
                        ),
                    ),
                    'OBESP' => array(
                        'title' => 'Книгообеспеченность',
                        'catal' => 'BOOK',
                        'zakaz' => 0,
                    ),
                    'NIR' => array(
                        'name' => 'Отчеты по научно-исследовательским работам',
                        'title' => 'Отчеты по научно-исследовательским работам (НИР)',
                        'zakaz' => 0,
                        'search_type' => array(
                            1 => 'A',
                            2 => 'T',
                        ),
                    ),
                    'RDR' => array(
                        'name' => 'База данных читателей',
                        'search_type' => array(
                            11 => 'RI',
                        ),
                    ),
                    'CMPL' => array(
                        'name' => 'Комплектование',
                    ),
                ),
            ),
        );

        $conf['edd_email'] = 'edd@muctr.ru';

        $conf['content'] = array(
            'news' => array('limit_to_page' => 20, 'require_main_rubric' => 1, 'main_rubric_default_id' => 3),
        );

        $conf['comments'] = array(
            'message_to_page' => 20,
            'send_answer_to_email' => true,
        );

        $conf['layout']['corp'] = 'layouts/layout.corp.tpl.php';

        $conf['api_apps'] = array(
            '06b91fbcb96f35a680f0c5e67ede9ad9' => array('app_name' => 'muctr'),
        );

        $conf['kcfinder'] = [
            'uploadDir' => '../../../../../public/files'
        ];

        $conf['ckeditor'] = [
            'styles'=> ['/assets/' . $conf['assets_version'] . '/styles/common.css']
        ];

        return $conf;
    }
}
