<?php 

include(__DIR__."/vendor/autoload.php");

class SiteFilter
{
    public function render($file)
    {
        $files = explode(',', $file);
        $media = isset($media) ? ' media="'.$media.'"' : '';
        $r = '';

        foreach($files as $link)
            $r .= '<link href="/theme/default/stylesheets/'.trim($link).'.css"'.$media.' rel="stylesheet" type="text/css" />'."\n";
        return $r;
    }

    public function image_url(){
        return '';
    }
}


class SectionTag extends \Liquid\AbstractTag
{
    public function render(\Liquid\Context $context)
    {
        return '';
    }
}

class StyleTag extends \Liquid\AbstractBlock
{
	
	private $to;


	public function render(\Liquid\Context $context)
	{
		return '';
	}
}


$template = new \Liquid\Template(__DIR__.'/tests/templates/crave');
$template->registerTag('style', StyleTag::class);
$template->registerTag('section', SectionTag::class);
$template->registerFilter(new SiteFilter());

$template->parseFile('layout/theme');
$nodes = $template->getRoot()->getNodelist();

$n = 0;
foreach($nodes as $node){
    if($node instanceof SectionTag){
        $n++;
    }
}
echo $n;

file_put_contents('1.php',"<?php \n" . var_export($nodes,true));


