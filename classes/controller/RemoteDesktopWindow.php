<?php
class RemoteDesktopWindow extends FrameWindow {   
    function construct($params) {
        parent::construct($params);
        $this->handler = "scripts/handlers/controller/RemoteDesktopWindow.js";
        $this->clientClass = "RemoteDesktopWindow";
        $this->parentClientClasses = "FrameWindow~Entity";        
    }

    function load() {
        if (!$this->loaded) {
            global $Objects;
            $host = $Objects->get($this->name);
            if (!$host->loaded)
                $host->load();
            $port= $host->remoteDesktopPort;
            $user = $host->remoteDesktopUser;
            $password = $host->remoteDesktopPassword;
            $app = $Objects->get($this->module_id);
            $iph = $app->remoteAddress;        
            if ($host->remoteDesktopProtocol=="vnc") {
                $xml = new DOMDocument();
                $xml->load($app->remotePath.$app->remoteVNCConfigFile);
                $root_doc = $xml->documentElement;
                $el = $root_doc->getElementsByTagName("authorize")->item(0);
                if ($el!=null)
                    $root_doc->removeChild($el);
                $el = $xml->createElement("authorize");
                $el->setAttribute("username",$user);
                $el->setAttribute("password",$password);
                $param = $xml->createElement("protocol");
                $param->appendChild($xml->createTextNode("vnc"));
                $el->appendChild($param);
                $param = $xml->createElement("param");
                $param->setAttribute("name","hostname");
                $param->appendChild($xml->createTextNode($host->fixed_address));
                $el->appendChild($param);
                $param = $xml->createElement("param");
                $param->setAttribute("name","port");
                $param->appendChild($xml->createTextNode($port));
                $el->appendChild($param);
                $param = $xml->createElement("param");
                $param->setAttribute("name","password");
                $param->appendChild($xml->createTextNode($password));
                $el->appendChild($param);
                $root_doc->appendChild($el);
                $xml->save($app->remotePath.$app->remoteVNCConfigFile);            
                $this->url = "http://".$iph.":8080/guacamole";       
            } else {
                $fp = fopen("tmp/rdesktop.sh","w");
                fwrite($fp,"rdesktop -u '".$host->remoteDesktopUser."' -p '".$host->remoteDesktopPassword."' -k en ".$host->fixed_address.":".$host->remoteDesktopPort);
                $this->url = "https://".$_SERVER["SERVER_ADDR"]."/tmp/rdesktop.sh";
                fclose($fp);
            }
            $this->loaded = true;
        }
    }

    function getHookProc($number) {
		switch ($number) {
			case '3': return "getObjectText";
		}
		return parent::getHookProc($number);
	}
	
	function getObjectText($arguments) {
		$this->object_text = @$arguments["object_text"];
	}
}
?>