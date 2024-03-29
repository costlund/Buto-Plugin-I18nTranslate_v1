<?php
class PluginI18nTranslate_v1{
  public $path = null;
  /**
   <p>Set in param events/document_render_string in theme settings.yml.</p>
   */
  public static function event_translate_string($value, $string, $element){
    $i18n = new PluginI18nTranslate_v1();
    $string = $i18n->translateFromTheme($string);
    /**
     * Make links.
     */
    $scramble = '€€€€';
    if( in_array($element['type'], array('p', 'div', 'li', 'span', 'small')) && isset($element['innerHTML']) && !is_array($element['innerHTML'])){
      $links = $i18n->getLinks();
      foreach($links as $k => $v){
        $href = '/';
        $target = '';
        if(isset($v['href'])){
          $href = $v['href'];
        }
        if(isset($v['target'])){
          $target = ' target="'.$v['target'].'"';
        }
        $string = wfPhpfunc::str_replace($k, '<a href="'.$href.'"'.$target.'>'.substr($k, 0, 1).$scramble.substr($k, 1).'</a>', $string);
      }
      $string = wfPhpfunc::str_replace($scramble, '', $string);
    }
    /**
     * 
     */
    return $string;
  }
  public function event_translate_title($event, $data){
    if(isset($data['innerHTML'])){
      $i18n = new PluginI18nTranslate_v1();
      /**
       * 
       */
      $array = preg_split("/ - /", $data['innerHTML']);
      $str = null;
      foreach($array as $k => $v){
        $str .= ' - '.$i18n->translateFromTheme($v);
      }
      $data['innerHTML'] = wfPhpfunc::substr($str, 3);
      $data['settings']['i18n'] = false;
    }
    return $data;
  }
  public static function event_translate_string_issue($string){
    if(is_numeric(wfPhpfunc::str_replace(array('.', '-'), '', $string))){
      return true;
    }elseif(wfPhpfunc::strlen($string)<=1){
      return true;
    }elseif(wfPhpfunc::strstr($string, '<')){
      return true;
    }elseif(wfPhpfunc::strstr($string, 'item[{')){
      return true;
    }elseif(wfPhpfunc::strstr($string, "\n")){
      return true;
    }elseif(wfPhpfunc::substr($string, 0, 1)=='&'){
      return true;
    }elseif(is_numeric(wfPhpfunc::substr($string, 0, 1)) && strtotime(wfPhpfunc::substr($string, 0, 10))){
      return true;
    }elseif(filter_var($string, FILTER_VALIDATE_EMAIL)){
      return true;
    }elseif(wfPhpfunc::substr($string, 0, 5)=='load:'){
      return true;
    }else{
      return false;
    }
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

    if($innerHTML=='July'){
      // wfHelp::print(PluginI18nTranslate_v1::event_translate_string_issue($innerHTML));
      // exit($innerHTML);
    }

    if(PluginI18nTranslate_v1::event_translate_string_issue($innerHTML)){
      return $innerHTML;
    }

    // if($innerHTML=='July'){
    //   exit($innerHTML);
    // }


    /**
     * 
     */
    $data = $this->getData();
    if($data && isset($data[$innerHTML])){
      $innerHTML = $data[$innerHTML];
    }elseif($data){
      /**
       * Log.
       */
      if(wfConfig::get('plugin/i18n/translate_v1/settings/log')){
        $log = true;
        if(wfConfig::get('plugin/i18n/translate_v1/settings/log_domain_filter') && !wfPhpfunc::strstr(wfServer::getServerName(), wfConfig::get('plugin/i18n/translate_v1/settings/log_domain_filter'))){
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
        if(is_array($value)){
          continue;
        }
        $innerHTML = wfPhpfunc::str_replace($key, $value, $innerHTML);
      }
    }
    return $innerHTML;
  }
  /**
   * Get all translations for a language.
   * @return array
   */
  public function getData($language = null){
    $data = null;
    /**
     * Path to translations files.
     */
    $path = $this->getPath();
    /**
     * Retreive language if not set.
     */
    if(!$language){
      $language = wfI18n::getLanguage();
    }
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
  private function getLinks(){
    $path = $this->getPath();
    $filename = $path.'/_links.yml';
    $links = array();
    if(wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/app_dir').$filename)){
      $links = wfSettings::getSettings($filename);
    }
    return $links;
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
     */
    if($this->log_issue($innerHTML)){
      return null;
    }
    /**
     * Replace slash.
     */
    $innerHTML = wfPhpfunc::str_replace('/', '%slash%', $innerHTML);
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
  private function log_issue($innerHTML){
    if(!$innerHTML){
      return true;
    }elseif(wfPhpfunc::strstr($innerHTML, "'")){
      return true;
    }else{
      return false;
    }
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
