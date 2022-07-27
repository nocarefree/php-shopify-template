<?php 

namespace Ncf\ShopifyLiquid;

use Illuminate\Support\Arr;
use Liquid\LiquidException;
use Liquid\Context;
use Liquid\Environment;
use Ncf\ShopifyLiquid\Drops\FontDrop;

class ShopifyTemplate{

    private $tags = [
        //Template
        'render'=> Tags\TagRender::class,
        'layout'=> Tags\TagLayout::class,
        'section'=> Tags\TagSection::class,

        //Iteration
        'paginate'=> Tags\TagPaginate::class,

        //Html
        'form'=> Tags\TagForm::class,
        'style'=> Tags\TagStyle::class,
    ];

    private $sectionTags = [
        //Config
        'schema'=> Tags\TagSchema::class,
        
        //section template
        'javascript'=> Tags\TagJavascript::class,
        'stylesheet'=> Tags\TagStylesheet::class,
    ];
    
    private $filters = [
        Filters\FilterArray::class,
        Filters\FilterColor::class,
        Filters\FilterFont::class,
        Filters\FilterHtml::class,
        Filters\FilterMath::class,
        Filters\FilterMedia::class,
        Filters\FilterMetafield::class,
        Filters\FilterMoney::class,
        Filters\FilterString::class,
        Filters\FilterUrl::class,
    ];

    protected $schema;
    protected $config;
    protected $process;
    protected $liquid;
    protected $sectionLiquid;

    protected $sections;
    protected $snippets;


    public function __construct($themePath, $cache = null)
    {
        $this->disk = new ShopifyFileSystem($themePath); //锁定目录
        $this->context = new Context(); //创建数据流

        $this->liquid = new Environment($this->disk); //创建template解析环境
        $this->liquid->variable = Variable::class;

        $this->liquid->registerTags($this->tags);
        foreach($this->filters as $filter){
            $this->liquid->registerFilters($filter);
        }
        $this->liquid->registerFilters(new Filters\FilterAdditional($this));

        $this->sectionLiquid = clone $this->liquid; //创建section解析环境
        $this->sectionLiquid->registerTags($this->sectionTags);

        $this->init();
    }

    public function init(){

        $this->context->registers['app'] = $this;
        $this->context->registers['sections'] = [];

        $this->setLocale('zh-CN');
        $this->setFontFamilies(new Drops\FontFamiliesDrop());

        //获取Theme配置
        $this->schema = $this->disk->readJsonFile("config/settings_schema_data");
        $config = $this->disk->readJsonFile("config/settings_data");
        $this->config = $this->settingsToValue($this->schema, $config['current']??$config['default']);

        //加载所有SECTION
        // $files = $this->disk->getSections();
        // foreach($files as $file){
        //     $this->sections[$file] = $this->parseSection($file);
        // }

        //加载所有SECTION
        // $files = $this->disk->get();
        // foreach($files as $file){
        //     $this->snippets[$file] = $this->parseSnippet($file);
        // }
    }

    //初始化section 参数
    protected function settingsToValue($name, $settings){
        $schema = $this->getSectionSchema($name);
        foreach($schema as $row){
            if(isset($row['settings'])){
                foreach($row['settings'] as $setting){
                    if($setting['type'] == 'font_picker'){
                        $settings[$setting['id']] = new FontDrop($settings[$setting['id']]);
                    }
                }
            }
        }
        return $setting;
    }

    //设置语言
    public function setLocale($iso_code){
        $default = $this->disk->readJsonFile(ShopifyFileSystem::PATH_LOCALE.'/' . $iso_code);
        $extends = $this->disk->readJsonFile(ShopifyFileSystem::PATH_LOCALE.'/'. $iso_code.'.schema');
    
        $this->context->registers['locale'] =  array_merge_recursive($default, $extends);
        return $this;
    }

