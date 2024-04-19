<?php

namespace ShopifyTemplate;

use Illuminate\Support\Str;

use JsonSchema\Validator;
use JsonSchema\SchemaStorage;
use JsonSchema\Constraints\Factory;
use Seld\JsonLint;
use ShopifyTemplate\Tags\TagSchema;

class ContentValidator
{
    protected $errors = [];
    protected $env = [];
    protected $schemaMap;
    protected $schemaValidator;

    public function __construct(Theme $env)
    {
        $this->env = $env;
        $this->schemaMap = json_decode(file_get_contents(dirname(__DIR__) . "/assets/json/schemas.json"));

        $schemaStorage = new SchemaStorage();

        foreach ($this->schemaMap as $key => $value) {
            $schemaStorage->addSchema('json://theme.com/schemas/' . $key, $value);
        }

        $this->schemaValidator = new Validator(new Factory($schemaStorage));

        // Provide $schemaStorage to the Validator so that references c   
    }

    public function validate($path, $content)
    {
        $errors = [];
        list($type) = explode('/', $path);
        if (Str::endsWith($path, '.liquid')) {
            if ($type == 'layout') {
                if (!preg_match('#{{[\s]?+content_for_header[\s]?+}}#', $content)) {
                    $errors[] = ("Missing {{content_for_header}} in the head section of the template");
                }
                if (!preg_match('#{{[\s]?+content_for_layout[\s]?+}}#', $content)) {
                    $errors[] = ("Missing {{content_for_layout}} in the content section of the template");
                }
                if ($errors) {
                    return $errors;
                }
            }

            $stream = new \Liquid\TokenStream($this->env, $content);
            $content = $stream->parse();

            if ($stream->syntaxError()->fails()) {
                $errors = $stream->syntaxError()->errors();
            }

            if (in_array($type, ['sections'])) {
                $schema = null;
                foreach ($content->nodes as $node) {
                    if ($node instanceof TagSchema) {
                        $schema = (string)$node;
                    }
                }

                if ($schema) {
                    $errors = $this->verifyJson($schema, 'sectionSchema');
                }
            }
        } else {
            $errors = $this->verifyJson($content, $path);
        }

        return $errors;
    }

    public function verifyJson($data, $type = '')
    {

        $data = $this->jsonDecode($data);

        if (Str::startsWith($type, 'sections')) {
            $type = 'sectionGroups';
        } else if (Str::startsWith($type, 'templates')) {
            $type = 'sections';
        } else if ($type == 'config/settings_schema.json') {
            $type = 'themeSettingsSchema';
        }


        $errors = $this->verifyJsonSchema($data, $type);

        if ($errors) {
            return $errors;
        }

        if ($type == 'sections' || $type == 'sectionGroups') {
            $errors = array_merge($errors, $this->verifySectionOrderSchema($data));
        }
        return $errors;
    }

    private function jsonDecode($content)
    {
        $json = @json_decode($content);
        if (json_last_error()) {
            $parser = new JsonLint\JsonParser();
            try {
                $parser->parse($content, JsonLint\JsonParser::DETECT_KEY_CONFLICTS);
            } catch (JsonLint\DuplicateKeyException $e) {
                $details = $e->getDetails();
                throw new \Exception("Invalid JSON: unexpected token '" . $details['key'] . "' at line '" . $details['line'] . "'");
            }
            throw new \Exception(json_last_error_msg());
        }
        return $json ?: (object)[];
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

            if (!in_array($value, $data->order)) {
                $errors[] = "Section id '$key' must exist in order";
            }
        }

        foreach ($data->order as $value) {
            if (!property_exists($data->sections, $value)) {
                $errors[] = "Section id '$value' must exist in sections";
            }
        }

        foreach ($sections as $id) {
            if (!(bool)$this->env->file("sections/$id.liquid")) {
                $errors[] = "Section type '$id' does not refer to an existing section file";
            }
        }

        return $errors;
    }


    public function verifyJsonSchema(object | array $data, $type = ''): array
    {
        if (!$type) {
            return [];
        }

        $errors = [];
        $schema = property_exists($this->schemaMap, $type)  ? $this->schemaMap->{$type} : false;


        if ($schema) {

            $this->schemaValidator->validate($data, $schema);
            if (!$this->schemaValidator->isValid()) {
                foreach ($this->schemaValidator->getErrors() as $error) {
                    $errors[] = sprintf("[%s] %s\n", $error['property'], $error['message']);
                }
            }
        }
        return $errors;
    }

    public function fails()
    {
        return count($this->errors) > 0;
    }

    public function errors()
    {
        return $this->errors;
    }
}
