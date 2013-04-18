<?php
/**
 * Класс, реализующий файловый менеджер
 *
 * @author andrey
 */
class FileManager extends WABEntity {
    
    public $fileOperations = array();
    
    function construct($params) {        
        parent::construct($params);
        global $Objects;
        $this->template = "templates/interface/FileManager/FileManagerMain.html";
        $this->handler = "scripts/handlers/interface/FileManager/FileManager.js";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->appUser = $app->User;
        $this->app = $app;
        $this->capp = $Objects->get($this->module_id);
        $this->shell = $Objects->get("Shell_shell");
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/fileman.png";       
        $this->width="600";
        $this->height="450";
        $this->overrided = "width,height";
		$this->ftpHome = "";
        // а может быть еще tree для дерева каталогов и list для списка файлов
        $this->viewForm = 'main'; 
        $this->clientClass = "FileManager";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Файловый менеджер";
        $this->classListTitle = "Файловый менеджер";
        $this->profileClass = "FileManagerProfile";
        $this->setRole();
    }
    
    function load() {
        switch ($this->viewForm) {
            case 'tree':
                $this->template = "templates/interface/FileManager/FileManagerTree.html";
                break;
            case 'list':
                $this->template = "templates/interface/FileManager/FileManagerList.html";
                break;
            case 'main':
                $this->template = "templates/interface/FileManager/FileManagerMain.html";
                break;
        }
        if (count($this->role)==0)
        	$this->setRole();
        if (isset($this->role["rootPath"]))
        	if ($this->rootPath=="" or $this->rootPath=="/")
        		$this->rootPath = $this->getRoleValue($this->role["rootPath"]);        	
        $this->loaded = true;
    }
	
    function getPresentation() {
    	return "Файловый менеджер";
    }

    function getArgs() {
    	if (!file_exists($this->arguments))
        	$args = (array)json_decode($this->arguments);
    	else {
    		$args = array();
    		$args["arguments"] = $this->arguments;
    	}    	
        if (file_exists(@$args["arguments"])) {
            $args = unserialize(file_get_contents($args["arguments"]));
            if (is_array($args)) {
                $args["viewForm"] = "list";
                $this->list_arguments = json_encode($args);
            }
        }
		$this->fileUploadId = "FileUpload_".$this->module_id."_".$this->name;
		if ($this->useCase=="fileUpload") {
			global $ftpUser;
			$this->ftpHome = @$ftpUser->ftpHome;
			$this->ftpUserName = @$ftpUser->name;
		}
        if (count($this->role)==0)
        	$this->setRole();
        if (isset($this->role["rootPath"]))
        	if ($this->rootPath=="" or $this->rootPath=="/")
        		$this->rootPath = $this->getRoleValue($this->role["rootPath"]);        	
		return parent::getArgs();
    }

    function getHookProc($number) {
            switch ($number) {
                    case '2': return "getFilesListJSON";
                    case '3': return "initFileManager";
                    case '4': return "makeDir";
                    case '5': return "rename";
                    case '6': return "copyMove";
                    case '7': return "delete";
                    case '8': return "initUploadFileManager";
                    case '9': return "checkFTPAccess";
            }
            return parent::getHookProc($number);
    }

