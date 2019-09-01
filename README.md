# translation
This package provide easy and smooth multi-language model translation for laravel application.

###installation
    composer require codtail/translation

######migrations:
    $table->longText('translations');

######model:
    <?php
    
    namespace App;
    
    use Codtail\Translation\Translator;
    use Illuminate\Database\Eloquent\Model;
    
    class post extends Model
    {
        ....
        use Translator;
        
        public static $translated = ['title', 'content'];
        ....
    }
    
######config
add the available locales in ```app.php``` in config folder
 ```php
    ...
    
    'app_locales' => ['en', 'es', 'ar'], 
    
    ...
```
###usage
when you create new post the package automatically fills the translations field in posts table, then the translation is available in update. 

__to get certain locales:__  
```
public function getTranslation(Post $post, $lang)
{
    ...
    
    $post->getTranslation($lang);
    
    ...
}
```

__to translate to certain locales:__
```
public function translate(Post $post, $lang, Request $request)
{
    ...
    
    $post->translate($lang, $request->all());
    
    ...   
}
```
   
