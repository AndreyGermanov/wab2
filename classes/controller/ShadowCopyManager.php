<?php
/**
 * Класс управляет окном менеджером подсистемы теневых копий.
 * 
 * @author andrey
 */
class ShadowCopyManager extends WABEntity {    
    function construct($params) {
        parent::construct($params);

        $this->itemsPerPage = 0;
        $this->currentPage = 1;
        $this->entityId = "";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;

        $this->contextMenuId = "";

        $this->parent_object_id = "";

        $this->windowWidth = 500;
        $this->windowHeight = 500;
        $this->template = "templates/controller/ShadowCopyManager.html";
        
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $this->enableShadowCopy = $this->fileServer->enableShadowCopy;
        $this->snapshotSize = $this->fileServer->snapshotSize;
        $this->snapshotsCount = $this->fileServer->snapshotsCount;
        $this->shadowCopyPeriodDays = $this->fileServer->shadowCopyPeriodDays;
        $this->shadowCopyPeriodHours = $this->fileServer->shadowCopyPeriodHours;
        $this->shadowCopyPeriodMinutes = $this->fileServer->shadowCopyPeriodMinutes;
        $this->snapshotsFolder = $this->fileServer->snapshotsFolder;
        $this->shadowCopyVgName = $this->fileServer->shadowCopyVgName;
        $this->shadowCopyLvName = $this->fileServer->shadowCopyLvName;
        $this->shadowCopyResizeSize = $this->fileServer->shadowCopyResizeSize;
        $this->shadowCopyResizeLimit = $this->fileServer->shadowCopyResizeLimit;
        $this->enableAutoSnapshotsRotation = $this->fileServer->enableAutoSnapshotsRotation;
        $this->expiredSnapshotsBackupFolder = $this->fileServer->expiredSnapshotsBackupFolder;
        $this->shadowCopyEnginePath = $this->fileServer->shadowCopyEnginePath;
        $this->backupViewerAddress = $this->fileServer->backupViewerAddress;
        
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."ShadowCopyManager";
        $this->tabs_string = "settings|Параметры|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "snapshots|Снимки|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "rotation|Ротация|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "settings";
        $this->tabsetName = $this->tabset_id;
        $this->gapp = $Objects->get("Application");
        if (!$this->gapp->initiated)
            $this->gapp->initModules();
        $this->app = $Objects->get($this->module_id);
        $this->shell = $Objects->get("Shell_shell");

        $this->handler = "scripts/handlers/controller/ShadowCopyManager.js";     
        $this->icon = $this->skinPath."images/Tree/shadowcopy.png";     

        $this->clientClass = "ShadowCopyManager";
        $this->parentClientClasses = "Entity";        
    }
    
    function getPresentation() {
        return "Теневые копии";
    }
    
