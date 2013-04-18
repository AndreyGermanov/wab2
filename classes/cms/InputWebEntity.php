<?php
/**
 * Класс реализует функциональность формы ввода данных на Web-странице.
 * Используется для организации форм обратной связи, для написания комментариев,
 * заполнения анкет, для создания записей в гостевые книги и т. д. 
 * Может являться основой для создания форумов.
 * 
 * Фактически является потомком WebEntity. В шаблоне нужно разместить форму
 * ввода, в которой будет скрытое поле action=submit. Это вызовет на стороне 
 * сервера процедуру submitForm данного класса, она должна создать дочерний элемент
 * формы и вызвать его метод afterInit(), который возьмет настройки для дочерних
 * элементов, указанные для самой формы и заполнит соответствующие поля дочернего
 * элемента. Далее вызывается унаследованный от WABEntity метод save(), который
 * должен принять из массива POST[] данные формы, проверить их на формальную
 * корректность (также в соответствии с массивом persistedFields) в методе checkData()
 * и сохраняет вновь созданный элемент. 
 * 
 * В методе checkData() должно сформироваться поле title для создаваемого элемента.
 * Для этой цели в форме должно быть скрытое поле "titleField", в качестве значения
 * которого должен быть указан шаблон для формирования значения поля title. 
 * Шаблон может включать стандартные подстановки полей в формате {поле}, которые
 * преобразуются из значения метода getArgs().
 * 
 * Метод checkData() выявляет ошибки и заполняет массив $errors сообщениями о
 * них. После того как метод save() выполнился, должен отобразиться определенный
 * шаблон. 
 * 
 * Если массив errors[] пуст, то отображается шаблон, указанный в поле $submitSuccessTemplate.
 * Если это поле не указано, то используется стандартный шаблон $userTemplate.
 * 
 * Если массив errors[] не пуст, то отображается шаблон, указанный в поле $submitErrorTemplate.
 * Если это поле не указано, то используется стандартный шаблон $userTemplate.
 * 
 * При отображаении шаблонов, если в форме есть поле с именем "query_options", то она используется
 * для того чтобы передать строку запроса в шаблон ответа.
 * 
 * Также, в случае успешного сохранения заполненной формы, может отправляться оповещение
 * на указанный адрес электронной почты. Шаблон оповещения определяется параметром $submitEmailTemplate.
 * Адрес электронной почты указывается в поле $submitEmailAddress.
 *
 * В случае, если в форме есть скрытое поле "ajax_submit" и оно равно "true", то поведение меняется.
 * В этом случае в форме также должен быть скрытый фрэйм (как в WebEntity) и форма должна
 * направлять свой вывод в него (target). В него должен быть выведен либо массив errors,
 * либо ничего. Если выведен массив errors, то счиается что форма сохранена не успешно,
 * иначе успешно.
 * 
 * В случае с "ajax_submit", после выполнения функции сохраения вызывается метод afterSave() у этого
 * объекта на клиенте, который должен либо выдать сообщения об ошибках, либо сделать 
 * что-либо другое в случае успеха.
 * 
 * Если в поле заполнено поле $afterSaveMethod, то вызывается именно этот метод, определенный в 
 * не в классе, а в обработчике handlder, причем, чтобы не было конфликта имен, метод в обработчике
 * должен быть назван как {object_id}_{$afterSaveMethod). 
 * 
 * Второй тип функциональности позволяет не перезагружать страницу, а менять ее нужные части
 * с помощью JavaScript.
 * 
 * @author andrey
 */
class InputWebEntity extends WebEntity {
    
