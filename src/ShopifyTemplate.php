<?php 

namespace Ncf\ShopifyLiquid;

use Liquid\Tag\TagDecrement;
use Liquid\Template;
use Ncf\Liquid\Filters\FilterAdditional;
use Illuminate\Support\Str;

class ShopifyTemplate{

    const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout'; 

    private $onlineStoreEditorData;

    private $innerTags = [
        'decrement'=> Tags\TagDecrement::class,
        'increment'=> Tags\TagIncrement::class,
        'layout'=> Tags\TagLayout::class,
        'paginate'=> Tags\TagPaginate::class,
        'style'=> Tags\TagStyle::class,
        'tablerow'=> Tags\TagTablerow::class,
        'render'=> Tags\TagRender::class,
        'section'=> Tags\TagSection::class,
        'schema'=> Tags\TagSchema::class,
        'form'=> Tags\TagForm::class,


        //section template
        'javascript'=> Tags\TagJavascript::class,
        'stylesheet'=> Tags\TagStylesheet::class,
    ];
    
    private $innerFilters = [
        Filters\FilterAdditional::class,
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
     * 加载shopify用filter tag
     *
     * @param [type] $path
     * @param [type] $cache
     */
    public function __construct($themePath = null, $cache = null)
    {
        $this->fileSystem = new ShopifyFileSystem($themePath);
        $this->liquid = new \Liquid\Template();
        $this->liquid->setFileSystem($this->fileSystem);



        foreach($this->innerTags as $name => $tag){
            $this->liquid->registerTag($name, $tag);
        }

        foreach($this->innerFilters as $filter){
            $this->liquid->registerFilter($filter);
        }

        $this->layout = 'theme';
        $this->sections = [];
    }

    /**
     * 解析shopify模板
     *
     * @param [type] $page
     * @return void
     */
    public function parseTemplate($template){

        $onlineStoreEditorData = new onlineStoreEditorData();
        $onlineStoreEditorData->set('template.type', $template);  
        $this->onlineStoreEditorData = $onlineStoreEditorData;  

        $type = $this->fileSystem->templateType($template);
        $this->onlineStoreEditorData->set('template.format', $type);    

        if($type == 'JSON'){
            $this->parseTemplateJson($template);
        }else{
            $layoutContent =  $this->liquid->parse($this->fileSystem->readTemplateFile(STATIC::PATH_TEMPLATE."/".$template));
        }
        
        return $this->liquid->parse($this->fileSystem->readTemplateFile($this->layout, 'layout'));
    }

    public function parseTemplateJson($template){
        $config =$this->fileSystem->readJson(STATIC::PATH_TEMPLATE."/".$template);
        $this->layout = $config['layout']?:$this->layout;
        $this->liquid->parse($this->fileSystem->readTemplateFile(STATIC::PATH_LAYOUT."/".$this->layout));


        foreach($config['order'] as $sectionId){
            $type = $config['sections'][$sectionId]['type'];

        }
    }


    public function render($template, $assigns) {
        $onlineStoreEditorData = new onlineStoreEditorData();
        
        $this->parseTemplate;

    }

  

}