<?php 

namespace Ncf\ShopifyTemplate;

use Liquid\LiquidException;
use Illuminate\Support\Arr;

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

        
        'color' => Drops\ColorDrop::class,
        'font_picker' => Drops\FontDrop::class,
        'image_picker' => Drops\ImageDrop::class,
        'link_list' => Drops\LinkListDrop::class, 
        'liquid' => Drops\LiquidDrop::class,
    ];


    protected $drops = [];

    protected $locale;


    public function __construct(ThemeCache $cache)
    {
        $this->cache = $cache;
        $this->drops = [];
        $this->context = new Context($this); //创建数据流
        $this->translate = new Translate($this);

        $this->initDrops();

    }

    public function cache(){
        return $this->cache;
    }

    public function setIntputDrop($key, $value = null){
        if(!is_array($key)){
            $t = [$key=>$value];
        }else{
            $t = $key;
        }

        foreach($t as $k=>$v){
            $this->inputSettings[$k] = $v;
        }
        return $this;
    }

    // 设置语言
    public function setLocale($isoCode){
        $this->translate->set($isoCode);

        $this->context->setFilters(['t'=> function($input, $data = []){
            return $this->translate->get($input, $data);
        }]);
        return $this;
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
                    return new $type($args);
            }
        }else{
            throw new LiquidException('"'.$name.'" type is invalid');
        }
    }

    public function initDrops(){
        $schemaFile = $this->cache->get(Theme::PATH_CONFIG,'settings_schema');
        $settingsFile = $this->cache->get(Theme::PATH_CONFIG,'settings_data');


        $this->drops['sections'] =  new Drops\SectionsDrop( $this->cache->getFiles(function($file){
            return $file && $file['path'] == Theme::PATH_SECTION && $file['node'];
        }));

        $this->drops['theme'] = new Drops\ThemeDrop($this, $schemaFile['node'], $settingsFile['node']);
        $this->drops['families'] = new Drops\FontFamiliesDrop(); 

    }

    public function getDrop($name): \Liquid\Models\Drop{
        return $this->drops[$name]?? new Drops\EmptyDrop;
    }

    public function renderSection($config){

        $sectionFile = $this->cache->get(Theme::PATH_SECTION, $config['type']);
        if(empty($sectionFile)){
            return "Liquid error: Error in tag 'section' - '".$config['type']."' is not a valid section type";
        }


        $this->context->push();
        $this->context->registers['in_section'] = $config['type'];

        

        $config['settings'] = $config['settings'] ?? ( $this->getDrop('theme')->sections[$config['type']]  ?? [] );

        $schema = $this->getDrop('sections')->schema[$config['type']] ?? [];

        $sectionDrop = new Drops\ThemeSectionDrop($this, $schema, $config);
        $this->context->set('section', $sectionDrop);
        $this->context->registers['sections'][] = $sectionDrop;

        $content = $sectionFile['node']->render($this->context);

        $this->context->pop(); 
        unset($this->context->registers['in_section']);

        return $content;
    }

    public function render($template, $data = []){

        $header = new Drops\ContentForHeader();
        $data['content_for_header'] = $header;
        $data['settings'] = $this->drops['theme'];

        $this->context->setCommon($data);
        $this->context->registers['sections'] = [];
        
        $file = $this->cache->get(Theme::PATH_TEMPLATE, $template);
        if(!$file){
            throw new \Liquid\FileNoFound( Theme::PATH_TEMPLATE.'/' .  $template );
        }

        $content = '';
        $node = $file['node'];
        
        if($file['type'] == 'JSON'){
            $layout = 'theme';
            foreach($node['order'] as $sectionId){
                if(isset($node['sections'][$sectionId])){
                    $section = $node['sections'][$sectionId];
                    $section['id'] = $sectionId;
                    
                    $content .= $this->renderSection($section);
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

        $contentDrop = new Drops\ContentForLayout($content, $this->context);
        $this->context->set('content_for_layout', $contentDrop);

        $layoutFile = $this->cache->get(Theme::PATH_LAYOUT, $layout);
        if(!empty($layoutFile)){
            $content = $layoutFile['node']->render($this->context);
        }else{
            $content = $contentDrop->toHtml();
        }

        return str_replace((string)$header, $header->toHtml(), $content);
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