    function getFilesList($dir) {
        if (count($this->role)==0)
        	$this->setRole();
        if (isset($this->role["rootPath"]))
        	if ($this->rootPath=="" or $this->rootPath=="/")
        		$this->rootPath = $this->getRoleValue($this->role["rootPath"]);
        if ($dir=="")
        	$dir=$this->rootPath;        	
    	$result = array();
            $this->setOperations();
            if ($this->path!=$dir and !$this->fileOperations["openDir"])
                    return 0;
            global $Objects,$firephp;
            $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
            if (!$this->fileServer->loaded)
                    $this->fileServer->load();
            $counter = 0;
			if ($this->useCase=="fileUpload") {
				global $ftpUser;
				$conn_id = ftp_connect("localhost");
				if (ftp_login($conn_id,$ftpUser->name,$ftpUser->ftpPassword)) {
					if (!@ftp_chdir($conn_id,$dir)) {
						$this->reportError("При входе в каталог возникла ошибка. Скорее всего у Вас нет прав на выполнение этой операции !");
						return 0;
					}

					$list = ftp_nlist($conn_id,$dir);
					$list[] = "..";
				} else {
					$this->reportError("Ошибка подключения к серверу ".$ftpUser->name."-".$ftpUser->ftpPassword);
					return 0;
				}
			}			
			
			if ($this->useCase=="sharesEditor" or $this->useCase=="selectPath" or $this->useCase=="selectFile") {
				$list = array();
				if (!file_exists($dir))
					$dir = "/";
				
	            if (is_dir($dir)) {
    	                if ($dh = opendir($dir)) {
        	                    while (($file = readdir($dh)) !== false) {
									$list[] = $file;
								}
						}
				}				
			}
			if ($this->useCase=="fileUpload")
				$condDir = str_replace("//","/",$ftpUser->ftpHome."/".$dir);
			else
				$condDir = $dir;
            foreach ($list as $file) {
				$file = array_pop(explode("/",$file));
				if ($dir == $this->rootPath and $file=="..") {
					continue;
				}
			
	            if ($file!=".") {
		           if (is_dir($condDir."/".$file)) {
					   if ($file=="..") 
							$key = "                                ";
						else
							$key = "                             ";
	    	           $result[$key.strtoupper($file)]["title"] = $file;
	                   $result[$key.strtoupper($file)]["path"] = str_replace($this->capp->remotePath,"",$dir)."/".$file;
	                   $result[$key.strtoupper($file)]["type"] = "directory";
	                   $result[$key.strtoupper($file)]["img"] = $this->skinPath."images/FileManager/folder.png";
	                   if ($this->useCase=="sharesEditor") {
							$share = $this->fileServer->containsPath(str_replace($this->rootPath."/","",$dir."/".$file));
							if ($share) {
	                        	if (!$share->loaded)
	                            	$share->load();
	                            $shareTypes = array();
	                            $share->getRows("hosts");
	                            if ($share->smbShareCheck)
	                            	$shareTypes[] = "smb";
	                            if ($share->nfsShareCheck)
	                            	$shareTypes[] = "nfs";
	                            if ($share->afpShareCheck)
	                            	$shareTypes[] = "afp";
	                            if ($share->ftpFolder)
	                            	$shareTypes[] = "ftp";
	                            $result[$key.strtoupper($file)]["sharetypes"] = implode(",",$shareTypes);
	                            $result[$key.strtoupper($file)]["share"] = $share->name;
	                       }
		               }
	            	} else {
			            $result[strtoupper($file)]["title"] = $file;
		                if ($file=="..") {
		                	$arr = explode("/",$dir);
		                    array_pop($dir);
							$arr = str_replace($this->capp->remotePath,"",implode("/",$arr));
		                    $result[strtoupper($file)]["path"] = $arr;
		                } else
							$result[strtoupper($file)]["path"] = str_replace($this->capp->remotePath,"",$condDir)."/".$file;
						$result[strtoupper($file)]["type"] = "file";
						if (file_exists($this->skinPath."images/FileManager/mimetypes/".str_replace("/","-",@mime_content_type($condDir."/".$file).".png")))
							$result[strtoupper($file)]["img"] = $this->skinPath."images/FileManager/mimetypes/".str_replace("/","-",@mime_content_type($condDir."/".$file).".png");
						else
							$result[strtoupper($file)]["img"] = $this->skinPath."images/FileManager/mimetypes/text-plain.png";
					}
			}
			$counter++;
		}
	    ksort($result);
	    return $result;
    }

    function setOperations() {
            global $Objects;
        if (count($this->role)==0)
        	$this->setRole();
        if (isset($this->role["rootPath"]))
        	if ($this->rootPath=="" or $this->rootPath=="/")
        		$this->rootPath = $this->getRoleValue($this->role["rootPath"]);        	
            switch ($this->useCase) {
                    case "sharesEditor":
                        $this->fileOperations["makeDir"] = true;
                        $this->fileOperations["openDir"] = true;
                        $this->fileOperations["rename"] = true;
                        $this->fileOperations["copy"] = true;
                        $this->fileOperations["move"] = true;
                        $this->fileOperations["delete"] = true;
                        $this->fileOperations["properties"] = true;
                        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
                        if (!$this->fileServer->loaded)
                                $this->fileServer->load();
                        $this->path = $this->capp->remotePath.$this->fileServer->shares_root;
                        $this->rootPath = $this->path;
                        break;
                    case "fileUpload":
                        $this->fileOperations["makeDir"] = true;
                        $this->fileOperations["openDir"] = true;
                        $this->fileOperations["rename"] = true;
                        $this->fileOperations["copy"] = true;
                        $this->fileOperations["move"] = true;
                        $this->fileOperations["delete"] = true;
                        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
                        if (!$this->fileServer->loaded)
                                $this->fileServer->load();
                        $this->path = "/";
                        $this->rootPath = "/";
                        break;
                    case "selectPath":
                        $this->fileOperations["makeDir"] = true;
                        $this->fileOperations["openDir"] = true;
                        $this->fileOperations["rename"] = true;
                        $this->fileOperations["copy"] = true;
                        $this->fileOperations["move"] = true;
                        $this->fileOperations["delete"] = true;
                        $this->fileOperations["properties"] = true;
                        $this->fileOperations["selectPath"] = true;
                        break;
                    case "selectFile":
                        $this->fileOperations["makeDir"] = true;
                        $this->fileOperations["openDir"] = true;
                        $this->fileOperations["rename"] = true;
                        $this->fileOperations["copy"] = true;
                        $this->fileOperations["move"] = true;
                        $this->fileOperations["delete"] = true;
                        $this->fileOperations["properties"] = true;
                        $this->fileOperations["selectFile"] = true;
                        break;
            }
    }

