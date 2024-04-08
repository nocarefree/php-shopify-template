<?php

namespace ShopifyTemplate;

use Exception;
use Liquid\LiquidException;
use Liquid\FileSystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FileAttributes;
use Illuminate\Support\Str;
use Seld\JsonLint;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;

class ThemeArchitecture
{

    protected FileSystem $fileSystem;
    protected array $structures = [];
    protected array $errors = [];
    protected array $schemaValidator;
    protected array $schemaMap;


    public function __construct()
    {

        $this->schemaMap = include(__DIR__ . '/schema.php');
    }

    private function checkFile($path, $content)
    {
        list($type, $name) = explode('/', $path);

        switch ($type) {
            case 'layout':
                if (!preg_match('#{{[\s]?+content_for_header[\s]?+}}#', $content)) {
                    $this->errors[] = ("Missing {{content_for_header}} in the head section of the template");
                }

                if (!preg_match('#{{[\s]?+content_for_layout[\s]?+}}#', $content)) {
                    $this->errors[] = ("Missing {{content_for_layout}} in the content section of the template");
                }
                break;
            case 'sections':

                if (Str::endsWith($path, '.liquid')) {
                } else if (Str::endsWith($path, '.json')) {
                    $res = $this->jsonDecode($content);
                    if ($res['error']) {
                        $this->errors[] = $res['error'];
                    } else {
                        $this->errors[] = [...$this->verifyTemplateSchema($res['data'])];
                    }
                }
                break;
            case 'templates':
                if (Str::endsWith($path, '.liquid')) {
                } else if (Str::endsWith($path, '.json')) {
                    $res = $this->jsonDecode($content);
                    if ($res['error']) {
                        $this->errors[] = $res['error'];
                    } else {
                        $this->errors[] = [...$this->verifySectionGroupSchema($res['data'])];
                    }
                }
                break;
            case 'config':

                $res = $this->jsonDecode($content);
                if ($res['error']) {
                    $this->errors[] = $res['error'];
                } else {
                    $this->errors[] = [...$this->verifySectionGroupSchema($res['data'])];
                }
                break;
        }
    }

    public function loadLocalFiles($dir)
    {
        $this->fileSystem = new FileSystem(new LocalFilesystemAdapter($dir));

        $this->structures = [
            ...$this->getAssets(),
            ...$this->getConfig(),
            ...$this->getLayout(),
            ...$this->getLocales(),
            ...$this->getSections(),
            ...$this->getSnippets(),
            ...$this->getTemplates(),
        ];

        foreach ($this->structures as $value) {
            $this->checkFile($value, $this->fileSystem->read($value));
        }
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
                //$content = $this->fileSystem->read($path);
                //$this->checkFile($path, $content);
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

                //$content = $this->fileSystem->read($path);
                //$this->checkFile($path, $content);
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

                //$content = $this->fileSystem->read($path);
                //$this->checkFile($path, $content);
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
        $error = '';
        if (json_last_error()) {
            $error = $this->verifyJson($content);
        }
        return ['data' => $json, 'error' => $error];
    }





    public function verifyJson($content)
    {
        $error = 'JSON unknow error';
        $parser = new JsonLint\JsonParser();
        try {
            $parser->parse($content, JsonLint\JsonParser::DETECT_KEY_CONFLICTS);
        } catch (JsonLint\DuplicateKeyException $e) {
            $details = $e->getDetails();
            $error = "Invalid JSON: unexpected token '" . $details['key'] . "' at line '" . $details['line'] . "', column 7";
        }
        return $error;
    }

    private function verifySectionOrder($data)
    {
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


    public function verifySectionGroupSchema(object $data): array
    {
        $errors = [];
        $validator = new Validator;
        $validator->validate($data, $this->schemaMap['sectionGroup']);

        if (!$validator->isValid()) {
            foreach ($validator->getErrors() as $error) {
                $errors[] = printf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        if ($errors) {
            return $errors;
        }

        return $this->verifySectionOrderSchema($data);
    }

    public function verifyTemplateSchema(object $data): array
    {
        $errors = [];
        $validator = new Validator;
        $validator->validate($data, $this->schemaMap['template']);

        if (!$validator->isValid()) {
            foreach ($validator->getErrors() as $error) {
                $errors[] = printf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        if ($errors) {
            return $errors;
        }

        return $this->verifySectionOrderSchema($data);
    }

    public function hasSection($id)
    {
        return in_array("sections/$id.liquid", $this->structures);
    }
}
