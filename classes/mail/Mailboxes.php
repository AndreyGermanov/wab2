<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Класс управляет списком почтовых ящиков (объектов Mailbox). Позволяет интеллектуально добавлять почтовые ящики
 * и удалять их, проверять наличие почтового ящика в базе.
 *
 * Для этого предназначены соответственно функции
 *
 * add
 * remove
 * contains
 *
 * Можно получить существующий почтовый ящик с помощью метода get.
 *
 * Также позволяет загружать недостающие почтовые ящики из файлов Application->postfixMailboxTable,
 * Application->dovecotUsersTable и Application->postfixGenericTable.
 *
 * @see Mailbox
 * 
 * Для этого предназначены методы 
 * 
 * load
 * save
 *
 * Класс не содержит собственной коллекции. Вместо этого он использует коллекцию Objects->query("Mailbox");
 * Для этого в классе содержится динамическое свойство mailboxes, дающее доступ к коллекции почтовых ящиков.
 * Также есть свойство count, показывающее количество загруженных в данный момент почтовых ящиков.
 *
 * @author Андрей Германов
 */
class Mailboxes extends WABEntity{

/**
 * Создает объект коллекции почтовых ящиков. Напрямую не вызывается. Вызывается с помощью Objects->getObject.
 *
 * @param <массив> $params  Массив параметров для создания объекта
 */
function construct($params) {
    $this->module_id = $params[0]."_".$params[1];
    $this->loaded = false;
    $this->clientClass = "Mailboxes";
	$this->parentClientClasses = "Entity";
}

/**
 *
 * Обработчик получения свойств объекта.
 *
 * @param <строка> $name Имя свойства
 */
function __get($name)
{
    global $Objects;
    switch ($name)
    {
        case "mailboxes":
            return $Objects->query("Mailbox");
            break;
        case "count":
            return count($Objects->query("Mailbox"));
            break;
        case "countLoaded":
            return count($Objects->query("Mailbox",array("loaded"=>true)));
            break;
        default:
            if (isset($this->fields[$name]))
                    return $this->fields[$name];
            else
                return "";
    }
}

    /**
     *
     * Загружает список почтовых ящиков из файлов. Причем подгружает только еще
     * не загруженные почтовые ящики. Почтовые ящики, которые были загружены ранее
     * и их свойства были изменены не трогает. Однако если были созданы новые почтовые
     * ящики с таким же названием, как существующие в базе, то информация о них загружается
     *
     */
    function load()
    {
        global $Objects;
        /**
         * Получаем доступ к объекту приложения, чтобы получить информацию о файлах,
         * из которых загружать данные
         */
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
            $app->load();

        // Загружаем существующие ящики из таблицы почтовых ящиков Postfix
        $strings = file($app->remotePath.$app->postfixMailboxTable);
        foreach($strings as $string)
        {
            if (trim($string)=="" or trim($string)=="\n")
                continue;
            $arr = explode(" ",$string);
            $arr = explode("@",$arr[0]);           
            $mbox = $Objects->get("Mailbox_".$this->module_id."_".$arr[0]."_".$arr[1]);
        }
        if (isset($mbox) and !$mbox->loaded)
            $mbox->spambox = $app->spambox;

        // Загружаем информацию об обратном адресе в Интернете из таблицы Generic
        // из Postfix
        $strings = file($app->remotePath.$app->postfixGenericTable);
        foreach($strings as $string)
        {
            if (trim($string)=="" or trim($string)=="\n")
                continue;
            $arr = explode(" ",$string);
            $arr1 = explode("@",$arr[0]);
            if ($Objects->contains("Mailbox_".$this->module_id."_".$arr1[0]."_".$arr1[1]))
            {
                $mbox = $Objects->get("Mailbox_".$this->module_id."_".$arr1[0]."_".$arr1[1]);
                if (!$mbox->loaded)
                {
                    $mbox->returnAddress = trim($arr[1]);
                }
            }
        }

        // Загружаем информацию о пользователе и пароле из таблицы пользователей
        // Dovecot
        $strings = file($app->remotePath.$app->dovecotUsersTable);
        foreach($strings as $string)
        {
            if (trim($string)=="" or trim($string)=="\n")
                continue;
            $arr = explode(":",$string);
            $arr1 = explode("@",$arr[0]);
            if ($Objects->contains("Mailbox_".$this->module_id."_".$arr1[0]."_".$arr1[1]))
            {
                $mbox = $Objects->get("Mailbox_".$this->module_id."_".$arr1[0]."_".$arr1[1]);
                if (!$mbox->loaded)
                {
                    $mbox->password = $arr[1];
                    $mbox->loaded = true;
                    $mbox->loaded_name = $arr1[0]."@".$arr1[1];
                }
            }
        }

        $strings = file($app->remotePath.$app->MailScannerRulesTable);
        for ($counter=0;$counter<count($strings);$counter++) {
            $string = explode(" ",$strings[$counter]);
            if (trim($string[0])=="To:") {
                $arr = explode("@",$string[1]);
                if ($Objects->contains("Mailbox_".$this->module_id."_".$arr[0]."_".$arr[1])) {
                    $mbox = $Objects->get("Mailbox_".$this->module_id."_".$arr[0]."_".$arr[1]);
                    if (!$mbox->loaded)
                        if (trim($strings[3])!=$mbox->name."@".$mbox->domain)
                            $mbox->spambox = trim($string[3]);
                        else
                            $mbox->spambox = "";
                }
            }
        }

        // Устанавливаем признак того, что объект загружен
        $this->loaded = true;
        
        // Очищаем временные массивы
        unset($strings);
        unset($arr);
        unset($arr1);
    }

