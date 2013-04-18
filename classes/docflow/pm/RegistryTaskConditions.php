<?php
/**
 * Класс, реализующий регистр состояний задач. Позволяет получить состояние задачи на любую дату,
 * а также историю изменения состояния задачи.
 * 
 * Реквизиты регистра:
 * 
 * taskDocument - Проект (элемент справочника DocumentTask)
 * taskCondition - Состояние задачи (справочник ReferenceTaskConditions)
 * 
 * @author andrey
 */
class RegistryTaskConditions extends Registry {

    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Состояние задачи";
        $this->classListTitle = "Состояния задач";
        $this->recvs = "taskDocument,taskCondition";
        $this->clientClass = "RegistryTaskConditions";
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
        						"taskDocument" => array (
        								"size" => "24%"
        						),
        						"taskCondition" => array (
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