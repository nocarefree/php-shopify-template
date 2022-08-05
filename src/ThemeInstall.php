<?php 

namespace Ncf\ShopifyTemplate;

use Illuminate\Support\Str;

class ThemeInstall{

    function __construct($path)
    {
        $this->disk = new FileSystem($path); 
        $this->cache = new ThemeCache();

        $this->layouts = [];
        $this->templates = [];
        $this->sections = []; 
        $this->snippets = [];
        $this->locales = [];
        $this->config = [];
        $this->assets = [];
    }

    protected function getTypeName($file, $path){
        if(Str::startsWith($file, $path.'/')){
            return Str::after($file, $path.'/');
        }
        return null;
    }

    protected function getLayout($file){
        if($name = $this->getTypeName($file, Theme::PATH_LAYOUT)){
            if(preg_match('/^([\w\._\-\s]+)\.liquid$/', $name, $matches)){
                $this->layouts[$matches[1]] = $name;
                return true;
            }
        }
        return null;
    }

    protected function getSection($file){
        if($name = $this->getTypeName($file, Theme::PATH_SECTION)){
            if(preg_match('/^([\w\._\-\s]+)\.liquid$/', $name, $matches)){
                $this->sections[$matches[1]] = $name;
                return true;
            }
        }
        return null;
    }

    protected function getTemplate($file){
        if($name = $this->getTypeName($file, Theme::PATH_TEMPLATE)){
            if(
                preg_match('/^([\w\._\-\s]+)\.(liquid|json)$/', $name, $matches) 
                || preg_match('/^(customers\/[\w\._\-\s]+)\.(liquid|json)$/', $name, $matches)
            ){
                if(!isset($this->templates[$matches[1]])){
                    $this->templates[$matches[1]] = $name;
                    return true;
                }
            }
        }
        return null;
    }

    protected function getSnippet($file){
        if($name = $this->getTypeName($file, Theme::PATH_SNIPPET)){
            if(preg_match('/^([\w\._\-\s]+)\.liquid$/', $name, $matches)){
                $this->snippets[$matches[1]] = $name;
                return true;
            }
        }
        return null;
    }

    protected function getConfig($file){
        if( Theme::PATH_CONFIG .'/settings_data.json' == $file ){
            $this->config['data'] =  $file;
        }else if(Theme::PATH_CONFIG .'/settings_schema.json' == $file ){
            $this->config['schema'] =  $file;
        }else{
            return null;
        }
        return true;
    }

    protected function getAsset($file){
        if($name = $this->getTypeName($file, Theme::PATH_ASSET)){
            if(preg_match('/^[\w\._\-\s]+\.(js|css|jpg|jpeg|gif|png|json|woff)$/', $name)){
                $this->assets[$name] = $name;
                return true;
            }
        }
        return null;
    }

    protected function getLocale($file){
        if($name = $this->getTypeName($file, Theme::PATH_LOCALE)){
            if(preg_match('/^([\w\._\-\s]+)\.json$/', $name, $matches)){
                $this->locales[$matches[1]] = $name;
                return true;
            }
        }
        return null;
    }

    public function run() {

        $files = $this->disk->getAllFiles('',3);
        $checkFunctions = ['layout','section','template','snippet','config','locale','asset'];

        foreach($files as $key=>$file){
            foreach($checkFunctions as $fun){
                if($this->{'get'.ucfirst($fun)}($file)){
                    unset($files[$key]);
                    break;
                }
            }
        }
	}
}