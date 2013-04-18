<?php
/**
 * Данный класс предназначен для отображения списка сущностей в виде таблицы.
 * Он выполняет запрос к указанному хранилищу adapter в котором он ищет сущности
 * указанных классов, которые передаются в формате сlassName, применяя при этом
 * условие condition. К этому условию также может добавляться параметры LIMIT,
 * которые вычисляются исходя из переменных currentPage и itemsPerPage. Параметр
 * sortOrder задает порядок сортировки.
 *
 * В колонках таблицы отображаются поля сущности, которые указываются в переменной
 * fieldList. Также добавляются две дополнительные колонки в начало: "GroupCheckbox"
 * с флажком, для того чтобы можно было выделять несколько строк для работы с ними
 * как с группой и entityImage - картинка-кнопка типа hidden, позволяющая разворачивать
 * сущность в случае если у нее есть дочерние элементы. Картинка, отображаемая
 * на кнопке определяется одной из двух иконок: entityIcon - если у сущности нет
 * потомков и groupIcon если у сущности есть потомки. Также вместо entityImage
 * может браться картинка из поля самой сущности. Поле определеяется параметром
 * entityImageField.
 *
 * Наличие потомков у сущности определяется значением поля сущности,
 * на которое указывает свойство parentField. Это должно быть поле типа Entity,
 * которая ссылается на родительскую сущность. Для каждой сущности определяется,
 * есть ли в базе данных сущности, у которых поле parentField указывает на нее
 * и если есть, то считается что у сущности есть потомки и в таблице для нее
 * отображается groupIcon. Будет ли вообще работать режим иерархичности определяется
 * параметром hierarchy. Если он установлен в true, то разворачивание по иерархии
 * будет работать, иначе нет.
 *
 * Также есть еще одна дополнительная колонка entityName, в которой находится имя
 * текущей сущности.
 *
 * Если указан параметр sortField и в нем указано поле, то по умолчанию сортировка
 * производится по этому полю и только в этом случае появляются кнопки "вверх"/
 * "вниз" над таблицей, что позволяет менять порядок сортировки полей вручную
 * на уровне базы данных, меняя значение поля sortField.
 *
 * 
 *
 * @author andrey
 */
class LVMSnapshotsDataTable extends DataTable {

    public $snapshots = array();
    
