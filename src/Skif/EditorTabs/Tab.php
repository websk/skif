<?php

namespace Skif\EditorTabs;

class Tab
{
    use \Skif\Util\ProtectProperties;

    protected $url = '';
    protected $title = '';
    protected $target = '';

    public function __construct($url, $title, $target = ''){
        $this->url = $url;
        $this->title = $title;
        $this->target = $target;
    }

    public function getUrl(){
        return $this->url;
    }
    public function getTitle(){
        return $this->title;
    }

    public function getTarget(){
        return $this->target;
    }
}