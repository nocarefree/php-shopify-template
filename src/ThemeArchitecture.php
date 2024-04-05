<?php

namespace ShopifyTemplate;

use Liquid\LiquidException;
use Liquid\FileSystem;

class Theme
{

    const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout';
    const PATH_SECTION = 'sections';
    const PATH_SNIPPET = 'snippets';
    const PATH_LOCALE = 'locales';
    const PATH_CONFIG = 'config';
    const PATH_ASSET = 'assets';

    protected $liquid;
    protected $fileSystem;


    public $inputSettings = [
        'checkbox' => 'bool',
        'number' => 'int',
        'radio' => 'string',
        'range' => 'int',
        'select' => 'string',
        'text' => 'string',
        'textarea' => 'string',
        'color_background' => 'string',
        'html' => 'string',
        'richtext' => 'string',
        'url' => 'string',
        'video_url' => 'string',


        'color' => Drops\ColorDrop::class,
        'font_picker' => Drops\FontDrop::class,
        'image_picker' => Drops\ImageDrop::class,
        'link_list' => Drops\LinkListDrop::class,
        'liquid' => Drops\LiquidDrop::class,
    ];


    protected $drops = [];

    protected $locale;


    public function __construct($dir)
    {
        $archite = [];

        $this->fileSystem = new FileSystem($dir);
    }

    public function getLocale($name)
    {

        $fileSystem = $this->liquid->getFileSystem();


        return $this->get(Theme::PATH_LOCALE, $name);
    }

    public function getLayout($name)
    {
        return $this->get(Theme::PATH_LOCALE, $name);
    }

    public function getSection($name)
    {
        return $this->get(Theme::PATH_SECTION, $name);
    }

    public function getTemplate($name)                     
    {
        return $this->get(Theme::PATH_TEMPLATE, $name);
    }

    public function getSnippet($name)
    {
        return $this->get(Theme::PATH_SNIPPET, $name);
    }

    public function getConfig($name)
    {
        return $this->get(Theme::PATH_CONFIG, $name);
    }

    public function getLocale($name)
    {
        return $this->get(Theme::PATH_LOCALE, $name);
    }

    public function getAssets()
    {
        $files = $this->fileSystem->listContents('assets',1);

        foreach($files as $file){
            $file;
        }


        // foreach([
        //     'assets'=> [
        //         'type'=>['image/*, font/*, .ttf, .eot, .woff, .woff2, .css, .scss, .js, .json, .liquid'],
        //     ],
        //     'confg'=>['type'=>'json'],
        //     'layout'=>[
        //         'type'=>['.liquid'],
        //         'required'=>['theme.liquid'],
        //     ],
        //     'locales'=>[],
        // ] as $dir){

        // }

        // $files = $file->listContents('layout',1);
    }

}