    function construct($params) {
        parent::construct($params);
        $this->sortOrder = "";
        $this->fieldList = "";
        $this->hierarchy = false;

        $this->itemsPerPage = 0;
        $this->currentPage = 1;
        $this->entityId = "";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;

        $this->entityImage = $this->skinPath."images/Buttons/EntityImage.png";
        $this->groupImage = $this->skinPath."images/Buttons/entityGroupImage.png";
        $this->defaultClassName = "";
        $this->entityImageField = "";
        $this->sortField = "sortOrder";

        $this->editorType = "WABWindow";

        $this->contextMenuId = "";

        $this->parent_object_id = "";

        $this->windowWidth = 400;
        $this->windowHeight = 500;
        $this->template = "templates/controller/LVMSnapshotsDataTable.html";

        $this->windowTitle = "";
        $this->additionalFields = "";
        $this->divName = "";
        $this->destroyDiv = false;
        
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $this->gapp = $Objects->get("Application");
        if (!$this->gapp->initiated)
            $this->gapp->initModules();
        $this->app = $Objects->get($this->module_id);
        $this->shell = $Objects->get("Shell_shell");

        $this->tableId = "";
        $this->forEntitySelect = false;        
//        $this->template = "tempates/EntityDataTable.html";
        $this->handler = "scripts/handlers/controller/LVMSnapshotsDataTable.js";
        
        $this->clientClass = "LVMSnapshotsDataTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }
    
    function getVgInfo() {
        if ($this->app->remoteSSHCommand!="") {
            $vg_info = shell_exec($this->app->remoteSSHCommand." '".$this->fileServer->vgDisplayCommand." --units G ".$this->fileServer->shadowCopyVgName." | fmt -us'");
            $lv_info = shell_exec($this->app->remoteSSHCommand." '".$this->fileServer->lvDisplayCommand." --units G /dev/".$this->fileServer->shadowCopyVgName."/".$this->fileServer->shadowCopyLvName." | fmt -us");
            $lv_info2 = shell_exec($this->app->remoteSSHCommand." '".$this->fileServer->lvDisplayCommand." --units G /dev/".$this->fileServer->shadowCopyVgName."/".$this->fileServer->shadowCopyLvName." | fmt -us");
        } else {
            $vg_info = $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->vgDisplayCommand." --units G ".$this->fileServer->shadowCopyVgName." | fmt -us");
            $lv_info = $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->lvDisplayCommand." --units G /dev/".$this->fileServer->shadowCopyVgName."/".$this->fileServer->shadowCopyLvName." | fmt -us");
        }
        $vg_strings = explode("\n",$vg_info);
        $lv_strings = explode("\n",$lv_info);
        $this->vgSize = "";
        foreach ($vg_strings as $str) {
            $str = trim($str);
            $matches = array();
            if (preg_match("/VG Size (.*) GB/",$str,$matches)) {
                $this->vgSize = str_replace(",",".",trim($matches[1]));
            }
            $matches = array();
            if (preg_match("~Alloc PE / .* / (.*) GB~",$str,$matches)) {
                $this->usedSize = str_replace(",",".",trim($matches[1]));
            }
            $matches = array();
            if (preg_match("~Free PE / .* / (.*) GB~",$str,$matches)) {
                $this->freeSize = str_replace(",",".",trim($matches[1]));
            }
        }
        $swch = false;
        $this->snapshots = array();
        foreach ($lv_strings as $str) {
            $str = trim($str);
            $matches = array();
            if (preg_match("/LV Size (.*) GB/",$str,$matches)) {
                $this->lvSize = str_replace(",",".",trim($matches[1]));
            }
            if ($str == "LV snapshot status source of") {
                $swch = true;
                continue;
            }
            if ($str=="LV Status available")
                $swch = false;
            if ($swch) {
                $cnt = count($this->snapshots);
                $this->snapshots[$cnt] = array();
                $arr = explode("[",$str);
                $this->snapshots[$cnt]["time"] = array_pop(explode("/",trim($arr[0])));
                $this->snapshots[$cnt]["active"] = str_replace("]","",trim($arr[1]));
            }
        }
        $this->usedSize = round($this->usedSize-$this->lvSize-10.74,2);
        $this->snapshotsSize = round($this->usedSize+$this->freeSize,2);
        if ($this->usedSize<0) $this->usedSize = 0;
        if ($this->snapshotSize<0) $this->snapshotSize = 0;
    }
    
    function getSnapshotsData() {
        foreach ($this->snapshots as $key=>$value) {
            if ($this->app->remoteSSHCommand!="") {
                $lv_info = shell_exec($this->app->remoteSSHCommand." '".$this->fileServer->lvDisplayCommand." --units G /dev/".$this->fileServer->shadowCopyVgName."/".$this->snapshots[$key]["time"]." | fmt -us");
            } else {
                $lv_info = $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->lvDisplayCommand." --units G /dev/".$this->fileServer->shadowCopyVgName."/".$this->snapshots[$key]["time"]." | fmt -us");
            }
            $lines = explode("\n",$lv_info);            
            foreach ($lines as $str) {
                $str = trim($str);
                $matches = array();
                if (preg_match("/COW-table size (.*) GB/",$str,$matches)) {
                    $this->snapshots[$key]["size"] = trim(str_replace(",",".",$matches[1]));
                }
                $matches = array();
                if (preg_match("/Allocated to snapshot (.*)\%/",$str,$matches)) {
                    $this->snapshots[$key]["usedPercents"] = round(trim(str_replace(",",".",$matches[1])),2);
                    $this->snapshots[$key]["usedSize"] = round($this->snapshots[$key]["size"]/100*$this->snapshots[$key]["usedPercents"],2);
                }
            }
        }
    }
    
    function createSnapshot($size) {
    	if (is_array($size))
    		$size = @$size["size"];
        $this->getVgInfo();
        $name = date("Y.m.d-H.i.s");
        if ($size>$this->freeSize) {
            $this->reportError("Недостаточно места на диске для создания снимка");
            return 0;
        }
        $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->lvCreateCommand." -L".$size."G -s -n ".$name." /dev/".$this->fileServer->shadowCopyVgName."/".$this->fileServer->shadowCopyLvName);
        $this->shell->exec_command($this->app->remoteSSHCommand." mkdir -p ".$this->fileServer->snapshotsFolder."/@GMT-".$name."");
        $this->shell->exec_command($this->app->remoteSSHCommand." mount -o ro,acl,user_xattr /dev/".$this->fileServer->shadowCopyVgName."/".$name." ".$this->fileServer->snapshotsFolder."/@GMT-".$name."");
        $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->shadowCopyEnginePath."/shadowcopy_make_links.php");
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("LVMSNAPSHOT_CREATE","message=Пользователь создал новый снимок ".$name);
    }
    
    function removeSnapshot($name) {
    	if (is_array($name))
    		$name = $name["item"];
        while (1==1) {
            $found = false;
            $this->getVgInfo();
            $this->getSnapshotsData();
            foreach ($this->snapshots as $key=>$value) {
                if ($value["time"]==$name) {
                    $this->shell->exec_command($this->app->remoteSSHCommand." umount -lf /dev/".$this->fileServer->shadowCopyVgName."/".$name."");
                    $this->shell->exec_command($this->app->remoteSSHCommand." rmdir ".$this->fileServer->snapshotsFolder."/@GMT-".$name."");
                    $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->lvRemoveCommand." -f /dev/".$this->fileServer->shadowCopyVgName."/".$name."");
                    $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->shadowCopyEnginePath."/shadowcopy_make_links.php");
                    $found = true;
                }
            }
            if (!$found)
                break;
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("LVMSNAPSHOT_REMOVE","message=Пользователь удалил снимок ".$name);        
    }
    
    function resizeSnapshot($name,$size=0) {
    	if (is_array($name)) {
    		$size = $name["size"];
    		$name = $name["name"];
    	}
        $this->getVgInfo();
        $this->getSnapshotsData();
        foreach ($this->snapshots as $key=>$value) {
            if ($value["time"]==$name) {
               if ($size>$value["size"]) {
                    if ($this->freeSize<$size-$value["size"]) {
                        $this->reportError("Недостаточно места на диске для изменения размера");
                        return 0;
                    }
                }
                $this->shell->exec_command($this->app->remoteSSHCommand." ".$this->fileServer->lvResizeCommand." -f -L".$size."G /dev/".$this->fileServer->shadowCopyVgName."/".$name);
            }
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("LVMSNAPSHOT_RESIZE","message=Пользователь изменил размер снимка ".$name);
    }
    
    function removeAllSnapshots() {
        $this->getVgInfo();
        $this->getSnapshotsData();
        foreach ($this->snapshots as $snapshot) {
            $this->removeSnapshot($snapshot["time"]);
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("LVMSNAPSHOT_REMOVEALL","message=Пользователь удалил все снимки");
    }
    
    function getSnapshotFiles($name) {
        $res = $this->shell->exec_command($this->app->remoteSSHCommand." php -r 'include \"/opt/WAB2/utils/functions.php\"~diffDirs(\"".$this->fileServer->snapshotsFolder."/@GMT-".$name."\",\"/data\")~'");
        return $res;
    }
    
    function writeSnapshotFilesFile($name) {
    	if (is_array($name))
    		$name = $name["item"];
        $res = explode("\n",$this->getSnapshotFiles($name));
        $cont = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
        
        $cont  .= "КАТАЛОГ СНИМКА: ".$this->fileServer->snapshotsFolder."/@GMT-".$name."/"."<br/><br/>ФАЙЛЫ:</br></br>";
        $all_size = 0;
        global $url_encode;
        foreach ($res as $line) {
            if (trim($line)=="")
                continue;
            $link  = $line;
            $all_size += filesize($link);
	    $link = str_replace("%","%25",$link);
            $link = str_replace('~','%7e',$link);
            $link = str_replace("#","%23",$link);
            $orig_link = str_replace($this->fileServer->snapshotsFolder."/@GMT-".$name."/","/data/",$link);
            $line  = str_replace($this->fileServer->snapshotsFolder."/@GMT-".$name."/","",$line);
            $cont .= "<a href='/root".$link."'>".$line."</a> (<a href='/root".$orig_link."'>Текущий вариант файла</a>)<br/>";
        }
        $all_size = round($all_size/1024/1024/1024,2);        
        $cont .= "</br><b>РАЗМЕР СОДЕРЖИМОГО СНИМКА: ".$all_size." Гб.</b>";
        $cont .= "</body></html>";
        file_put_contents("tmp/snapshot.html",$cont);
    }
    
    function copySnapshotFiles($name,$folder="") {
    	if (is_array($name)) {
    		$folder = $name["folder"];
    		$name = $name["item"];
    	}
        $res = explode("\n",$this->getSnapshotFiles($name));
        $strings = array();
        $arr = explode("-",$name);
        $date = explode(".",$arr[1]);
        $time = explode(".",$arr[2]);
        $timestr = $date[1].$date[0].$time[0].$time[1];
        $strings[] = "#!/bin/bash";
        $strings[] = "rm -rf ".$this->app->remotePath.$folder."/@GMT-".$name;
        foreach ($res as $line) {
            if (trim($line)=="")
                continue;
            $dir = $this->app->remotePath.$folder."/@GMT-".$name."/".str_replace($this->fileServer->snapshotsFolder."/@GMT-".$name,"",$line);
            $dir2 = $dir;
            $dir = explode("/",$dir);
            array_pop($dir);
            $dir = implode("/",$dir);
            $strings[] = "mkdir -p '".$dir."'";
            $strings[] = "cp -rf '".$this->app->remotePath.$line."' '".$dir."'";
            $strings[] = "touch -m -t ".$timestr." '".$dir2."'";
        }        
        $strings[] = "chmod -R 7777 '".$this->app->remotePath.$folder."'";
        $strings[] = "chown -R guest:smbusers '".$this->app->remotePath.$folder."'";
        file_put_contents("tmp/copysnap.sh",implode("\n",$strings));
        $this->shell->exec_command("/bin/bash ".$this->gapp->root_path."/tmp/copysnap.sh");
    }

    function getArgs() {
        global $Objects;
        $result = parent::getArgs();
        $str = "";
        $str .= $this->getId()."Rows = new Array;\n";
        $str .= $this->getId()."Rows[0] = new Array;\n";
        $str .= $this->getId()."tbl.columns = new Array;\n";
        $str .= $this->getId()."Rows[0]['class'] = '';\n";
        $str .= $this->getId()."Rows[0]['properties'] = 'valign=top';\n";
        $str .= $this->getId()."Rows[0]['cells'] = new Array;\n";

        $str .= $this->getId()."tbl.columns[0] = new Array;\n";
        $str .= $this->getId()."tbl.columns[0]['name'] = 'entityName';\n";
        $str .= $this->getId()."tbl.columns[0]['title'] = '';\n";
        $str .= $this->getId()."tbl.columns[0]['class'] = 'hidden';\n";
        $str .= $this->getId()."tbl.columns[0]['properties'] = 'width=1';\n";
        $str .= $this->getId()."tbl.columns[0]['control'] = 'plaintext';\n";
        $str .= $this->getId()."tbl.columns[0]['control_properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[0]['must_set'] = false;\n";
        $str .= $this->getId()."tbl.columns[0]['unique'] = false;\n";
        $str .= $this->getId()."tbl.columns[0]['readonly'] = false;\n";

        $str .= $this->getId()."Rows[0]['cells'][0] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['properties'] = 'width=1%';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['class'] = 'hidden';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['value'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['control'] = 'plaintext';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['control_properties'] = '';\n";

        $str .= $this->getId()."tbl.columns[1] = new Array;\n";
        $str .= $this->getId()."tbl.columns[1]['name'] = 'entityImage';\n";
        $str .= $this->getId()."tbl.columns[1]['title'] = '';\n";
        $str .= $this->getId()."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $this->getId()."tbl.columns[1]['properties'] = 'width=1%';\n";
        $str .= $this->getId()."tbl.columns[1]['control'] = 'hidden';\n";
        $str .= $this->getId()."tbl.columns[1]['control_properties'] = 'showValue=false';\n";
        $str .= $this->getId()."tbl.columns[1]['must_set'] = false;\n";
        $str .= $this->getId()."tbl.columns[1]['unique'] = false;\n";
        $str .= $this->getId()."tbl.columns[1]['readonly'] = false;\n";

        $str .= $this->getId()."tbl.columns[2] = new Array;\n";
        $str .= $this->getId()."tbl.columns[2]['name'] = 'time';\n";
        $str .= $this->getId()."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $this->getId()."tbl.columns[2]['properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[2]['control'] = 'plaintext';\n";
        $str .= $this->getId()."tbl.columns[2]['control_properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[2]['must_set'] = false;\n";
        $str .= $this->getId()."tbl.columns[2]['unique'] = false;\n";
        $str .= $this->getId()."tbl.columns[2]['readonly'] = false;\n";

        $str .= $this->getId()."tbl.columns[3] = new Array;\n";
        $str .= $this->getId()."tbl.columns[3]['name'] = 'size';\n";
        $str .= $this->getId()."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $this->getId()."tbl.columns[3]['properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[3]['control'] = 'plaintext';\n";
        $str .= $this->getId()."tbl.columns[3]['control_properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[3]['must_set'] = false;\n";
        $str .= $this->getId()."tbl.columns[3]['unique'] = false;\n";
        $str .= $this->getId()."tbl.columns[3]['readonly'] = false;\n";

        $str .= $this->getId()."tbl.columns[4] = new Array;\n";
        $str .= $this->getId()."tbl.columns[4]['name'] = 'full';\n";
        $str .= $this->getId()."tbl.columns[4]['class'] = 'cell';\n";
        $str .= $this->getId()."tbl.columns[4]['properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[4]['control'] = 'plaintext';\n";
        $str .= $this->getId()."tbl.columns[4]['control_properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[4]['must_set'] = false;\n";
        $str .= $this->getId()."tbl.columns[4]['unique'] = false;\n";
        $str .= $this->getId()."tbl.columns[4]['readonly'] = false;\n";

        $str .= $this->getId()."tbl.columns[5] = new Array;\n";
        $str .= $this->getId()."tbl.columns[5]['name'] = 'active';\n";
        $str .= $this->getId()."tbl.columns[5]['class'] = 'cell';\n";
        $str .= $this->getId()."tbl.columns[5]['properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[5]['control'] = 'plaintext';\n";
        $str .= $this->getId()."tbl.columns[5]['control_properties'] = '';\n";
        $str .= $this->getId()."tbl.columns[5]['must_set'] = false;\n";
        $str .= $this->getId()."tbl.columns[5]['unique'] = false;\n";
        $str .= $this->getId()."tbl.columns[5]['readonly'] = false;\n";

        $str .= $this->getId()."Rows[0]['cells'][1] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['properties'] = 'width=1%';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['value'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['control'] = 'plaintext';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['control_properties'] = '';\n";
        $c = 2;

        $str .= $this->getId()."Rows[0]['cells'][2] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['value'] = 'Время снимка';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['control'] = 'header';\n";

        $str .= $this->getId()."Rows[0]['cells'][3] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['value'] = 'Размер (Гб)';\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['control'] = 'header';\n";

        $str .= $this->getId()."Rows[0]['cells'][4] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][4]['properties'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][4]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][4]['value'] = 'Заполненность (Гб)';\n";
        $str .= $this->getId()."Rows[0]['cells'][4]['control'] = 'header';\n";

        $str .= $this->getId()."Rows[0]['cells'][5] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][5]['properties'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][5]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][5]['value'] = 'Активность';\n";
        $str .= $this->getId()."Rows[0]['cells'][5]['control'] = 'header';\n";

        $rw=1;
        $this->getVgInfo();
        $this->getSnapshotsData();
        $this->entityCount = count($this->snapshots);
        $this->numPages = 0;            
        foreach ($this->snapshots as $key=>$entity) {

            if ($entity["active"]=="active") {
                $title = "Активен";
                $entityImage = $this->skinPath."images/Tree/snapshot.png";
            } else {
                $title = "Неактивен";
                $entityImage = $this->skinPath."images/Tree/snapshot2.png";
            }

            $str .= $this->getId()."Rows[$rw] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['class'] = '';\n";
            $str .= $this->getId()."Rows[$rw]['properties'] = 'valign=top';\n";
            $str .= $this->getId()."Rows[$rw]['cells'] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][0] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][0]['properties'] = 'width=1%';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][0]['class'] = 'hidden';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][0]['value'] = '".$entity["time"]."';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][0]['control'] = 'plaintext';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][0]['control_properties'] = '';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][1] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][1]['properties'] = 'width=1%';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][1]['class'] = 'cell';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][1]['value'] = '".$entityImage."';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][1]['control'] = 'hidden';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['control_properties'] = 'buttonImage=".$entityImage.",actionButton=true';\n";
            $entityTime = explode("-",$entity["time"]);
            $entityTime = implode(".",array_reverse(explode(".",$entityTime[0])))." ".str_replace(".",":",$entityTime[1]);
            $str .= $this->getId()."Rows[$rw]['cells'][2] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][2]['properties'] = '';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][2]['class'] = 'cell';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][2]['value'] = '".$entityTime."';\n";                                    
            $str .= $this->getId()."Rows[$rw]['cells'][2]['control'] = 'static';\n";                    
            $str .= $this->getId()."Rows[$rw]['cells'][2]['control_properties'] = '';\n";

            $str .= $this->getId()."Rows[$rw]['cells'][3] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][3]['properties'] = '';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][3]['class'] = 'cell';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][3]['value'] = '".$entity["size"]."';\n";                    
            $str .= $this->getId()."Rows[$rw]['cells'][3]['control'] = 'static';\n";                    
            $str .= $this->getId()."Rows[$rw]['cells'][3]['control_properties'] = '';\n";

            $str .= $this->getId()."Rows[$rw]['cells'][4] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][4]['properties'] = '';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][4]['class'] = 'cell';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][4]['value'] = '".$entity["usedSize"]."(".$entity["usedPercents"]."%)';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][4]['control'] = 'static';\n";                    
            $str .= $this->getId()."Rows[$rw]['cells'][4]['control_properties'] = '';\n";

            $str .= $this->getId()."Rows[$rw]['cells'][5] = new Array;\n";
            $str .= $this->getId()."Rows[$rw]['cells'][5]['properties'] = '';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][5]['class'] = 'cell';\n";
            $str .= $this->getId()."Rows[$rw]['cells'][5]['value'] = '".$title."';\n";                    
            $str .= $this->getId()."Rows[$rw]['cells'][5]['control'] = 'static';\n";                    
            $str .= $this->getId()."Rows[$rw]['cells'][5]['control_properties'] = '';\n";

            $rw++;
        }

        $str .= $this->getId()."EntityCount = ".$this->entityCount.";\n";
        $str .= $this->getId()."NumPages = ".$this->numPages.";\n";
        $str .= $this->getId()."tbl.snapshotsSize ='".$this->snapshotsSize."';\n";
        $str .= $this->getId()."tbl.freeSize ='".$this->freeSize."';\n";
        $str .= $this->getId()."tbl.usedSize ='".$this->usedSize."';";
        
        $this->data = $str;
        $this->loaded = true;
        $result["{data}"] = $this->data;
        $result["{className}"] = $this->className;
        if ($this->destroyDiv)
            $result["{destroyDivStr}"] = "true";
        else
            $result["{destroyDivStr}"] = "false";

        if ($this->forEntitySelect)
            $result["{forEntitySelectStr}"] = "true";
        else
            $result["{forEntitySelectStr}"] = "false";    
        return $result;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '5': return "createSnapshot";
    		case '6': return "writeSnapshotFilesFile";
    		case '7': return "copySnapshotFiles";
    		case '8': return "removeSnapshot";
    		case '9': return "resizeSnapshot";
    	}
    	return parent::getHookProc($number);
    }
}
?>
