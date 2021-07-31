###### _TODO: image_

# Laravel Larex: Crowdin Plugin

###### _TODO: badges_

## 📋 Requirements

- PHP 7.4 | 8.0
- Laravel ≥ 7
- [Laravel Larex](https://github.com/Lukasss93/laravel-larex) ≥ v3.0
- [Crowdin API Token v2](https://crowdin.com/settings#api-key)
- Crowdin Project ID `Crowdin > Project > Settings > API & Webhooks > Project Id [API v2]`

## 🚀 Installation

You can install the package using composer:
###### _TODO: publish to packagist_

Then add the service provider to `config/app.php`.  
This step *can be skipped* if package auto-discovery is enabled.

```php
'providers' => [
    Lukasss93\LarexCrowdin\LarexCrowdinServiceProvider::class
];
```

## ⚙ Publishing the config file

Publishing the config file is optional:

```bash
php artisan vendor:publish --provider="Lukasss93\LarexCrowdin\LarexCrowdinServiceProvider" --tag="larex-crowdin-config"
```

## 🔧 Configuration
1. Install [Laravel Larex](https://github.com/Lukasss93/laravel-larex) ≥ v3.0
2. Publish (if you haven't already) and edit your [larex.php](https://github.com/Lukasss93/laravel-larex#-publishing-the-config-file) config
3. Append the Crowdin importer in the `importers.list` array:
    ```php
    //...
    'importers' => [
        //...
        'list' => [
            //...
            'crowdin' => Lukasss93\LarexCrowdin\Importers\CrowdinImporter::class,
        ],
    ],
    //...
    ```
4. Append the Crowdin exporter in the `exporters.list` array:
    ```php
    //...
    'exporters' => [
        //...
        'list' => [
            //...
            'crowdin' => Lukasss93\LarexCrowdin\Exporters\CrowdinExporter::class,
        ],
    ],
    //...
    ```
5. Edit your .env file and append the following strings:
    ```dotenv
    # You can generate your token here: https://crowdin.com/settings#api-key
    # Please note: this library supports only the Crowdin API v2
    LAREX_CROWDIN_TOKEN=<crowdin-token>
    
    # You can get your project id (API v2) here: 
    # https://crowdin.com/project/your-project/settings#api
    LAREX_CROWDIN_PROJECT_ID=<crowdin-projectID>
    ```

## 👓 Usage
###### _TODO: usage_

## ⚗️ Testing

```bash
composer test
```

## 📃 Changelog

Please see the [CHANGELOG.md](https://github.com/Lukasss93/laravel-larex/blob/master/CHANGELOG.md) for more information
on what has changed recently.

## 🏅 Credits

- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](https://github.com/Lukasss93/laravel-larex/contributors)

## 📖 License

Please see the [LICENSE.md](https://github.com/Lukasss93/laravel-larex/blob/master/LICENSE.md) file for more
information.
