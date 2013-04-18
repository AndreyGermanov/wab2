<?php
class ShareAccessRulesTable extends WABEntity {
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->idnumber = $params[2];
        $this->type = $params[3];

        switch ($this->type) {
            case "hosts":
                $this->title = "Хост";
                break;
            case "users":
                $this->title = "Пользователь";
                break;
            case "groups":
                $this->title = "Группа";
                break;
        }
        $this->width = "99%";

        $this->template = "templates/interface/Table.html";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/controller/ShareAccessRulesTable.js";
        
        $this->clientClass = "ShareAccessRulesTable";
        $this->parentClientClasses = "Entity";        
    }

    function getId() {
        return "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_".$this->type;
    }

    function getRows() {

        global $Objects;

        $fileShare = $Objects->get("FileShare_".$this->module_id."_".$this->idnumber);
        if (!$fileShare->loaded)
         	$fileShare->load();

        $result = array();
        $c = count($result);
        $result[$c][0] = " ~class=header";
        $result[$c][1] = $this->title."~class=header&style=width:100%";
        $result[$c][2] = '<input column="groupRead" type="checkbox" id="'.$this->getId().'_checkAllRead" onclick="$O(\''.$this->getId().'\',\'\').checkAllRead(event)">Чтение~class=header&nowrap';
        $result[$c][2] = str_replace('"','\"',$result[$c][2]);
        $result[$c][3] = '<input column="groupWrite" type="checkbox" id="'.$this->getId().'_checkAllWrite" onclick="$O(\''.$this->getId().'\',\'\').checkAllWrite(event)">Запись~class=header&nowrap';
        $result[$c][3] = str_replace('"','\"',$result[$c][3]);

        $check_read = "";
        $check_write = "";
        if ($this->type=="hosts") {
            if (isset($fileShare->accessRules[$fileShare->name])) {
                if ($fileShare->accessRules[$fileShare->name]["read_only"]=="yes") {
                    $check_read = 'checked value="checked"';
                    $check_write = "";
                } else {
                    $check_read = 'checked value="checked"';
                    $check_write = 'checked value="checked"';
                }
            }
        }
        if ($this->type=="users") {
            if (isset($fileShare->users_access_rules[$fileShare->name])) {
                if (array_pop(explode("~",$fileShare->users_access_rules[$fileShare->name]))=="r") {
                    $check_read = 'checked value="checked"';
                    $check_write = "";
                } else if (array_pop(explode("~",$fileShare->users_access_rules[$fileShare->name]))=="rw") {
                    $check_read = 'checked value="checked"';
                    $check_write = 'checked value="checked"';
                }
            }
        }
        if ($this->type=="groups") {
            if (isset($fileShare->groups_access_rules[$fileShare->name])) {
                if (array_pop(explode("~",$fileShare->groups_access_rules[$fileShare->name]))=="r") {
                    $check_read = 'checked value="checked"';
                    $check_write = "";
                } else if (array_pop(explode("~",$fileShare->groups_access_rules[$fileShare->name]))=="rw") {
                    $check_read = 'checked value="checked"';
                    $check_write = 'checked value="checked"';
                }
            }
        }
        if ($this->type != "groups") {
            $c = count($result);
            $result[$c][0] = '~class=cell';
            $result[$c][1] = "По умолчанию~class=cell";
            $result[$c][2] = '<input '.$check_read.' column="defaultRead" type="checkbox" id="'.$this->getId().'_Default_checkRead" onclick="$O(\''.$this->getId().'\',\'\').checkRead(event)">~class=cell&nowrap';
            $result[$c][2] = str_replace('"','\"',$result[$c][2]);
            $result[$c][3] = '<input '.$check_write.' column="defaultWrite" type="checkbox" id="'.$this->getId().'_Default_checkWrite" onclick="$O(\''.$this->getId().'\',\'\').checkWrite(event)">~class=cell&nowrap';
            $result[$c][3] = str_replace('"','\"',$result[$c][3]);
        }

        $fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $object_groups = array();
        if ($this->type=="hosts") {
            if (!$fileServer->objectGroupsLoaded)
                $fileServer->loadObjectGroups();
            foreach ($fileServer->objectGroups as $group) {
                $object_groups[$group->name] = $group;
            }
        }
        if ($this->type=="users") {
            if (!$fileServer->usersLoaded)
                $fileServer->loadUsers();
            $users = array();
            foreach ($fileServer->users as $user) {
                if ($user->name=="" or $user->name =="root")
                    continue;
                $users[$user->name] = $user;
            }
            $object_groups["users"] = $users;
        }
        if ($this->type=="groups") {
            if (!$fileServer->groupsLoaded)
                $fileServer->loadGroups();
            $groups = array();
            foreach ($fileServer->groups as $group) {
                if ($group->name=="" or $group->name =="root")
                    continue;
                    $groups[$group->name] = $group;
            }
            $object_groups["groups"] = $groups;
        }
        
        ksort($object_groups);

        foreach($object_groups as $group) {
            if ($this->type=="hosts") {
                $hosts = $group->getObjects();
                $parent_group = $group->idnumber;
                $display_style = "none";
            }
            else {
                $hosts = $group;
                $parent_group = "''";
                $display_style = "";
            }
            ksort($hosts);
            if ($this->type=="hosts") {
                $c = count($result);
                $result[$c][0] = '<img src="'.$group->icon.'" onclick="$O(\''.$this->getId().'\',\'\').expandGroup(event,\''.$group->idnumber.'\')">~class=expandable_cell1';
                $result[$c][0] = str_replace('"','\"',$result[$c][0]);
                $result[$c][1] = $group->name."~class=expandable_cell1";
                $result[$c][2] = '<input column="groupRead" type="checkbox" id="'.$this->getId().'_'.$group->idnumber.'_checkGroupRead" onclick="$O(\''.$this->getId().'\',\'\').checkGroupRead(event,\''.$group->idnumber.'\')">~class=expandable_cell1&nowrap';
                $result[$c][2] = str_replace('"','\"',$result[$c][2]);
                $result[$c][3] = '<input column="groupWrite" type="checkbox" id="'.$this->getId().'_'.$group->idnumber.'_checkGroupWrite" onclick="$O(\''.$this->getId().'\',\'\').checkGroupWrite(event,\''.$group->idnumber.'\')">~class=expandable_cell1&nowrap';
                $result[$c][3] = str_replace('"','\"',$result[$c][3]);
            }
            foreach($hosts as $host) {
                $c = count($result);
                $check_read = ""; $check_write="";
                if ($this->type=="hosts") {
                    if (!$host->loaded)
                        $host->getAccessRules();
                    if (isset($host->accessRules[$fileShare->name])) {
                        if ($host->accessRules[$fileShare->name]["read_only"]=="yes") {
                            $check_read = 'checked value="checked"';
                            $check_write = "";
                        } else {
                            $check_read = 'checked value="checked"';
                            $check_write = 'checked value="checked"';
                        }
                    }
                    $id = $this->getId().'_'.$host->getId();
                } else {
                    if (!$host->loaded)
                        $host->getAccessRules();
                    if (isset($host->shares_access_rules[$fileShare->name])) {
                        if (array_pop(explode("~",$host->shares_access_rules[$fileShare->name]))=="r") {
                            $check_read = 'checked value="checked"';
                            $check_write = "";
                        } else if (array_pop(explode("~",$host->shares_access_rules[$fileShare->name]))=="rw") {
                            $check_read = 'checked value="checked"';
                            $check_write = 'checked value="checked"';
                        }
                    }
                    $id = $host->name;
                }

                $result[$c][0] = '<img src="'.$host->icon.'">~class=cell';
                $result[$c][0] = str_replace('"','\"',$result[$c][0]);
                $result[$c][1] = $host->name."~class=cell";
                $result[$c][2] = '<input '.$check_read.' column="hostRead" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkRead" onclick="$O(\''.$this->getId().'\',\'\').checkRead(event)">~class=cell&nowrap';
                $result[$c][2] = str_replace('"','\"',$result[$c][2]);
                $result[$c][3] = '<input '.$check_write.' column="hostWrite" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkWrite" onclick="$O(\''.$this->getId().'\',\'\').checkWrite(event)">~class=cell&nowrap';
                $result[$c][3] = str_replace('"','\"',$result[$c][3]);
                $result[$c][4] = "row_attrs~parent_group=".$parent_group."&style=display:".$display_style;
            }
        }

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