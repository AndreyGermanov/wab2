<?php

class ReferenceUserInfoCard extends ReferenceInfoCard {
	
	function construct($params) {
		parent::construct($params);
		$this->classListTitle = "Пользователи сервера";
		$this->classTitle = "Пользователь сервера";		
		$this->models[] = "ReferenceInfoCard";
		$this->clientClass = "ReferenceUserInfoCard";
		$this->parentClientClasses = "ReferenceInfoCard~Reference~WABEntity";
		$this->icon = $this->skinPath."images/Tree/user.png";		
        $this->classTitle = "Информационная карточка пользователей";
        $this->classListTitle = "Информационные карточки пользователей";
	}
}

?>