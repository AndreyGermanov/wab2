<?php

class ReferenceDhcpSubnetInfoCard extends ReferenceInfoCard {
	
	function construct($params) {
		parent::construct($params);
		$this->classListTitle = "Сети";
		$this->classTitle = "Сеть";		
		$this->models[] = "ReferenceInfoCard";
		$this->clientClass = "ReferenceDhcpSubnetInfoCard";
		$this->parentClientClasses = "ReferenceInfoCard~Reference~WABEntity";
		$this->icon = $this->skinPath."images/Tree/network.png";		
        $this->classTitle = "Информационная карточка подсети";
        $this->classListTitle = "Информационные карточки подсетей";
	}
}

?>