<?php
/* 
 * Класс предназначен для создания отчетов по сетям, которыми управляет класс
 * DhcpServer. Отчет выводит указанные поля сетевых устройств, сгруппированные по подсетям и
 * группам объектов в виде таблицы или в виде списка. Поддерживается возможность
 * отбора выводимых данных по подсетям или по группам объектов. Также поддерживается
 * сортировка по любым полям.
 *
 * Отчет формируется в виде файла ODT, который пользователь скачивает
 * его и сохраняет на своем компьютере
 *
 * Поля, которые участвуют в отчете и по которым сортируются данные:
 *
 * hw_address - MAC-адрес - аппаратный адрес узла
 * fixed_address - IP-адрес - IP-адрес узла
 * name - имя узла - Имя узла
 * title - Краткое описание - краткое описание узла
 * descr - Подробное описание - подробное описание
 * subnet_mask - Маска сети - маска сети
 * routers - Шлюзы - шлюзы
 * domain_name_servers - DNS-серверы - DNS-серверы
 * netbios_name_servers - WINS-серверы - WINS-серверы
 * next_server - TFTP-сервер
 * root_path - корневая папка на TFTP-сервере
 * filename - файл загрузчика на TFTP-сервере
 * interface_mtu - максимальный размер фрэйма
 * ip_forwarding - включена маршрутизация
 * allow_bootings - разрешена загрузка
 * subnet_name - подсеть
 * objectGroup - группа объектов
 * accessRules - права доступа к общим папкам
 *
 * Поля, по которым можно группировать и отбирать данные:
 *
 * subnet_name - подсеть
 * objectGroup - группа объектов
 * 
 */
class NetCenterReport extends WABEntity {

    public $report_fields = array();
    public $selected_fields = array();
    public $group_fields = array();
    public $sort_fields = array();
    public $condition_fields = array();

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template = "templates/controller/NetCenterReport.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/NetCenterReport.js";

        $this->icon = $app->skinPath."images/Tree/networks.gif";
        $this->skinPath = $app->skinPath;
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."NetCenterReport";
        
        $this->width = "540";
        $this->height = "440";
        $this->overrided = "width,height";

        $this->tabs_string = "fields|Поля|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "sort|Сортировка|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "groups|Группировка|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "conditions|Отбор|".$app->skinPath."images/spacer.gif";

        $this->active_tab = "fields";

        $this->report_type = "table";
        
        $this->report_fields["hw_address"] = "MAC-адрес";
        $this->report_fields["fixed_address"] = "IP-адрес";
        $this->report_fields["name"] = "Имя хоста";
        $this->report_fields["title"] = "Краткое описание";
        $this->report_fields["host_type"] = "Тип хоста";
        $this->report_fields["descr"] = "Подробное описание";
        $this->report_fields["subnet_mask"] = "Маска сети";
        $this->report_fields["routers"] = "Шлюзы";
        $this->report_fields["domain_name_servers"] = "Серверы DNS";
        $this->report_fields["netbios_name_servers"] = "Серверы WINS";
        $this->report_fields["next_server"] = "TFTP-сервер";
        $this->report_fields["root_path"] = "Путь к корневой файловой системе";
        $this->report_fields["filename"] = "Загрузочный образ на TFTP-сервере";
        $this->report_fields["interface_mtu"] = "Максимальный размер фрэйма";
        $this->report_fields["ip_forwarding"] = "Включена маршрутизация";
        $this->report_fields["allow_booting"] = "Загрузка разрешена";
        $this->report_fields["subnet_name"] = "Подсеть";
        $this->report_fields["objectGroup"] = "Группа объекта";
        $this->report_fields["accessRules"] = "Права доступа";

        $this->selected_fields[0] = "name";
        $this->selected_fields[1] = "fixed_address";
        $this->selected_fields[2] = "hw_address";
        $this->selected_fields[3] = "title";
        $this->selected_fields[4] = "subnet_mask";
        $this->selected_fields[5] = "routers";
        $this->selected_fields[6] = "domain_name_servers";
        $this->selected_fields[7] = "netbios_name_servers";

