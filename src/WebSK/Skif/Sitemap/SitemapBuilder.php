<?php

namespace WebSK\Skif\Sitemap;

use WebSK\Slim\ConfWrapper;

/**
 * Class SitemapBuilder
 * @package WebSK\Skif\Sitemap
 */
class SitemapBuilder implements InterfaceSitemapBuilder
{
    const XML_URL_STEP = 100;
    const XML_URL_LIMIT = 2500;

    const STORAGE_TIME_IN_DAYS = 2;

    protected $data_path = null;
    protected $sitemap_root = null;
    protected $sitemap_build_time = null;
    protected $current_file_number = 1;
    protected $current_url_number = 1;
    protected $current_file_name = null;

    protected $xml_files_arr = array();

    /**
     * @var null|\XMLWriter
     */
    protected $writer = null;

    public function __construct()
    {
        $this->data_path = ConfWrapper::value('static_data_path');
        $this->sitemap_root = ConfWrapper::value('sitemap.root');
        $this->sitemap_build_time = time();

        if (!is_dir($this->data_path)) {
            throw new \Exception('No data directory');
        }

        if (!is_dir($this->data_path . $this->sitemap_root . '/' . $this->sitemap_build_time)) {
            mkdir($this->data_path . $this->sitemap_root . '/' . $this->sitemap_build_time, 0777, true);
        }
    }

    /**
     * @param $url
     * @param string $freq
     */
    public function add($url, $freq = 'never')
    {
        if ($this->current_file_name === null) {
            $this->startFile();
        }

        $this->writer->startElement('url');
        $this->writer->writeElement('loc', $url);
        $this->writer->writeElement('changefreq', $freq);
        $this->writer->endElement();

        $this->current_url_number++;

        if ($this->current_url_number % self::XML_URL_STEP == 0) {
            $this->appendToFile();
        }

        if ($this->current_url_number >= self::XML_URL_LIMIT) {
            $this->endFile();
            $this->current_url_number = 1;
        }
    }

    public function finish()
    {
        if ($this->current_file_name !== null) {
            $this->endFile();
        }

        $this->createIndexFile();
    }

    protected function createIndexFile()
    {
        $current_domain = ConfWrapper::value('site_domain');

        $this->writer = new \XMLWriter();
        $this->writer->openMemory();
        $this->writer->setIndent(true);
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->startElement('sitemapindex');
        $this->writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->xml_files_arr as $i => $xml_file_name) {
            $xml_file_url = $current_domain . '/sitemap-' . $this->sitemap_build_time . '-' . ($i + 1) . '.xml';
            $this->writer->startElement('sitemap');
            $this->writer->writeElement('loc', $xml_file_url);
            $this->writer->writeElement('lastmod', date('Y-m-d\TH:i:sP', $this->sitemap_build_time));
            $this->writer->endElement();
        }

        $this->writer->endElement();

        file_put_contents(
            $this->data_path . $this->sitemap_root . '/sitemap.xml',
            $this->writer->flush(true)
        );

        $this->writer = null;
    }

    protected function startFile()
    {
        if ($this->current_file_name !== null) {
            return;
        }

        $this->current_file_name =
            $this->sitemap_root . '/' . $this->sitemap_build_time . '/' . $this->current_file_number . '.xml';

        file_put_contents($this->data_path . $this->current_file_name, '');

        $this->writer = new \XMLWriter();
        $this->writer->openMemory();
        $this->writer->setIndent(true);
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->startElement('urlset');
        $this->writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    protected function appendToFile()
    {
        file_put_contents(
            $this->data_path . $this->current_file_name,
            $this->writer->flush(true),
            FILE_APPEND
        );
    }

    protected function endFile()
    {
        if ($this->current_file_name === null) {
            return;
        }

        $this->writer->endElement();
        $this->appendToFile();

        $this->xml_files_arr[] = $this->current_file_name;
        $this->current_file_name = null;
        $this->current_file_number++;

        $this->writer = null;
    }

    public function log($controller_name)
    {
        echo "[" . date('H:i:s') . "] Building controller " . $controller_name . "\n";
    }

    public static function removeOldSitemapFiles()
    {
        $time = time();
        $data_path = ConfWrapper::value('static_data_path');
        $sitemap_root = ConfWrapper::value('sitemap.root');

        $dir_arr = glob($data_path . $sitemap_root . '/*', GLOB_ONLYDIR);
        foreach ($dir_arr as $dir_name) {
            $dir_time = preg_replace('@.*sitemap/(\d+)$@', '$1', $dir_name);
            $dir_create_time_diff = $time - intval($dir_time);
            if ($dir_create_time_diff > (self::STORAGE_TIME_IN_DAYS * 24 * 60 * 60)) {
                $files_arr = glob($dir_name . '/*');
                foreach ($files_arr as $file) {
                    unlink($file);
                }

                rmdir($dir_name);
            }
        }
    }
}
