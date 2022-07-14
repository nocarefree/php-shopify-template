<?php 

namespace Ncf\ShopifyLiquid;


use Liquid\LiquidException;
use Liquid\Context;

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


    /**
     * 默认样式
     *
     * @var string
     */
	private $layout;

    /**
     * 默认模块
     *
     * @var [object]
     */
	private $sections;
    
    /**
     * 默认语言
     *
     * @var [type]
     */
    private $locale;

  

    public function __construct($themePath = null, $cache = null)
    {
        $this->fileSystem = new ShopifyFileSystem($themePath);
        $this->liquid = new \Liquid\Template($this->fileSystem);

        $this->liquid->registerTags($this->tags);
        foreach($this->filters as $filter){
            $this->liquid->registerFilters($filter);
        }

        $add = new Filters\FilterAdditional($this);
        $this->liquid->registerFilters($add);
        

        $this->layout = 'theme'; 
        $this->sections = [];
    }

    /**
     * 解析shopify模板
     *
     * @param [type] $page
     * @return void
     */
    public function renderTemplate($template){    

        $type = $this->fileSystem->templateType($template);
        // $this->onlineStoreEditorData->set('template.type', $template);  
        // $this->onlineStoreEditorData->set('template.format', $type);    

        if($type == 'JSON'){
            $contentForLayout = $this->renderContentJson($template);
        }else{
            $contentForLayout = $this->renderContentLiquid($template);
        }

        $contentForLayout = '<!-- BEGIN template -->' .$contentForLayout .'<!-- END template -->';

        $this->context->set('content_for_layout', $contentForLayout);

        $this->log('layout', $this->layout);

        $layout = $this->liquid
            ->parse($this->fileSystem->readTemplateSource(STATIC::PATH_LAYOUT."/". $this->layout))
            ->render($this->context);  
                
        
        return $layout;
    }

    public function renderContentLiquid($template){
        $this->log('template', $template);
        return $this->liquid
            ->parse($this->fileSystem->readTemplateSource(STATIC::PATH_TEMPLATE."/". $template))
            ->render($this->context);    
    }

    public function parseSnippetLiquid($path){
        return $this->liquid
            ->parseFile(STATIC::PATH_SNIPPET."/". $path);
    }

    public function renderSnippetLiquid($template){
        $this->log('snippet', $template);

        $this->context->push();
        $this->context->merge($data);
        $content =  $this->liquid
            ->parse($this->fileSystem->readTemplateSource(STATIC::PATH_SNIPPET."/". $template))
            ->render($this->context);  
            $this->context->pop();    
        return $content; 
    }

    public function renderContentJson($template){
        $config = $this->fileSystem->readJsonFile(STATIC::PATH_TEMPLATE."/".$template);

        //独立分析section
        $contentForLayout = '';
        foreach($config['order'] as $sectionId){
            $section = $config['sections'][$sectionId];
            $section['id'] = $sectionId;
            try{
                $contentForLayout .= $this->renderSectionFile($section['type'], ['section'=> $section]);
            }catch(LiquidException $e){
                $contentForLayout .= 'Liquid error (sections/'.$section['type'].'):'. $e->getMessage() . "\n";
            }
        }
        return $contentForLayout;
    }

    public function renderSectionFile($template, $data = []){
        $this->log('section', $template);

		//禁止section 调用section
		if(isset($this->context->registers['in_section']) && !empty($this->context->registers['in_section'])){
			throw new LiquidException(" Cannot render sections '".$template."' inside sections '".$this->context->registers['in_section']."'");
		}

        $this->context->push();
        $this->context->merge($data);
        $this->context->registers['in_section'] = $template;

        try{
            $content =  $this->liquid
                ->parse($this->fileSystem->readTemplateSource(STATIC::PATH_SECTION."/". $template))
                ->render($this->context);  
                
        }catch(\Liquid\LiquidException $e){
            $content = $e->getMessage();
        }

        unset($this->context->registers['in_section']);
        $this->context->pop();    
        
        return $content;
    }


    public function render($template, $assigns = []) {

        $this->config = $this->fileSystem->readJsonFile("config/settings_data");
        $this->context = new Context($assigns, ['settings'=>$this->config['current']]);

        return $this->renderTemplate($template);

    }

    public function getAssigns(){
		return $this->assigns;
	}

    public function log($type, $file){
        $this->logs[] = [$type, $file];
    }

    
	public function getLocale(){
		if(!$this->locale){
            $default = $this->fileSystem->readJsonFile(static::PATH_LOCALE.'/zh-CN');
            $extends = $this->fileSystem->readJsonFile(static::PATH_LOCALE.'/zh-CN.schema');
        
			$this->locale =  array_merge_recursive($default, $extends);
		}
        return $this->locale;
	}
    

  

}