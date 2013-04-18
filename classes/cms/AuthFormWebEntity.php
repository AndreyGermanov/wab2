<?php
class AuthFormWebEntity extends InputWebEntity {
    
	function construct($params) {       
		parent::construct($params);
        
        global $Objects;
		$this->models[] = "WebEntity";
        $this->persistedFields = $this->explodePersistedFields(array());
        $this->clientClass = "AuthFormWebEntity";                
        $this->childClass = "AuthFormWebEntity";        
        $this->parentClientClasses = "WebEntity~Entity~InputWebEntity";    
	}
        
    function submitForm() {     
    	                
        global $Objects;

        if ((!$this->loaded))
        	$this->load();
        
        if ($this->submitSuccessTemplate == "")
        	$this->submitSuccessTemplate = $this->userTemplate;
        if ($this->submitErrorTemplate == "")
        	$this->submitErrorTemplate = $this->userTemplate;
        if ($this->submitSendEmail) {
        	if ($this->submitEmailTemplate == "")
        		return 0;
        }        
        
        $login = trim($_POST[str_replace($this->module_id."_","",$this->getId())."_login"]);
        if (!ctype_alnum($login)) {
        	$login = "";
        }
        $password = trim($_POST[str_replace($this->module_id."_","",$this->getId())."_password"]);
        $user = $Objects->get("ApacheUser_".$this->module_id."_".$login);
        $user->load();
        if ($user->password==$password)
       		$_SESSION["user"] = $login;        
        echo "<script>if (window.parent.objects.objects['".$this->getId()."']!=null) window.parent.objects.objects['".$this->getId()."'].afterSave();</script>";
    }
    
    function unsubmitForm() {
        if ((!$this->loaded))
        	$this->load();
    	$_SESSION["user"] = "default";
        	echo "<script>if (window.parent.objects.objects['".$this->getId()."']!=null) window.parent.objects.objects['".$this->getId()."'].afterSave();</script>";
   	}
            
    function show($instance,$out) {
    	if (!$this->loaded)
    		$this->load();
    	if (@$_SESSION["user"]!="default")
    		$this->user = @$_SESSION["user"];
    	else
    		$this->user = "";
    	if ($this->user=="" or $this->user=="default")
    		$this->userTemplate = $this->submitErrorTemplate;
    	else
    		$this->userTemplate = $this->submitSuccessTemplate;
    	return parent::show($instance,$out);    	 
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "submitForm";
    		case '4': return "unsubmitForm";
    	}
    	return parent::getHookProc($number);
    }
    
    function checkData() {
    	if ($this->submitSuccessTemplate=="-1")
    		$this->submitSuccessTemplate = "";
    	if ($this->submitErrorTemplate=="-1")
    		$this->submitErrorTemplate = "";
    	if ($this->submitEmailTemplate=="-1")
    		$this->submitEmailTemplate = "";
    	if ($this->class==$this->old_class) {
    		if ($this->submitSuccessTemplate=="") {
    			$this->reportError("Не указан шаблон при успешном заполнении");
    			return 0;
    		}
    		if ($this->submitErrorTemplate=="") {
    			$this->reportError("Не указан шаблон при ошибочном заполнении");
    			return 0;
    		}
    	}
    	return 1;
    }    
    
}
?>