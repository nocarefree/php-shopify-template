<?php 

namespace Ncf\ShopifyTemplate;


class SectionEnv extends BaseEnv{


    public function __construct($fileSystem = null)
    {
        parent::__construct($fileSystem);

        $this->registerTags([
            //Config
            'schema'=> Tags\TagSchema::class,
            
            //section template
            'javascript'=> Tags\TagJavascript::class,
            'stylesheet'=> Tags\TagStylesheet::class,
        ]);
    }

    
}