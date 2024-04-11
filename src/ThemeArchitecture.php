<?php

namespace ShopifyTemplate;

use Exception;
use Liquid\Context;
use Liquid\FileSystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FileAttributes;
use Illuminate\Support\Str;
use Seld\JsonLint;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;
use Liquid\Liquid;
use stdClass;

class ThemeArchitecture
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
        $this->liquid = new Liquid();

        $this->liquid->registerFilters([
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

        $this->liquid->registerTags([
            'form' => Tags\TagForm::class,
            'paginate' => Tags\TagPaginate::class,
            'layout' => Tags\TagLayout::class,
            'render' => Tags\TagRender::class,
            'section' => Tags\TagSection::class,
            'style' => Tags\TagStyle::class,

            'javascript' => Tags\TagJavascript::class,
            'stylesheet' => Tags\TagStylesheet::class,
            'schema' => Tags\TagSchema::class,
        ]);

        $this->schemaMap = include(__DIR__ . '/schema.php');
    }

    public function render($name, $data)
    {
        $this->context = new Context($this->liquid, $data);
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

        $layout->render($this->context);
    }

    private function renderSectionGroup($group)
    {
        $content = [];
        foreach ($group['order'] as $key) {
            $content[] = $this->renderSection($key, $group['sections'][$key]);
        }
        return implode("", $content);
    }

    private function renderSection($id, \stdClass $config)
    {
        $name = $config->type;
        $config->id = $id;
        if (isset($this->structures["sections/$name/.liquid"])) {
            $section = $this->structures["sections/$name/.liquid"] ?? '';
            $context = $this->context->push(['section' => new Drops\SectionDrop($config)], true);

            return $section->render($context);
        } else if (isset($this->structures["sections/$name/.json"])) {
            return $this->renderSectionGroup($this->structures["sections/$name/.json"]);
        } else {
            return "Liquid error: Error in tag 'section' - '" . $name . "' is not a valid section type";
        }
    }


    public function checkFile($path, &$content)
    {
        $errors = [];
        list($type, $name) = explode('/', $path);
        if (Str::endsWith($path, '.liquid')) {
            switch ($type) {
                case 'layout':
                    if (!preg_match('#{{[\s]?+content_for_header[\s]?+}}#', $content)) {
                        $errors[] = ("Missing {{content_for_header}} in the head section of the template");
                    }
                    if (!preg_match('#{{[\s]?+content_for_layout[\s]?+}}#', $content)) {
                        $errors[] = ("Missing {{content_for_layout}} in the content section of the template");
                    }
                    break;
                default:
                    break;
            }

            try {
                $document = $this->liquid->parse($content);

                if ($type == 'sections') {
                    foreach ($document->nodes as $node) {
                        if (!$node) {
                            var_dump($content, $document->nodes, $node);
                            exit;
                        }

                        if (is_object($node) && in_array($node->name, ['javascript', 'stylesheet', 'schema'])) {
                            $this->addSectionInner($node->name, (string)$node);
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = $e;
            }
        } else {
            try {
                $data = $this->jsonDecode($content);
                $this->verifyJsonSchema($data, $type);

                if (empty($errors) && in_array($type, ['sections', 'templates'])) {
                    $this->verifySectionOrderSchema($data);
                }
                $content = $data;
            } catch (ContentVerifyException $e) {
                $errors = $e->errors();
            }
        }
        return $errors;
    }

    public function loadLocalFiles($dir)
    {
        $this->fileSystem = new FileSystem(new LocalFilesystemAdapter($dir));

        $files = array_merge(
            $this->getAssets(),
            $this->getConfig(),
            $this->getLayout(),
            $this->getLocales(),
            $this->getSections(),
            $this->getSnippets(),
            $this->getTemplates()
        );

        foreach ($files as $value) {
            if (Str::endsWith($value, ['.liquid', '.json'])) {
                $content = $this->fileSystem->read($value);
                $this->checkFile($value, $content);
                $this->structures[$value] = $content;
            } else {
                $this->structures[$value] = $value;
            }
        }

        $this->liquid->setCache($this->structures);

        return $this;
    }

    public function addSectionInner($name, $content)
    {
    }

    private function getAssets()
    {
        $files = $this->fileSystem->listContents('assets', 1);

        $data = [];
        foreach ($files as $file) {
            if ($file instanceof FileAttributes) {
                $mimeType = $file->mimeType();
                if ($mimeType) {
                    if (Str::startsWith($mimeType, ['image/', 'font/'])) {
                        $data[] = $file->path();
                    }
                } else {
                    if (Str::endsWith($file->path(), ['.ttf', '.eot', '.woff', '.woff2', '.css', '.scss', '.js', '.json', '.liquid'])) {
                        $data[] = $file->path();
                    }
                }
            }
        }

        array_filter($data, function ($name) use ($data) {
            if (Str::endsWith($name, ['.css', '.js'])) {
                return !in_array($name . '.liquid', $data);
            }
            return true;
        });

        return $data;
    }

    private function getConfig()
    {
        $data = [];
        if ($this->fileSystem->fileExists('config/settings_schema.json')) {
            $data[] = 'config/settings_schema.json';
        } else {
            $this->errors[] = "Theme settings no found";
        }
        if ($this->fileSystem->fileExists('config/settings_data.json')) {
            $data[] = 'config/settings_data.json';
        } else {
            $this->errors[] = "Theme settings area of the theme editor.";
        }
        return $data;
    }

    private function getLayout()
    {
        $data = [];
        $files = $this->fileSystem->listContents('layout', 1);
        foreach ($files as $file) {
            $path = $file->path();
            if ($file instanceof FileAttributes && Str::endsWith($path, '.liquid')) {
                $data[] = $file->path();
            }
        }
        if (!in_array('layout/theme.liquid', $data)) {
            $this->errors[] = "The default layout file, which must be included in all themes, is theme.liquid.";
        }

        return $data;
    }

    private function getTemplates()
    {
        $data = [];
        $files = $this->fileSystem->listContents('templates', 1);
        foreach ($files as $file) {
            $path = $file->path();
            $fileName = basename($path);
            if (
                $file instanceof FileAttributes &&
                Str::endsWith($fileName, ['.liquid', '.json']) &&
                Str::startsWith($fileName, ['404.', 'article.', 'blog.', 'cart.', 'collection.', 'gift_card.', 'index.', 'list-conllections.', 'page.', 'password.', 'product.', 'search.'])
            ) {
                $data[] = $file->path();
            }
        }
        $files = $this->fileSystem->listContents('templates/customers', 1);
        foreach ($files as $file) {
            $path = $file->path();
            if (
                $file instanceof FileAttributes &&
                Str::endsWith($fileName, ['.liquid', '.json']) &&
                Str::startsWith($fileName, ['account.', 'activate_account.', 'addresses.', 'login.', 'order.', 'register.', 'reset_password.'])
            ) {
                $data[] = $file->path();
            }
        }
        return $data;
    }

    private function getLocales()
    {
        $data = [];
        $files = $this->fileSystem->listContents('locales', 1);
        $default = 0;
        $defaultSchema = 0;
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), '.json')) {
                if (Str::endsWith($file->path(), '.default.json')) {
                    $default++;
                }

                if (Str::endsWith($file->path(), '.default.schema.json')) {
                    $defaultSchema++;
                }

                $data[] = $file->path();
            }
        }
        if ($default == 0 || $default > 1) {
            $this->errors[] = "The only one default locale file";
        }

        if ($defaultSchema == 0 || $defaultSchema > 1) {
            $this->errors[] = "The only one default locale.schema file";
        }
        return $data;
    }

    private function getSections()
    {
        $data = [];
        $files = $this->fileSystem->listContents('sections', 1);
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), ['.json', '.liquid'])) {
                $data[] = $file->path();
            }
        }
        return $data;
    }

    private function getSnippets()
    {
        $data = [];
        $files = $this->fileSystem->listContents('layout', 1);
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), '.liquid')) {
                $data[] = $file->path();
            }
        }
        return $data;
    }

    private function jsonDecode($content)
    {
        $json = @json_decode($content);
        if (json_last_error()) {
            $this->verifyJson($content);
        }
        return !empty($json) ? $json : [];
    }


    public function verifyJson($content)
    {
        $error = 'JSON unknow error';
        $parser = new JsonLint\JsonParser();
        try {
            $parser->parse($content, JsonLint\JsonParser::DETECT_KEY_CONFLICTS);
        } catch (JsonLint\DuplicateKeyException $e) {
            $details = $e->getDetails();
            throw new ContentVerifyException("Invalid JSON: unexpected token '" . $details['key'] . "' at line '" . $details['line'] . "', column 7");
        }
        return $error;
    }


    public function verifySectionOrderSchema(object $data): array
    {
        $errors = [];
        foreach ($data->sections as $key => $value) {
            if (!property_exists($value, 'type')) {
                $errors[] = "Section id '$key' is missing a type field";
            } else {
                $sections[] = $value->type;
            }

            if (!in_array($key, $data->order)) {
                $errors[] = "Section id '$key' must exist in order";
            }
        }

        foreach ($data->order as $value) {
            if (!property_exists($data->sections, $value)) {
                $errors[] = "Section id '$value' must exist in sections";
            }
        }

        foreach ($sections as $id) {
            if (!$this->hasSection($id)) {
                $errors[] = "Section type '$id' does not refer to an existing section file";
            }
        }

        return $errors;
    }


    public function verifyJsonSchema(object | array $data, $type): array
    {

        $errors = [];
        $schema = $this->schemaMap[$type] ?? false;

        if ($schema) {

            $validator = new Validator;
            $validator->validate($data, $schema);

            if (!$validator->isValid()) {
                foreach ($validator->getErrors() as $error) {
                    $errors[] = printf("[%s] %s\n", $error['property'], $error['message']);
                }
            }
        }
        return $errors;
    }

    public function hasSection($id)
    {
        return in_array("sections/$id.liquid", $this->structures);
    }
}
