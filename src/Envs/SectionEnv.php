<?php 

namespace Ncf\ShopifyTemplate\Envs;


class SectionEnv extends BaseEnv{

    public function __construct($fileSystem = null)
    {
        parent::__construct($fileSystem);

        $this->registerTags(static::getInnerTags());
    }

    public static function getInnerTags(){
        return [
            //Config
            'schema'=> Tags\TagSchema::class,
            
            //section template
            'javascript'=> Tags\TagJavascript::class,
            'stylesheet'=> Tags\TagStylesheet::class,
        ];
    }

    
}