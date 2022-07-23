<?php 

namespace Ncf\ShopifyLiquid\Drops;

class FontDrop extends \Liquid\Models\Drop{
    public $allowModifies = [
        'style' => ['normal', 'italic', 'oblique'],
        'weight' => ['normal', 'bold', 'lighter','bolder','200','300','400','500','800','900'],
        'display' => ['auto', 'block', 'swap', 'fallback',  'optional'],
        'url' => ['woff','woff2']
    ];
    
    function modify($key, $value){

        if($key == 'style'){
            $this->style($value);
        }

        if($key == 'weight'){
            $this->weight($value);
        }
        return $this;
    }

    function style($value){
        return $this->setAttribute('style', in_array($value,$this->allowModifies['style'])?$value:'');
    }

    function weight($value){
        $weight = "";
        if(in_array($value,$this->allowModifies['weight'])){
            $weight = $value;
        }else if(strpos($value,'+') === 0 && is_numeric($num = substr($value,1))){
            $num += 400;
            if(!in_array($num,$this->allowModifies['weight'])){
                $weight = $num; 
            }
        }
        return $this->setAttribute('weight', $weight);
    }


    function url($key){
        if(in_array($key,$this->allowModifies['url'])){
            return $this->{'url'.$key};
        }else{
            return 'Liquid error: font_url only supports the woff2 and woff formats';
        }
    }

    function toHtml($data = []){
        $display = null;
        if(isset($data['font_display'])){
            if(in_array($data['font_display'],$this->allowModifies['display'])){
                $display = $data['font_display'];
            }else{
                return 'Liquid error: font_display can only be set to auto, block, swap, fallback, and optional';
            }
        }

        $displayHtml = $display?("\n\tfont-display:".$display) : '';

        return '@font-face {
    font-family: '.$this->family.';
    font-weight: '.$this->weight.'; '. $displayHtml .'
    font-style: normal;
    src: url("'.$this->url('woff2').'") format("woff2"),
            url("'.$this->url('woff').'") format("woff");
}';

    }

}