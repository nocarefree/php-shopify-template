<?php 

namespace Ncf\ShopifyTemplate;

use Illuminate\Support\Str;

class ThemeCache{

    function __construct()
    {
        $this->layouts = [];
        $this->templates = [];
        $this->sections = []; 
        $this->snippets = [];
        $this->locales = [];
        $this->config = [];
        $this->assets = [];
    }


    public function getLayout($name){
        return $this->layouts[$name] ?? null;
    }

    public function getSection($name){
        return $this->layouts[$name] ?? null;
    }

    public function getTemplate($name){
        return $this->layouts[$name] ?? null;
    }

    public function getSnippet($name){
        return $this->layouts[$name] ?? null;
    }

    public function getConfig($name){
        return $this->layouts[$name] ?? null;
    }

    public function getAsset($name){
        return $this->layouts[$name] ?? null;
    }

    public function getLocale($name){
        return $this->layouts[$name] ?? null;
    }


}