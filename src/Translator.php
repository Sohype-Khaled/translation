<?php

namespace Codtail\Translation;


trait Translator
{
    public static function create(array $options = array())
    {
        if (property_exists(self::class, 'translated')) {
            $translations = [];
            foreach (config('app.app_locales') as $locale) {
                foreach (self::$translated as $field) {
                    if ($locale != config('app.locale'))
                        $translations[$locale][$field] = "";
                }
            }
            // TODO check if options has translations
            $options['translations'] = json_encode($translations);
        }
        return static::query()->create($options);
    }

    public function translate($lang, array $attributes = [])
    {
        $trans = json_decode($this->attributes['translations'], true);
        $to_update = [];

        foreach ($trans[$lang] as $key => $field) {
            if (isset($attributes[$key]))
                $to_update[$key] = $attributes[$key];
        }
        $trans[$lang] = $to_update;
        $this->translations = json_encode($trans);
        $dispatcher = self::getEventDispatcher();
        self::unsetEventDispatcher();
        $this->save();
        self::setEventDispatcher($dispatcher);

        $this->fireModelEvent('translated', false);
        return $this;
    }

    public function getTranslation($lang)
    {
        $lang = json_decode($this->attributes['translations'], true)[$lang];
        foreach ($lang as $key => $value) {
            if ($value != '')
                $this->attributes[$key] = $value;
        }
        return $this;
    }

    public function getAttributeValue($key)
    {
        $locale = app()->getLocale();
        if ($locale != config('app.fallback_locale')) {
            $value = $this->getTranslation($locale);
            if (in_array($key, self::$translated))
                return $value->attributes[$key];
        }
        return parent::getAttributeValue($key);
    }

    public function getTranslationsAttribute($value)
    {
        return json_decode($value, true);
    }
}
