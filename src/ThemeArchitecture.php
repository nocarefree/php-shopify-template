<?php

namespace ShopifyTemplate;

use Liquid\Context;
use Liquid\FileSystem;
use Illuminate\Support\Str;
use Seld\JsonLint;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;
use Liquid\Liquid;
use stdClass;

class ThemeArchitecture extends Liquid
{

    protected FileSystem $fileSystem;
    protected Liquid $liquid;
    protected Context $context;
    protected array $structures = [];
    protected array $errors = [];
    protected array $schemaValidator;
    protected array $schemaMap;


    public function __construct()
    {
        parent::__construct();

        $this->registerFilters([
            Filters\FilterArray::class,
            Filters\FilterColor::class,
            Filters\FilterFont::class,
            Filters\FilterHtml::class,
            Filters\FilterMath::class,
            Filters\FilterMedia::class,
            Filters\FilterMetafield::class,
            Filters\FilterMoney::class,
            Filters\FilterString::class,
            Filters\FilterUrl::class
        ]);

        $this->registerTags([
            'form' => Tags\TagForm::class,
            'paginate' => Tags\TagPaginate::class,
            'layout' => Tags\TagLayout::class,
            'render' => Tags\TagRender::class,
            'section' => Tags\TagSection::class,
            'sections' => Tags\TagSections::class,
            'style' => Tags\TagStyle::class,

            'javascript' => Tags\TagJavascript::class,
            'stylesheet' => Tags\TagStylesheet::class,
            'schema' => Tags\TagSchema::class,
        ]);

        $this->schemaMap = include(__DIR__ . '/schema.php');
    }

    public function renderTemplate($name, $data)
    {
        $this->context = new Context($this, $data);
        if (isset($this->structures["templates/$name/.liquid"])) {
            $layout = $this->structures["templates/$name/.liquid"];
        } else {
            $contentForLayout = '';
            $template = $this->structures["templates/$name/.json"] ?? $this->structures["templates/404.json"];

            foreach ($template->order as $sectionId) {
                $contentForLayout .= $this->renderSection($sectionId, $template->sections->{$sectionId});
            }

            $this->context->assign('content_for_layout', $contentForLayout);
            $layout = property_exists($template, 'layout') && isset($this->structures['layout/' . $template->layout . '.liquid']) ?
                $this->structures['layout/' . $template->layout . '.liquid'] : $this->structures['layout/theme.liquid'];
        }
        return $layout->render($this->context);
    }

    public function renderSectionGroup($name)
    {
        if (isset($this->structures["sections/$name/.json"])) {
            $group = $this->structures["sections/$name/.json"];
            $content = '';
            foreach ($group['order'] as $key) {
                $content .= $this->renderSection($key, $group['sections'][$key]);
            }
            return $content;
        } else {
            return '';
        }
    }

    public function renderSection($id, \stdClass $config)
    {
        $name = $config->type;
        $config->id = $id;
        if (isset($this->structures["sections/$name/.liquid"])) {
            $section = $this->structures["sections/$name/.liquid"] ?? '';
            $context = $this->context->push(['section' => new Drops\SectionDrop($config)], true);

            return $section->render($context);
        } else {
            return "Liquid error: Error in tag 'section' - '" . $name . "' is not a valid section type";
        }
    }



    public function addSectionInner()
    {
    }

    public function install($dir)
    {
        $local = new LocalThemeFiles($dir);
        $validator = new ContentValidator($this);

        $data = $local->get();

        foreach ($data as $value => $content) {
            if (Str::endsWith($value, ['.liquid', '.json'])) {
                $validator->validate($value, $content);
                $this->structures[$value] = $content;
            } else {
                $this->structures[$value] = $value;
            }
        }

        if ($validator->errors()) {
            var_dump($validator->errors());
        }

        return $this;
    }

    public function structures()
    {
        return $this->structures;
    }
}
