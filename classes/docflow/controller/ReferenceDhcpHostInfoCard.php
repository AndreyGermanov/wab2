<?php

class ReferenceDhcpHostInfoCard extends ReferenceInfoCard {
	
	function construct($params) {
		parent::construct($params);
		$this->classListTitle = "Хосты";
		$this->classTitle = "Хост";		
		$this->models[] = "ReferenceInfoCard";
		$this->clientClass = "ReferenceDhcpHostInfoCard";
		$this->parentClientClasses = "ReferenceInfoCard~Reference~WABEntity";
		$this->icon = $this->skinPath."images/Window/system-settings.png";		
        $this->classTitle = "Информационная карточка хоста";
        $this->classListTitle = "Информационные карточки хостов";
	}
}

?>