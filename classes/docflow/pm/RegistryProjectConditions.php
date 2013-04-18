<?php
/**
 * Класс, реализующий регистр состояний проектов. Позволяет получить состояние заказа на любую дату,
 * а также историю изменения состояния проекта.
 * 
 * Реквизиты регистра:
 * 
 * projectReference - Проект (элемент справочника ReferenceProject)
 * projectCondition - Состояние проекта (справочник ReferenceProjectConditions)
 * 
 * @author andrey
 */
class RegistryProjectConditions extends Registry {

    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Состояние проекта";
        $this->classListTitle = "Состояния проектов";
        $this->recvs = "projectReference,projectCondition";
        $this->clientClass = "RegistryProjectConditions";
        $this->parentClientClasses = "Registry~Entity";        
        $this->printProfiles["Основной"] = array (
        		"settings" => array (
        				"settingsClass" => "RegistrySettingsDialog"
        		),
        		"Основной" => array (
        				"printFields" => array (
        						"regDate" => array (
        								"size" => "10%"
        						),
        						"document" => array (
        								"size" => "23%"
        						),
        						"projectReference" => array (
        								"size" => "24%"
        						),
        						"projectCondition" => array (
        								"size" => "23%"
        						)
        				),
        				"totals" => array (
        						"bgColor" => "#DDDDDD",
        						"fontFace" => "Arial",
        						"fontSize" => "15",
        						"fontWeight" => "bold",
        						"fontColor" => "#000000"
        				)
        		)
        );
    }
}
?>