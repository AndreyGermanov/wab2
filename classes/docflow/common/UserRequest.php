<?php
class UserRequest extends WABEntity {
	
	function construct($params) {
		parent::construct($params);
		$this->template = "renderForm";
		global $Objects;
		$this->app = $Objects->get("Application");
		if (!$this->app->initiated)
			$this->app->initModules();
		$this->skinPath = $this->app->skinPath;
		$this->icon = $this->skinPath."images/Tree/mail.gif";
	}
	
	function getHookProc($number) {
		switch($number) {
			case '3': return "sendRequest";
		}
		return parent::getHookProc($number);
	}
	
	function sendRequest($arguments) {
		if (isset($arguments["requestText"]) and $arguments["requestText"]!="") {
			$to = "andrey@it-port.ru";
			$headers = "From: Request\n";
			$headers.= "MIME-Version: 1.0\n";
			$headers.= "Content-type: text/html; charset=utf-8\n";
			$subject = "Вопрос или пожелание пользователя";
			$message  = "Пользователь: ".$this->app->User."<br/><br/>";
			$message .= "Сообщение:<br/><br/>";
			$message .= str_replace("\n","<br/>",$arguments["requestText"]);
			mail($to,$subject,$message,$headers);				
		} else
			$this->reportError("Текст сообщения пуст","sendRequest");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/UserRequest.html"));
		$out = $blocks["header"];
		return $out;
	}
	
	function getPresentation() {
		return "Отправить запрос в службу поддержки";
	}
}