### This is the fork of the project https://github.com/fanart-tv/laravel-lokalise. Unlike it, these classes dynamically export and import all language files from the folder /lang/ 

# laravel-lokalise
Console integration with lokali.se

This is a very simple pair of files that will allow you to use `php artisan lang:push` and `php artisan lang:pull` to automatically push your all languages and import your translations to and from https://lokali.se


## Requirements
These scripts use GuzzleHttp so make sure that is installed first via composer, it also uses ZipArchive so if you are having issues make sure your PHP has zip support.

composer require guzzlehttp/guzzle


## Assumptions
As I mentioned this is currently a very simple pair of files, as such it makes the following assumptions
* your base language is english
* all your translatable strings reside in app.php
* you already have a folder created in the lang directory for the language you want to pull in

## Installation


Put LangPull.php and LangPush.php in /app/Console/Commands/

in /app/Console/Kernal.php add the following to  `protected $commands = [` array

    Commands\LangPush::class,
    Commands\LangPull::class,

Update both files with your API key and project id

    protected $apikey = 'YOUR_API_KEY';
    protected $project = 'YOUR_PROJECT_ID';
    
## Usage

    php artisan lang:push
This will push your base language file up to locali.se replacing what is currently there and adding additional keys

    php artisan:pull
This will pull every translation, then it will match it against your array of languages, skip the english one and add any others.

All files from /resources/lang directory will be import and export to lokalise project.

