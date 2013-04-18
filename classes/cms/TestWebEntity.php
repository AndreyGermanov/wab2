<?php
/**
 * Description of TestWebEntity
 *
 * @author andrey
 */
class TestWebEntity extends WebEntity {

    function construct($params) {
        
        parent::construct($params);
        $this->models[] = "InputWebEntity";

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
        
        $this->clientClass = "TestWebEntity";
        $this->parentClientClasses = "WebEntity~Entity";        
    }
        
    function submitForm() {     
        if ((!$this->loaded))
            $this->load();
        if ($this->submitSuccessTemplate == "")
            $this->submitSuccessTemplate = $this->userTemplate;
        if ($this->submitErrorTemplate == "")
            $this->submitErrorTemplate = $this->userTemplate;
        if ($this->submitSendEmail) {
            if ($this->submitEmailTemplate == "") 
                return 0;
        }
        global $Objects;
        foreach ($_POST as $key=>$value) {
            if (preg_match("/^".$this->getId()."/",$key)) {
                $arr[str_replace($this->getId()."_","",$key)] = $value;
            }
        }        
        $submitForm = $Objects->get($this->childClass."_".$this->module_id."_".$this->siteId."_");
        $submitForm->parent = $this;
        $submitForm->afterInit($this->getId());
        $fields = explode("~",$this->submitFormFields);
        foreach ($fields as $value) {
            if (isset($arr[$value])) {
                $submitForm->fields[$value] = $arr[$value];
                $this->fields[$value] = $arr[$value];
            }            
        }
        $submitForm->title = strtr($this->submitTitleField,$submitForm->changeKeyBrackets($submitForm->getArgs(),"(",")"));
        ob_start();
        if (isset($_SESSION['captcha_keystring']) and $_SESSION['captcha_keystring'] != @$arr["keystring"]) {
            $this->reportError("Символы изображенные на картинке введены неверно!");
        } else
            $submitForm->save();        
        $this->errors = ob_get_contents();
        ob_end_clean();
        if ($this->errors!="" and !is_numeric($this->errors)) {
            $tpl = $this->submitErrorTemplate;
        } else {
            $tpl = $this->submitSuccessTemplate;
            if ($this->submitSendEmail) {
                $emailTpl = $Objects->get($this->submitEmailTemplate);
                if (!$emailTpl->loaded)
                    $emailTpl->load();
                $text = $submitForm->parseTemplate($emailTpl->template_file,$emailTpl->handler_file,$emailTpl->css_file,"",false);
                $headers = "From: ".$this->submitEmailAddressFrom."\n";

                $headers.= "MIME-Version: 1.0\n";
                $headers.= "Content-type: text/html; charset=utf-8\n";

                $to = $this->submitEmailAddress;
                $from = $this->submitEmailAddressFrom;
                $subject = strtr($this->submitEmailSubject,$submitForm->changeKeyBrackets($submitForm->getArgs(),"(",")"));
                mail($to,$subject,$text,$headers);                
            }
        }
        $this->userTemplate = $tpl;        
        include_once("boot.php");include_once("scripts.php");
        $arr = $submitForm->getPersistedArray();
        foreach ($arr as $key=>$value) {
            if (!isset($this->fields[$key]))
                    $this->fields[$key] = $submitForm->fields[$key];
        }                
        $this->show();
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
        return $result;
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