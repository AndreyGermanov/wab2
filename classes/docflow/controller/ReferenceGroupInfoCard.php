<?php

class ReferenceGroupInfoCard extends ReferenceInfoCard {
	
	function construct($params) {
		parent::construct($params);
		$this->classListTitle = "Группы пользователей";
		$this->classTitle = "Группа пользователей";		
		$this->models[] = "ReferenceInfoCard";
		$this->clientClass = "ReferenceGroupInfoCard";
		$this->parentClientClasses = "ReferenceInfoCard~Reference~WABEntity";
		$this->icon = $this->skinPath."images/Tree/group.png";		
        $this->classTitle = "Информационная карточка группы пользователей";
        $this->classListTitle = "Информационные карточки групп пользователей";
	}
}

?>