    function save($arguments=null) {
    	global $Objects;
    	if (isset($arguments)) {
    		$this->load();
    		$this->setArguments($arguments);
    	}
        if ($this->enableShadowCopy) {
            if (trim($this->shadowCopyVgName)=="") {
                $this->reportError("Укажите группу разделов данных","save1");
                return 0;
            }
            if (trim($this->shadowCopyLvName)=="") {
                $this->reportError("Укажите раздел данных","save2");
                return 0;
            }
            if (trim($this->snapshotsFolder)=="") {
                $this->reportError("Укажите путь к хранилищу снимков","save3");
                return 0;
            }
            if (trim($this->shadowCopyResizeLimit)!="" and !is_numeric(trim($this->shadowCopyResizeLimit))) {
                $this->reportError("Неправильно указан процент заполненности снимка","save4");
                return 0;                
            }
            if (trim($this->shadowCopyResizeSize)!="" and !is_numeric(trim($this->shadowCopyResizeSize))) {
                $this->reportError("Неправильно указан размер приращения снимка при достижении критической заполненности","save5");
                return 0;                
            }
        } else
            $this->enableAutoSnapshotsRotation = "0";
        if ($this->enableAutoSnapshotsRotation) {
            if (trim($this->snapshotSize)==0 or trim($this->snapshotSize)=="") {
                $this->reportError("Укажите размер нового снимка","save6");
                return 0;                
            }
            if (trim($this->snapshotsCount)==0 or trim($this->snapshotsCount)=="") {
                $this->reportError("Укажите количество снимков в ротации","save7");
                return 0;                
            }
            if (trim($this->shadowCopyPeriodDays)=="") {
                $this->reportError("Укажите 'Дни' в периодичности ротации снимков","save8");
                return 0;                
            }
            if (trim($this->shadowCopyPeriodHours)=="") {
                $this->reportError("Укажите 'Часы' в периодичности ротации снимков","save9");
                return 0;                
            }
            if (trim($this->shadowCopyPeriodHours)=="") {
                $this->reportError("Укажите 'Минуты' в периодичности ротации снимков","save10");
                return 0;                
            }
        }
        $app = $Objects->get("Application");
        $module = $app->getModuleByClass($this->module_id);
        $module["enableShadowCopy"] = $this->enableShadowCopy;
        $module["enableAutoSnapshotsRotation"] = $this->enableAutoSnapshotsRotation;
        $module["shadowCopyVgName"] = $this->shadowCopyVgName;
		$module["shadowCopyLvName"] = $this->shadowCopyLvName;
		$module["snapshotsFolder"] = $this->snapshotsFolder;
		$module["shadowCopyResizeLimit"] = $this->shadowCopyResizeLimit;
		$module["shadowCopyResizeSize"] = $this->shadowCopyResizeSize;
		$module["expiredSnapshotsBackupFolder"] = $this->expiredSnapshotsBackupFolder;
		$module["backupViewerAddress"] = $this->backupViewerAddress;
		$module["snapshotSize"] = $this->snapshotSize;
		$module["snapshotsCount"] = $this->snapshotsCount;
		$module["shadowCopyPeriodDays"] = $this->shadowCopyPeriodDays;
		$module["shadowCopyPeriodHours"] = $this->shadowCopyPeriodHours;
		$module["shadowCopyPeriodMinutes"] = $this->shadowCopyPeriodMinutes;
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);
		
        $strings = file($this->app->remotePath."".$this->fileServer->crontabFile);
        if ($this->app->remoteSSHCommand=="")
            $this->shell->exec_command($this->app->remoteSSHCommand." chown ".$this->gapp->apacheServerUser." ".$this->fileServer->crontabFile);
        $result_strings = array();
        $found = false;
        foreach ($strings as $line) {
            if (stripos($line,"rotate_snapshots.php")!==FALSE) {
                $found = true;
                if ($this->enableAutoSnapshotsRotation) {
                    $line = $this->shadowCopyPeriodMinutes." ".$this->shadowCopyPeriodHours." ".$this->shadowCopyPeriodDays." * * root ".$this->fileServer->shadowCopyEnginePath."rotate_snapshots.php &\n";
                    $result_strings[] = $line;
                }                         
            } else
                $result_strings[] = $line;
        }
        if ($this->enableAutoSnapshotsRotation and !$found)
            $result_strings[] = $this->shadowCopyPeriodMinutes." ".$this->shadowCopyPeriodHours." ".$this->shadowCopyPeriodDays." * * root ".$this->fileServer->shadowCopyEnginePath."rotate_snapshots.php &\n";
        $fp = fopen($this->app->remotePath."".$this->fileServer->crontabFile,"w");
        foreach($result_strings as $line)
            fwrite($fp,$line);
        fclose($fp);
        $this->shell->exec_command($this->app->remoteSSHCommand." chown root ".$this->fileServer->crontabFile);
        $strings = file($this->fileServer->crontabFile);
        $this->shell->exec_command("chown ".$this->gapp->apacheServerUser." ".$this->fileServer->crontabFile);
        $result_strings = array();
        $found = false;
        foreach ($strings as $line) {
            if (stripos($line,"shadowcopy_make_links.php")!==FALSE) {
                $found = true;
                if ($this->enableAutoSnapshotsRotation) {
                    $line = "*/5 * * * * root ".$this->fileServer->shadowCopyEnginePath."shadowcopy_make_links.php\n";
                    $result_strings[] = $line;
                }                         
            } else
                $result_strings[] = $line;
        }
        if ($this->enableAutoSnapshotsRotation and !$found)
            $result_strings[] = "*/5 * * * * root ".$this->fileServer->shadowCopyEnginePath."shadowcopy_make_links.php\n";
        $fp = fopen($this->fileServer->crontabFile,"w");
        foreach($result_strings as $line)
            fwrite($fp,$line);
        fclose($fp);
        $this->shell->exec_command("chown root ".$this->fileServer->crontabFile);
        
