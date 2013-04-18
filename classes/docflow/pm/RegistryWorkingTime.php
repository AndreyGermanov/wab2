<?php
/**
 * Класс, реализующий регистр отработанного трудовыми ресурсами времени. 
 * С его помощью можно отслеживать, чем трудовой ресурс был занят в любой период времени
 * и над чем работал. Ресурсом может быть либо сотрудник, либо оборудование
 * 
 * Реквизиты регистра:
 * 
 * dateStart - Начало периода работы
 * dateEnd - Конец периода работы
 * period - разница между dateEnd и dateStart (время в секундах, которое отработал ресурс)
 * resource - ссылка на объект трудового ресурса
 * workObject - ссылка на объект, над которым работал ресурс
 * reportText - текст отчета о работе
 * firm - организация, в рамках которой проходила работа
 * 
 * @author andrey
 */
class RegistryWorkingTime extends Registry {
    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Рабочее время ресурса";
        $this->classListTitle = "Рабочее время ресурса";
        $this->recvs = "dateStart,dateEnd,firm,resource,workObject,reportText";
        $this->clientClass = "RegistryWorkingTime";
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
	   					"reportText" => array (
							"size" => "10%"
	   					),
	   					"firm" => array (
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