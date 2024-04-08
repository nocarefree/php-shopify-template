<?php

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
    'sectionGroup' => json_decode($sectionGroup),
    'template' => json_decode($template),
];