        $strings = file($this->app->remotePath.$this->fileServer->rcLocalFile);
        $result_strings = array();
        $found = false;
        foreach ($strings as $line) {
            if (stripos($line,"exit 0")!==FALSE)
                continue;
            if (stripos($line,"snapshot_rotator.sh")!==FALSE) {
                $found = true;
                if ($this->enableShadowCopy) {
                    $result_strings[] = $line;
                }                         
            } else
                $result_strings[] = $line;
        }
        if ($this->enableShadowCopy and !$found)
            $result_strings[] = $this->fileServer->shadowCopyEnginePath."/snapshot_rotator.sh -m\n";
        $fp = fopen($this->app->remotePath.$this->fileServer->rcLocalFile,"w");
        foreach($result_strings as $line)
            fwrite($fp,$line);
        fclose($fp);                        
        if ($this->enableShadowCopy) {
            $this->shell->exec_command($this->app->remoteSSHCommand." chmod a+x /root");
            file_put_contents($this->app->remotePath.$this->fileServer->shadowCopyEnginePath."libsnapshot.pm",file_get_contents($this->fileServer->snapshotLibTemplate));
            file_put_contents($this->app->remotePath.$this->fileServer->shadowCopyEnginePath."snapshot_rotator.sh",strtr(file_get_contents($this->fileServer->snapshotRotatorTemplate),$this->getArgs()));
            file_put_contents($this->app->remotePath.$this->fileServer->shadowCopyEnginePath."rotate_snapshots.php",strtr(file_get_contents($this->fileServer->snapshotPHPRotatorTemplate),$this->getArgs()));
            file_put_contents($this->app->remotePath.$this->fileServer->shadowCopyEnginePath."shadowcopy_make_links.php",file_get_contents($this->fileServer->snapshotCopyLinksTemplate));
            $this->shell->exec_command($this->app->remoteSSHCommand." chmod -R a+x ".$this->fileServer->shadowCopyEnginePath);
            $this->fileServer->loadShares(true);
            foreach($this->fileServer->shares as $share) {
                if ($share->name!="root" and $share->name!="trash") {
                    $share->shadowCopy = true;
                    //$share->save();
                }
            }
        } else {
            global $Objects;
            $lvmTable = $Objects->get("LVMSnapshotsDataTable_".$this->module_id."_table");
            $lvmTable->removeAllSnapshots();
            $this->fileServer->loadShares(true);
            foreach($this->fileServer->shares as $share) {
                if ($share->name!="root" and $share->name!="trash") {
                    $share->shadowCopy = false;
                    $share->save();
                    $this->shell->exec_command($this->app->remoteSSHCommand." rm ".$this->fileServer->shares_root."/".$share->path."/@*");
                }
            }
        }
        $this->gapp->raiseRemoteEvent("SHADOWCOPY_CHANGED");
        $this->shell->exec_command($this->app->remoteSSHCommand." /etc/init.d/cron restart");        
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>