    //设置字体
    public function setFontFamilies($font_families){
        $this->context->registers['font_families'] = $font_families; 
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

    public function parseTemplate($name){    
        $type = $this->disk->templateType($name);
        if($type == 'JSON'){
            return $this->parseTemplateJson($name);
        }else{
            return $this->liquid->parseFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
        }       
    }

    public function parseLayout($name){ 
        return $this->liquid->parseFile(ShopifyFileSystem::PATH_LAYOUT."/". $name);
    }

    protected function parseSection($name){
        return $this->sectionLiquid->parseFile(ShopifyFileSystem::PATH_SECTION."/". $name);
    }

    protected function parseSnippet($name){
        return $this->sectionLiquid->parseFile(ShopifyFileSystem::PATH_SNIPPET."/". $name);
    }

    /**
     * 解析Template json
     *
     * @param [type] $name
     * @return void
     */
    public function parseTemplateJson($name){
        $config = $this->disk->readJsonFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
        $sections = [];
        foreach($config['sections'] as $sectionId=>$data){
            $sections[$sectionId] = $data;
            if(!in_array($sectionId, $config['order'])){
                $sections[$sectionId] = new LiquidException("Section id '{$sectionId}' must exist in order");
            }else{ 
                // if(isset($this->sections[$name])){
                //     $data['node'] = $this->sections[$name];
                //     $sections[$sectionId] = $data;
                // }else{
                //     $sections[$sectionId] = new LiquidException('Failed to render section "'.$data['type'].'": section file "'.$data['type'].'.liquid" does not exist');
                // }

                try{
                    $data['node'] = $this->parseSection($name);
                    $sections[$sectionId] = $data;
                }catch(FileNoFound){
                    $sections[$sectionId] = new LiquidException('Failed to render section "'.$data['type'].'": section file "'.$data['type'].'.liquid" does not exist');
                }
            }
        }
        foreach($config['order'] as $sectionId){
            if(!isset($sections[$sectionId])){
                $sections[$sectionId] = new LiquidException("Section id '{$sectionId}' must exist in sections");
            }
        }

        if(isset($config['layout'])){
            $this->context->registers['layout'] = $config['layout'];
        }
        return $sections;
    }

    public function parseTemplateLiquid($name){
        $node = $this->liquid->parseFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
        return $node;
    }

    protected function getSectionSchema($name){
        return $this->sections[$name]->options['schema']??[];
    }

    protected function setctionToHeader(){
        return '';
    }

    //渲染Template
    public function renderTemplate($name){    

        $this->context->registers['sections'] = [];

        $data = $this->parseTemplate($name);

        $this->context->set('content_for_header', '#content_for_header#');   

        if(is_array($data)){
            $content = $this->renderTemplateSections($data['settings']);
        }else{
            $content = $data->render($this->context);
        }

        $layout = $this->context->registers['layout']??'theme';
        if($layout){
            $this->context->set('content_for_layout', new ContentForLayout($this->context, $content));
            $content = $this->parseLayout($layout)->render($this->context);  
        }else{
            $content = (string)$content;
        }
        return str_replace('content_for_header', $this->setctionToHeader(), $content);
    }

    public function renderTemplateSections($sections){
        $contentForLayout = '';
        foreach($sections as $section){
            if($section['error']){
                $contentForLayout .= '<!-- Liquid error:  '.$section['error'].' -->';
            }else{
                try{
                    $contentForLayout .= $this->renderSection($section['type'], $section['settings']);
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

    public function renderSection($name, $settings){
        $this->context->push();
        $this->context->registers['in_section'] = $name;
        $this->context->set('section', $this->settingsToValue($name , $settings??null));

        try{
            if(!isset($this->section[$name])){
                $content = "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
            }else{
                $content = $this->sections[$name]->render($this->context);
            }

            
        }catch(\Liquid\LiquidException $e){
            $content = $e->getMessage();
        }
        $this->context->pop(); 
        unset($this->context->registers['in_section']);

        return $content;
    }

    /**
     * 先通过解析获取存在的sections
     *
     * @return void
     */
    public function getContentForHeader(){
        $javascript = false;
        $stylesheet = false;
        if($this->liquid->getRoot() && $this->liquid->getRoot()->options['sections']){
            $sections = $this->liquid->getRoot()->options['sections'];
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

    //     $layout = $this->liquid
    //         ->parse($this->disk->readTemplateSource(ShopifyFileSystem::PATH_LAYOUT."/". $this->layout))
    //         ->render($this->context);  
                
    //     return $layout;
    // }


    // public function renderContentLiquid($template){
    //     return $this->liquid
    //         ->parse($this->disk->readTemplateSource(ShopifyFileSystem::PATH_TEMPLATE."/". $template))
    //         ->render($this->context);    
    // }

    // public function parseSnippetLiquid($path){
    //     return $this->liquid
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
    //         $this->liquid->parse($this->disk->readTemplateSource(ShopifyFileSystem::PATH_SECTION."/". $template));
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
    //         $content = $this->liquid->render($this->context);  
    //     }catch(\Liquid\LiquidException $e){
    //         $content = $e->getMessage();
    //     }
    //     $this->context->pop(); 
        
    //     return $content;
    // }

    public function render($template, $assigns = []) {
        $this->context->merge($assigns);
        return $this->renderTemplate($template);
    }

    public function getContext(){
        return $this->context;
    }

  

}