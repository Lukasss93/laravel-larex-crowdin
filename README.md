<p align="center">
    <img style="max-height:400px" src="https://i.imgur.com/yXO1kQC.png"/>
</p>

# Laravel Larex: Crowdin Plugin

[![Version](https://poser.pugx.org/lukasss93/laravel-larex-crowdin/v/stable)](https://packagist.org/packages/lukasss93/laravel-larex-crowdin)
[![Downloads](https://poser.pugx.org/lukasss93/laravel-larex-crowdin/downloads)](https://packagist.org/packages/lukasss93/laravel-larex-crowdin)
![PHP](https://img.shields.io/badge/PHP-%E2%89%A5%208.0-blue)
![Laravel](https://img.shields.io/badge/Laravel-%E2%89%A5%208.0-orange)
[![License](https://poser.pugx.org/lukasss93/laravel-larex-crowdin/license)](https://packagist.org/packages/lukasss93/laravel-larex-crowdin)
![Build](https://img.shields.io/github/workflow/status/Lukasss93/laravel-larex-crowdin/run-tests)
[![Coverage](https://img.shields.io/codecov/c/github/lukasss93/laravel-larex-crowdin?token=7YedNMIso5)](https://codecov.io/gh/Lukasss93/laravel-larex-crowdin)


> A Laravel Larex plugin to import/export localization strings from/to Crowdin

## 📋 Requirements

- PHP ≥ 8.0
- Laravel ≥ 8
- [Laravel Larex](https://github.com/Lukasss93/laravel-larex) ≥ v4.0
- [Crowdin API Token v2](https://crowdin.com/settings#api-key)
- Crowdin Project ID `Crowdin > Project > Settings > API & Webhooks > Project Id [API v2]`

## 🚀 Installation

You can install the package using composer:

```bash
composer require lukasss93/laravel-larex-crowdin --dev
```

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
1. Install [Laravel Larex](https://github.com/Lukasss93/laravel-larex) ≥ v3.5
2. Publish (if you haven't already) and edit
   your [larex.php](https://github.com/Lukasss93/laravel-larex#-publishing-the-config-file) config
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

- Run `php artisan larex:import crowdin` to import strings from your Crowdin Project into your Larex CSV
- Run `php artisan larex:export crowdin` to export strings from your Larex CSV into your Crowdin Project
- You can still use the `--include` and `--exclude` options to select specific languages to import/export

## ⚗️ Testing

```bash
composer test
```

## 🔰 Version Support

| Larex Crowdin | L7.x | L8.x | L9.x |
|---------------|------|------|------|
| ^1.0          | ✅    | ✅    | ❌    |
| ^2.0          | ❌    | ✅    | ✅    |

| Larex Crowdin | PHP7.4 | PHP8.0 | PHP8.1  |
|---------------|--------|--------|---------|
| ^1.0          | ✅      | ✅      | ✅       |
| ^2.0          | ❌      | ✅      | ✅       |

## 📃 Changelog

Please see the [CHANGELOG.md](CHANGELOG.md) for more information
on what has changed recently.

## 🏅 Credits

- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](https://github.com/Lukasss93/laravel-larex-crowdin/contributors)

## 📖 License

Please see the [LICENSE.md](LICENSE.md) file for more
information.
