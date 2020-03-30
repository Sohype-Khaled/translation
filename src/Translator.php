<?php

namespace Codtail\Translation;

use Codtail\Translation\Exceptions\InvalidLocaleException;
use Codtail\Translation\Exceptions\PropertyNotTranslatableException;
use Codtail\Translation\Exceptions\TranslationForFallbackLocaleException;
use Illuminate\Support\Facades\Config;

trait Translator
{
    /**
     * holds the available locales
     */
    protected $locales;

    /**
     * holds model translations for create or update
     */
    protected $translationsArray;

    /**
     * Available translation model events
     */
    protected $translationEvent = ['translated', 'translation_cleared', 'all_translations_cleared'];

    /**
     * Add the translations to casts array
     *
     * @return array
     */
    public function getCasts()
    {
        return is_array($this->casts) ? array_merge($this->casts, ['translations' => 'array']) : ['translations' => 'array'];
    }

    /**
     * Adds the translation events to the observables array
     * @return array
     */
    public function getObservables()
    {
        return is_array($this->observables) ? array_merge($this->observables, $this->translationEvent) : $this->translationEvent;
    }

    /**
     *
     * @param $locale
     * @param $attributes
     * @return mixed
     * @throws InvalidLocaleException
     * @throws PropertyNotTranslatableException
     * @throws TranslationForFallbackLocaleException
     */
    public function setTranslation($locale, array $attributes)
    {
        $this->initializeTranslationsArray();

        $this->getTranslationLocales();

        $this->checkFallbackLocale($locale);

        $this->checkValidLocale($locale);

        $this->initializeLocaleProperties($locale);

        $this->isTranslatable($attributes);
        foreach ($attributes as $field => $value)
            $this->translationsArray[$locale][$field] = $value;

        $this->attributes['translations'] = json_encode($this->translationsArray);
        $this->saveTranslation();

        return $this;
    }

    /**
     * gets the translatable attributes from the model provided in $translatable property
     */
    protected function initializeTranslationsArray()
    {
        $translations = $this->translations;
        if (!is_array($translations))
            $this->translationsArray = [];
        else
            $this->translationsArray = $translations;
    }

    /**
     * Retrieves the available locales from config
     */
    protected function getTranslationLocales()
    {
        foreach (Config::get('translation.locales') as $locale) {
            if ($locale != Config::get('translation.locale'))
                $this->locales[] = $locale;
        }
    }

    /**
     * Check if the used locale is the config fallback locale
     * @param $locale
     * @throws TranslationForFallbackLocaleException
     */
    protected function checkFallbackLocale($locale)
    {
        if ($locale == Config::get('translation.fallback_locale'))
            throw TranslationForFallbackLocaleException::error($locale);
    }

    /**
     * check if the used locale is provided in the config locales
     * @param $locale
     * @throws InvalidLocaleException
     */
    protected function checkValidLocale($locale)
    {
        if (!in_array($locale, $this->locales))
            throw InvalidLocaleException::error($locale);
    }

    /**
     * initialize the locale properties to their values if the values exists in the database
     * if the values are not set then it initialize them to empty string
     * @param $locale
     */
    protected function initializeLocaleProperties($locale)
    {
        if (!in_array($locale, $this->translationsArray))
            foreach ($this->getTranslatableProperties() as $prop)
                $this->translationsArray[$locale][$prop] = $this->translationsArray[$locale][$prop] ?? '';
    }

    /**
     * get the $translatable $attributes if provided or empty array if it is not set
     * @return array
     */
    public function getTranslatableProperties()
    {
        return is_array($this->translatable) ? $this->translatable : [];
    }

    /**
     * check if the $attribute exists in the translatable properties
     * @param $attributes
     * @throws PropertyNotTranslatableException
     */
    protected function isTranslatable($attributes)
    {
        foreach ($attributes as $key => $attribute)
            if (!in_array($key, $this->getTranslatableProperties()))
                throw PropertyNotTranslatableException::error($key, $this);
    }

    /**
     * saves the translation to the database
     */
    protected function saveTranslation()
    {
        if (Config::get('translation.disable_save_event')) {
            $dispatcher = self::getEventDispatcher();
            self::unsetEventDispatcher();
            $this->save();
            if (Config::get('translation.translation_events')) {
                $this->fireModelEvent('translated', false);
            }
            self::setEventDispatcher($dispatcher);
        }

        $this->save();
        $this->fireModelEvent('translated', false);
    }

    /**
     * removes the translations of the provided $locale for a model
     * @param $locale
     */
    public function clearTranslation($locale)
    {
        $translations = $this->translations;
        unset($translations[$locale]);
        $this->translations = $translations;
        $this->save();
        if (Config::get('translation.translation_events')) {
            $this->fireModelEvent('translation_cleared', false);
        }
    }

    /**
     * removes all the translations for a model
     */
    public function clearAllTranslations()
    {
        $translations = $this->translations;
        foreach ($translations as $key => $value)
            unset($translations[$key]);
        $this->translations = $translations;
        $this->save();
        if (Config::get('translation.translation_events')) {
            $this->fireModelEvent('all_translations_cleared', false);
        }
    }

    /**
     * Get the attribute value if the locale is not the fallback locale
     * or return the original value
     * @param string $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $locale = app()->getLocale();
        if ($locale != config('translation.fallback_locale')) {
            $value = $this->getAttributeTranslation($locale, $key);
            if (in_array($key, $this->getTranslatableProperties())
                && array_key_exists($locale, $value->translations)) {
                return $value->translations[$locale][$key];
            }
        }
        return parent::getAttributeValue($key);
    }

    /**
     * get translation for a certain attribute
     * @param $locale
     * @param $attribute
     * @return mixed
     */
    public function getAttributeTranslation($locale, $attribute)
    {
        if (in_array($attribute, $this->translatable))
            if (array_key_exists($locale, $this->translations))
                return array_key_exists($attribute, $this->translations[$locale]) ?
                    $this->translations[$locale][$attribute] : false;
        return $this;
    }

    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes()
    {
        return is_array($this->attributes) ? array_merge($this->attributes, ["translations" => "{}"]) : ["translations" => "{}"];
    }
}
