<?php
$old_build = trim(@file_get_contents("/etc/WAB2/config/old_build"));
$build = trim(@file_get_contents("/etc/WAB2/config/build"));

$app = $Objects->get("Application");
if (!$app->initiated)
    $app->initModules();
$shell = $Objects->get("Shell_shell");
if ($app->apacheServerUser=="") {
    $app->apacheServerUser = "www-data";
}
$shell->exec_command($app->chownCommand." -R ".$app->apacheServerUser." /etc/WAB2/config");

// Обновление на версию 1.1.01
if ($old_build < 1) {   
    // Добавляем дополнительные опции в конфигурационные файлы
    $dir = "/etc/WAB2/config/";    
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!=".." and substr($file,-5)==".conf" and $file != "NetCenterReport.conf") {
                    if (is_file($dir.$file)) {
                        $config = new DOMDocument();
                        $config->load($dir.$file);
                        $root = $config->getElementsByTagName("Modules")->item(0);
                        $el = $config->createElement("ApacheRestartCommand");
                        $el->setAttribute("value","/etc/init.d/apache2 restart");
                        $root->appendChild($el);
                        $el = $config->createElement("ApacheServerUser");
                        $el->setAttribute("value","www-data");
                        $root->appendChild($el);
                        $el = $config->createElement("SshdConfigFile");
                        $el->setAttribute("value","/etc/ssh/sshd_config");
                        $root->appendChild($el);
                        $el = $config->createElement("SshdRestartCommand");
                        $el->setAttribute("value","/etc/init.d/ssh restart");
                        $root->appendChild($el);
                        $el = $config->createElement("GetActiveUnixUsersCommand");
                        $el->setAttribute("value","cat /etc/shadow | grep -v '\:\*\:' | grep -v '\:\!\:' | cut -d ':' -f1");
                        $root->appendChild($el);
                        $el = $config->createElement("FindCommand");
                        $el->setAttribute("value","find");
                        $root->appendChild($el);
                        $el = $config->createElement("GetUserHomeDirCommand");
                        $el->setAttribute("value","cat /etc/passwd | grep {user} | cut -d ':' -f6");
                        $root->appendChild($el);
                        $el = $config->createElement("SudoersFile");
                        $el->setAttribute("value","/etc/sudoers");
                        $root->appendChild($el);
                        $el = $config->createElement("ApacheEnvFile");
                        $el->setAttribute("value","/etc/apache2/envvars");
                        $root->appendChild($el);
                        $config->save($dir.$file);
                    }
                }
            }
            closedir($dh);
        }
    } 
    
    // Закрываем доступ к Web-серверу по протоколу HTTP
    $shell->exec_command($app->deleteCommand." /etc/apache2/sites-enabled/000-default");
    $fp = fopen("/etc/WAB2/config/apache_restart.sh","w");
    fwrite($fp,$app->apacheRestartCommand);
    fclose($fp);
    $shell->exec_command("at -f /etc/WAB2/config/apache_restart.sh now");
    
    // Закрываем пользователю www-data доступ к серверу по SSH
    $shell->exec_command('chown www-data /etc/ssh/sshd_config');
    file_put_contents('/etc/ssh/sshd_config',file_get_contents('/etc/ssh/sshd_config')."\nDenyUsers www-data");
    $shell->exec_command('chown root /etc/ssh/sshd_config');        
    $shell->exec_command("/etc/init.d/ssh restart");
        
    // Если это обновление на первый билд, значит раньше билдов не было,
    // поэтому инициализируем систему билдов
    $fp = fopen("/etc/WAB2/config/build","w");fwrite($fp,"1");fclose($fp);
    $fp = fopen("/etc/WAB2/config/old_build","w");fwrite($fp,"1");fclose($fp);
    $build=1;$old_build=1;
}

// Обновление на версию 1.1.02
if ($old_build == 1) {
    $dir = "/etc/WAB2/config/";
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!=".." and substr($file,-5)==".conf" and $file != "NetCenterReport.conf") {
                    if (is_file($dir.$file)) {
                        $config = new DOMDocument();
                        $config->load($dir.$file);
                        $root = $config->getElementsByTagName("Module");
                        foreach ($root as $module) {
                            if ($module->getAttribute("class")=="MailApplication_Mail") {
                                $el = $config->createElement("PostconfEditCommand");
                                $el->setAttribute("value","postconf -c {config} -e ``{param}``");
                                $module->appendChild($el);
                                $el = $config->createElement("PostconfShowCommand");
                                $el->setAttribute("value","postconf -c {config} -h ``{param}``");
                                $module->appendChild($el);
                                $el = $config->createElement("SaslPasswordFile");
                                $el->setAttribute("value","/etc/postfix/sasl_passwd");
                                $module->appendChild($el);
                                $el = $config->createElement("PostfixOutConfigFile");
                                $el->setAttribute("value","/etc/postfix.out/main.cf");
                                $module->appendChild($el);
                            }
                        }                        
                        $config->save($dir.$file);
                    }
                }
            }
            closedir($dh);
        }
    }     
    $fp = fopen("/etc/WAB2/config/old_build","w");fwrite($fp,"2");fclose($fp);
    $shell->exec_command($app->copyCommand." -arf /etc/WAB2/config/admin.conf /etc/WAB2/config/template.conf");
}

// После завершения операций обновления ставим признак того, что обновления прошли
if ($build!=$old_build) {
    $shell->exec_command($app->copyCommand." -arf /etc/WAB2/config/build /etc/WAB2/config/old_build");
}

?>
