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

```
events:
  document_render_string:
    -
      plugin: i18n/translate_v1
      method: translate_string

```
```
i18n:
  language: fi
plugin:
  i18n:
    translate_v1:
      settings:
        log: true
        log_domain_filter: 'localhost'

```

<p>This in fi.yml</p>

```
About: Noin
```

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


<p>Unset i18n event for a plugin module.</p>

```
$GLOBALS = wfArray::setUnset($GLOBALS, 'sys/settings/events/document_render_string');
```

