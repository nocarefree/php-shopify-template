<?php 

namespace Ncf\ShopifyTemplate;

use Illuminate\Support\Arr;
use Liquid\LiquidException;
use Liquid\Context;

class Theme{

    const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout'; 
    const PATH_SECTION = 'sections'; 
    const PATH_SNIPPET = 'snippets'; 
    const PATH_LOCALE = 'locales'; 
    const PATH_CONFIG = 'config'; 
    const PATH_ASSET = 'assets';


    public $inputSettings = [
        'checkbox' => 'bool',
        'number' => 'int',
        'radio' => 'string',
        'range' => 'int',
        'select' => 'string',
        'text' => 'string',
        'textarea' => 'string',
        'color_background' => 'string',
        'html' => 'string',
        'richtext' => 'string',
        'url' => 'string',
        'video_url' => 'string',

        'article' => ArticleDrop::class,
        'blog' => BlogDrop::class,
        'collection' => CollectionDrop::class,
        'collection_list' => CollectionListDrop::class,
        'color' => ColorDrop::class,
        'font_picker' => FontDrop::class,
        'image_picker' => ImageDrop::class,
        'link_list' => LinkListDrop::class, 
        'liquid' => LiquidDrop::class,
        'page' => PageDrop::class,
        'product' => ProductDrop::class,
        'product_list' => ProductListDrop::class,
    ];

    public $drops = '';


    public function __construct(ThemeCache $cache, $isoCode = 'en.default')
    {
        $this->cache = $cache;
        $this->locale = $isoCode;
        $this->context = new Context(); //创建数据流

        $this->initDrops();

    }

    public function getThemeDrop($name, $args = null){
        if(isset($this->inputSettings[$name])){
            $type = $this->inputSettings[$name]; 
            switch($type){
                case 'string':
                    return (string)$args;
                case 'int':
                    return (int)$args;
                case 'bool':
                    return boolval($args);
                default:
                    return new $type($name);
            }
        }else{
            throw new LiquidException('"'.$name.'" type is invalid');
        }
    }

    //设置语言
    // public function setLocale($isCode){
    //     $this->locale['s'] = $this->cache->get(Theme::PATH_LOCALE, $isoCode);
    //     $extends = $this->disk->readJsonFile(Theme::PATH_LOCALE.'/'. $iso_code.'.schema');
    
    //     $this->context->registers['locale'] =  array_merge_recursive($default, $extends);
    //     return $this;
    // }

    public function initDrops(){
        $schema = $this->cache->get(Theme::PATH_CONFIG,'settings_schema_data');
        $settings = $this->cache->get(Theme::PATH_CONFIG,'settings_data');

        $this->drops['sections'] =  new Drops\SectionsDrop(array_filter($this->files, function($file){
            return $file['path'] == Theme::PATH_SECTION && $file['node'];
        }));

        $this->drops['theme'] = new Drops\ThemeDrop($this, $schema, $settings);
        $this->drops['content_for_header'] = new Drops\ContentForHeader();
        $this->drops['content_for_layout'] = new Drops\ContentForLayout($this->context);
        $this->drops['families'] = new Drops\FontFamiliesDrop(); 
    }

    public function getDrop($name): \Liquid\Models\Drop{
        return $this->drops[$name]?? new Drops\EmptyDrop;
    }

    public function renderSection($config){

        $sectionFile = $this->cache->get(Theme::PATH_SECTION, $config['type']);
        if(empty($sectionFile)){
            return "Liquid error: Error in tag 'section' - ".$config['type']." is not a valid section type";
        }


        $this->context->push();
        $this->context->registers['in_section'] = $config['type'];


        $config['settings'] = $config['settings'] ?? $this->getDrop('theme')->sections[$config['id']];
        $schema = $this->getDrop('sections')->schema[$config['type']];

        $this->context->set('section', new Drops\ThemeSectionDrop($this, $schema, $config));
        $content = $sectionFile['node']->render($this->context);
        $this->context->pop(); 
        unset($this->context->registers['in_section']);

        return $content;
    }

    public function render($template){
        
        $file = $this->cache->get(Theme::PATH_TEMPLATE, $template);
        if(!$file){
            throw new \Liquid\FileNoFound( Theme::PATH_TEMPLATE.'/', $template );
        }

        $content = '';
        $node = $file['node'];
        $layout = 'theme';
        if($file['type'] == 'JSON'){
            foreach($node['order'] as $sectionId){
                if(isset($node['sections'][$sectionId])){
                    $section = $node['sections'][$sectionId];
                    $sectionFile = $this->cache->get(Theme::PATH_SECTION, $section['type']);
                    
                    if(empty($sectionFile)){
                        $content .= "Liquid error: Error in tag 'section' - '".$section['type']."' is not a valid section type";
                    }else{
                        $content .= $sectionFile['node']->render($this->context);
                    }
                }
            }
            if(isset($node['layout'])){
                $layout = $node['layout'];
            }
        }else{
            $content .= $node->render($this->context);
            if(isset($this->context->registers['layout'])){
                $layout = $this->context->registers['layout'];
            }
        }

        $layoutFile = $this->cache->get(Theme::PATH_LAYOUT, $layout);

        if(empty($layoutFile)){
            return $content;
        }

        $header = $this->drops['content_for_header'];
        $this->drops['content_for_layout'] = new Drops\ContentForLayout($this, $content);

        $html = $layoutFile->render($this->context);

        if($header && $header instanceof Drops\ContentForHeader){
            return str_replace((string)$header, $header->toHtml(), $layoutFile->render($this->context));
        }else{
            return $html;
        }
    }

    //翻译
    public function translate($input, $data = []){
        $content = Arr::get($this->context->registers['locale'], $input);
        if(is_array($data)){
            foreach($data as $key=>$value){
                $content = preg_replace("/{{\s*".preg_quote($key,'/')."\s*}}/", $value, $content);
            } 
        }
        return $content;
    }


    public function renderTemplateSections($sections){
        $contentForLayout = '';
        foreach($sections as $section){
            if($section instanceof LiquidException){
                $contentForLayout .= '<!-- Liquid error:  '.$section->getMessage().' -->';
            }else{
                try{
                    $contentForLayout .= $this->renderSection($section);
                }catch(\Liquid\LiquidException $e){
                    $contentForLayout .= $e->getMessage();
                }
            }
        }
        return $contentForLayout;
    }


    public function getContentForHeader(){
        $javascript = false;
        $stylesheet = false;
        if($this->env->getRoot() && $this->env->getRoot()->options['sections']){
            $sections = $this->env->getRoot()->options['sections'];
            foreach($sections as $section=>$v){
                if(isset($this->sections[$v])){
                    if(isset($this->sections[$v]->options['javascript'])){
                        $javascript = true;
                    }

                    if(isset($this->sections[$v]->options['stylesheet'])){
                        $stylesheet = true;
                    }
                }
            }
        }
    }

 



    public function getContext(){
        return $this->context;
    }

  

}