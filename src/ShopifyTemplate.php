<?php 

namespace Ncf\ShopifyLiquid;

use Liquid\Tag\TagDecrement;
use Liquid\Template;
use Ncf\Liquid\Filters\FilterAdditional;
use Illuminate\Support\Str;

class ShopifyTemplate extends Template{

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

        parent::__construct($themePath, $cache);
        $this->setFileSystem($this->fileSystem);

        foreach($this->innerTags as $name => $tag){
            $this->registerTag($name, $tag);
        }

        foreach($this->innerFilters as $filter){
            $this->registerFilter($filter);
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

        $path = $this->fileSystem->templatePath($template);
        $type = Str::endsWith($path,  '.json')?'JSON':'LIQUID';

        $onlineStoreEditorData->set('template.format', $type);    

        if($type == 'json'){
            $setting = json_decode(file_get_contents($path), true);
            $this->setting = $setting;
            $this->layout = $this->setting['layout']??$this->layout;
        }
        
        return $this->parse($this->fileSystem->readTemplateFile($this->layout, 'layout'));
    }

    public function getSectionPresets(){

    }

    /**
     * 解析shopify模板
     *
     * @param [type] $page
     * @return void
     */
    public function render(array $assigns = array(), $filters = null, array $registers = array()) {

        // $this->renderLayout();
        // $this->renderHeader();


		// $context = new Context($assigns, $registers);

		// if (!is_null($filters)) {
		// 	if (is_array($filters)) {
		// 		$this->filters = array_merge($this->filters, $filters);
		// 	} else {
		// 		$this->filters[] = $filters;
		// 	}
		// }

		// foreach ($this->filters as $filter) {
		// 	$context->addFilters($filter);
		// }

		// return $this->root->render($context);
    }

  

}