<?php 

namespace Ncf\ShopifyTemplate\Drops;

use Ncf\ShopifyTemplate\Theme;

class ThemeSectionDrop extends \Liquid\Models\Drop{
    
    function __construct($cache)
    {
        
        $schema = $this->cache->get(Theme::PATH_CONFIG,'settings_schema_data');
        $settings = $this->cache->get(Theme::PATH_CONFIG,'settings_data');
        
    
    }

}