    function getFilesListJSON($arguments) {
            global $Objects;
            $this->useCase = @$arguments["useCase"];
            $this->setOperations();
            echo json_encode($this->getFilesList($arguments["dir"]));
    }

    function initFileManager($arguments) {
        global $Objects;
        if (count($this->role)==0)
        	$this->setRole();
        if (isset($this->role["rootPath"]))
        	if ($this->rootPath=="" or $this->rootPath=="/")
        		$this->rootPath = $this->getRoleValue($this->role["rootPath"]);        	
            $this->fileOperations = array();
            $args = $arguments;
            $this->useCase = @$arguments["useCase"];
            $arguments = (array)json_decode(@$arguments["arguments"]);
            $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
            $this->backupsViewerPath = $this->fileServer->backupViewerAddress;
            if (!isset($arguments["useCase"]))
                $arguments = $args;
			if ($this->useCase=="")
				$this->useCase = "fileUpload";
            if ($this->useCase == "selectPath") {
            		if ($this->rootPath=="") {
                    	$this->rootPath = $this->capp->remotePath.str_replace($this->capp->remotePath,"",$arguments["rootPath"]);
                    	$this->currentPath = $this->capp->remotePath.str_replace($this->capp->remotePath,"",@$arguments["currentPath"]);
            		}
                    if ($this->currentPath=="")
                        $this->path = $this->rootPath;
                    else
                     $this->path = $this->currentPath;
            }
            if ($this->useCase == "selectFile") {
            	if ($this->rootPath=="") {
	            	$this->rootPath = $this->capp->remotePath.str_replace($this->capp->remotePath,"",$arguments["rootPath"]);
                	$this->currentPath = $this->capp->remotePath.str_replace($this->capp->remotePath,"",@$arguments["currentPath"]);
            	}
                if ($this->currentPath=="")
    	            $this->path = $this->rootPath;
                else
        	      	$this->path = $this->currentPath;
            }
            if (isset($arguments["useCase"]) and @$arguments["viewForm"]=="list") {
                    $this->setOperations();
                    $this->viewForm = "list";
                    $this->template = "templates/interface/FileManager/FileManagerList.html";
                    if ($this->useCase == "sharesEditor") {
                        if (!$this->fileServer->loaded)
                                $this->fileServer->load();
                            $this->path = $this->capp->remotePath.str_replace($this->capp->remotePath,"",$this->fileServer->shares_root);
                            $this->rootPath = $this->capp->remotePath.str_replace($this->capp->remotePath,"",$this->fileServer->shares_root);
                    }
                    $this->loaded = true;
                    $this->show();
            }
			if ($this->useCase=="sharesEditor") {
	            if (!$this->fileServer->loaded)
    		        $this->fileServer->load();
           		$this->path = $this->capp->remotePath.str_replace($this->capp->remotePath,"",$this->fileServer->shares_root);
		        $this->rootPath = $this->capp->remotePath.str_replace($this->capp->remotePath,"",$this->fileServer->shares_root);
		        $this->list_arguments = $this->arguments;		        
			}
    }

