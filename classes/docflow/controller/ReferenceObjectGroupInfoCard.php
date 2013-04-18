<?php

class ReferenceObjectGroupInfoCard extends ReferenceInfoCard {
	
	function construct($params) {
		parent::construct($params);
		$this->classListTitle = "Группы объектов";
		$this->classTitle = "Группа объектов";		
		$this->models[] = "ReferenceInfoCard";
		$this->clientClass = "ReferenceObjectGroupInfoCard";
		$this->parentClientClasses = "ReferenceInfoCard~Reference~WABEntity";
		$this->icon = $this->skinPath."images/Tree/objectgroup.png";		
        $this->classTitle = "Информационная карточка группы объектов";
        $this->classListTitle = "Информационные карточки групп объектов";
	}
}

?>