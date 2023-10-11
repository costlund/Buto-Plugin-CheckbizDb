<?php
class PluginCheckbizDb{
  private $settings = null;
  private $mysql = null;
  function __construct() {
    $this->settings = wfPlugin::getPluginSettings('checkbiz/db', true);
    $this->mysql =new PluginWfMysql();
  }
  public function has_settings(){
    if($this->settings->get('settings/mysql')){
      return true;
    }else{
      return false;
    }
  }
  public function checkbiz_personalinformation_insert($data){
    $data['id'] = wfCrypt::getUid();
    $this->db_open();
    $sql = new PluginWfYml('/plugin/checkbiz/db/sql/sql.yml', 'checkbiz_personalinformation_insert');
    $sql->setByTag($data, 'rs', true);
    $this->mysql->execute($sql->get());
    return null;
  }
  public function db_open(){
    $this->mysql->open($this->settings->get('settings/mysql'));
  }
  public function on_auth(){
    /**
     * check
     */
    $check = $this->db_checkbiz_personalinformation_select_by_SSN(wfUser::getSession()->get('plugin/banksignering/ui/pid'));
    if($check->get('id') && $check->get('created_at_days')<180){
      return null;
    }
    /**
     * 
     */
    wfPlugin::includeonce('checkbiz/api');
    $checkbiz_api = new PluginCheckbizApi();
    $checkbiz_api->get_personalinformation(wfUser::getSession()->get('plugin/banksignering/ui/pid'));
    /**
     * 
     */
    return null;
  }
  public function db_checkbiz_personalinformation_select_by_SSN($SSN){
    $this->db_open();
    $sql = new PluginWfYml('/plugin/checkbiz/db/sql/sql.yml', __FUNCTION__);
    $sql->setByTag(array('SSN' => $SSN));
    $this->mysql->execute($sql->get());
    return $this->mysql->getOne(array('sql' => $sql->get()));
  }
}