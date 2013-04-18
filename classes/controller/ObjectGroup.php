<?php
/**
 * Класс управляет общим файловым ресурсом. Общий файловый ресурс состоит из
 * уникального идентификатора, имени и пути к нему. Информация об общем ресурсе
 * хранится в каталоге LDAP, в записи, использующей объектные классы top и fileShare.
 * Записи общих ресурсов хранятся в контейнере FileServer->getDN(), который состоит из
 * "ou=".FileServer->shares_base.",".FileServer->base_dn.
 *
 * Ресурс уникально идентифицируется своим уникальным идентификатором.
 * Общие ресурсы можно создавать, редактировать и удалять.  Удаляются общие ресурсы
 * методом removeShare класса FileServer. Для остальных операций предназначены
 * методы этого класса:
 *
 * load() - загружает информацию о ресурсе из базы
 * save() - сохраняет информацию о ресурсе в базу
 * getId() - возвращает идентификатор объекта
 * getDN() - возвращает имя DN-записи объекта
 * getPresentation() - возвращает представление объекта
 * 
 *
 * @author andrey
 */
class ObjectGroup extends WABEntity {

    public $fileServer;

    function construct($params) {

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->module_id = $params[0]."_".$params[1];
        $this->idnumber = $params[2];

        global $Objects;
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();

        $this->shares_dn = $this->fileServer->getObjectGroupDN();

        $this->name = "";
        $this->old_name = $this->name;
        $this->icon = $app->skinPath."images/Tree/objectgroup.png";

        $this->template = "templates/controller/ObjectGroup.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->skinPath = $app->skinPath;
        $this->handler = "scripts/handlers/controller/ObjectGroup.js";

        $this->width = "500";
        $this->height = "320";
        $this->overrided = "width,height";

        $this->clientClass = "ObjectGroup";
        $this->parentClientClasses = "Entity";
        
        $this->referenceCode = "";
        
        $this->loaded = false;
    }

    function getNextId() {
        $ds = ldap_connect($this->fileServer->ldap_proto."://".$this->fileServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->fileServer->ldap_user,$this->fileServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds,$this->shares_dn,("(objectClass=objectGroup)"));
        $entries = ldap_get_entries($ds,$res);

        if ($entries == FALSE)
            return 1;
        if ($entries["count"]<1)
            return 1;

        $max = 0;
        foreach ($entries as $key=>$value) {
            if (is_numeric($key)) {
                if ($max<$value["idnumber"])
                    $max = $value["idnumber"][0];
            }
        }
        return $max+1;
    }
    
    function save($arguments=null) {

    	if (isset($arguments)) {
    		$this->load();
    		$this->setArguments($arguments);
    		$this->changed_rules = $arguments["changed_rules"];
    	}
        if ($this->name == "") {
            $this->reportError("Укажите имя группы","save");
            return 0;
        }

        if ($this->name != $this->old_name) {
            if ($this->fileServer->containsObjectGroup($this->name)) {
                $this->reportError("Группа с именем ".$this->name." уже существует","save");
                return 0;
            }
        }

        if ($this->name == "Вне групп") {
            $this->reportError("Недопустимое имя группы. Это имя зарезервировано");
            return 0;
        }
        if ($this->idnumber!="0") {

            $ds = ldap_connect($this->fileServer->ldap_proto."://".$this->fileServer->ldap_host);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
                return 0;
            }

            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            $r = ldap_bind($ds,$this->fileServer->ldap_user,$this->fileServer->ldap_password);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
                return 0;
            }

            $entry = array();
            $entry["objectClass"][0] = "objectGroup";
            $entry["comment"] = $this->name;

            global $Objects;

