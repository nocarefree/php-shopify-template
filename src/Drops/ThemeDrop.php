<?php 

namespace Ncf\ShopifyTemplate\Drops;


use Ncf\ShopifyTemplate\Theme;

class ThemeDrop extends \Liquid\Models\Drop{

    
    function __construct($cache)
    {
        
        $schema = $this->cache->get(Theme::PATH_CONFIG,'settings_schema_data');
        $settings = $this->cache->get(Theme::PATH_CONFIG,'settings_data');


        $this->theme = array_shift($schema);
        $this->settingsSchema = $schema;

        $settings = array_merge($settings['presets']['Default']??[],$settings['current']??[]);

        $this->sections = $settings['sections'];
        $settings(['sections']);
        $this->settings = $settings;

        $this->settingsToAttributes();
    }


    function settingInit($id, $setting){
        
        return $setting;
    }

    private function settingsToAttributes(){
        $types = [];
        foreach($this->settingsSchema as $group){
            foreach($group['settings'] as $row){
                if($row['id']){
                    $types[$row['id']] = $row['type'];
                }
            }
        }

        foreach($this->settings as $id=>$setting){
            if(isset($types[$id])){
                $this->attributes[$id] = 
            }
        }
    }
}