    /**
     *
     * Функция сохраняет все загруженные почтовые ящики в файлы postfixMailboxTable,
     * postfixGenericTable и dovecotUsersTable
     */
    function save()
    {
        global $Objects;
        /**
         * Получаем доступ к объекту приложения, чтобы получить информацию о файлах,
         * из которых загружать данные
         */
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
                $app->load();

        // Открываем файлы
        $fp1 = fopen($app->remotePath.$app->postfixMailboxTable);
        $fp2 = fopen($app->remotePath.$app->dovectoUsersTable);
        $fp3 = fopen($app->remotePath.$app->postfixGenericTale);
        $fp4 = fopen($app->remotePath.$app->MailScannerRulesTable);
        // Идем по списку, записывая информацию о каждом ящике
        foreach($this->mailboxes as $mbox)
        {
            // Записываем объект в глобальный кэш объектов. Если мы изменили имя объекта или домен,
            // то предварительно удаляем из кэша запись с прошлым именем или доменом.
            if ($mbox->loaded_name!=$mbox->name."@".$mbox->domain)
            {
                // Если ящик с таким именем уже загружен, то не сохраняем информацию о нем
                if ($this->contains($mbox->name,$mbox->domain))
                        continue;
                $arr = explode("@",$mbox->loaded_name);
                $Objects->remove("Mailbox_".$this->module_id."_".$arr[0]."_".$arr[1]);
                // Перемещаем почту пользователя в новый каталог, соответствующий его новому имени
                shell_exec($app->remoteSSHCommand.' mkdir -p '.$app->mailBasePath.$mbox->domain);
                shell_exec($app->remoteSSHCommand.' mv '.$app->mailBasePath.$arr[1]."/".$mbox->loaded_name." ".$app->mailBasePath.$this->domain."/".$mbox->name."@".$mbox->domain);
                // Если после этого каталог домена оказался пустым, удаляем его
                shell_exec($app->remoteSSHCommand.'rmdir '.$app->mailBasePath.$arr[1]);
                // Записываем объект в кэш
                $Objects->set("Mailbox_".$this->module_id."_".$mbox->name."_".$mbox->domain,$mbox);
            }

            // Если нет каталога, в котором хранится почта пользователя, устанавливаем его
            if (!file_exists($app->remotePath.$app->mailBasePath.$mbox->domain."/".$mbox->name."@".$mbox->domain))
                    shell_exec($app->remoteSSHCommand.' mkdir -p '.$app->mailBasePath.$mbox->domain."/".$mbox->name."@".$mbox->domain);

            // Записываем информацию о ящике в таблицу Postfix
            fwrite($fp1,$mbox->name."@".$mbox->domain." ".$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain."/\n");
            // Записываем информацию о ящике в таблицу Dovecot
            fwrite($fp2,$mbox->name."@".$mbox->domain.":".$mbox->password.":".$app->mailUID.":".$app->mailGID."::".$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain."/\n");
            // Если есть обратный адрес, записываем информацию о нем в таблицу Generic
            if ($mbox->returnAddress!="")
                fwrite($fp3,$mbox->name."@".$mbox->domain." ".$mbox->returnAddress);

            if ($mbox->spambox!=$app->spambox) {
                if ($mbox->spambox == "")
                    $spam_box = $mbox->name."@".$mbox.domain;
                else
                    $spam_box = $this->spambox;
                fwrite($fp4,'To: '.$mbox->name.'@'.$mbox->domain.' forward '.$spam_box.' header "X-Spam-Status: yes"'."\n");
            }

            // Устанавливаем признак того, что объект был загружен, т.е. признак того что объект соответствует
            // тому, что записано о нем в файлах. Также устанавливаем что текущее имя соответствует загруженному,
            // так как только что оно было записано
            $mbox->loaded = true;
            $mbox->loaded_name = $mbox->name."@".$mbox->domain;
        }

        if ($app->spambox!="")
                fwrite($fp4,'ToOrFrom: default forward '.$app->spambox.' header "X-Spam-Status: yes"'."\n");
        
        // Закрываем все открытые файлы
        fclose($fp1);fclose($fp2);fclose($fp3);fclose($fp4);

        // Устанавливаем признак того, что все было загружено
        $this->loaded = true;
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixMailboxTable);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postMailboxTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postGenericTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
    }

    /**
     *
     * Функция показывает, существует ли указанный почтовый ящик среди загруженных
     *
     * @global <массив> $Objects глобальный массив объектов
     * @param <строка> $name имя ящика
     * @param <type> $domain домен ящика
     * @return <булево>
     *
     */
    function contains($name,$domain)
    {
        if (!$this->loaded)
                $this->load();
       global $Objects;
       
       if ($Objects->contains("Mailbox_".$this->module_id."_".$name."_".$domain))
               return true;
       else
           return false;
    }

    /**
     *
     * Функция добавляет новый почтовый ящик к списку загруженных.
     * Рекомендуется создавать новые почтовые ящики именно с помощью этого метода
     *
     * @param <строка> $name - имя ящика
     * @param <строка> $domain - домен ящика
     *
     */
    function add($name,$domain)
    {
        global $Objects;
        if ($this->contains($name,$domain))
        {
                echo "<error>".$name."@".$domain.". Указанный почтовый ящик уже существует</error>";
                return 0;
        }
        else
            return $Objects->get("Mailbox_".$this->module_id."_".$name."_".$domain);
    }

    /**
     *
     * Функция возвращает почтовый ящик по имени, при условии что он существует
     *
     * @param <строка> $name имя ящика
     * @param <type> $domain домен ящика
     */
    function get($name,$domain)
    {
        global $Objects;
        if (!$this->contains($name,$domain))
                return 0;
        else
            return $Objects->get("Mailbox_".$this->module_id."_".$name."_".$domain);
    }

    /**
     *
     * Функция удаляет указанный почтовый ящик из массива объектов, из файлов
     * postfixMailboxTable, postfixGenericTable и dovecotUsersTable, также удаляется
     * каталог с почтой
     *
     * @global массив $Objects массив загруженных объектов
     * @param <строка> $name имя почтового ящика
     * @param <строка> $domain домен почтового ящика
     * @return <type> ничего
     */
    function remove($name,$domain)
    {
        global $Objects;

        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILBOX_DELETED","object_id=Mailbox_".$this->module_id."_".$name."_".$domain);
        
        // Получаем доступ к объекту приложения
        $app = $Objects->get("MailApplication_".$this->module_id);

        // если почтового ящика нет, то выходим
        if (!$this->contains($name,$domain))
                return 0;

        // Удаляем ящик из массива объектов
        $Objects->remove("Mailbox_".$this->module_id."_".$name."_".$domain);

        // Удаляем каталог с почтой
        shell_exec($app->remoteSSHCommand.' rm -rf '.$app->mailBasePath.$domain."/".$name."@".$domain);
        shell_exec($app->remoteSSHCommand.' rmdir '.$app->mailBasePath.$domain);
        // Удаляем информацию о ящике из файлов
        $files[$app->postfixMailboxTable] = file($app->remotePath.$app->postfixMailboxTable);
        $files[$app->dovecotUsersTable] = file($app->remotePath.$app->dovecotUsersTable);
        $files[$app->postfixGenericTable] = file($app->remotePath.$app->postfixGenericTable);

        foreach ($files as $key=>$value)
        {
            $fp = fopen($app->remotePath.$key,"w");
            foreach ($value as $line)
            {
                if ($key!=$app->dovecotUsersTable)
                    $arr = explode(" ",$line);
                else
                    $arr = explode(":",$line);
                if ($arr[0]!=$name."@".$domain)
                    fwrite($fp,trim(str_replace("\n","",$line))."\n");

            }
            fclose($fp);
        }
        
        $strings = file($app->remotePath.$app->MailScannerRulesTable);
        $fp = fopen($app->remotePath.$app->MailScannerRulesTable,"w");
        foreach($strings as $line) {
            $arr = explode(" ",$line);
            if (trim($arr[1])!=$name."@".$domain)
                fwrite($fp,trim(str_replace("\n","",$line))."\n");
        }
        fclose($fp);

        $strings = file($app->remotePath.$app->autoreplyAliasesFile);
        $fp = fopen($app->remotePath.$app->autoreplyAliasesFile,"w");
        foreach($strings as $line) {
            $arr = explode(" ",$line);
            if (trim($arr[0])!=$name."@".$domain)
                fwrite($fp,trim(str_replace("\n","",$line))."\n");
        }
        fclose($fp);
        
        // Удаляем все подчиненные почтовые ящики Интернет
        $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
        $owned_mailboxes=$remote_mailboxes->getByOwner($name."@".$domain);
        foreach($owned_mailboxes as $value)
        {
            $value->load();
            $remote_mailboxes->remove($value->name);
        }
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." rm ".$app->autoreplyTextsPath.$name."@".$domain);
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixMailboxTable);
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixGenericTable);
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->autoreplyAliasesFile);
        $shell->exec_command($app->remoteSSHCommand." /etc/init.d/mailscanner reload 2>/dev/null >/dev/null");
        $shell->exec_command($app->remoteSSHCommand." /etc/init.d/postfix restart");
    }

    function getHookProc($number) {
    	switch($number) {
    		case '3': return "removeHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function removeHook($arguments) {
    	$this->load();
    	$this->remove($arguments["name"],$arguments["domain"]);
    }    
}
?>