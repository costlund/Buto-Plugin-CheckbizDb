<?php
class PluginCheckbizDb{
  private $settings = null;
  private $mysql = null;
  function __construct() {
    $this->settings = wfPlugin::getPluginSettings('checkbiz/db', true);
    $this->mysql =new PluginWfMysql();
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
}