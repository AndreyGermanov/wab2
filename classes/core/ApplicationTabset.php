<?php
class ApplicationTabset extends Tabset {
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ApplicationTabset";
		$this->parentClientClasses = "Tabset~Mailbox~Entity";		
	}
}
?>