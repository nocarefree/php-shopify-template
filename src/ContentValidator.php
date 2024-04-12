<?php

namespace ShopifyTemplate;

use Illuminate\Support\Str;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;
use Seld\JsonLint;

class ContentValidator extends \Exception
{
    protected $errors = [];
    protected $env = [];
    protected $schemaMap;

    public function __construct(ThemeArchitecture $env)
    {
        $this->env = $env;
        $this->schemaMap = include(__DIR__ . '/schema.php');
    }

    public function validate($path, &$content)
    {
        $errors = [];
        list($type) = explode('/', $path);
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


            $stream = new \Liquid\TokenStream($this->env, $content);
            $content = $stream->parse();

            if ($stream->syntaxError()->fails()) {
                $errors = $stream->syntaxError()->errors();
            }



            // if ($type == 'sections') {
            //     foreach ($content->nodes as $node) {
            //         if (is_object($node) && in_array($node->name, ['javascript', 'stylesheet', 'schema'])) {
            //             $this->addSectionInner($node->name, (string)$node);
            //         }
            //     }
            // }

        } else {
            try {
                $data = $this->jsonDecode($content);
                $errors = $this->verifyJsonSchema($data, $type);
                $content = $data;

                if (empty($errors) && in_array($type, ['sections', 'templates'])) {
                    $errors = array_merge($errors, $this->verifySectionOrderSchema($data));
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                $content = [];
            }
        }
        if ($errors) {
            $this->errors[$path] = $errors;
        }
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
        return !empty($json) ? $json : [];
    }


    public function verifyJson($content)
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
            if (!(bool)$this->env->file("sections/$id.liquid")) {
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

    public function fails()
    {
        return count($this->errors) > 0;
    }

    public function errors()
    {
        return $this->errors;
    }
}
