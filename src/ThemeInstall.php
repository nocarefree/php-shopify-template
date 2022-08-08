<?php 

namespace Ncf\ShopifyTemplate;

use Illuminate\Support\Str;
use Liquid\LiquidException;

class ThemeInstall{

    function __construct($path)
    {
        $this->disk = new FileSystem($path); 

        $this->env = new Envs\BaseEnv;
        $this->sectionEnv = new Envs\sectionEnv;
        $this->layoutEnv = new Envs\layoutEnv;

        $this->env->debug = true;
        $this->sectionEnv->debug = true;
        $this->layoutEnv->debug = true;

        $this->files = [];
    }

    protected function getTypeName($file, $path){
        if(Str::startsWith($file, $path.'/')){
            return Str::after($file, $path.'/');
        }
        return null;
    }

    protected function getLayout($file){
        if(preg_match('/^'.Theme::PATH_LAYOUT.'\/([\w\._\-\s]+)\.liquid$/', $file, $matches)){
            $name = $matches[1];
            $content = $this->disk->get($file);
            try{
                $this->files[] = [
                    'name' => $name,
                    'path' => Theme::PATH_LAYOUT,
                    'content' => $content,
                    'node' => $this->layoutEnv->parse($content),
                ];
                return true;
            }catch(LiquidException $e){
                return null;
            }
        }
        return null;
    }

    protected function getSection($file){
        if(preg_match('/^'.Theme::PATH_SECTION.'\/([\w\._\-\s]+)\.liquid$/', $file, $matches)){
            $name = $matches[1];
            $content = $this->disk->get($file);
            try{
                $this->files[] = [
                    'name' => $name,
                    'path' => Theme::PATH_SECTION,
                    'content' => $content,
                    'node' => $this->sectionEnv->parse($content),
                ];
                return true;
            }catch(LiquidException $e){
                return null;
            }
        }
        return null;
    }

    protected function getTemplate($file){
        if(
            preg_match('/^'.Theme::PATH_TEMPLATE.'\/([\w\._\-\s]+)\.(liquid|json)$/', $file, $matches) 
            || preg_match('/^'.Theme::PATH_TEMPLATE.'\/(customers\/[\w\._\-\s]+)\.(liquid|json)$/', $file, $matches)
        ){
            $name = $matches[1];
            $content = $this->disk->get($file);

            try{

                if($matches[2] == 'json'){
                    $node = json_decode($content, true);
                }else{
                    $node = $this->env->parse($content);
                }

                $this->files[] = [
                    'name' => $name,
                    'type' => strtoupper($matches[2]),
                    'path' => Theme::PATH_TEMPLATE,
                    'content' => $content,
                    'node' => $node,
                ];
                return true;
            }catch(LiquidException $e){
                return null;
            }
        }
        return null;
    }

    protected function getSnippet($file){
        if(preg_match('/^'.Theme::PATH_SNIPPET.'\/([\w\._\-\s]+)\.liquid$/', $file, $matches)){
            $name = $matches[1];
            $content = $this->disk->get($file);
            try{
                $this->files[] = [
                    'name' => $name,
                    'path' => Theme::PATH_SNIPPET,
                    'content' => $content,
                    'node' => $this->sectionEnv->parse($content),
                ];
                return true;
            }catch(LiquidException $e){
                return null;
            }
        }
        return null;
    }

    protected function getConfig($file){
        if( preg_match('/^'.Theme::PATH_CONFIG .'\/(settings_data|settings_schema)\.json$/',$file, $matches) ){
            $name = $matches[1];
            $content = $this->disk->get($file);
            try{
                $this->files[] = [
                    'name' => $name,
                    'path' => Theme::PATH_CONFIG,
                    'content' => $content,
                    'node' => json_decode($content, true),
                ];
                return true;
            }catch(LiquidException $e){
                return null;
            }
            return null;
        }
        return null;
    }

    protected function getLocale($file){
        if( preg_match('/^'.Theme::PATH_LOCALE .'\/([\w\._\-\s]+)\.json$/',$file, $matches) ){
            $name = $matches[1];
            $content = $this->disk->get($file);
            try{
                $this->files[] = [
                    'name' => $name,
                    'path' => Theme::PATH_LOCALE,
                    'content' => $content,
                    'node' => json_decode($content, true),
                ];
                return true;
            }catch(LiquidException $e){
                return null;
            }
            return null;
        }
        return null;
    }

    protected function getAsset($file){
        if(preg_match('/^'.Theme::PATH_ASSET.'\/[\w\._\-\s]+\.(js|css|jpg|jpeg|gif|png|json|woff)$/', $file)){
            $this->files[] = [
                'name' => $file,
                'path' => Theme::PATH_ASSET,
                'content' => $this->disk->get($file),
            ];
            return true;
        }
        return null;
    }

    public function run() {
        $this->files = [];

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

    public function getFiles(){
        return $this->files;
    }
}