<?php

$config = <<<JSON
{
    "type": "object",
    "properties": {
        "current": {
            "onlyOf":[
                {"type": "string"},
                {"type": "object"}
            ]
        } ,
        "presets":{"type": "object"},
    },
    "required": ["current", "presets"],
}
JSON;

$inputSetting = <<<JSON
{
    "type": "object",
    "properties": {
        "type": {"type":"string"},
        "id":{"type":"string"},
        "label": { "type": "string"},
        "default": { "type": "string" },
        "info": { "type" : "string"}
    },
    "required": ["type", "id", "label"],
}
JSON;

$sidebarSetting = <<<JSON
{
    "type": "object",
    "properties": {
        "type": { "enum": ["header", "paragraph"] },
        "content": { "type": "string"},
        "info": { "type" : "string"}
    },
    "required": ["type", "content"],
    "additionalProperties":false

}
JSON;


$template = <<<JSON
{
    "type": "object",
    "properties": {
        "layout": { 
            "onlyOf":[
                {"type":"string"},
                {"enum":[false] }
            ] 
        },
        "wrapper":{"type":"string"},
        "name": { "type": "string"},
        "sections": { "type": "object",  "minProperties":1, "maxProperties":25 },
        "order": { "type" : "array", "minItems":1,"maxItems":25, "prefixItems":[{ "type":"string"}],"uniqueItems":true}
    },
    "required": ["sections","order"],
    "additionalProperties":false

}
JSON;

$sectionGroup = <<<JSON
{
    "type": "object",
    "properties": {
        "type": { 
            "anyOf": [
                { "enum": ["header", "footer", "aside"] },
                { "type": "string",  "pattern": "^customer\\\\.[\\\\w+]$"}
            ]
        },
        "name": { "type": "string"},
        "sections": { "type": "object",  "minProperties":1, "maxProperties":25},
        "order": { "type" : "array", "maxItems":25, "prefixItems":[{ "type":"string"}]}
    },
    "required": ["type","name","sections","order"],
    "additionalProperties":false

}
JSON;



return [
    'sections' => json_decode($sectionGroup),
    'templates' => json_decode($template),
];