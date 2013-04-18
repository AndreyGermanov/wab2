<?php
/* 
 * Вспомогательный класс, используемый для выполнения команд на сервере
 * от имени пользователя root
 */
class Shell {
    function construct($params) {
        $this->object_id = @$params[0];
        $this->clientClass = "Shell";
        $this->parentClientClasses = "";        
    }

    function exec_command($command) {
        global $Objects;        
        $app = $Objects->get("Application");
        $command = str_replace("~",";",$command);
		return shell_exec(str_replace('``','"',$command));
        if (!$app->initiated)
            $app->initModules();
        if (substr($app->sudoPasswordFile,0,1)!="/")
            $cmd = 'export SUDO_ASKPASS="'.$_SERVER["DOCUMENT_ROOT"]."/".$app->sudoPasswordFile.'";';
        else
            $cmd = 'export SUDO_ASKPASS="'.$app->sudoPasswordFile.'";';
        $command_array = explode(";",$command);
        for ($counter=0;$counter<count($command_array);$counter++) {
            $cmd .= "sudo -u root -A ".$command_array[$counter].";";
            $cmd = str_replace("~",";",$cmd);
            $cmd = str_replace("``",'"',$cmd);
            //echo $cmd;
        }       
        return shell_exec($cmd);
    }  
}
?>