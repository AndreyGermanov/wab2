<?php
/**
 * Класс, реализующий регистр контролеров задачи.

 * Реквизиты регистра:
 * 
 * controller - контролер задачи (пользователь системы - ReferenceUsers)
 * task - задача (задача - DocumentTask)
 * 
 * @author andrey
 */
class RegistryTaskControllers extends Registry {
    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Контролеры задачи";
        $this->classListTitle = "Контролеры задачи";
        $this->recvs = "controller,task";
        $this->clientClass = "RegistryTaskControllers";
        $this->parentClientClasses = "Registry~Entity";        
    }
}
?>