	function construct($params) {
			
        parent::construct($params);
        global $Objects;
	    $this->models[] = "WebEntity";    
        $this->persistedFields = $this->explodePersistedFields(array());
        
        $this->tabs_string.= ";submitForm|Форма ввода|".$this->skinPath."images/spacer.gif";
        $this->submitSuccessTemplate = "";
        $this->submitErrorTemplate = "";
        $this->submitEmailTemplate = "";
        $this->submitAfterSaveMethod = "afterSave";
        $this->submitEmailAddress = "";
        $this->submitEmailAddressFrom = "";
        $this->submitEmailSubject = "";
        $this->submitTitleField = "";
        $this->submitFormFields = "";
        $this->submitSendEmail = false;
        $this->errors = "";
        $this->template = "templates/cms/InputWebEntity.html";
        $this->sortFields = "title ASC";
        
        $this->clientClass = "InputWebEntity";
        $this->parentClientClasses = "WebEntity~Entity";    
        $this->childClass = "InputEntryWebEntity";    
        $this->submitForm = $Objects->get($this->childClass."_".$this->module_id."_".$this->siteId."_");
	}
    
    function getData() {
    	global $Objects;
    	
    	foreach ($_POST as $key=>$value) {
    		if (preg_match("/^".str_replace($this->module_id."_","",$this->getId())."/",$key)) {
    				$value = trim($value);
    				$arr[str_replace(str_replace($this->module_id."_","",$this->getId())."_","",$key)] = $value;
    		} else
    		if (preg_match("/^".$this->getId()."/",$key)) {
    				$value = trim($value);
    				$arr[str_replace($this->getId()."_","",$key)] = $value;
    		} else
    			$arr[$key] = trim($value);
    	}   
    	
    	$fields = explode("~",$this->submitFormFields);
    	
    	foreach ($fields as $value) {
    		if (isset($arr[$value])) {
    			$this->submitForm->fields[$value] = $arr[$value];    			
    			$this->fields[$value] = $arr[$value];
    		}
    	}
    	$this->fields["keystring"] = @$arr["keystring"];     	 
    }
        
    function submitForm() {     
    	                
        global $Objects;
        
        if ((!$this->loaded))
        	$this->load();
        
        if (ob_get_contents()=="") {
        	$this->getData();        	
        	ob_start();
        }
        if ($this->submitSuccessTemplate == "")
        	$this->submitSuccessTemplate = $this->userTemplate;
        if ($this->submitErrorTemplate == "")
        	$this->submitErrorTemplate = $this->userTemplate;
        if ($this->submitSendEmail) {
        	if ($this->submitEmailTemplate == "")
        		return 0;
        }        
        $submitForm = $this->submitForm;
        $submitForm->afterInit($this->getId());
        $submitForm->createdTime = date("d.m.Y H:i:s",time());
        $submitForm->siteId = $this->siteId;
        $submitForm->persistedFields = $submitForm->childPersistedFields;
        $submitForm->defaultPersistedFields = "";
        $submitForm->title = strtr($this->submitTitleField,$submitForm->changeKeyBrackets($submitForm->getArgs(),"(",")"));

        if (isset($_SESSION['captcha_keystring']) and $_SESSION['captcha_keystring'] != $this->keystring) {
            $this->reportError("Символы изображенные на картинке введены неверно!");
        } else
            $submitForm->save();        
        $this->errors = ob_get_contents();
        if (@$_POST["ajax"]==true or @$_POST["ajax"]=="true") {    
        	$this->errors = str_replace("<br>","\n",$this->errors);
        }
        echo $this->errors;
        ob_end_clean();
        if (trim($this->errors)!="" and !is_numeric($this->errors)) {
            $tpl = $this->submitErrorTemplate;
        } else {  
        	if (@$_POST["ajax"]!=true and @$_POST["ajax"]!="true") {      	
            	$this->errors = "<font color='red'><b>Ваше сообщение принято.</b></font><br/>";
            	$submitForm->errors = "<font color='red'><b>Ваше сообщение принято.</b></font><br/>";
        	}
            $tpl = $this->submitSuccessTemplate;
            if ($this->submitSendEmail) {
                $emailTpl = $Objects->get($this->submitEmailTemplate);                
                if (!$emailTpl->loaded)
                    $emailTpl->load();
                ob_start();
                $text = $submitForm->parseTemplate($emailTpl->template_file,$emailTpl->handler_file,$emailTpl->css_file,"",false);
                ob_end_clean();
                if (@$_POST["ajax"]) {
                	$text = (array)json_decode($text);
                	$text = $text["css"].$text["html"];
                }
                $headers = "From: ".$this->submitEmailAddressFrom."\n";
				
                $headers.= "MIME-Version: 1.0\n";
                $headers.= "Content-type: text/html; charset=utf-8\n";
                $to = $this->submitEmailAddress;
                $from = $this->submitEmailAddressFrom;
                $subject = strtr($this->submitEmailSubject,$submitForm->changeKeyBrackets($submitForm->getArgs(),"(",")"));
                if (is_array($text))
                	$text = implode("\n",$text);
                mail($to,$subject,$text,$headers);
            }
        }
        $this->userTemplate = $tpl;        
        if (@$_POST["ajax"]!=true and @$_POST["ajax"]!="true") {    
        	include_once("boot.php");include_once("scripts.php");
        }

        $arr = $submitForm->getPersistedArray();
        foreach ($arr as $key=>$value) {
            if (!isset($this->fields[$key]))
                    $this->fields[$key] = $submitForm->fields[$key];
        }       
        if ($_POST["ajax"]!=true and $_POST["ajax"]!="true")         
        	$this->show();
        else {       	
        	echo $this->errors;
        	echo "<script>if (window.parent.objects.objects['".$this->getId()."']!=null) window.parent.objects.objects['".$this->getId()."'].afterSave();</script>";
        }
    }