	function initUploadFileManager($arguments) {
        if (@$this->role["fmCanUpload"]=="false")
          	return 0;
		$this->useCase = @$arguments["useCase"];
		$this->viewForm = @$arguments["viewForm"];
        if (count($this->role)==0)
        	$this->setRole();
        if (isset($this->role["rootPath"]))
        	if ($this->rootPath=="" or $this->rootPath=="/")        		 
        		$this->rootPath = $this->getRoleValue($this->role["rootPath"]);        	
		if ($this->useCase=="fileUpload" and $this->viewForm!="list") {
            $this->template = "templates/interface/FileManager/FileManagerUpload.html";
			$this->path = "/";
			$this->rootPath = "/";
			$list_args = array();
			$list_args["viewForm"] = "list";
			$list_args["useCase"] = "fileUpload";
			$this->list_arguments = json_encode($list_args);
			$this->show();
		}
		if ($this->useCase=="fileUpload" and $this->viewForm=="list") {
			$this->setOperations();
			$this->viewForm = "list";
            $this->template = "templates/interface/FileManager/FileManagerList.html";
			$this->path = "/";
			$this->rootPath = "/";
			$this->loaded = true;
			$this->show();
		}
	}

    function makeDir($arguments) {
            global $Objects,$ftpUser;
            if (@$this->role["fmCanMakeFolder"]=="false")
            	return 0;
            $this->useCase = $arguments["useCase"];
            $this->setOperations();
            if (!$this->fileOperations["makeDir"]) {
                    return 0;
            }
            $path = $arguments["path"];
            $dir = $arguments["dir"];
            $dir = array_shift(explode("/",$dir));
            $dir = str_replace("//","/",$path."/".$dir);
			if ($this->useCase=="fileUpload")
				$condDir = str_replace("//","",$ftpUser->ftpHome."/".$dir);
			else
				$condDir = $dir;
            if (file_exists($this->capp->remotePath.$condDir)) {
                    $this->reportError("Указанный каталог уже существует!","makeDir");
                    return 0;
            }
			if ($this->useCase=="fileUpload") {
				$conn_id = ftp_connect("localhost");
				if (ftp_login($conn_id,$ftpUser->name,$ftpUser->ftpPassword)) {
					if (!@ftp_chdir($conn_id,$path))
						$this->reportError("При входе в каталог возникла ошибка. Скорее всего у Вас нет прав на выполнение этой операции !");

					if (!@ftp_mkdir($conn_id,array_pop(explode("/",$dir))))
						$this->reportError("При создании каталога возникла ошибка. Скорее всего у Вас нет прав на выполнение этой операции !");
					else {
						ftp_close($conn_id);
					}
				} else
					$this->reportError("Ошибка подключения к серверу");
			} else {
	            if ($this->capp->remoteSSHCommand=="")
    	        	$this->shell->exec_command($this->app->makeDirCommand." '".$dir."'");
        	    else
            		shell_exec($this->capp->remoteSSHCommand." ".$this->app->makeDirCommand." '".$dir."'");
			}
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();
			$app->raiseRemoteEvent("FM_MAKEDIR","message=Пользователь создал каталог `".$dir."`");
    }

    function rename($arguments) {
            global $Objects,$ftpUser;
            if (@$this->role["fmCanRename"]=="false")
            	return 0;
            $this->useCase = $arguments["useCase"];
            $this->setOperations();
            if (!$this->fileOperations["rename"]) {
            	return 0;
            }
            $path = @$arguments["path"];
            $dir = @$arguments["dir"];
            $oldDir = @$arguments["oldDir"];
            $dir = array_shift(explode("/",$dir));
            $dir = $path."/".$dir;
            $oldDir = array_shift(explode("/",$oldDir));
            $oldDir = $path."/".$oldDir;
            if ($this->useCase=="fileUpload") {
            	$condDir = str_replace("//","",$ftpUser->ftpHome."/".$dir);
            }
			else
				$condDir = $dir;
            if (file_exists($this->capp->remotePath.$condDir)) {
                    $this->reportError("Указанный каталог уже существует!","rename");
                    return 0;
            }
			if ($this->useCase=="fileUpload") {
				$conn_id = ftp_connect("localhost");
				if (ftp_login($conn_id,$ftpUser->name,$ftpUser->ftpPassword)) {
					if (!@ftp_chdir($conn_id,$path))
						$this->reportError("При входе в каталог возникла ошибка. Скорее всего у Вас нет прав на выполнение этой операции !");

					if (!@ftp_rename($conn_id,array_pop(explode("/",$oldDir)),array_pop(explode("/",$dir))))
						$this->reportError("При переименовании возникла ошибка. Скорее всего у Вас нет прав на выполнение этой операции !");
					else {
						ftp_close($conn_id);
					}
				} else
					$this->reportError("Ошибка подключения к серверу");
			} else {
	            if ($this->capp->remoteSSHCommand=="")
	            	$this->shell->exec_command($this->app->moveDirCommand." '".$oldDir."' '".$dir."'");
	            else
	              shell_exec($this->capp->remoteSSHCommand." ".$this->app->moveDirCommand." '".$oldDir."' '".$dir."'");
	            if ($this->useCase=="sharesEditor") {
					$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
					$oldFolders = $ftpServer->getFolders();
	            	$this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
	                    if (!$this->fileServer->loaded)
    	                        $this->fileServer->load();
        	            $share = $this->fileServer->containsPath(str_replace($this->rootPath."/","",$oldDir));
            	        if ($share) {
                	            if (!$share->loaded)
                    	                $share->load();
                	            $share->oldFolders = $oldFolders;
                        	    $share->path = str_replace($this->rootPath."/","",$dir);
                            	$share->save();
                    	}
            	}
            	if (is_object($this->capp->docFlowApp)) {
            		$adapter = $Objects->get("DocFlowDataAdapter_".$this->capp->docFlowApp->getId()."_1");
            		if (!$adapter->connected)
            			$adapter->connect();
            		if ($adapter->connected) {
            			$sql = "UPDATE fields SET value=REPLACE(value,'".$oldDir."','".$dir."') WHERE name='path' AND value LIKE '".$oldDir."%' AND classname='ReferenceFiles'";
            			$stmt = $adapter->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
            			$stmt->execute();            			
            		}            		
            	}
			}
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();
			$app->raiseRemoteEvent("FM_RENAME","message=Пользователь переименовал файл/каталог `".$oldDir."` в `".$dir."`");				
    }

