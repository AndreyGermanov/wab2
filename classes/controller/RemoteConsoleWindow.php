<?php
class RemoteConsoleWindow extends FrameWindow {
    function load() {
        if (!$this->loaded) {
            global $Objects;
            $host = $Objects->get($this->name);
             $host->load();                
            $arr = explode(".",$host->fixed_address);
            array_shift($arr);array_shift($arr);
            $port = $arr[0].$arr[1].$host->remoteConsolePort;
            if ($port<1000)
                $port = "1".$port;
            $ip = $host->fixed_address;
            $consolePort = $host->remoteConsolePort;
            $app = $Objects->get($this->module_id);
            $gapp = $Objects->get("Application");
            if (!$gapp->initiated)
                $gapp->initModules();
            $shell = $Objects->get("Shell_shell");
            $iph = $app->remoteAddress;        
            $this->cmd = strtr($app->shellInABoxCommand,array("{ip}" => $ip, "{port}" => $port, "{consolePort}" => $consolePort));
            exec($app->remoteSSHCommand." '".$this->cmd."'");
            $this->url = "http://".$iph.":".$port; 
            $this->loaded = true;
            
	        $this->clientClass = "RemoteConsoleWindow";
    	    $this->parentClientClasses = "FrameWindow~Entity";        
        }
    }
    
    function getId() {
        return "RemoteConsoleWindow_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return "Консоль";
    }
    
    function unload ($arguments=null) {
    	if (isset($arguments))
    		$this->cmd = $arguments["cmd"];
        global $Objects;
        $app = $Objects->get($this->module_id);
        exec($app->remoteSSHCommand."kill -9 `ps -ef | grep \"".$this->cmd."\" | head -1 | fmt -us | cut -d ' ' -f2 | head -1`");
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "getObjectText";
			case '4': return "unload";
		}
		return parent::getHookProc($number);
	}
	
	function getObjectText($arguments) {
		$this->object_text = @$arguments["object_text"];
	}
}
?>