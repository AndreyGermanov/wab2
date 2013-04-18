<?php
/*
 * Класс адаптера данных для базы данных журнала доступа к файловым ресурсам 
 * Samba.
 */
class FullAuditDataAdapter extends PDODataAdapter {   
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $this->driver = "pdo_mysql";
        $this->host = $fileServer->smbAuditDBHost;
        $this->port = $fileServer->smbAuditDBPort;
        $this->dbname = $fileServer->smbAuditDBName;
        $this->user = $fileServer->smbAuditDBUser;
        $this->password = $fileServer->smbAuditDBPassword;
                                
        $this->clientClass = "FullAuditDataAdapter";
        $this->parentClientClasses = "PDODataAdapter~DataAdapter~Entity";        
    }
}
?>