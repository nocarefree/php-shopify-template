<?php

namespace ShopifyTemplate;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class Translate
{

    protected $data = [];
    protected $code = [];

    function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    function set($code)
    {
        $this->code = $code;

        $data = [];

        $t1 = $this->theme->cache()->get(Theme::PATH_LOCALE, $code);
        $t2 = $this->theme->cache()->get(Theme::PATH_LOCALE, $code . '.schema');

        if (isset($t1['node'])) {
            $data = array_merge_recursive($data, $t1['node']);
        }

        if (isset($t2['node'])) {
            $data = array_merge_recursive($data, $t2['node']);
        }

        //dd($data);
        $this->data = $data;
    }

    public function get($input, $data = [])
    {


        $content = Arr::get($this->data, $input);

        if (!$content) {
            return 'translation missing: ' . $this->code . '.' . $input;
        }

        if (is_array($content) && isset($data['count']) && is_numeric($data['count'])) {
            $count = $data['count'];

            if ($count == 1 && isset($content['one'])) {
                $content = $content['one'];
            } else if ($count != 1 && isset($content['other'])) {
                $content = $content['other'];
            }
        }

        if (is_array($content)) {
            return json_encode($content);
        } else {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $content = preg_replace("/{{\s*" . preg_quote($key, '/') . "\s*}}/", $value, $content);
                }
            }
        }

        return Str::endsWith($input, '_html') ? htmlspecialchars($content) : $content;
    }
}