        $this->sort_fields[0] = "name";

        $this->group_fields[0] = "subnet_name";
        $this->group_fields[1] = "objectGroup";

        $this->settingsFile = "/etc/WAB2/config/NetCenterReport.conf";
        
        $this->clientClass = "NetCenterReport";
        $this->parentClientClasses = "Entity";
        
        $this->loaded = false;        
    }

    function load() {
        if (file_exists($this->settingsFile)) {
            $strings = file($this->settingsFile);
            $this->report_type = trim($strings[0]);
            if (isset($strings[1]))
                $this->selected_fields = explode(",",trim($strings[1]));
            if (isset($strings[2]))
                $this->sort_fields = explode(",",trim($strings[2]));
            if (isset($strings[3]) and trim($strings[3])!="")
                $this->group_fields = explode(",",trim($strings[3]));
            else
                $this->group_fields = array();
            if (isset($strings[4]) and trim($strings[4])!="" and trim($strings[4])!="\n") {
                $this->condition_fields_string = trim($strings[4]);
                $this->condition_fields = array();
                $arr = explode("|",$strings[4]);
                foreach($arr as $condition_rule) {
                    $value = explode("^",$condition_rule);
                    if (@$value[0]=="")
                        continue;
                    $this->condition_fields[$value[0]] = @$value[1];
                }
            }
        }
        $this->loaded = true;
    }

    function save() {
        if ($this->settingsFile != "") {
            $fp = fopen($this->settingsFile,"w");
            fwrite($fp,$this->report_type."\n");
            if (count($this->selected_fields)>0)
                fwrite($fp,implode(",",$this->selected_fields)."\n");
            else {
                fewrite($fp,"\n");
            }
            if (count($this->sort_fields)>0)
                fwrite($fp,implode(",",$this->sort_fields)."\n");
            else
                fwrite($fp,"\n");
            if (count($this->group_fields)>0)
                fwrite($fp,implode(",",$this->group_fields)."\n");
            else
                fwrite($fp,"\n");
            if (count($this->condition_fields)>0) {
                $arr = array();
                foreach($this->condition_fields as $key=>$value) {
                    if ($value=="")
                        continue;
                    $arr[count($arr)] = $key."^".$value;
                }
                fwrite($fp,implode("|",$arr)."\n");
            }
            else fwrite($fp,"\n");
        }
        $this->loaded = true;
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{all_fields}"] = implode(",",array_keys($this->report_fields))."|".implode(",",array_values($this->report_fields));
        $result["{selected_fields}"] = implode(",",$this->selected_fields);
        $fields = array();$titles=array();
        foreach ($this->selected_fields as $fld) {
            $fields[count($fields)] = $fld;
            $titles[count($titles)] = $this->report_fields[$fld];
        }
        $result["{selected_fields_with_titles}"] = implode(",",$fields)."|".implode(",",$titles);
        $fields1 = array_diff(array_keys($this->report_fields),$fields);
        $titles1 = array_diff(array_values($this->report_fields),$titles);
        $result["{not_selected_fields_with_titles}"] = implode(",",$fields1)."|".implode(",",$titles1);

        $result["{sort_fields}"] = implode(",",$this->sort_fields);

        $fields = array();$titles=array();
        foreach ($this->sort_fields as $fld) {
            $fields[count($fields)] = $fld;
            $titles[count($titles)] = $this->report_fields[$fld];
        }
        $result["{sort_fields_with_titles}"] = implode(",",$fields)."|".implode(",",$titles);
        $fields1 = array_diff(array_keys($this->report_fields),$fields);
        $titles1 = array_diff(array_values($this->report_fields),$titles);
        $result["{not_sort_fields_with_titles}"] = implode(",",$fields1)."|".implode(",",$titles1);

        $result["{group_fields}"] = implode(",",$this->group_fields);

        $fields = array();$titles=array();
        foreach ($this->group_fields as $fld) {
            if ($fld=="")
                continue;
            $fields[count($fields)] = $fld;            
            $titles[count($titles)] = @$this->report_fields[$fld];
        }
        $result["{group_fields_with_titles}"] = implode(",",$fields)."|".implode(",",$titles);
        $fields1 = array_diff(array_keys($this->report_fields),$fields);
        $titles1 = array_diff(array_values($this->report_fields),$titles);
        $result["{not_group_fields_with_titles}"] = implode(",",$fields1)."|".implode(",",$titles1);


        $result["{condition_fields}"] = str_replace("'","\'",@$this->condition_fields_string);

        if ($this->report_type=="table") {
            $result["{table_report_checked}"] = "checked";
            $result["{list_report_checked}"] = "";
        }
        if ($this->report_type=="list") {
            $result["{table_report_checked}"] = "";
            $result["{list_report_checked}"] = "checked";
        }

        return $result;
    }

    function getPresentation() {
        return "Отчет";
    }

    function getId() {
        return "NetCenterReport_".$this->module_id."_".$this->name;
    }

    function report() {
        
        $result_array = array();
        global $Objects;
        $value_arr = array();
        $dhcpSubnets = $Objects->get("DhcpSubnets_".$this->module_id."_Subnets");
        if (!$dhcpSubnets->loaded)
            $dhcpSubnets->load();
        $groups = array();
        foreach ($dhcpSubnets->subnets as $subnet) {
            if (!$subnet->hosts_loaded)
                $subnet->loadHosts(true);
            $hosts_arr = $subnet->hosts;
            $hosts = $hosts_arr;
            foreach ($hosts_arr as $host) {
                $obj_groups = explode(",",$host->objectGroup);
                if (count($obj_groups)>1) {
                    $host->objectGroup = $obj_groups[0];
                    array_shift($obj_groups);
                    foreach($obj_groups as $obj_group) {
                        $host_copy = clone $host;
                        $host_copy->objectGroup=$obj_group;
                        $hosts[] = $host_copy;
                    }
                }
            }
            foreach ($hosts as $host) {
                $pass = true;
                foreach($this->condition_fields as $key=>$value) {
                    if ($value=="")
                        continue;
                    if ($key=="descr") {
                        $value = str_replace("<br>","\n",$value);
                        $value_arr = explode("~",$value);
                        $host_value = $host->fields["descr"];
                    } else
                        if ($key=="accessRules") {
                            $val_arr = array();
                            foreach($host->accessRules as $ke=>$val) {
                                if ($val["read_only"]=="yes")
                                    $val_arr[count($val_arr)] = $ke."-".$val["path"]."-".$val["read_only"];
                                else
                                    $val_arr[count($val_arr)] = $ke."-".$val["path"]."-".$val["read_only"];
                            }
                            $host_value = implode("<br>",$val_arr);
                            $value_arr = explode("~",$value);
                        }
                        else {
                            $value = str_replace("<br>","\n",$value);
                            $value_arr = explode("~",$value);
                            $host_value = $host->fields[$key];
                        }
                        if (count($value_arr)>0 and array_search($host_value,$value_arr)===FALSE)
                            $pass = false;                    
                }
                if (!$pass)
                    continue;
                $key = array();
                $key_values = array();
                if (count($this->group_fields)>0) {
                    foreach($this->group_fields as $group) {
                        $value = @$host->fields[$group];
                        if ($group=="accessRules") {
                            $value_arr = array();
                            foreach($host->accessRules as $key1=>$val) {
                                if ($val["read_only"]=="yes")
                                    $value_arr[count($value_arr)] = $key1."-".$val["path"]."-чтение";
                                else
                                    $value_arr[count($value_arr)] = $key1."-".$val["path"]."-запись";
                            }
                            $value = implode("|",$value_arr);
                        } else {                            
                            $value = str_replace("\n","|",$value);
                        }
                        if ($value=="allow booting")
                            $value = "да";
                        if ($value=="deny booting")
                            $value = "нет";
                        if ($value=="false")
                            $value = "нет";
                        if ($value=="true")
                            $value = "да";
                        if ($group=="objectGroup") {
                            $value = explode(",",$value);
                            $value = $value[0];
                            $obj_group = $Objects->get("ObjectGroup_".$this->module_id."_".$value);
                            if (!$obj_group->loaded)
                                    $obj_group->load();
                            if ($obj_group->loaded)
                                $value = $obj_group->name;
                            else
                                $value = "Вне групп";
                        }
                        if ($group=="subnet_name") {
                            $value = $subnet->title."(".$value.")";
                        }
                        if ($group=="host_type") {
                            $value = $host->host_types[$value];
                        }
                        if ($group=="descr")
                            $value = str_replace("\n","|",$value);
                        $key[count($key)] = str_replace("_","|",$value);
                        $key_values[count($key_values)] = $value;
                    }
                }
                $groups[implode("_",$key)] = implode("_",$key_values);
                foreach($this->sort_fields as $sort) {
                        $value = $host->fields[$sort];
                        if ($sort=="accessRules") {
                            $value_arr = array();
                            foreach($host->accessRules as $key1=>$val) {
                                if ($val["read_only"]=="yes")
                                    $value_arr[count($value_arr)] = $key1."-".$val["path"]."-чтение";
                                else
                                    $value_arr[count($value_arr)] = $key1."-".$val["path"]."-запись";
                            }
                            $value = implode("|",$value_arr);
                        } else {
                            $value = str_replace("\n","|",$value);
                        }
                        if ($value=="allow booting")
                            $value = "да";
                        if ($value=="deny booting")
                            $value = "нет";
                        if ($value=="false")
                            $value = "нет";
                        if ($value=="true")
                            $value = "да";
                        if ($sort=="objectGroup") {
                            $value = explode(",",$value);
                            $value = $value[0];
                            $obj_group = $Objects->get("ObjectGroup_".$this->module_id."_".$value);
                            if (!$obj_group->loaded)
                                    $obj_group->load();
                            if ($obj_group->loaded)
                                $value = $obj_group->name;
                            else
                                $value = "Вне групп";
                        }
                        if ($sort=="subnet_name") {
                            $value = $subnet->title."(".$value.")";
                        }
                        if ($sort=="host_type") {
                            $value = $host->host_types[$value];
                        }
                        if ($sort=="descr")
                            $value = str_replace("\n","|",$value);
                    $key[count($key)] = str_replace("_","|",$value);
                }
                $values = array();
                foreach($this->selected_fields as $field) {                    
                    if ($field=="accessRules") {
                        $value_arr = array();
                        foreach($host->accessRules as $ke=>$val) {
                            $share_types=array();
                            if (isset($host->smbShares[$ke]))
                                    $share_types[] = "SMB";
                            if (isset($host->nfsShares[$ke]))
                                    $share_types[] = "NFS";
                            if (isset($host->afpShares[$ke]))
                                    $share_types[] = "AFP";
                            $share_string = implode("/",$share_types);
                            if ($val["read_only"]=="yes")
                                $value_arr[count($value_arr)] = $ke."-".$val["path"]."-чтение-".$share_string;
                            else
                                $value_arr[count($value_arr)] = $ke."-".$val["path"]."-запись-".$share_string;
                        }
                        $value = implode("<br>",$value_arr);
                    } else {
                        $value = $host->fields[$field];
                        $value = str_replace("|","\n",$value);
                    }
                    if ($value=="allow booting")
                        $value = "да";
                    if ($value=="deny booting")
                        $value = "нет";
                    if ($value=="false")
                        $value = "нет";
                    if ($value=="true")
                        $value = "да";
                    if ($field=="objectGroup") {
                        $obj_group = $Objects->get("ObjectGroup_".$this->module_id."_".$value);
                        if (!$obj_group->loaded)
                                $obj_group->load();
                        if ($obj_group->loaded)
                            $value = $obj_group->name;
                        else
                            $value = "Вне групп";
                    }
                    if ($field=="subnet_name") {
                        $value = $subnet->title."(".$value.")";
                    }
                    if ($field=="host_type") {
                        $value = $host->host_types[$value];
                    }
                    if ($field=="descr")
                        $value = str_replace("|","\n",$value);

                    $values[count($values)] = $value;
                }
                $result_array[implode("_",$key)] = implode("|",$values);
            }
        }
        $groups = array_unique($groups);
        uksort($groups,"cmp");
        $predgrp = array();
        $curgrp = array();
        foreach($groups as $key=>$value) {
            $key_arr = explode("_",$key);
            $value_arr = explode("_",$value);
            for ($counter=0;$counter<count($key_arr);$counter++) {
                if ($key_arr[$counter] != @$predgrp[$counter]) {
                    if ($counter==0)
                        $counter=1;
                    $key_arr = array_slice($key_arr,0,$counter);
                    $result_array[implode("_",$key_arr)] = implode("/",array_slice($value_arr,0,$counter));
                    $predgrp = array_slice($key_arr,0,$counter);
                    continue;
                }
            }
            $result_array[$key] = str_replace("_","/",$value);
        }
        $arr = array();
        foreach($this->selected_fields as $fld) {
            $arr[count($arr)] = $this->report_fields[$fld];
        }
        $result_array["______header"] = implode("|",$arr);
        uksort($result_array,"cmp");
        $fp = fopen("tmp/report.odt","w");
        fwrite($fp,'<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Отчет</title></head><body>'."\n");
        if ($this->report_type=="table")
            fwrite($fp,"<table cellpadding=5 cellspacing='1' bgcolor='#000000'>\n");
        foreach($result_array as $key=>$value) {
            if ($key=="")
                continue;
            $value = str_replace("\n","<br>",$value);
            if ($this->report_type=="table")
                fwrite($fp,"<tr valign='top' bgcolor='#ffffff'>\n");
            if (count(explode("_",$key))<=count($this->group_fields) and $key!="______header") {
                if ($this->report_type=="table")
                    fwrite($fp,"<td style='font-weight:bold;font-face:Arial;font-size:13px;background-color:#CCCCCC;' colspan='".count($this->selected_fields)."'>".$value);
                else
                    fwrite($fp,"<span style='font-face:Arial;font-size:13px;font-weight:bold'>".$value."</span><br>");
            } else {
                if ($key=="______header") {
                    if ($this->report_type=="table")
                        $style = "text-align:center;font-weight:bold;font-face:Arial;font-size:15px;background-color:#AAAAAA;";
                        $header = explode("|",$value);
                }
                else {
                    $style = "font-face:Arial;font-size:11px;";
                }
                $value_arr = explode("|",$value);
                for($c=0;$c<count($value_arr);$c++) {
                    $val = $value_arr[$c];
                    if ($val=="")
                        $val="&nbsp;";
                    if ($this->report_type=="table")
                        fwrite($fp,"<td style='".$style."'>".$val."</td>");
                    else {
                        if ($key!="______header") {
                            fwrite($fp,"<span style='".$style."'><b>".$header[$c].": </b>".$val."</span><br>");
                        }
                    }
                }
                if ($this->report_type=="list")
                    fwrite($fp,"<br>");
            }
            if ($this->report_type=="table")
                fwrite($fp,"</tr>\n");
        }
        if ($this->report_type=="table")
            fwrite($fp,"</table>\n");
        fwrite($fp,"</body></html>");
        fclose($fp);
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "reportHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function reportHook($arguments) {
    	$this->setArguments($arguments);
    	$this->selected_fields = (array)$arguments["selected_fields"];
    	$this->sort_fields = (array)$arguments["sort_fields"];
    	$this->group_fields = (array)$arguments["group_fields"];
    	$this->condition_fields = (array)$arguments["condition_fields"];
    	$this->save();
    	$this->report();
    }
}
?>