    function copyMove($arguments) {
            global $Objects;
            if (@$this->role["fmCanCopyMove"]=="false")
            	return 0;
            $this->useCase = $arguments["useCase"];
            $this->operation = $arguments["operation"];
            $this->setOperations();
            if ($this->operation=="copy" and !$this->fileOperations["copy"])
                    return 0;
            if ($this->operation=="cut" and !$this->fileOperations["move"])
                    return 0;
            $this->path = $arguments["path"];
            $this->files = (array)$arguments["files"];
            $app = $Objects->get("Application");
            if (!$app->initiated)
            	$app->initModules();
            if ($this->useCase=="sharesEditor") {
            	$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
            	$oldFolders = $ftpServer->getFolders();            	 
            }
			foreach ($this->files as $key=>$value) {
                    $value = (array)$value;
                    if ($this->capp->remoteSSHCommand=="") {
                            if ($this->operation=="copy")
                            	$this->shell->exec_command($this->app->copyCommand." -a '".$value["path"]."' '".$this->path."/'");
                            else
                            $this->shell->exec_command($this->app->moveDirCommand." '".$value["path"]."' '".$this->path."/'");
            			if ($this->operation=="copy")
            				$app->raiseRemoteEvent("FM_COPY","message=Пользователь скопировал файл/каталог `".$value["path"]."` в `".$this->path."`");
            			else            
            				$app->raiseRemoteEvent("FM_MOVE","message=Пользователь переместил файл/каталог `".$value["path"]."` в `".$this->path."`");
                    }
                    else {
                            if ($this->operation=="copy")
                            	shell_exec($this->capp->remoteSSHCommand." ".$this->app->copyCommand." -a '".$value["path"]."' '".$this->path."/'");
                            else
                            shell_exec($this->capp->remoteSSHCommand." ".$this->app->moveDirCommand." '".$value["path"]."' '".$this->path."/'");
            			if ($this->operation=="copy")
            				$app->raiseRemoteEvent("FM_COPY","message=Пользователь скопировал файл/каталог `".$value["path"]."` в `".$this->path."`");
            			else            
            				$app->raiseRemoteEvent("FM_MOVE","message=Пользователь переместил файл/каталог `".$value["path"]."` в `".$this->path."`");
                    }
                    if ($this->operation!="copy" and is_object($this->capp->docFlowApp)) {
                    	$adapter = $Objects->get("DocFlowDataAdapter_".$this->capp->docFlowApp->getId()."_1");
                    	if (!$adapter->connected)
                    		$adapter->connect();
                    	if ($adapter->connected) {
                    		$sql = "UPDATE fields SET value=REPLACE(value,'".$value["path"]."','".$this->path."/".array_pop(explode("/",$value["path"]))."') WHERE name='path' AND value LIKE '".$value["path"]."%' AND classname='ReferenceFiles'";
                    		$stmt = $adapter->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                    		$stmt->execute();
                    	}
                    }                    
                    $oldDir = $value["path"];
                    $dir = $this->path;
                    if ($this->useCase=="sharesEditor" and $this->operation!="copy") {
                    	$this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
                            if (!$this->fileServer->loaded)
                                    $this->fileServer->load();
                            $share = $this->fileServer->containsPath(str_replace($this->rootPath."/","",$oldDir));
                            if ($share) {
                            		if (!$share->loaded)
                                    	$share->load();
                            		$share->oldFolders = $oldFolders;
                                    $share->path = str_replace($this->rootPath."/","",$dir."/".array_pop(explode("/",$oldDir)));
                                    $share->save();
                            }
                    }
            }
    }

