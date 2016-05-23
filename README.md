## Laravel Debugbar - ViewComposer Collection

This package allows you to see which [View Composers](https://laravel.com/docs/master/views#view-composers) are being triggered during page rendering. The aim of this package is to help you drill-down exactly where your View variables are coming from.

## Installation 

Require this package with Composer:

```
composer require joshbrw/laravel-debugbar-viewcomposers
```

After running `composer update`, add the Service Provider to your `providers` array in `config/app.php`:

```
Joshbrw\DebugbarViewComposers\ServiceProvider::class
```
