<?php
/**
 * Класс, обрабатывающий раздел Web-сайта типа "Переворачивающаяся книга"
 *
 * @author andrey
 */
class FlippingBookWebEntity extends WebEntity {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "FlippingBookWebEntity";
		$this->parentClientClasses = "WebEntity~Entity";		
	}
   
    function getImagesList() {
        $handle = @opendir($this->flippingFolder."/");
        if ($handle) {
            $farr = array();
	    while (false !== ($file = readdir($handle))) {
    		if ($file != "." && $file != "..") {
    	    	if (is_dir($file))
            		continue;
	            $farr[] = '"'.str_replace($_SERVER["DOCUMENT_ROOT"],"",$this->flippingFolder."/".$file.'|"');
    	        }
            }
    	    if (count($farr)>0)
        	$farr[count($farr)-1] = str_replace("|","",$farr[count($farr)-1]);
            return implode(",",$farr);;        
        } else
        	return 0;
    }

    function getArgs() {       
        $result = parent::getArgs();
        $result["{imagesList}"] = $this->getImagesList();
        return $result;
    }      
}
?>