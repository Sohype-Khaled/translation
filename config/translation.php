<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Available Locales
    |--------------------------------------------------------------------------
    |
    | The locales sets the languages used by the app
    |
    */

    'locales' => ['en'],

    /*
    |--------------------------------------------------------------------------
    | Locales Legal Names
    |--------------------------------------------------------------------------
    |
    | Locals Legal Names
    |
    */

    'locales_names' => [
        'ar' => 'Arabic',
        'en' => 'English',
    ],

    /*
    |--------------------------------------------------------------------------
    | Disable Model Save Event
    |--------------------------------------------------------------------------
    |
    | This Disables the model save event on translating model
    |
    */

    'disable_save_event' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable Translation model events
    |--------------------------------------------------------------------------
    |
    | This Enables model translation events
    |
    */

    'translation_events' => true,


];
