<?php 

namespace Ncf\ShopifyTemplate;


class ThemeCache{

    public $files;

    function __construct($theme){
        $server = new ThemeInstall($theme);
        $server->run();

        $this->files = $server->getFiles();
    }

    public function getFiles(){
        return $this->files;
    }

    public function getTheme(){
        $this->theme;
    }

    public function get($path, $name){
        foreach($this->files as $file){
            if($file['path'] == $path && $file['name'] == $name){
                return $file;
            }
        }
        return null;
    }


    public function getLayout($name){
        return $this->get(Theme::PATH_LOCALE, $name);
    }

    public function getSection($name){
        return $this->get(Theme::PATH_SECTION, $name);
    }

    public function getTemplate($name){
        return $this->get(Theme::PATH_TEMPLATE, $name);
    }

    public function getSnippet($name){
        return $this->get(Theme::PATH_SNIPPET, $name);
    }

    public function getConfig($name){
        return $this->get(Theme::PATH_CONFIG, $name);
    }

    public function getLocale($name){
        return $this->get(Theme::PATH_LOCALE, $name);
    }


}