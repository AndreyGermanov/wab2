<?php
class GroupMembersTable extends WABEntity {

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->idnumber = $params[2];
        $this->width = "99%";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template = "templates/interface/Table.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/controller/GroupMembersTable.js";
        
        $this->clientClass = "GroupMembersTable";
        $this->parentClientClasses = "Entity";        
    }

    function getId() {
        return "GroupMembersTable_".$this->module_id."_".$this->idnumber;
    }

    function getRows() {
        $result = array();
        $c = count($result);
        $result[$c][0] = '<input type="checkbox" id="'.$this->getId().'_checkAll" onclick="$O(\''.$this->getId().'\',\'\').checkAll(event)">~class=header';
        $result[$c][0] = str_replace('"','\"',$result[$c][0]);
        $result[$c][1] = " ~class=header";
        $result[$c][2] = "Сетевое устройство~class=header&style=width:100%";
        $hosts_in_group = array();
        $hosts_not_in_group = array();
        global $Objects;
        $fileServer = $Objects->get("FileServer_".$this->module_id."_Files");
        if (!$fileServer->loaded)
            $fileServer->load();
        
        $ds = ldap_connect($fileServer->ldap_host,$fileServer->ldap_port);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }        

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        $r = ldap_bind($ds,$fileServer->ldap_user,$fileServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }        

        $base_dn = $fileServer->ldap_base;
        $res = @ldap_search($ds,$base_dn,"(objectClass=dhcpHost)");
        if ($res!=false) {
            $entries = ldap_get_entries($ds,$res);
        }        
        foreach ($entries as $entry) {
                $arr = explode(",",str_replace("cn=","",$entry["dn"]));                
                $hostid = 'DhcpHost_'.$this->module_id.'_'.@$arr[1].'_'.@$arr[0];
                $host = $Objects->get($hostid);
                $host->setIcon();
                if (@$entry["objectgroup"][0] == $this->idnumber and $this->idnumber!="") {                    
                    $c1 = count($hosts_in_group);                    
                    $hosts_in_group[strtoupper($host->name)][0] = '<input type="checkbox" checked value="checked"  onclick="$O(\''.$this->getId().'\',\'\').check(event)" id="ObjectGroup_'.$this->idnumber.'_'.$host->getId().'">~class=cell';
                    $hosts_in_group[strtoupper($host->name)][0] = str_replace('"','\"',$hosts_in_group[strtoupper($host->name)][0]);
                    $hosts_in_group[strtoupper($host->name)][1] = "<img src='".$host->icon."'>~class=cell";
                    $hosts_in_group[strtoupper($host->name)][2] = $host->name."~class=cell";
                } else if ($host->name!="") {
                    $c1 = count($hosts_not_in_group);
                    $hosts_not_in_group[strtoupper($host->name)][0] = '<input type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').check(event)" id="ObjectGroup_'.$this->idnumber.'_'.$host->getId().'">~class=cell';
                    $hosts_not_in_group[strtoupper($host->name)][0] = str_replace('"','\"',$hosts_not_in_group[strtoupper($entry["cn"][0])][0]);
                    $hosts_not_in_group[strtoupper($host->name)][1] = "<img src='".$host->icon."'>~class=cell";
                    $hosts_not_in_group[strtoupper($host->name)][2] = $host->name."~class=cell";
                }            
        }

        ksort($hosts_in_group);
        ksort($hosts_not_in_group);
        if (count($hosts_in_group)>0) {
            $c = count($result);
            $result[$c][0] = "~class=expandable_cell1";
            $result[$c][1] = " ~class=expandable_cell1";
            $result[$c][2] = "Входят в группу~class=expandable_cell1";
        }
        foreach ($hosts_in_group as $row)
            $result[count($result)] = $row;

        if (count($hosts_not_in_group)>0) {
            $c = count($result);
            $result[$c][0] = "~class=expandable_cell1";
            $result[$c][1] = " ~class=expandable_cell1";
            $result[$c][2] = "Не входят в группу~class=expandable_cell1";
        }

        foreach ($hosts_not_in_group as $row)
            $result[count($result)] = $row;

        $res = array();
        foreach ($result as $item)
            $res[count($res)] = implode("#",$item);

        return implode("|",$res);
    }

    function getArgs() {
        $result = parent::getArgs();
        return $result;
    }
}
?>