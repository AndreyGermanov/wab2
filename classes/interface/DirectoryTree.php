<?
class DirectoryTree extends Tree {

    function  construct($params) {
        parent::construct($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->handler = "scripts/handlers/interface/DirectoryTree.js";
        $this->css = $app->skinPath."styles/Tree.css";
        $this->icon = $app->skinPath."images/Tree/folder.png";
        $this->skinPath = $app->skinPath;
        $this->initial_dir = "/";
        $this->target_item = "";
        $this->show_files = false;
        $this->absolute_path = true;
        $this->clientClass = "DirectoryTree";
        $this->parentClientClasses = "Tree~Entity";        
    }

    function setTreeItems($dir="")
    {
        global $Objects;
        $app = $Objects->get($this->module_id);
        if ($this->root_dir=="")
            $this->root_dir = $this->rootPath;
        if ($this->root_dir=="")
            $this->root_dir = $this->title;            
        if ($dir=="") {            
            $parent = "";
            $dir = $this->rootPath;
        } else
            $parent = $dir;
        $handle = opendir($app->remotePath.$dir."/");
        chdir($app->remotePath.$dir."/");
        $result = array();
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if ($this->show_files==false) {
                    if (!is_dir($file))
                        continue;
                }
                if ($file=="content")
                    $file=="xoxontent";
                if (is_dir($file)) {
                    $key = strtoupper($file);
                }
                else {
                    $key = "____".strtoupper($file);
                }                
                              
                $result[$key]["id"] = $dir."/".$file;
                $result[$key]["title"] = $file;
                $result[$key]["icon"] = "images/Tree/folder.png";
                $result[$key]["parent"] = $parent;
                $result[$key]["loaded"] = "false";
                if (is_dir($file)) {
                    $result[$key]["loaded"] = "false";
                    $result[$key]["icon"] = $this->skinPath."images/Tree/folder.png";
                }
                else {
                    $result[$key]["loaded"] = "true";
                    $result[$key]["icon"] = $this->skinPath."images/Tree/file.png";
                }
            }
        }

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
        chdir($_SERVER["DOCUMENT_ROOT"]);
    }

    function getId() {
        return "DirectoryTree_".$this->module_id."_".$this->name;
    }

    function add_folder($folder) {
        global $Objects;
        
        if (is_array($folder))
			$folder = $folder["folder"];
			
        $app = $Objects->get($this->module_id);
        if (file_exists($app->remotePath.$folder)) {
            $this->reportError("Указанная папка $folder уже существует!","add_folder");
            return 0;
        }

        global $Objects;
        $shell = $Objects->get("Shell_shell");
        if ($app->remoteSSHCommand!="") {
            $shell->exec_command($app->remoteSSHCommand." \"".$this->app->makeDirCommand." -p '".$folder."'\"");
            $shell->exec_command($app->remoteSSHCommand." chmod -R 777 '".$folder."'");
        } else {
            $shell->exec_command($this->app->makeDirCommand." -p '".$folder."'");
            $shell->exec_command("chmod -R 777 '".$folder."'");            
        }
    }
    
    function add_file($file) {

        global $Objects;
        
        if (is_array($file))
			$file = $file["file"];
			
        $app = $Objects->get($this->module_id);

        if (file_exists($app->remotePath.$file)) {
            $this->reportError("Указанный файл уже существует!","add_file");
            return 0;
        }

        $shell = $Objects->get("Shell_shell");
        if ($app->remoteSSHCommand!="") {
            $shell->exec_command($app->remoteSSHCommand." \"".$this->app->touchCommand." '".$file."'\"");
            $shell->exec_command($app->remoteSSHCommand." chmod -R 666 '".$file."'");
        } else {
            $shell->exec_command($this->app->touchCommand." '".$file."'");
            $shell->exec_command("chmod -R 666 '".$file."'");            
        }
    }
    

    function change_folder($old_folder,$new_folder=null) {
        global $Objects;
        
        if (is_array($old_folder)) {
			$new_folder = $old_folder["new_folder"];
			$old_folder = $old_folder["old_folder"];
			
		}
        
        $app = $Objects->get($this->module_id);
        if (file_exists($app->remotePath.$new_folder)) {
            $this->reportError("Указанная папка или файл уже существует!","change_folder");
            return 0;
        }

        global $Objects;
        $shell = $Objects->get("Shell_shell");
        if ($app->remoteSSHCommand!="")
            $shell->exec_command($app->remoteSSHCommand." \"".$this->app->moveDirCommand." '".$old_folder."' '".$new_folder."'\"");
        else
            $shell->exec_command($this->app->moveDirCommand." '".$old_folder."' '".$new_folder."'");
    }
    
    function change_file($old_file,$new_file=null) {
        global $Objects;
        
        if (is_array($old_file)) {
			$old_file = $old_file["old_file"];
			$new_file = $old_file["new_file"];
		}
        
        $app = $Objects->get($this->module_id);
        if (file_exists($app->remotePath.$new_file)) {
            $this->reportError("Указанный файл уже существует!","change_file");
            return 0;
        }

        $shell = $Objects->get("Shell_shell");
        if ($app->remoteSSHCommand!="")
            $shell->exec_command($app->remoteSSHCommand." \"".$this->app->moveDirCommand." '".$old_file."' '".$new_file."'\"");
        else
            $shell->exec_command($this->app->moveDirCommand." '".$old_file."' '".$new_file."'");
    }    

    function remove_folder($folder) {
        global $Objects;
        
        if (is_array($folder)) {
			$folder = $folder["folder"];
		}
        
        $app = $Objects->get($this->module_id);
        if (!file_exists($app->remotePath.$folder)) {
            $this->reportError("Указанная папка или файл не существует!","remove_folder");
            return 0;
        }

        $shell = $Objects->get("Shell_shell");
        if ($app->remoteSSHCommand!="")
            $shell->exec_command($app->remoteSSHCommand." \"".$this->app->deleteCommand." -rf '".$folder."'\"");
        else
            $shell->exec_command($this->app->deleteCommand." -rf \"".$folder."\"");
    }

    function remove_file($file) {
        global $Objects;
        
        if (is_array($file)) {
			$file = $file["file"];
		}
        
        $app = $Objects->get($this->module_id);        
        if (!file_exists($app->remotePath.$file)) {
            $this->reportError("Указанный файл не существует!","remove_file");
            return 0;
        }

        $shell = $Objects->get("Shell_shell");
        if ($app->remoteSSHCommand!="")
            $shell->exec_command($app->remoteSSHCommand." \"".$this->app->deleteCommand." '".$file."'\"");
        else
            $shell->exec_command($this->app->deleteCommand." \"".$file."\"");
    }

    function check_file($file) {
        global $Objects;
        $app = $Objects->get($this->module_id);        
        if (!file_exists($app->remotePath.$file))
            return 1;
        else
            return 0;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "showFilesHook";
			case '4': return "add_folder";
			case '5': return "add_file";
			case '6': return "change_folder";
			case '7': return "remove_folder";
			case 'show': return "showHook";
		}
		return parent::getHookProc($number);
	}
	
	function showHook($arguments) {		
		$object = $this;
		$object->title = "";
		$object->rootPath = "/";		
		$object->setArguments($arguments);
		$object->icon = $this->skinPath.'images/Tree/folder.gif';
		$object->SetTreeItems();
		$object->show();
	}
	
	function showFilesHook($arguments) {
		$this->show_files=$arguments["show_files"];
		$this->setTreeItems($arguments["dir"]);
		echo $this->items_string;
	}
}
?>