<?php

namespace ShopifyTemplate;

use Exception;
use Liquid\LiquidException;
use Liquid\FileSystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FileAttributes;
use Illuminate\Support\Str;

class ThemeArchitecture
{

    protected FileSystem $fileSystem;
    protected array $structures = [];


    public function __construct()
    {
    }

    public function checkFile($path, $content)
    {
        list($type, $name) = explode('/', $path);

        switch ($type) {
            case 'layout':
                if (!preg_match('#{{[\s]?+content_for_header[\s]?+}}#', $content)) {
                    throw new \Exception("Missing {{content_for_header}} in the head section of the template");
                }

                if (!preg_match('#{{[\s]?+content_for_layout[\s]?+}}#', $content)) {
                    throw new \Exception("Missing {{content_for_layout}} in the content section of the template");
                }
                break;
        }
    }

    public function loadLocalFiles($dir)
    {
        $this->fileSystem = new FileSystem(new LocalFilesystemAdapter($dir));

        $this->structures = [
            'assets' => $this->getAssets(),
            'config' => $this->getConfig(),
            'layout' => $this->getLayout(),
            'locals' => $this->getLocales(),
            'sections' => $this->getSections(),
            'snippets' => $this->getSnippets(),
        ];
    }

    private function getAssets()
    {
        $files = $this->fileSystem->listContents('assets', 1);

        $data = [];
        foreach ($files as $file) {
            if ($file instanceof FileAttributes) {
                $mimeType = $file->mimeType();
                if ($mimeType) {
                    if (Str::startsWith($mimeType, ['image/', 'font/'])) {
                        $data[] = $file->path();
                    }
                } else {
                    if (Str::endsWith($file->path(), ['.ttf', '.eot', '.woff', '.woff2', '.css', '.scss', '.js', '.json', '.liquid'])) {
                        $data[] = $file->path();
                    }
                }
            }
        }

        array_filter($data, function ($name) use ($data) {
            if (Str::endsWith($name, ['.css', '.js'])) {
                return !in_array($name . '.liquid', $data);
            }
            return true;
        });

        return $data;
    }

    private function getConfig()
    {
        $data = [];
        if ($this->fileSystem->fileExists('config/settings_schema.json')) {
            $data[] = 'config/settings_schema.json';
        } else {
            throw new \Exception();
        }
        if ($this->fileSystem->fileExists('config/settings_data.json')) {
            $data[] = 'config/settings_data.json';
        } else {
            throw new \Exception();
        }
        return $data;
    }

    private function getLayout()
    {
        $data = [];
        $files = $this->fileSystem->listContents('layout', 1);
        foreach ($files as $file) {
            $path = $file->path();
            if ($file instanceof FileAttributes && Str::endsWith($path, '.liquid')) {

                $content = $this->fileSystem->read($path);
                $this->checkFile($path, $content);
                $data[] = $file->path();
            }
        }
        if (!in_array('layout/theme.liquid', $data)) {
            throw new \Exception();
        }

        return $data;
    }

    private function getLocales()
    {
        $data = [];
        $files = $this->fileSystem->listContents('locales', 1);
        $default = 0;
        $defaultSchema = 0;
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), '.json')) {
                if (Str::endsWith($file->path(), '.default.json')) {
                    $default++;
                }

                if (Str::endsWith($file->path(), '.default.schema.json')) {
                    $defaultSchema++;
                }

                $data[] = $file->path();
            }
        }
        if ($default == 0 || $default > 1) {
            throw new \Exception("有且仅有一个默认语言文件");
        }

        if ($defaultSchema == 0 || $defaultSchema > 1) {
            throw new \Exception("有且仅有一个默认语言文件");
        }
        return $data;
    }

    private function getSections()
    {
        $data = [];
        $files = $this->fileSystem->listContents('layout', 1);
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), ['.json', '.liquid'])) {
                $data[] = $file->path();
            }
        }
        return $data;
    }

    private function getSnippets()
    {
        $data = [];
        $files = $this->fileSystem->listContents('layout', 1);
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), '.liquid')) {
                $data[] = $file->path();
            }
        }
        return $data;
    }
}
