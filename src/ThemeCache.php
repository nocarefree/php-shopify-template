<?php 

namespace Ncf\ShopifyTemplate;


class ThemeCache{

    public $files;

    function __construct($theme){
        $server = new ThemeInstall($theme);
        $server->run();

        $this->files = $server->getFiles();

        $this->sectionsDrop = new Drops\SectionsDrop(array_filter($this->files, function($file){
            return $file['path'] == Theme::PATH_SECTION && $file['node'];
        }));

        $this->themeDrop = new Drops\ThemeDrop($this);
    }

    public function initSchema(){
        $schema = $this->cache->get(Theme::PATH_CONFIG,'settings_schema_data');
        
        $this->theme = $schema['theme'];
        $this->settingsSchema = $schema['settings'];

        $settings = $this->cache->get(Theme::PATH_CONFIG,'settings_data');
        $settings = array_merge($settings['presets']['Default']??[],$settings['current']??[]);

        $this->sectionSettings = $settings['sections'];
        $this->settings = $this->settingsInit($settings);
        

        unset($settings['sections']);

    }

    function settingInit($id, $setting){
        
    }



    protected function settingsInit($settings){
        unset($settings['sections']);
        foreach($settings as $id=>&$setting){
            foreach($this->settingsSchema as $row){
                if($row['id'] == $id){
                    if($row['type'] == 'color'){
                        return new Drops\ColorDrop($setting);
                    }else if($row['type'] == 'font_picker'){
                        return new Drops\FontDrop($setting);
                    }
                }
            }
        }
        return $settings;
    }

    public function getTheme(){
        $this->theme;
    }

    public function get($path, $name){
        foreach($this->files as $file){
            if($file['path'] == $path && $file['name'] == $name){
                return $file;
            }
        }
        return null;
    }


    public function getLayout($name){
        return $this->get(Theme::PATH_LOCALE, $name);
    }

    public function getSection($name){
        return $this->get(Theme::PATH_SECTION, $name);
    }

    public function getTemplate($name){
        return $this->get(Theme::PATH_TEMPLATE, $name);
    }

    public function getSnippet($name){
        return $this->get(Theme::PATH_SNIPPET, $name);
    }

    public function getConfig($name){
        return $this->get(Theme::PATH_CONFIG, $name);
    }

    public function getLocale($name){
        return $this->get(Theme::PATH_LOCALE, $name);
    }


}