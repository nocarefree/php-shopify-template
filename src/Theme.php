<?php 

namespace Ncf\ShopifyTemplate;

use Illuminate\Support\Arr;
use Liquid\LiquidException;
use Liquid\Context;
use Liquid\Environment;
use Ncf\ShopifyTemplate\Drops\FontDrop;

class Theme{

    const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout'; 
    const PATH_SECTION = 'sections'; 
    const PATH_SNIPPET = 'snippets'; 
    const PATH_LOCALE = 'locales'; 
    const PATH_CONFIG = 'config'; 
    const PATH_ASSET = 'assets';

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
    protected $sectionEnv;

    protected $sections;
    protected $snippets;


    public function __construct($themePath, $cache = null)
    {
        $this->disk = new ShopifyFileSystem($themePath); //锁定目录
        $this->context = new Context(); //创建数据流

        $this->env = new Environment($this->disk); //创建template解析环境
        $this->env->variable = Variable::class;

        $this->env->registerTags($this->tags);
        foreach($this->filters as $filter){
            $this->env->registerFilters($filter);
        }
        $this->env->registerFilters(new Filters\FilterAdditional($this));

        $this->sectionEnv = clone $this->env; //创建section解析环境
        $this->sectionEnv->registerTags($this->sectionTags);

        $this->installTheme();
    }

    public function getFileSystem(){
        return $this->disk;
    }

    public function installTheme(){
        $install = new ThemeInstall($this);
        $install->run();
    }


    

    public function init(){
        
        $this->context->registers['app'] = $this;
        $this->context->registers['sections'] = [];

        $this->context->set('content_for_header', new Drops\ContentForHeader($this)); 

        $this->setLocale('zh-CN');
        $this->setFontFamilies(new Drops\FontFamiliesDrop());

 
    }

    public function initData(){
        
        $common['settings'] = new Drops\ThemeDrop(
            $this->disk->readJsonFile("config/settings_schema_data"), 
            $this->disk->readJsonFile("config/settings_data")
        );
        // $common['request'] = new RequestDrop();
    }

    public function getSectionSetting(){
        return [];
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
        $this->context->register['type'] = $this->disk->templateType($name);
        if($this->context->register['type'] == 'JSON'){
            return $this->parseTemplateJson($name);
        }else{
            return $this->env->parseFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
        }       
    }

    public function parseLayout($name){ 
        return $this->env->parseFile(ShopifyFileSystem::PATH_LAYOUT."/". $name);
    }

    protected function parseSection($name){
        if(!isset($this->sections[$name])){
            $node = $this->sectionEnv->parseFile(ShopifyFileSystem::PATH_SECTION."/". $name);
            if($node instanceof \Liquid\Nodes\Document){
                $this->sections[$name]['node'] = $node;
                foreach($node->getNodelist() as $sub){
                    foreach($this->sectionTags as $tag){
                        if(is_object($sub) && $sub instanceof $tag){
                            $this->sections[$name][$tag] = $sub->options['content'];
                            break;
                        }
                    }
                }
            }else{
                $this->sections[$name] = null;
            }
        }
        return $this->sections[$name];
    }

    protected function parseSnippet($name){
        return $this->sectionEnv->parseFile(ShopifyFileSystem::PATH_SNIPPET."/". $name);
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
            if(!in_array($sectionId, $config['order'])){
                throw new LiquidException("Section id '{$sectionId}' must exist in order");
            }else{ 
                $data['id'] = $sectionId;
                $sections[$sectionId] = $data;
            }
        }
        foreach($config['order'] as $sectionId){
            if(!isset($sections[$sectionId])){
                throw new LiquidException("Section id '{$sectionId}' must exist in sections");
            }
        }

        if(isset($config['layout'])){
            $this->context->registers['layout'] = $config['layout'];
        }
        return $sections;
    }


    public function parseTemplateLiquid($name){
        $node = $this->env->parseFile(ShopifyFileSystem::PATH_TEMPLATE."/".$name);
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

        if($this->context->register['type'] == 'JSON'){
            $content = $this->renderTemplateSections($data['settings']);
        }else{
            $content = $data->render($this->context);
        }

        $layout = $this->context->registers['layout']??'theme';
        if($layout){
            $this->context->set('content_for_layout', new Drops\ContentForLayout($this->context, $content));
            $content = $this->parseLayout($layout)->render($this->context);  
        }else{
            $content = (string)$content;
        }
        return str_replace('content_for_header', $this->setctionToHeader(), $content);
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

    public function renderSection($name, $config = []){


        $this->context->push();
        $this->context->registers['in_section'] = $name;
        $this->context->set('section', new \Drops\ThemeSectionDrop($this, $name, $config));

        $node = $this->parseSection($name);
        if($node){
            $content = $node->render($this->context);
        }else{
            $content = "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
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

    public function render($template, $assigns = []) {
        $this->context->merge($assigns);
        return $this->renderTemplate($template);
    }

    public function getContext(){
        return $this->context;
    }

  

}