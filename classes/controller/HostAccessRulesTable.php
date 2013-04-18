<?php

class HostAccessRulesTable extends WABEntity {
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->subnet = $params[2];
        $this->host = $params[3];
        $this->width = "99%";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->template = "templates/interface/Table.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/controller/HostAccessRulesTable.js";

        $this->clientClass = "HostAccessRulesTable";
        $this->parentClientClasses = "Entity";        
    }

    function getId() {
        return "HostAccessRulesTable_".$this->module_id."_".$this->subnet."_".$this->host;
    }

    function getAccessRules($name) {
        global $Objects;
        $host = $Objects->get("DhcpHost_".$this->module_id."_".$this->subnet."_".$this->host);
        $read_checked = ""; $write_checked = "";
        if (!$host->loaded)
        	$host->load();
        $access_rules = $host->accessRules;   
        $smbShares = $host->smbShares;
        $nfsShares = $host->nfsShares;
        $afpShares = $host->afpShares;
        $read_checked = ""; $write_checked = "";$smb_checked="";$nfs_checked="";$afp_checked="";
        if (isset($access_rules[$name])) {
            if (@$access_rules[$name]["read_only"]=="yes") {
                $read_checked = "checked";
                $write_checked = "";
            } else {
                $read_checked = "checked";
                $write_checked = "checked";
            }
            if (isset($smbShares[$name])) {
                $smb_checked="checked";                
            }
            if (isset($nfsShares[$name])) {
                $nfs_checked="checked";                
            }
            if (isset($afpShares[$name])) {
                $afp_checked="checked";                
            }
        }
        return $read_checked." ".$write_checked." ".$smb_checked." ".$nfs_checked." ".$afp_checked;
    }

    function getRows() {
        $result = array();
        $c = count($result);
        $result[$c][0] = " ~class=header";
        $result[$c][1] = "Общая папка~class=header&style=width:100%;height:0";
        $result[$c][2] = '<input type="checkbox" id="'.$this->getId().'_checkAllRead" onclick="$O(\''.$this->getId().'\',\'\').checkAllRead(event)">Чтение~class=header&nowrap';
        $result[$c][2] = str_replace('"','\"',$result[$c][2]);
        $result[$c][3] = '<input type="checkbox" id="'.$this->getId().'_checkAllWrite" onclick="$O(\''.$this->getId().'\',\'\').checkAllWrite(event)">Запись~class=header&nowrap';
        $result[$c][3] = str_replace('"','\"',$result[$c][3]);
        if (get_class($this)=="HostAccessRulesTable") {
            $result[$c][4] = '<input type="checkbox" id="'.$this->getId().'_checkAllSMB" onclick="$O(\''.$this->getId().'\',\'\').checkAllSMB(event)">SMB~class=header&nowrap';
            $result[$c][4] = str_replace('"','\"',$result[$c][4]);
            $result[$c][5] = '<input type="checkbox" id="'.$this->getId().'_checkAllNFS" onclick="$O(\''.$this->getId().'\',\'\').checkAllNFS(event)">NFS~class=header&nowrap';
            $result[$c][5] = str_replace('"','\"',$result[$c][5]);
            $result[$c][6] = '<input type="checkbox" id="'.$this->getId().'_checkAllAFP" onclick="$O(\''.$this->getId().'\',\'\').checkAllAFP(event)">AFP~class=header&nowrap';
            $result[$c][6] = str_replace('"','\"',$result[$c][6]);
        }

        $shares = array();
                
        global $Objects;
        $fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$fileServer->sharesLoaded)
            $fileServer->loadShares();
        foreach($fileServer->shares as $share) {
                $shares[$share->name][0] = "<img src='".$share->icon."'>~class=cell";
                if ($share->name!="root")
                    $sharepath = $fileServer->shares_root."/".$share->path;
                else
                    $sharepath = $share->path;
                $shares[$share->name][1] = "<span title='".$sharepath."'>".$share->name."</span>~class=cell";
                if ($share->idnumber!=0)
                    $share_path = $fileServer->shares_root.'/'.$share->path;
                else
                    $share_path = $share->path;
                $read_checked = ""; $write_checked = "";
                $checked = explode(" ",$this->getAccessRules($share->name));
                $read_checked = $checked[0];
                $write_checked = $checked[1];
                if (get_class($this)=="HostAccessRulesTable") {
                    $smb_checked = $checked[2];
                    $nfs_checked = $checked[3];
                    $afp_checked = $checked[4];
                }
                $shares[$share->name][2] = '<input '.@$read_checked.' column="read" share="'.$share->name.'" path="'.$share_path.'" type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').checkRead(event)" id="'.$this->getId().'_Share_'.$share->idnumber.'_Read">~class=cell';
                $shares[$share->name][2] = str_replace('"','\"',$shares[$share->name][2]);
                $shares[$share->name][3] = '<input '.@$write_checked.' column="write" share="'.$share->name.'" path="'.$share_path.'" type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').checkWrite(event)" id="'.$this->getId().'_Share_'.$share->idnumber.'_Write">~class=cell';
                $shares[$share->name][3] = str_replace('"','\"',$shares[$share->name][3]);
                if (get_class($this)=="HostAccessRulesTable") {
                    $shares[$share->name][4] = '<input '.@$smb_checked.' column="SMB" share="'.$share->name.'" path="'.$share_path.'" type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').checkSMB(event)" id="'.$this->getId().'_Share_'.$share->idnumber.'_SMB">~class=cell';
                    $shares[$share->name][4] = str_replace('"','\"',$shares[$share->name][4]);
                    $shares[$share->name][5] = '<input '.@$nfs_checked.' column="NFS" share="'.$share->name.'" path="'.$share_path.'" type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').checkNFS(event)" id="'.$this->getId().'_Share_'.$share->idnumber.'_NFS">~class=cell';
                    $shares[$share->name][5] = str_replace('"','\"',$shares[$share->name][5]);
                    $shares[$share->name][6] = '<input '.@$afp_checked.' column="AFP" share="'.$share->name.'" path="'.$share_path.'" type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').checkAFP(event)" id="'.$this->getId().'_Share_'.$share->idnumber.'_AFP">~class=cell';
                    $shares[$share->name][6] = str_replace('"','\"',$shares[$share->name][6]);
                }
        }

        ksort($shares);

        foreach ($shares as $row)
            $result[count($result)] = $row;

        $res = array();
        foreach ($result as $item)
            $res[count($res)] = implode("#",$item);

        return implode("|",$res);
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{table_rows}"] = $this->getRows();
        return $result;
    }
}
?>