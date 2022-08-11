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
            'schema'=> \Ncf\ShopifyTemplate\Tags\TagSchema::class,
            
            //section template
            'javascript'=> \Ncf\ShopifyTemplate\Tags\TagJavascript::class,
            'stylesheet'=> \Ncf\ShopifyTemplate\Tags\TagStylesheet::class,
        ];
    }

    
}