    function delete($arguments) {
            global $Objects,$ftpUser;
            if (@$this->role["fmCanDelete"]=="false")
            	return 0;
            $this->useCase = $arguments["useCase"];
            $this->setOperations();
            if (!$this->fileOperations["delete"])
                    return 0;
            $this->files = (array)$arguments["files"];
			$error_files = array();
			$conn_id = ftp_connect("localhost");
			if ($this->useCase=="fileUpload") {
				if (!ftp_login($conn_id,$ftpUser->name,$ftpUser->ftpPassword)) {
					$this->reportError("Ошибка подключения к серверу!");
					return 0;
				}
			}
			if ($this->useCase=="sharesEditor") {
				$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
				$oldFolders = $ftpServer->getFolders();
			}
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();
			foreach ($this->files as $key=>$value) {
                    $value = (array)$value;
                    if ($this->useCase=="sharesEditor") {
                            $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
                            if (!$this->fileServer->loaded)
                                    $this->fileServer->load();
                            $share = $this->fileServer->containsPath(str_replace($this->rootPath."/","",$value["path"]));
                            if ($share) {
                                    if (!$share->loaded)
                                    	$share->load();
                                    $share->oldFolders = $oldFolders;
                                    $this->fileServer->removeShare($share->idnumber);
                            }
                    }
					if ($this->useCase=="fileUpload") {
						$file = str_replace("//","/",$value["path"]);
						if (is_dir($ftpUser->ftpHome."/".$file)) {
							if (!@ftp_rmdir($conn_id,$file))
								$error_files[] = $file;
						}
						else {
							if (!@ftp_delete($conn_id,str_replace($ftpUser->ftpHome."/","",$file)))
								$error_files[] = str_replace($ftpUser->ftpHome,"",$file);
						}
					}
                    if ($this->capp->remoteSSHCommand=="") {
                    	$this->shell->exec_command($this->app->deleteCommand." -rf '".$value["path"]."'");
                    }
                    else {
                    	shell_exec($this->capp->remoteSSHCommand." ".$this->app->deleteCommand." -rf '".$value["path"]."'");
                    }
                   	$app->raiseRemoteEvent("FM_DELETE","message=Пользователь удалил файл/каталог `".$value["path"]."`");
                    
            }
			if (count($error_files)>0) {
				$this->reportError("Возникли ошибки при удалении следующих файлов или каталогов:\n\n ".implode("\n",$error_files));
			}
			if (is_object($this->capp->docFlowApp)) {
				$adapter = $Objects->get("DocFlowDataAdapter_".$this->capp->docFlowApp->getId()."_1");
				if (!$adapter->connected)
					$adapter->connect();
				if ($adapter->connected) {
					$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @path LIKE '".$value["path"]."%' AND @classname='ReferenceFiles'",$adapter,$this->capp->docFlowApp->getId());
					foreach ($entities as $entity) {
						$entity->loaded = false;
						$entity->load();
						$entity->deleted = 1;
						$entity->save();
					}
				}
			}				
    }

	function checkFTPAccess($arguments) {
		global $ftpUser;
		$dir = $arguments["path"];
		$conn_id = ftp_connect("localhost");
		if (!@ftp_login($conn_id,$ftpUser->name,$ftpUser->ftpPassword)) {
			$this->reportError("Ошибка подключения к серверу!");
			return 0;
		}
		if (!@ftp_chdir($conn_id,$dir)) {
			$this->reportError("Произошла ошибка при входе в каталог сервера! Возможно у Вас не достаточно прав доступа.");
			return 0;
		}
		if (!@ftp_mkdir($conn_id,"xoxzoz")) {
			$this->reportError("Не могу создать файл в каталоге сервера. Возможно у Вас не достаточно прав доступа.");
			return 0;
		}
		ftp_rmdir($conn_id,'xoxzoz');
	}
}
?>