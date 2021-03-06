<?php
class PluginI18nTranslate_v1{
  public $path = null;
  /**
   <p>Set in param events/document_render_string in theme settings.yml.</p>
   */
  public static function event_translate_string($value, $string){
    if(is_numeric(str_replace(array('.', '-'), '', $string))){
      return $string;
    }
    if(strlen($string)<2){
      return $string;
    }
    if(strstr($string, '<')){
      return $string;
    }
    if(strstr($string, 'item[{')){
      return $string;
    }
    if(strstr($string, "\n")){
      return $string;
    }
    $i18n = new PluginI18nTranslate_v1();
    $string = $i18n->translateFromTheme($string);
    return $string;
  }
  /**
   * Translate method.
   */
  public function translateFromTheme($innerHTML, $replace = null){
    /**
     * Skip translation.
     */
    if(wfGlobals::get('settings/plugin/i18n/translate_v1/settings/disabled')==true){
      return $innerHTML;
    }
    /**
     * 
     */
    $data = $this->getData($innerHTML);
    if($data && isset($data[$innerHTML])){
      $innerHTML = $data[$innerHTML];
    }elseif($data){
      /**
       * Log.
       */
      if(wfConfig::get('plugin/i18n/translate_v1/settings/log')){
        $log = true;
        if(wfConfig::get('plugin/i18n/translate_v1/settings/log_domain_filter') && !strstr(wfServer::getServerName(), wfConfig::get('plugin/i18n/translate_v1/settings/log_domain_filter'))){
          $log = false;
        }
        if($log){
          $language = wfI18n::getLanguage();
          $path = $this->getPath();
          $this->log($path, $language, $innerHTML);
        }
      }
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
  /**
   * Get all translations for one language.
   * @return array
   */
  public function getData($innerHTML = null){
    $data = null;
    /**
     * Path to translations files.
     */
    $path = $this->getPath();
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
    }
    return $data;
  }
  public function getPath(){
    /**
     * Path is set in other plugin via object mode.
     */
    if($this->path){
      return $this->path;
    }
    /**
     * Path is set in other plugin via Globals.
     */
    if(wfGlobals::get('settings/plugin/i18n/translate_v1/settings/path')){
      return wfGlobals::get('settings/plugin/i18n/translate_v1/settings/path');
    }
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
    return $path;
  }
  /**
   * Set current path for this object.
   * @param string $path
   */
  public function setPath($path){
    $this->path = $path;
  }
  /**
   * Log when no match.
   * @param string $path
   * @param string $language
   * @param string $innerHTML
   * @return null
   */
  private function log($path, $language, $innerHTML){
    /**
     * Replace slash.
     */
    $innerHTML = str_replace('/', '%slash%', $innerHTML);
    /**
     * 
     */
    wfPlugin::includeonce('wf/yml');
    $filename = $language.'_log.yml';
    $logfile = new PluginWfYml(wfGlobals::getAppDir().$path.'/'.$filename);
    $logfile->set($innerHTML, '');
    $logfile->save();
    return null;
  }
  /**
   * Use this only if you want to change path in Globals param. Use ->path instead for only this object.
   */
  public function set_path($path){
    $GLOBALS['sys']['settings']['plugin']['i18n']['translate_v1']['settings']['path'] = $path;
  }
  public function get_path(){
    if(isset($GLOBALS['sys']['settings']['plugin']['i18n']['translate_v1']['settings']['path'])){
      return $GLOBALS['sys']['settings']['plugin']['i18n']['translate_v1']['settings']['path'];
    }else{
      return null;
    }
  }
}