    function checkData() {
        if ($this->submitSuccessTemplate=="-1")
            $this->submitSuccessTemplate = "";
        if ($this->submitErrorTemplate=="-1")
            $this->submitErrorTemplate = "";
        if ($this->submitEmailTemplate=="-1")
            $this->submitEmailTemplate = "";        
        if ($this->class==$this->old_class) {
            if ($this->submitSuccessTemplate=="") {
                $this->reportError("Не указан шаблон при успешном заполнении");
                return 0;
            }
            if ($this->submitErrorTemplate=="") {
                $this->reportError("Не указан шаблон при ошибочном заполнении");
                return 0;            
            }
            if ($this->submitTitleField=="") {
                $this->reportError("Не указан заголовок для дочерних элементов");
                return 0;            
            }
            if ($this->submitFormFields=="") {
                $this->reportError("Не указаны поля дочернего элемента");
                return 0;            
            }
            if ($this->submitSendEmail) {
                if ($this->submitEmailAddress=="") {
                    $this->reportError("Не указан E-Mail-адрес получателя");
                    return 0;                            
                }
                if ($this->submitEmailAddressFrom=="") {
                    $this->reportError("Не указан E-Mail-адрес отправителя");
                    return 0;                            
                }
                if ($this->submitEmailSubject=="") {
                    $this->reportError("Не указана тема письма");
                    return 0;                            
                }
                if ($this->submitEmailTemplate=="") {
                    $this->reportError("Не указан шаблон письма");
                    return 0;                            
                }            
            }
        }
        return parent::checkData();
    }
        
    function getArgs() {
        if ($this->submitSendEmail==true)
            $this->submitEmailDisplay = "display:";
        else
            $this->submitEmailDisplay = "display:none";               
        $result = parent::getArgs();
        $result["{submitEmailDisplay}"] = $this->submitEmailDisplay;
        $result["{|errors}"] = $this->errors;
        return $result;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "submitForm";
    	}
    	return parent::getHookProc($number);
    }
    
    function load() {
        parent::load();
        global $Objects;
        $submitForm = $Objects->get($this->childClass."_".$this->module_id."_".$this->siteId."_");
        $submitForm->parent = $this;
        $submitForm->persistedFields = $this->childPersistedFields;
        $arr = $submitForm->getPersistedArray();
        foreach ($arr as $key=>$value) {
            if (!isset($this->fields[$key]))
                    $this->fields[$key] = "";
        }        
    }    
}
?>