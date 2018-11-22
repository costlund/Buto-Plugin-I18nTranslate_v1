<?php
/**
<p>Event to translate innerHTML param for an element.</p>
<p>Example to translate the word About to Finnish.</p>
<ul>
<li>Create folder /theme/_parent_/_child_/i18n.
<li>Create file /theme/_parent_/_child_/i18n/fi.yml.
<li>Register translations in the file fi.yml.
<li>Set the event listener and the i18n param in theme settings.yml.
</ul>
<p>This settings in theme settings.yml along with event settings.</p>
#code-yml#
i18n:
  language: fi
#code#
<p>This in fi.yml</p>
#code-yml#
About: Noin
#code#
 */
class PluginI18nTranslate_v1{
  /**
   <p>Set in param events/document_render_string in theme settings.yml.</p>
   */
  public static function event_translate_string($value, $string){
    $i18n = new PluginI18nTranslate_v1();
    $string = $i18n->translateFromTheme($string);
    return $string;
  }
  /**
   * Translate method.
   */
  public function translateFromTheme($innerHTML, $replace = null){
    $data = $this->getData($innerHTML);
    if($data && isset($data[$innerHTML])){
      $innerHTML = $data[$innerHTML];
    }
    /**
     * Replace.
     */
    if($replace){
      foreach ($replace as $key => $value) {
        $innerHTML = str_replace($key, $value, $innerHTML);
      }
    }
    return $innerHTML;
  }
  public function getData($innerHTML){
    $data = null;
    /**
     * Path to translations files.
     */
    $path = '/theme/[theme]/i18n';
    /**
     * Check if path is changed via theme settings file.
     */
    $settings = wfPlugin::getPluginSettings('i18n/translate_v1', true);
    if($settings && $settings->get('settings/path')){
      $path = $settings->get('settings/path');
    }
    /**
     * Retreive language.
     */
    $language = wfI18n::getLanguage();
    if($language){
      /**
       * Check from theme.
       */
      $filename = $path.'/'.$language.'.yml';
      if(wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/app_dir').$filename)){
        $data = wfSettings::getSettings($filename);
      }
      if(wfConfig::get('plugin/i18n/translate_v1/settings/log')){
        if(is_null($data)){
          $this->log($path, $language, 'file_missing', $innerHTML);
        }elseif(!isset($data[$innerHTML])){
          $this->log($path, $language, 'key_missing', $innerHTML);
        }
      }
    }
    return $data;
  }
  /**
   * Log issues.
   * @param type $path
   * @param type $language
   * @param type $type
   * @param type $innerHTML
   * @return type
   */
  private function log($path, $language, $type, $innerHTML){
    wfPlugin::includeonce('wf/yml');
    $filename = 'log_'.date('ymd').'.yml';
    $logfile = new PluginWfYml(wfGlobals::getAppDir().$path.'/'.$filename);
    $array = array('la' => $language, 'time' => date('Y-m-d H:i:s'), 'type' => $type, 'innerHTML' => $innerHTML);
    $logfile->set(wfCrypt::getUid(), $array);
    $logfile->save();
    return null;
  }
}
