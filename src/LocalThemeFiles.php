<?php

namespace ShopifyTemplate;

use Liquid\FileSystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FileAttributes;
use Illuminate\Support\Str;


class LocalThemeFiles
{

    protected FileSystem $fileSystem;
    protected array $structures = [];
    protected array $errors = [];

    public function __construct($dir)
    {
        $this->fileSystem = new FileSystem(new LocalFilesystemAdapter($dir));
    }

    public function get()
    {
        $this->errors = [];
        $files = array_merge(
            $this->getAssets(),
            $this->getConfig(),
            $this->getLayout(),
            $this->getLocales(),
            $this->getSections(),
            $this->getSnippets(),
            $this->getTemplates()
        );

        $data = [];
        foreach ($files as $path) {
            if (Str::endsWith($path, ['.liquid', '.json'])) {
                $data[] = [
                    'key' => $path,
                    'value' => $this->fileSystem->read($path)
                ];
            } else {
                $data[] = ['key' => $path, 'value' => null];
            }
        }
        return $data;
    }

    public function getErrors()
    {
        return $this->errors;
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
            $this->errors[] = "Theme settings no found";
        }
        if ($this->fileSystem->fileExists('config/settings_data.json')) {
            $data[] = 'config/settings_data.json';
        } else {
            $this->errors[] = "Theme settings area of the theme editor.";
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
                $data[] = $file->path();
            }
        }
        if (!in_array('layout/theme.liquid', $data)) {
            $this->errors[] = "The default layout file, which must be included in all themes, is theme.liquid.";
        }

        return $data;
    }

    private function getTemplates()
    {
        $data = [];
        $files = $this->fileSystem->listContents('templates', 1);
        foreach ($files as $file) {
            $path = $file->path();
            $fileName = basename($path);
            if (
                $file instanceof FileAttributes &&
                Str::endsWith($fileName, ['.liquid', '.json']) &&
                Str::startsWith($fileName, ['404.', 'article.', 'blog.', 'cart.', 'collection.', 'gift_card.', 'index.', 'list-conllections.', 'page.', 'password.', 'product.', 'search.'])
            ) {
                $data[] = $file->path();
            }
        }
        $files = $this->fileSystem->listContents('templates/customers', 1);
        foreach ($files as $file) {
            $path = $file->path();
            if (
                $file instanceof FileAttributes &&
                Str::endsWith($fileName, ['.liquid', '.json']) &&
                Str::startsWith($fileName, ['account.', 'activate_account.', 'addresses.', 'login.', 'order.', 'register.', 'reset_password.'])
            ) {
                $data[] = $file->path();
            }
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
            $this->errors[] = "The only one default locale file";
        }

        if ($defaultSchema == 0 || $defaultSchema > 1) {
            $this->errors[] = "The only one default locale.schema file";
        }
        return $data;
    }

    private function getSections()
    {
        $data = [];
        $files = $this->fileSystem->listContents('sections', 1);
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
        $files = $this->fileSystem->listContents('snippets', 1);
        foreach ($files as $file) {
            if ($file instanceof FileAttributes && Str::endsWith($file->path(), '.liquid')) {
                $data[] = $file->path();
            }
        }
        return $data;
    }
}
