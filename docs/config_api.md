# The Configuration API

**Are you writing a new feature for Rehike?** In that case, it is likely that you might want to implement a configuration property in order to toggle the feature.

Fortunately, the configuration API isn't all that difficult to work your way around.

Here's how you can add a new configuration property and check for it in another file!

## Adding a configuration property

Rehike configuration properties are defined through `modules/Rehike/ConfigDefinitions.php`. Anything added to the list in this file will be considered an official configuration property, and exported into the user's `config.json` file when Rehike is updated.

Simply add a new field for your property into this file, and then it should be available for consumption by any other users.

## Checking for user configuration

### In PHP code

In order to check for a user's Rehike configuration preferences, you must first import the configuration API into the file you need it in. In order to do this, include the following line:

```php
use Rehike\ConfigManager\Config;
```

In order to check for a configuration property, you can simply use `Config::getConfigProp`. For example, here is an excerpt from our code regarding the printing of view counts on the watch page.

```php
if (Config::getConfigProp("appearance.noViewsText"))
{
    $number = (int)ExtractUtils::isolateViewCnt($this->viewCount);
    if (is_int($number))
    {
        $this->viewCount = $i18n->formatNumber($number);
    }
}
```

Configuration properties should rarely be set from within PHP code, but you can refer to `controllers/rehike/update_config.php` for a implementation should you need it.

### In Twig code

User configuration is exposed to Twig code through the global `rehike.config` variable. This makes it very easy to check for anywhere it's needed.

Configuration properties are accessed just like properties of this variable, so effectively, you just append the property name to this variable name.

For example, here is an excerpt from one of templates regarding the printing of usernames:

```twig
<span class="stat attribution">
    {% if rehike.config.appearance.usernamePrepends %}
        <span>{{ yt.msgs.usernamePrepend }}</span>
    {% endif %}
    <span>{{ rehike.getText(author) }}</span>
</span>
```