            if ($this->fileServer->containsObjectGroup($this->old_name)) {
                ldap_modify($ds,"idnumber=".$this->idnumber.",".$this->shares_dn,$entry);
            } else {
                $entry["idnumber"] = $this->getNextId();                
                $this->idnumber = $entry["idnumber"];
                ldap_add($ds,"idnumber=".$entry["idnumber"].",".$this->shares_dn,$entry);
            }
        }
        if ($this->changed_rules != "") {
            $changed_arr = explode("|",$this->changed_rules);
            foreach($changed_arr as $rule) {
                $rule_arr = explode("=",$rule);                
                $obj = $Objects->get($rule_arr[0]);
                if (!$obj->loaded) {
                    $obj->load();
                    if (!$obj->loaded)
                        continue;
                    $arr = explode(",",$obj->objectGroup);
                    if ($rule_arr[1]=="yes") {
                        if (array_search($this->idnumber,$arr)===FALSE) {
                            $arr[] = $this->idnumber;
                        }
                    }
                    else {                        
                        if (array_search($this->idnumber,$arr)!==FALSE)
                        unset($arr[array_search($this->idnumber,$arr)]);
                    }
                    if (array_search("0",$arr)!==FALSE)
                        unset($arr[array_search("0",$arr)]);
                    $obj->objectGroup = implode(",",$arr);
                    $obj->save(true);
                }                
            }
        }
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        if ($this->old_name=="")
        	$app->raiseRemoteEvent("OBJECTGROUP_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("OBJECTGROUP_CHANGED","object_id=".$this->getId());
        	
        $this->old_name = $this->name;
        echo $this->idnumber;
        $this->loaded = true;
    }

    function load() {
    	global $Objects;
        $ds = ldap_connect($this->fileServer->ldap_proto."://".$this->fileServer->ldap_host);
        $this->accessRulesTableId = "GroupMembersTable_".$this->module_id."_".$this->idnumber;
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->fileServer->ldap_user,$this->fileServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }
        $result = ldap_list($ds,$this->shares_dn,"(idnumber=".$this->idnumber.")");

        if ($result == FALSE)
            return 0;
        $entries = ldap_get_entries($ds,$result);
        if ($entries["count"]==0)
            return 0;
        $this->name = $entries[0]["comment"][0];
        $this->old_name = $this->name;
        $this->loaded = true;

        $app = $Objects->get($this->module_id);
        if ($this->loaded and is_object($app->docFlowApp)) {
        	$this->adapter = $Objects->get("DocFlowDataAdapter_".$app->docFlowApp->getId()."_1");
        	$query = "SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceObjectGroupInfoCard'";
        	$res = PDODataAdapter::makeQuery($query, $this->adapter,$app->docFlowApp->getId());
        	if (count($res)>0) {
        		$card = current($res);
        		$this->referenceCode = "<input type='button' fileid='".$card->getId()."' id='referenceButton' value='Открыть описание'/>";
        	} else {
        		$this->referenceCode = "<input type='button' fileid='ReferenceObjectGroupInfoCard_".$app->docFlowApp->getId()."_' id='referenceButton' value='Открыть описание'/>";
        	}
        }        
    }
    
    function getRows() {
        $result = array();
        $c = count($result);
        $result[$c][0] = '<input type="checkbox" id="'.$this->accessRulesTableId.'_checkAll" onclick="$O(\''.$this->accessRulesTableId.'\',\'\').checkAll(event)">~class=header';
        $result[$c][0] = str_replace('"','\"',$result[$c][0]);
        $result[$c][1] = " ~class=header";
        $result[$c][2] = "Сетевое устройство~class=header&style=width:100%";
        $hosts_in_group = array();
        $hosts_not_in_group = array();
        global $Objects;
        $fileServer = $this->fileServer;
        if (!$fileServer->loaded)
            $fileServer->load();
        
        $ds = ldap_connect($fileServer->ldap_proto."://".$fileServer->ldap_host);
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
            foreach ($entries as $entry) {
                    $arr = explode(",",str_replace("cn=","",$entry["dn"]));                
                    $hostid = 'DhcpHost_'.$this->module_id.'_'.@$arr[1].'_'.@$arr[0];
                    $host = $Objects->get($hostid);
                    $host->host_type = $entry["hosttype"][0];
                    $arr2 = array_flip(explode(",",@$entry["objectgroup"][0]));
                    $host->setIcon();
                    if (isset($arr2[$this->idnumber]) and $this->idnumber!="") {                    
                        $c1 = count($hosts_in_group);                    
                        $hosts_in_group[strtoupper($arr[0])][0] = '<input type="checkbox" checked value="checked"  onclick="$O(\''.$this->accessRulesTableId.'\',\'\').check(event)" id="ObjectGroup_'.$this->idnumber.'_'.$hostid.'">~class=cell';
                        $hosts_in_group[strtoupper($arr[0])][0] = str_replace('"','\"',@$hosts_in_group[strtoupper($arr[0])][0]);
                        $hosts_in_group[strtoupper($arr[0])][1] = "<img src='".$host->icon."'>~class=cell";
                        $hosts_in_group[strtoupper($arr[0])][2] = "<span title='".@$entry["comment"][0]."'>&nbsp;&nbsp;&nbsp;&nbsp;".$arr[0]."</span>~class=cell";
                    } else if ($arr[0]!="") {
                        $c1 = count($hosts_not_in_group);
                        $hosts_not_in_group[strtoupper($arr[0])][0] = '<input type="checkbox" onclick="$O(\''.$this->accessRulesTableId.'\',\'\').check(event)" id="ObjectGroup_'.$this->idnumber.'_'.$hostid.'">~class=cell';
                        $hosts_not_in_group[strtoupper($arr[0])][0] = str_replace('"','\"',$hosts_not_in_group[strtoupper($arr[0])][0]);
                        $hosts_not_in_group[strtoupper($arr[0])][1] = "<img src='".$host->icon."'>~class=cell";
                        $hosts_not_in_group[strtoupper($arr[0])][2] = "<span title='".@$entry["comment"][0]."'>&nbsp;&nbsp;&nbsp;&nbsp;".$arr[0]."</span>~class=cell";
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
        }

        return implode("|",$res);
    }   

    function getId() {
        return "ObjectGroup_".$this->module_id."_".$this->idnumber;
    }

    function getDN() {
        return "idnumber=".$this->idnumber.",".$this->shares_dn;
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->name;
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{access_rules_table_id}"] = "GroupMembersTable_".$this->module_id."_".$this->idnumber;
        $result["{window_id}"] = str_replace("_","",$this->getId());
        $this->table_rows = $this->getRows();
        $this->table_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->table_rows))));
        $result["{table_rows}"] = $this->table_rows;       
        $result["{|referenceCode}"] = $this->referenceCode; 
        return $result;
    }

    function getObjects() {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        $result = array();
        $dhcpSubnets = $Objects->get("DhcpSubnets_".$this->module_id."_Subnets");
        if (!$dhcpSubnets->loaded)
            $dhcpSubnets->load();
        foreach ($dhcpSubnets->subnets as $subnet) {
            if (!$subnet->hosts_loaded)
                $subnet->loadHosts();
                foreach ($subnet->hosts as $host) {
                    $obj_group = $host->objectGroup;
                    $arr = array_flip(explode(",",$obj_group));
                    if ($obj_group=="")
                        $obj_group = 0;
                    if (isset($arr[$this->idnumber]))
                        $result[strtoupper($host->name)] = $host;
                }
        }
        return $result;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    }
}
?>