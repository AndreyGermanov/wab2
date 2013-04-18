<?php
/**
 * Класс, реализующий регистр состояний заказов. Позволяет получить состояние заказа на любую дату,
 * а также историю изменения состояния заказа.
 * 
 * Реквизиты регистра:
 * 
 * orderDocument - Заказ (документ DocumentOrder)
 * orderCondition - Состояние заказа (справочник ReferenceOrderConditions)
 * 
 * @author andrey
 */
class RegistryOrderConditions extends Registry {

    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Состояние заказа";
        $this->classListTitle = "Состояния заказов";
        $this->recvs = "orderDocument,orderCondition";
        $this->clientClass = "RegistryOrderConditions";
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
        						"orderDocument" => array (
        								"size" => "24%"
        						),
        						"orderCondition" => array (
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