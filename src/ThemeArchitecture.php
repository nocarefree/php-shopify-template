<?php

namespace ShopifyTemplate;

use Liquid\Context;
use Liquid\FileSystem;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Seld\JsonLint;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;
use Liquid\Liquid;
use Liquid\Nodes\Document;
use stdClass;

class ThemeArchitecture extends Liquid
{

    protected FileSystem $fileSystem;
    protected Liquid $liquid;
    protected Context $context;
    protected array $files = [];
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

        if ($file = $this->file("templates/$name.liquid")) {
            $layout = $file['value'];
        } else {
            $file = $this->file("templates/$name.json", "templates/404.json");
            $contentForLayout = '';
            if ($file) {
                $template = $file['value'];

                foreach ($template->order as $sectionId) {
                    $contentForLayout .= $this->renderSection($template->sections->{$sectionId}, $sectionId);
                }
            }

            $layoutName = property_exists($template, 'layout') ? $template->layout : 'theme';
            $layout = $this->file("layout/$layoutName.liquid");
        }
        return $layout->render($this->context);
    }

    public function renderSectionGroup($name)
    {
        if ($file = $this->file("sections/$name/.json")) {
            $group = $file['value'];
            $content = '';
            var_dump($group);
            foreach ($group['order'] as $key) {
                $content .= $this->renderSection($group->sections->{$key}, $key);
            }
            return $content;
        } else {
            return '';
        }
    }

    public function renderSection($config, $id): string
    {
        $name = $config->type;
        $config->id = $id;

        $file = $this->file("sections/$name.liquid");
        if ($file) {
            if ($file['value'] instanceof Document) {
                $context = $this->context->clone(['section' => new Drops\SectionDrop($config)]);
                return $file['value']->render($context);
            } else {
                var_dump($file);
                return $file['value'];
            }
        } else {
            return "Liquid error: Error in tag 'section' - '" . $name . "' is not a valid section type";
        }
    }

    public function renderSnippet($name, $args = [])
    {
        if ($file = $this->file("snippets/$name.liquid")) {
            $context = $this->context->clone($args);
            return $file['value']->render($context);
        } else {
            return "Liquid error: Could not find snippets/$name.liquid";
        }
    }

    public function addSectionInner()
    {
    }

    public function install($dir)
    {
        $local = new LocalThemeFiles($dir);
        $validator = new ContentValidator($this);

        $this->files = $local->get();




        foreach ($this->files as $index => $file) {
            if (Str::endsWith($file['key'], ['.liquid', '.json'])) {
                $validator->validate($file['key'], $file['value']);
                $this->files[$index]['value'] = $file['value'];
            }
        }

        if ($validator->errors()) {
            var_dump($validator->errors());
        }

        return $this;
    }

    public function file($name, $default = null)
    {
        return Arr::first($this->files, function ($v) use ($name) {
            return $v['key'] == $name;
        }, $default ? Arr::first($this->files, function ($v) use ($default) {
            return $v['key'] == $default;
        }, null) : null);
    }
}
