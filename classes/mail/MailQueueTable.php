<?php
/* 
 * Класс предназначен для управления таблицей почтовой очереди в окне
 * просмотра почтовой очереди. Методы этого класса получают данные, необходимые
 * для таблицы.
 *
 * Таблица находится в массиве queue.
 *
 * Метод updateQueue обновляет этот массив.
 *
 * Метод getArgs() выводит очередь в виде строки в форме:
 *
 * Ключ~Идентификатор~Время~Отправитель~Получатель~Очередь|Ключ~Идентификатор~Время~
 * Отправитель~Получатель~Очередь ...
 */
class MailQueueTable extends WABEntity {
    
    public $queue;

    function construct($params) {
        $this->module_id = @$params[0]."_".@$params[1];
        $this->name = @$params[2];
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->template = "templates/interface/Table.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/mail/MailQueueTable.js";
        $this->clientClass = "MailQueueTable";
        $this->parentClientClasses = "Entity";        
    }

    function updateQueue() {
        global $Objects;
        $queues[0] = "active";
        $queues[1] = "incoming";
        $queues[2] = "hold";
        $queues[3] = "deferred";
        //$queues[3] = "defer";
        $app = $Objects->get("MailApplication_".$this->module_id);
        $shell = $Objects->get("Shell_shell");
        $this->queue = array();
        $monthes = array("Jan"=>"01","Feb"=>"02","Mar"=>"03","Apr"=>"04","May"=>"05","Jun"=>"06","Jul"=>"07",
                         "Aug"=>"08","Sep"=>"09","Oct"=>"10","Nov"=>"11","Dec"=>"12");
        foreach($queues as $queue) {
            $cmd = str_replace("{queue}",$queue,$app->remoteSSHCommand." ".$app->listQueueCommand);
            $list = $shell->exec_command($cmd);  

            $list = explode("\n",$list);
            foreach ($list as $q) {
                if ($q=="")
                    continue;
                $id = $q;
                $d = trim($shell->exec_command($app->remoteSSHCommand." \"".strtr($app->postcatCommand,array("{id}" => $q, "{configPath}" => "/etc/postfix.in"))." | grep 'Date:' | sed -e 's/Date: //g' | cut -d ' ' -f2,3,4,5\""));
                $d.= trim($shell->exec_command($app->remoteSSHCommand." \"".strtr($app->postcatCommand,array("{id}" => $q, "{configPath}" => "/etc/postfix"))." | grep 'Date:' | sed -e 's/Date: //g' | cut -d ' ' -f2,3,4,5\""));
                $d.= trim($shell->exec_command($app->remoteSSHCommand." \"".strtr($app->postcatCommand,array("{id}" => $q, "{configPath}" => "/etc/postfix.out"))." | grep 'Date:' | sed -e 's/Date: //g' | cut -d ' ' -f2,3,4,5\""));
                $d = explode(" ",str_replace("\n","",$d));
                $day = $d[0];
                $month = strtr(@$d[1],$monthes);
                $year = @$d[2];
                $time = @$d[3];
                if (strlen($day)!=2)
                    $day = "0".$day;
                $message = htmlentities(trim($shell->exec_command($app->remoteSSHCommand." ".strtr($app->postcatCommand,array("{id}" => $q,"{configPath}" => "/etc/postfix.in")))));//" | grep '^From:' | sed -e 's/From: //g'")));
                $message.= htmlentities(trim($shell->exec_command($app->remoteSSHCommand." ".strtr($app->postcatCommand,array("{id}" => $q,"{configPath}" => "/etc/postfix")))));//" | grep '^From:' | sed -e 's/From: //g'")));
                $message.= htmlentities(trim($shell->exec_command($app->remoteSSHCommand." ".strtr($app->postcatCommand,array("{id}" => $q,"{configPath}" => "/etc/postfix.out")))));//" | grep '^From:' | sed -e 's/From: //g'")));
                $message = explode("\n",$message);
                for($i=0;$i<count($message);$i++) {
                    $matches = array();
                    $line = $message[$i];
                    if (preg_match("/^From:\ (.*)$/U",$line,$matches)==1)
                        $from = strtr(trim($message[$i+1]),array("<" => "",">" => ""));
                    if (preg_match("/^To:\ (.*)$/U",$line,$matches)==1) {
                        if (substr(trim($message[$i+1]),0,1)=="<")
                            $to = strtr(trim($message[$i+1]),array("<" => "",">" => ""));
                        else
                            $to = $matches[1];
                    }
                    if (preg_match("/^Subject:\ (.*)$/U",$line,$matches)==1)
                        $subject = trim($matches[1]);
                }
                
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["id"]=$id;
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["time"]=$day.".".$month.".".$year." ".$time;
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["from"]=@$from;
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["to"]=@$to;
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["queue"]=$queue;
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["subject"]=@$subject;
                $this->queue[$year."-".$month."-".$day." ".$time." ".$queue]["message"]=$message;
            }
        }
        krsort($this->queue);
        $arr = array();
        foreach($this->queue as $key=>$q) {
            $arr[count($arr)] = $key."~".$q["id"]."~".$q["time"]."~".$q["from"]."~".$q["to"]."~".$q["subject"]."~".$q["queue"]."~".str_replace("'","\'",implode("<br>",$q["message"]));
        }
        return implode("|",$arr);
    }

