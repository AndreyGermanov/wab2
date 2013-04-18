<?php
/**
 * Класс, реализующий регистр заинтересованных лиц задачи.

 * Реквизиты регистра:
 * 
 * listener - заинтересованное лицо (пользователь системы - ReferenceUsers)
 * task - задача (задача - DocumentTask)
 * 
 * @author andrey
 */
class RegistryTaskListeners extends Registry {
    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Заинтересованное лицо задачи";
        $this->classListTitle = "Заинтересованные лица задачи";
        $this->recvs = "listener,task";
        $this->clientClass = "RegistryTaskListeners";
        $this->parentClientClasses = "Registry~Entity";        
    }
}
?>