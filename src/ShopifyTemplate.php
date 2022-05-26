<?php 

namespace Ncf\ShopifyLiquid;

use Liquid\Tag\TagDecrement;
use Liquid\Template;
use Ncf\Liquid\Filters\FilterAdditional;
use Illuminate\Support\Str;
use Liquid\LiquidException;

class ShopifyTemplate{

    const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout'; 
    const PATH_SECTION = 'sections'; 

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
    public function renderTemplate($template){    

        $type = $this->fileSystem->templateType($template);
        $this->onlineStoreEditorData->set('template.type', $template);  
        $this->onlineStoreEditorData->set('template.format', $type);    

        if($type == 'JSON'){
            $contentForLayout = $this->renderContentJson($template);
        }else{
            $contentForLayout = $this->renderContentLiquid($template);
        }
        return $contentForLayout;
    }


    public function renderContentLiquid($template){
        $context = new Context($this, $this->assigns);
        $this->assigns['content_for_layout'] .= $this->liquid
            ->parse($this->fileSystem->readTemplateFile(STATIC::PATH_TEMPLATE."/". $template))
            ->render($context);    
    }

    public function renderContentJson($template){
        $config = $this->fileSystem->readJsonFile(STATIC::PATH_TEMPLATE."/".$template);
        $this->onlineStoreEditorData->set('layout', $config['layout']??'');

        //独立分析section
        $contentForLayout = '';
        foreach($config['order'] as $sectionId){
            $section = $config['sections'][$sectionId];
            $assigns = $this->assigns;
            $section['id'] = $sectionId;
            $assigns['section'] = $section;
            $context = new Context($this, $assigns);
            try{
                $this->onlineStoreEditorData->set('in_section', $section['type']);
                $this->liquid->parse($this->fileSystem->readTemplateFile(STATIC::PATH_SECTION."/". $section['type']));
                file_put_contents('1.txt', var_export($this->liquid->getRoot()->getNodelist(), true));

                $html = $this->liquid->render($context);
                
                var_dump($html);exit;
                $contentForLayout .= $html;
            }catch(LiquidException $e){
                $contentForLayout .= '';
            }
        }
        $this->onlineStoreEditorData->set('in_section', false);
        return $contentForLayout;
    }


    public function render($template, $assigns = []) {
        $this->assigns = $assigns;
        $this->onlineStoreEditorData = new \Illuminate\Config\Repository();
        return $this->renderTemplate($template);

    }

  

}