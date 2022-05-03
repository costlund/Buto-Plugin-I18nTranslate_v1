# Buto-Plugin-I18nTranslate_v1


<p>Event to translate innerHTML param for an element.</p>


<p>Example to translate the word About to Finnish.</p>
<ul>
<li>Create folder /theme/_parent_/_child_/i18n.
<li>Create file /theme/_parent_/_child_/i18n/fi.yml.
<li>Register translations in the file fi.yml.
<li>Set the event listener and the i18n param in theme settings.yml.
</ul>
<p>This settings in theme settings.yml along with event settings. Check values to turn on log.</p>

## Event
```
events:
  document_render_string:
    -
      plugin: i18n/translate_v1
      method: translate_string

```
## Theme settings.

Set disabled to true to turn off translation.

```
i18n:
  language: fi
plugin:
  i18n:
    translate_v1:
      settings:
        log: true
        log_domain_filter: 'localhost'
        disabled: false

```
## Translation file
<p>This in fi.yml</p>

```
About: Noin
```

## Dismiss translation for an element
<p>To avoid tranlation for an element set settings/event/document_render_string/disabled to true.</p>

```
type: title
settings:
  event:
    document_render_string:
      disabled: true
innerHTML: 'globals:sys/page/settings/title'
```
<p>Or.</p>

```
type: title
settings:
  i18n: false
innerHTML: 'globals:sys/page/settings/title'
```

## Unset
<p>Unset i18n event for a plugin module.</p>

```
$GLOBALS = wfArray::setUnset($GLOBALS, 'sys/settings/events/document_render_string');
```

Unset for an element and it´s child elements.

```
type: tbody
settings:
  globals:
    -
      path_to_key: 'settings/plugin/i18n/translate_v1/settings/disabled'
      value: true
innerHTML:
  -
    type: tr
    innerHTML: 'td elements...'
```


## PHP
Set optional path to override theme folder.
```
wfPlugin::includeonce('i18n/translate_v1');
$i18n = new PluginI18nTranslate_v1();
$i18n->path = '/plugin/_path_/_to_/_folder_';
echo $i18n->translateFromTheme('Hello World');

```

## Globals
One could set path in globals for a plugin. In this example we handle it in __construct method.

```
if(wfGlobals::get('class')=='invoice'){
  wfGlobals::set('settings/plugin/i18n/translate_v1/settings/path', '/plugin/invoice/invoice_v1/i18n');
}
```

## Element

One could use settings/globals to override globals for an element. By doing so it is possible to set other i18n folder instead of the one located in theme.

```
settings:
  globals:
    -
      path_to_key: 'settings/plugin/i18n/translate_v1/settings/path'
      value: '/plugin/_folder_/_folder_/i18n'
```

## Disable
One could disable in php.
````
function __construct() {
  wfGlobals::set('settings/plugin/i18n/translate_v1/settings/disabled', true);
}
````
