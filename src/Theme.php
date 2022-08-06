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
        'article' => ArticleDrop::class,
        'blog' => BlogDrop::class,
        'collection' => CollectionDrop::class,
        'collection_list' => CollectionListDrop::class,
        'color' => ColorDrop::class,
        'color_background' => 'string',
        'font_picker' => FontDrop::class,
        'html' => 'string',
        'image_picker' => ImageDrop::class,
        'link_list' => LinkListDrop::class, 
        'liquid' => LiquidDrop::class,
        'page' => PageDrop::class,
        'product' => ProductDrop::class,
        'product_list' => ProductListDrop::class,
        'richtext' => 'string',
        'url' => 'string',
        'video_url' => 'string',
    ];

    public $drops = '';


    public function __construct(ThemeCache $cache, $isoCode = 'en.default')
    {
        $this->cache = $cache;
        $this->locale = $isoCode;
        $this->context = new Context(); //创建数据流

        $this->initDrops();
    }

    //设置语言
    public function setLocale($isCode){
        //$this->locale['s'] = $this->cache->get(Theme::PATH_LOCALE, $isoCode);
        //$extends = $this->disk->readJsonFile(Theme::PATH_LOCALE.'/'. $iso_code.'.schema');
    
        //$this->context->registers['locale'] =  array_merge_recursive($default, $extends);
        return $this;
    }

    public function initDrops(){

        $this->drops['content_for_header'] = new Drops\ContentForHeader();
        $this->drops['content_for_layout'] = new Drops\ContentForLayout($this->context);
        $this->drops['families'] = new Drops\FontFamiliesDrop(); 
        $this->drops['settings'] = new Drops\ThemeDrop($this->cache);
    }

    public function renderSection($name, $config = []){

        $sectionFile = $this->cache->get(Theme::PATH_SECTION, $name);
        if(empty($sectionFile)){
            return "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
        }
        $node = $sectionFile['node'];

        $this->context->push();
        $this->context->registers['in_section'] = $name;
        $this->context->set('section', new Drops\ThemeSectionDrop($node, $config));
        $content = $sectionFile['node']->render($this->context);
        $this->context->pop(); 
        unset($this->context->registers['in_section']);

        return $content;
    }

    public function render($template, $data){
        
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



    // public function parseTemplateJson($name){
    //     $config = $this->disk->readJsonFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
    //     $sections = [];
    //     foreach($config['sections'] as $sectionId=>$data){
    //         if(!in_array($sectionId, $config['order'])){
    //             throw new LiquidException("Section id '{$sectionId}' must exist in order");
    //         }else{ 
    //             $data['id'] = $sectionId;
    //             $sections[$sectionId] = $data;
    //         }
    //     }
    //     foreach($config['order'] as $sectionId){
    //         if(!isset($sections[$sectionId])){
    //             throw new LiquidException("Section id '{$sectionId}' must exist in sections");
    //         }
    //     }

    //     if(isset($config['layout'])){
    //         $this->context->registers['layout'] = $config['layout'];
    //     }
    //     return $sections;
    // }


    // public function parseTemplateLiquid($name){
    //     $node = $this->env->parseFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
    //     return $node;
    // }



    //渲染Template
    // public function renderTemplate($name){    

    //     $this->context->registers['sections'] = [];

    //     $data = $this->parseTemplate($name);

    //     if($this->context->register['type'] == 'JSON'){
    //         $content = $this->renderTemplateSections($data['settings']);
    //     }else{
    //         $content = $data->render($this->context);
    //     }

    //     $layout = $this->context->registers['layout']??'theme';
    //     if($layout){
    //         $this->context->set('content_for_layout', new Drops\ContentForLayout($this->context, $content));
    //         $content = $this->parseLayout($layout)->render($this->context);  
    //     }else{
    //         $content = (string)$content;
    //     }
    //     return str_replace('content_for_header', $this->setctionToHeader(), $content);
    // }

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

    // public function renderTemplateLiquid($node){
    //     $contentForLayout = '';
    //     $sections= [];
    //     if($node instanceof \Liquid\Nodes\Block){
    //         $contentForLayout = $node->render($this->context);
    //         $sections = $this->context->registers['sections']; 

    //         unset($this->context->registers['sections']);
    //     }
    //     return ['content'=>$contentForLayout,'layout'=>$layout,'sections'=>$sections] ;
    // }

    // public function renderSection($name, $config = []){


    //     $this->context->push();
    //     $this->context->registers['in_section'] = $name;
    //     $this->context->set('section', new \Drops\ThemeSectionDrop($this, $name, $config));

    //     $node = $this->parseSection($name);
    //     if($node){
    //         $content = $node->render($this->context);
    //     }else{
    //         $content = "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
    //     }
    //     $this->context->pop(); 
    //     unset($this->context->registers['in_section']);

    //     return $content;
    // }

    /**
     * 先通过解析获取存在的sections
     *
     * @return void
     */
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

    // public function renderTemplate($name){    

    //     $type = $this->parseTemplate($name);

    //     if(!($type instanceof \Liquid\Nodes\Document)){
            
    //     }

    //     $contentForLayout = '<!-- BEGIN template -->' .$contentForLayout .'<!-- END template -->';

    //     $this->context->set('content_for_layout',$contentForLayout);
    //     $this->setContentHeader();

    //     $layout = $this->env
    //         ->parse($this->disk->readTemplateSource(ShopifyFileSystem::PATH_LAYOUT."/". $this->layout))
    //         ->render($this->context);  
                
    //     return $layout;
    // }


    // public function renderContentLiquid($template){
    //     return $this->env
    //         ->parse($this->disk->readTemplateSource(ShopifyFileSystem::PATH_TEMPLATE."/". $template))
    //         ->render($this->context);    
    // }

    // public function parseSnippetLiquid($path){
    //     return $this->env
    //         ->parseFile(ShopifyFileSystem::PATH_SNIPPET."/". $path);
    // }

    // public function renderContentJson($template){
    //     $config = $this->disk->readJsonFile(ShopifyFileSystem::PATH_TEMPLATE."/".$template);

    //     //独立分析section
    //     $contentForLayout = '';


    //     foreach($config['order'] as $sectionId){
    //         $section = $config['sections'][$sectionId];
    //         $section['id'] = $sectionId;
    //         try{
    //             $contentForLayout .= $this->renderSectionFile($section['type'], $section);
    //         }catch(LiquidException $e){
    //             $contentForLayout .= 'Liquid error (sections/'.$section['type'].'):'. $e->getMessage() . "\n";
    //         }
    //     }
    //     return $contentForLayout;
    // }

    // public function renderSectionFile($template, $data = []){
	// 	//禁止section 调用section
	// 	if(isset($this->context->registers['in_section']) && !empty($this->context->registers['in_section'])){
	// 		throw new LiquidException(" Cannot render sections '".$template."' inside sections '".$this->context->registers['in_section']."'");
	// 	}

        
    //     $content = null;
    //     $this->context->registers['in_section'] = $template;
    //     try{
    //         $this->env->parse($this->disk->readTemplateSource(ShopifyFileSystem::PATH_SECTION."/". $template));
    //         unset($this->context->registers['in_section']);
    //     }catch(\Liquid\LiquidException $e){
    //         unset($this->context->registers['in_section']);
    //         return $e->getMessage();
    //     }    
        

    //     $schema = $this->context->registers['schema'] ?? [];
    //     unset($this->context->registers['schema']);

    //     $this->context->push();
    //     $this->context->set('section', $data);
    //     try{
    //         $content = $this->env->render($this->context);  
    //     }catch(\Liquid\LiquidException $e){
    //         $content = $e->getMessage();
    //     }
    //     $this->context->pop(); 
        
    //     return $content;
    // }



    public function getContext(){
        return $this->context;
    }

  

}