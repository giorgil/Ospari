<?php

namespace Ospari\Helper;

Class Theme {

    protected $option;
    protected $defaultContent;
    protected $paginationContent;
    protected $indexContent;
    protected $postContent;

    public function __construct() {
        
    }

    public function getDefaultContent() {
        return $this->defaultContent;
    }

    public function getPaginationContent() {
        return $this->paginationContent;
    }

    public function getIndexContent() {
        return $this->indexContent;
    }

    public function getPostContent() {
        return $this->postContent;
    }

    public function getDefaultPath() {
        return OSPARI_PATH . '/content/themes/simply-pure';
    }

    public function getPath() {
        $setting = \OspariAdmin\Model\Setting::getAsStdObject();
        if (!isset($setting->theme)) {
            $theme = 'simply-pure';
        } else {
            $theme = $setting->theme;
        }
        $themePath = OSPARI_PATH . '/content/themes/' . $theme;

        return $themePath;
    }

    public function prepare() {
        $themePath = $this->getPath();

        $defaultContent = file_get_contents($themePath . '/default.hbs');
        $headers = '<meta name="generator" content="Ospari '.OSPARI_VERSION.'" />';
        $defaultContent = str_replace('{{ghost_head}}', $headers, $defaultContent);
        $defaultContent = str_replace('{{ospari_head}}', $headers, $defaultContent);
        
        $footer = '<script src="//platform.twitter.com/widgets.js"></script>'
                . '<script src="//connect.facebook.net/en_US/all.js#xfbml=1"></script>'
                . '<script src="//apis.google.com/js/plusone.js"></script>';
        
        
        $defaultContent = str_replace('{{ghost_foot}}', $footer, $defaultContent);
        $defaultContent = str_replace('{{ospari_foot}}', $footer, $defaultContent);
        
        $indexContent = file_get_contents($themePath . '/index.hbs');

        $postContent = file_get_contents($themePath . '/post.hbs');

        if (file_exists($themePath . '/partials/pagination.hbs')) {
            $paginationContent = file_get_contents($themePath . '/partials/pagination.hbs');
        } else {
            $paginationContent = file_get_contents($this->getDefaultPath() . '/partials/pagination.hbs');
        }

        //<meta name="generator" content="WordPress 3.5.1" />

        $this->paginationContent = $paginationContent;

        $this->defaultContent = $this->replaceGlobals($defaultContent);
        $this->indexContent = $this->replaceGlobals($indexContent);
        $this->postContent = $this->replaceGlobals($postContent);
    }

    private function replaceGlobals($content) {
        /**
         * replace @blog.something with blog_something
         */
        $content = preg_replace("/@blog\.(.*?)/", "blog_$1", $content);
        $content = str_replace("{{asset", "{{#asset", $content);

        /** replaces alle {{var arg="val"}} with just {{var}} 
         * 
         */
        $content = str_replace('{{#foreach', '{{#each', $content);
        $content = str_replace('{{/foreach}}', '{{/each}}', $content);

        $content = preg_replace_callback("/\{\{([a-z0-9_]+)\s+(.*?)\}\}/ms", function($r) {

            $ra = explode(' ', $r[0]);
            return $ra[0] . '}}';
        }, $content);


        return $content;
    }

}
