<?php 

namespace Ncf\ShopifyLiquid;

use Illuminate\Support\Arr;
use Liquid\LiquidException;
use Liquid\Context;
use Liquid\Template;

class ShopifyTemplate{

    const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout'; 
    const PATH_SECTION = 'sections'; 
    const PATH_SNIPPET = 'snippets'; 
    const PATH_LOCALE = 'locales'; 

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


    public function __construct($themePath, $cache = null)
    {
        $this->fileSystem = new ShopifyFileSystem($themePath);
        $this->liquid = new Template($this->fileSystem);
        $this->context = new Context();

        $this->liquid->registerTags($this->tags);
        foreach($this->filters as $filter){
            $this->liquid->registerFilters($filter);
        }
        $this->liquid->registerFilters(new Filters\FilterAdditional($this));

        $this->sectionLiquid = clone $this->liquid;
        $this->sectionLiquid->registerTags($this->sectionTags);
    }

    public function init(){
        $this->schema = $this->fileSystem->readJsonFile("config/settings_schema_data");
        $this->config = $this->fileSystem->readJsonFile("config/settings_data");

    }

    public function setLocale($iso_code){
        $default = $this->fileSystem->readJsonFile(static::PATH_LOCALE.'/' . $iso_code);
        $extends = $this->fileSystem->readJsonFile(static::PATH_LOCALE.'/'.$iso_code.'.schema');
    
        $this->context->registers['locale'] =  array_merge_recursive($default, $extends);
        return $this;
    }

    public function setFontFamilies($font_families){
        $this->context->registers['font_families'] = $font_families; 
    }

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
        $type = $this->fileSystem->templateType($name);
        if($type == 'JSON'){
            return $this->parseTemplateJson($name);
        }else{
            return [ 
                [
                    'node' => $this->liquid->parseFile(STATIC::PATH_TEMPLATE."/". $name)
                ],
            ];
        }       
    }

    public function parseLayout($name){ 
        return $this->liquid->parseFile(STATIC::PATH_LAYOUT."/". $name);
    }

    public function parseSectionFile($name){
        return $this->sectionLiquid->parseFile(STATIC::PATH_SECTION."/". $name);
    }

    public function parseTemplateJson($name){
        $config = $this->fileSystem->readJsonFile(STATIC::PATH_TEMPLATE."/".$name);

        $sections = [];
        foreach($config['sections'] as $sectionId=>$data){
            $sections[$sectionId] = $data;
            if(!in_array($sectionId, $config['order'])){
                $sections[$sectionId]['error'] = "Section id '{$sectionId}' must exist in order";
            }else{ 
                try{
                    $sections[$sectionId]['node'] = $this->parseSectionFile($name);
                }catch(FileNoFound $e){
                    $sections[$sectionId]['error'] = 'Failed to render section "'.$name.'": section file "'.$name.'.liquid" does not exist';
                }
            }
        }

        foreach($config['order'] as $sectionId){
            if(!isset($sections[$sectionId])){
                $sections[$sectionId]['error'] = "Section id 'slideshow2' must exist in sections";
            }
        }
        return $sections;
    }

    protected function settingsToValue($schema, $setting){
        return $setting;
    }

    protected function setctionToHeader($configs){
        return '';
    }

    public function renderTemplate($name){    
        $sections = $this->parseTemplate($name);

        $layout = 'theme';
        $sectionAttributes = [];
        $contentForLayout = '';
        $configs = [];
        
        foreach($sections as $section){
            if($section['error']){
                $contentForLayout .= '<!-- Liquid error:  '.$section['error'].' -->';
            }else{

                foreach($this->sectionTags as $key=>$value){
                    if(isset($section['node'][$key])){
                        $sectionAttributes[$section['type']][$key] = $section['node'];
                    }
                }
                
                $this->context->push();
                $this->context->registers['in_serction'] = $section['type'];
                $this->context->set('section', $this->settingsToValue($section['node']['schema']??[] ,$section['settings']));

                try{
                    $contentForLayout .= $section['node']->render($this->context);
                    
                    if(isset($this->context->registers['layout'])){
                        $layout = $this->context->registers['layout'];
                    }
                }catch(\Liquid\LiquidException $e){
                    $contentForLayout .= $e->getMessage();
                }

                $this->context->pop(); 
                unset($this->context->registers['in_serction']);
                unset($this->context->registers['layout']);
                
            }
        }


        if($layout){
            $contentForLayout = '<!-- BEGIN template -->' .$contentForLayout .'<!-- END template -->';

            $this->context->set('content_for_layout', $contentForLayout);
            $this->context->set('content_for_header', $this->setctionToHeader($configs));

            return $this->parseLayout($layout)->render($this->context);  
        }else{
            return $contentForLayout;
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
    //         ->parse($this->fileSystem->readTemplateSource(STATIC::PATH_LAYOUT."/". $this->layout))
    //         ->render($this->context);  
                
    //     return $layout;
    // }


    // public function renderContentLiquid($template){
    //     return $this->liquid
    //         ->parse($this->fileSystem->readTemplateSource(STATIC::PATH_TEMPLATE."/". $template))
    //         ->render($this->context);    
    // }

    // public function parseSnippetLiquid($path){
    //     return $this->liquid
    //         ->parseFile(STATIC::PATH_SNIPPET."/". $path);
    // }

    // public function renderContentJson($template){
    //     $config = $this->fileSystem->readJsonFile(STATIC::PATH_TEMPLATE."/".$template);

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
    //         $this->liquid->parse($this->fileSystem->readTemplateSource(STATIC::PATH_SECTION."/". $template));
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