    function load() {

    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{queue}"] = $this->updateQueue();
        return $result;
    }

    function sendMessage($id) {
        global $Objects;
        if (is_array($id))
        	$id = $id["id"];
        $app = $Objects->get("MailApplication_".$this->module_id);
        $shell = $Objects->get("Shell_shell");
        echo $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postSuperCommand,array("{cmd}" => "-r ".$id,"{configPath}" => "/etc/postfix.in")));
        echo $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postSuperCommand,array("{cmd}" => "-r ".$id,"{configPath}" => "/etc/postfix")));
        echo $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postSuperCommand,array("{cmd}" => "-r ".$id,"{configPath}" => "/etc/postfix.out")));
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILQUEUE_RESEND","message=Пользователь запросил повторную отправку письма с идентификатором $id из почтовой очереди");
    }

    function deleteMessage($id) {
        global $Objects;
        if (is_array($id))
        	$id = $id["id"];
        $app = $Objects->get("MailApplication_".$this->module_id);
        $shell = $Objects->get("Shell_shell");
        echo $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postSuperCommand,array("{cmd}" => "-d ".$id,"{configPath}" => "/etc/postfix.in")));
        echo $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postSuperCommand,array("{cmd}" => "-d ".$id,"{configPath}" => "/etc/postfix")));
        echo $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postSuperCommand,array("{cmd}" => "-d ".$id,"{configPath}" => "/etc/postfix.out")));
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILQUEUE_DELETE","message=Пользователь запросил удаление письма с идентификатором $id из почтовой очереди");
    }

    function getDeferReason($id) {
        global $Objects;
        if (is_array($id))
        	$id = $id["id"];
        $app = $Objects->get("MailApplication_".$this->module_id);
        $shell = $Objects->get("Shell_shell");
        $arr = array("{first_symb}" => substr($id,0,1), "{id}" => $id);
        echo $shell->exec_command(strtr($app->remoteSSHCommand." ".$app->getDeferReasonCommand." -c /etc/postfix.in",$arr));
        echo $shell->exec_command(strtr($app->remoteSSHCommand." ".$app->getDeferReasonCommand,$arr));
        echo $shell->exec_command(strtr($app->remoteSSHCommand." ".$app->getDeferReasonCommand." -c /etc/postfix.out",$arr));
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILQUEUE_REASON","message=Пользователь запросил причину задержки письма с идентификатором $id из почтовой очереди");
    }

    function getId() {
        return "MailQueueTable_".$this->module_id."_".$this->name;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "sendMessage";
    		case '4': return "deleteMessage";
    		case '5': return "getDeferReason";
    		case '6': return "updateQueueHook";
    	}
    }    
    
    function updateQueueHook() {
    	$this->load();
    	echo $this->updateQueue();
    }
}
?>