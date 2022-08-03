<?php 

namespace Ncf\ShopifyTemplate;

class ThemeInstall{

    function __construct($theme)
    {
        $this->theme;   
    }

    protected function checkLayout(){
        $this->theme->parseLayout();
    }

    protected function checkSections(){
        
    }

    protected function checkTemplates(){
        
    }

    protected function checkSnippets(){
        
    }

    protected function checkConfig(){
        
    }
}