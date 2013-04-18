<?php
/**
 * Класс, реализующий регистр исполнителей задачи.

 * Реквизиты регистра:
 * 
 * worker - исполнитель (пользователь системы - ReferenceUsers)
 * workObject - объект задачи 
 * task - задача (задача - DocumentTask)
 * dateStart - дата и время начала
 * dateEnd - дата и время окончания
 * period - время на выполнение задачи (разница между датой начала и датой окончания)
 * 
 * @author andrey
 */
class RegistryTaskWorkers extends Registry {
    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Плановая занятость сотрудников";
        $this->classListTitle = "Плановая занятость сотрудников";
        $this->recvs = "task,dateStart,dateEnd,period,worker,workObject";
        $this->clientClass = "RegistryTaskWorkers";
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
        						"dateStart" => array (
        								"size" => "24%"
        						),
        						"dateEnd" => array (
        								"size" => "23%"
        						),
        						"period" => array (
        								"size" => "10%",
        						),
        						"workObject" => array (
        								"size" => "10%"
        						),
        						"worker" => array (
        								"size" => "10%"
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