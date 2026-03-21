<p align="center">
    <img style="max-height:400px" src="https://i.imgur.com/yXO1kQC.png"/>
</p>

# Laravel Larex: Crowdin Plugin

[![Version](https://img.shields.io/packagist/v/lukasss93/laravel-larex-crowdin?label=composer&logo=composer)](https://packagist.org/packages/lukasss93/laravel-larex-crowdin)
[![Downloads](https://img.shields.io/packagist/dt/lukasss93/laravel-larex-crowdin)](https://packagist.org/packages/lukasss93/laravel-larex-crowdin)
![License](https://img.shields.io/packagist/l/lukasss93/laravel-larex-crowdin)
![PHP](https://img.shields.io/packagist/dependency-v/lukasss93/laravel-larex-crowdin/php?logo=php)
![Laravel](https://img.shields.io/packagist/dependency-v/lukasss93/laravel-larex-crowdin/illuminate/support?label=laravel&logo=laravel)

![Tests](https://img.shields.io/github/actions/workflow/status/lukasss93/laravel-larex-crowdin/run-tests.yml?label=Test%20Suite&logo=github)
[![Maintainability](https://qlty.sh/gh/Lukasss93/projects/laravel-larex-crowdin/maintainability.svg)](https://qlty.sh/gh/Lukasss93/projects/laravel-larex-crowdin)
[![Code Coverage](https://qlty.sh/gh/Lukasss93/projects/laravel-larex-crowdin/coverage.svg)](https://qlty.sh/gh/Lukasss93/projects/laravel-larex-crowdin)

> A Laravel Larex plugin to import/export localization strings from/to Crowdin

## 📋 Requirements

- PHP ≥ 8.2
- Laravel ≥ 11
- [Laravel Larex](https://github.com/Lukasss93/laravel-larex) ≥ v4.7
- Crowdin API Token
  - Standard account: `https://crowdin.com/settings#api-key` (API v2)
  - Enterprise account: `https://<your-organization-name>.crowdin.com/u/user_settings/access-tokens`
- Crowdin Project ID 
  - Standard account: `https://crowdin.com/project/<your-project>/tools/api` (API v2)
  - Enterprise account: `https://<your-organization-name>.crowdin.com/u/projects/<your-project-id>#home`

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
1. Install [Laravel Larex](https://github.com/Lukasss93/laravel-larex) ≥ v4.4
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
    # For more info about this fields, see the "config/larex-crowdin.php" file
    LAREX_CROWDIN_TOKEN=<crowdin-token>
    LAREX_CROWDIN_PROJECT_ID=<crowdin-projectID>
    LAREX_CROWDIN_ORGANIZATION=<crowdin-organization-name>
    ```

## 👓 Usage

- Run `php artisan larex:import crowdin` to import strings from your Crowdin Project into your Larex CSV
- Run `php artisan larex:export crowdin` to export strings from your Larex CSV into your Crowdin Project
- You can still use the `--include` and `--exclude` options to select specific languages to import/export

## ⚗️ Testing

```bash
composer test
```

## 📃 Changelog

Please see the [CHANGELOG.md](CHANGELOG.md) for more information
on what has changed recently.

## 🏅 Credits

- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](https://github.com/Lukasss93/laravel-larex-crowdin/contributors)

## 📖 License

Please see the [LICENSE.md](LICENSE.md) file for more
information.
