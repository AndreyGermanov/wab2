<?php
/* 
 * Класс отвечает за окно очереди сообщений почтового сервера,
 * в котором находится наблица MailQueueTable
 */
class MailQueue extends WABEntity {
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
        $this->skinPath = $app->skinPath;
        $this->template = "templates/mail/MailQueue.html";
        $this->css = $this->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/mail/MailQueue.js";
        $this->icon = $this->skinPath."images/Tree/mailqueue.png";
        $this->MailQueueTable = "MailQueueTable_".$this->module_id."_Table";
        $this->width="600";
        $this->height="350";
        $this->overrided = "width,height";
        $this->clientClass = "MailQueue";
        $this->parentClientClasses = "Entity";        
	    $this->classTitle = "Почтовая очередь";
	    $this->classListTitle = "Почтовая очередь";
    }

    function load() {
        
    }

    function getArgs() {
    	$args = array();
    	$args["window_id"] = $this->window_id;
        $this->frame_src = "?object_id=".$this->MailQueueTable."&hook=show&arguments=".urlencode(json_encode($args));
        $result = parent::getArgs();
        return $result;
    }

    function getId() {
        return "MailQueue_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return "Почтовая очередь";
    }
}
?>