<?php
/* 
 * Класс управляет контроллером. Содержит подразделы по управлению компьютерами
 * и поользователями.
 *
 * Функция init инициализирует основные параметры контроллера: 
 *
 * На данном этапе управляет DHCP-сервером. Читает параметр dhcpConfigFile из
 * конфигурационного файла пользователя. В нем находится конфигурация DHCP-сервера,
 * такие параметры как ldap_host, ldap_port, ldap_user, ldap_password и ldap_base
 * для увязки с LDAP-сервером, где лежит фактическая конфигурация DHCP.
 *
 * Также считывает параметр slapdConfigFile из конфигурационного файла пользователя.
 * В нем находится конфигурация LDAP-сервера.
 * 
 *
 */
class ControllerApplication extends WABEntity {

    public $config,$module,$defaultModule;
    
    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->template = "templates/controller/ControllerApplication.html";
        $this->handler = "scripts/handlers/controller/ControllerApplication.js";
        $this->object_id = @$params[0];
        $this->css=$app->skinPath."styles/MailApplication.css";
        $this->icon=$app->skinPath."images/Window/header-lva.gif";
        $this->tree_init_string = '$object->icon = "'.$app->skinPath.'images/Window/collectormx.jpg";$object->title="LVA Business Server";$object->setTreeItems();';
        $this->domain = "";
        
        $this->clientClass = "ControllerApplication";
        $this->parentClientClasses = "Entity";
        
        $this->init();
    }
    
    function checkConfigOptions() {
    	return 0;
        global $Objects;
        $app = $Objects->get("Application");
        $shell = $Objects->get("Shell_shell");
        if (!$app->initiated)
            $app->initModules();
        $config = new DOMDocument();
        $config->load($app->config->user->config_file);
        $items = $config->getElementsByTagName("Module");
        foreach ($items as $item) {
            if ($item->getAttribute("class")==$this->getId())
                $module_root = $item;
        }
        $changed=false;        
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaGetUserACLCommand")->item(0)) {
            $el = $config->createElement("SambaGetUserACLCommand");
            $el->setAttribute("value","/bin/bash /root/getuseracls.sh {user_or_group} {user} {shares_list}");
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
            $changed=true;
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaGetShareACLCommand")->item(0)) {
            $el = $config->createElement("SambaGetShareACLCommand");
            $el->setAttribute("value","getfacl -cp '{share}' | grep '{type}:' | grep -v 'default:' | grep -v '::'  tr ':' '~' | cut -d '~' -f2,3");
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
            $changed=true;
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAutoRestart")->item(0)) {
            $el = $config->createElement("SambaAutoRestart");
            $el->setAttribute("value","1");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("NfsRestartCommand")->item(0)) {
            $el = $config->createElement("NfsRestartCommand");
            $el->setAttribute("value","exportfs -ra");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("NfsConfigFile")->item(0)) {
            $el = $config->createElement("NfsConfigFile");
            $el->setAttribute("value","/etc/exports");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("NfsDefaultOptions")->item(0)) {
            $el = $config->createElement("NfsDefaultOptions");
            $el->setAttribute("value","sync,subtree_check");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("DhcpServer")->item(0)->getElementsByTagName("NetCenterAutoRestart")->item(0)) {
            $el = $config->createElement("NetCenterAutoRestart");
            $el->setAttribute("value","1");
            $changed=true;
            $config->getElementsByTagName("DhcpServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaSharesConfigPath")->item(0)) {
            $el = $config->createElement("SambaSharesConfigPath");
            $el->setAttribute("value","/etc/samba/shares");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaShareVFSTemplateFile")->item(0)) {
            $el = $config->createElement("SambaShareVFSTemplateFile");
            $el->setAttribute("value","templates/controller/smb_share_vfs.conf");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditLogFile")->item(0)) {
            $el = $config->createElement("SambaAuditLogFile");
            $el->setAttribute("value","/var/log/samba/log.audit");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->smbAuditLogFile = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditLogFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditPeriod")->item(0)) {
            $el = $config->createElement("SambaAuditPeriod");
            $el->setAttribute("value","30");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditDBHost")->item(0)) {
            $el = $config->createElement("SambaAuditDBHost");
            $el->setAttribute("value","localhost");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditDBPort")->item(0)) {
            $el = $config->createElement("SambaAuditDBPort");
            $el->setAttribute("value","3306");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditDBName")->item(0)) {
            $el = $config->createElement("SambaAuditDBName");
            $el->setAttribute("value","fullAudit");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditDBUser")->item(0)) {
            $el = $config->createElement("SambaAuditDBUser");
            $el->setAttribute("value","root");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaAuditDBPassword")->item(0)) {
            $el = $config->createElement("SambaAuditDBPassword");
            $el->setAttribute("value","root");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaDenyUnknownHosts")->item(0)) {
            $el = $config->createElement("SambaDenyUnknownHosts");
            $el->setAttribute("value","0");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaFirewallFile")->item(0)) {
            $el = $config->createElement("SambaFirewallFile");
            $el->setAttribute("value","/root/firewall.sh");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->smbFirewallFile = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaFirewallFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("RcLocalFile")->item(0)) {
            $el = $config->createElement("RcLocalFile");
            $el->setAttribute("value","/etc/rc.local");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->rcLocalFile = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("RcLocalFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("EnableShadowCopy")->item(0)) {
            $el = $config->createElement("EnableShadowCopy");
            $el->setAttribute("value","0");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotSize")->item(0)) {
            $el = $config->createElement("SnapshotSize");
            $el->setAttribute("value","10");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotsCount")->item(0)) {
            $el = $config->createElement("SnapshotsCount");
            $el->setAttribute("value","5");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyPeriodDays")->item(0)) {
            $el = $config->createElement("ShadowCopyPeriodDays");
            $el->setAttribute("value","*");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyPeriodHours")->item(0)) {
            $el = $config->createElement("ShadowCopyPeriodHours");
            $el->setAttribute("value","*/6");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyPeriodMinutes")->item(0)) {
            $el = $config->createElement("ShadowCopyPeriodMinutes");
            $el->setAttribute("value","1");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotsFolder")->item(0)) {
            $el = $config->createElement("SnapshotsFolder");
            $el->setAttribute("value","/data/snapshots");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->snapshotsFolder = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotsFolder")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyEnginePath")->item(0)) {
            $el = $config->createElement("ShadowCopyEnginePath");
            $el->setAttribute("value","/root/shadow_copy/");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->shadowCopyEnginePath = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyEnginePath")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("CrontabFile")->item(0)) {
            $el = $config->createElement("CrontabFile");
            $el->setAttribute("value","/etc/crontab");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->crontabFile = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("CrontabFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotLibTemplate")->item(0)) {
            $el = $config->createElement("SnapshotLibTemplate");
            $el->setAttribute("value","templates/controller/libsnapshot.pm");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->snapshotLibTemplate = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotLibTemplate")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotRotatorTemplate")->item(0)) {
            $el = $config->createElement("SnapshotRotatorTemplate");
            $el->setAttribute("value","templates/controller/snapshot_rotator.sh");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->snapshotRotatorTemplate = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotRotatorTemplate")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotPHPRotatorTemplate")->item(0)) {
            $el = $config->createElement("SnapshotPHPRotatorTemplate");
            $el->setAttribute("value","templates/controller/rotate_snapshots.php");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->snapshotPHPRotatorTemplate = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotPHPRotatorTemplate")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SnapshotCopyLinksTemplate")->item(0)) {
            $el = $config->createElement("SnapshotCopyLinksTemplate");
            $el->setAttribute("value","templates/controller/shadowcopy_make_links.php");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("GetAuditDataScript")->item(0)) {
            $el = $config->createElement("GetAuditDataScript");
            $el->setAttribute("value","/etc/WAB2/config/getauditdata.php");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->getAuditDataScript = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("GetAuditDataScript")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("GetAuditDataScriptTemplate")->item(0)) {
            $el = $config->createElement("GetAuditDataScriptTemplate");
            $el->setAttribute("value","templates/controller/getauditdata.php");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->getAuditDataScriptTemplate = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("GetAuditDataScriptTemplate")->item(0)->getAttribute("value");

        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("EraseTrashScript")->item(0)) {
            $el = $config->createElement("EraseTrashScript");
            $el->setAttribute("value","/etc/WAB2/config/erasetrash.php");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->eraseTrashScript = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("EraseTrashScript")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("EraseTrashScriptTemplate")->item(0)) {
            $el = $config->createElement("EraseTrashScriptTemplate");
            $el->setAttribute("value","templates/controller/erasetrash.php");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        $this->eraseTrashScriptTemplate = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("EraseTrashScriptTemplate")->item(0)->getAttribute("value");
        // Версия 1.1.05
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("LvCreateCommand")->item(0)) {
            $el = $config->createElement("LvCreateCommand");
            $el->setAttribute("value","/sbin/lvcreate");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("LvRemoveCommand")->item(0)) {
            $el = $config->createElement("LvRemoveCommand");
            $el->setAttribute("value","/sbin/lvremove");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("LvResizeCommand")->item(0)) {
            $el = $config->createElement("LvResizeCommand");
            $el->setAttribute("value","/sbin/lvresize");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("LvDisplayCommand")->item(0)) {
            $el = $config->createElement("LvDisplayCommand");
            $el->setAttribute("value","/sbin/lvdisplay");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("VgDisplayCommand")->item(0)) {
            $el = $config->createElement("VgDisplayCommand");
            $el->setAttribute("value","/sbin/vgdisplay");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyVgName")->item(0)) {
            $el = $config->createElement("ShadowCopyVgName");
            $el->setAttribute("value","vg0");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyLvName")->item(0)) {
            $el = $config->createElement("ShadowCopyLvName");
            $el->setAttribute("value","DATA");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyResizeSize")->item(0)) {
            $el = $config->createElement("ShadowCopyResizeSize");
            $el->setAttribute("value","3");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ShadowCopyResizeLimit")->item(0)) {
            $el = $config->createElement("ShadowCopyResizeLimit");
            $el->setAttribute("value","80");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("EnableAutoSnapshotsRotation")->item(0)) {
            $el = $config->createElement("EnableAutoSnapshotsRotation");
            $el->setAttribute("value","1");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("ExpiredSnapshotsBackupFolder")->item(0)) {
            $el = $config->createElement("ExpiredSnapshotsBackupFolder");
            $el->setAttribute("value","/data/share/trash/snapshots");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FTPServer")->item(0)) {
            $el = $config->createElement("FTPServer");
            $changed=true;
            $module_root->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPUserHomesMountsFile")->item(0)) {
            $el = $config->createElement("FTPUserHomesMountsFile");
            $el->setAttribute("value","/root/ftphomes.sh");            
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        $this->fTPHomesMountsFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPUserHomesMountsFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPFoldersMountsFile")->item(0)) {
            $el = $config->createElement("FTPFoldersMountsFile");
            $el->setAttribute("value","/root/ftpfolders.sh");            
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        $this->fTPFoldersMountsFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPFoldersMountsFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdConfigFile")->item(0)) {
            $el = $config->createElement("ProFTPdConfigFile");
            $el->setAttribute("value","/etc/proftpd/proftpd.conf");            
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        $this->proFTPdConfigFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdConfigFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdConfigPath")->item(0)) {
            $el = $config->createElement("ProFTPdConfigPath");
            $el->setAttribute("value","/etc/proftpd/");            
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        $this->proFTPdConfigPath = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdConfigPath")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdWAB2ConfigFile")->item(0)) {
            $el = $config->createElement("ProFTPdWAB2ConfigFile");
            $el->setAttribute("value","/etc/proftpd/wab2.conf");            
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        $this->proFTPdWAB2ConfigFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdWAB2ConfigFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdTemplateConfigFile")->item(0)) {
            $el = $config->createElement("ProFTPdTemplateConfigFile");
            $el->setAttribute("value","templates/controller/proftpd.conf");            
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("ProFTPdRestartCommand")->item(0)) {
            $el = $config->createElement("ProFTPdRestartCommand");
            $el->setAttribute("value","/etc/init.d/proftpd restart");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPUsersFile")->item(0)) {
            $el = $config->createElement("FTPUsersFile");
            $el->setAttribute("value","/etc/ftpusers");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }        
        $this->fTPUsersFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPUsersFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("BackupViewerAddress")->item(0)) {
            $el = $config->createElement("BackupViewerAddress");
            $el->setAttribute("value","http://localhost");
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaInvalidUsersFile")->item(0)) {
            $el = $config->createElement("SambaInvalidUsersFile");
            $el->setAttribute("value","/etc/samba/invalid.conf");            
            $changed=true;
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }        
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPWhoCommand")->item(0)) {
            $el = $config->createElement("FTPWhoCommand");
            $el->setAttribute("value","ftpwho -v -o oneline -S {hostname} | fmt -usw 1000 | grep '\[' | cut -d ' ' -f2-");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPVirtualHostsConfigFile")->item(0)) {
            $el = $config->createElement("FTPVirtualHostsConfigFile");
            $el->setAttribute("value","/etc/proftpd/virtuals.conf");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }
		$this->fTPVirtualHostsConfigFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPVirtualHostsConfigFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPShapingConfigFile")->item(0)) {
            $el = $config->createElement("FTPShapingConfigFile");
            $el->setAttribute("value","/root/ftpshaping.sh");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }
		$this->fTPShapingConfigFile = $config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPShapingConfigFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPVirtualHostsTemplateFile")->item(0)) {
            $el = $config->createElement("FTPVirtualHostsTemplateFile");
            $el->setAttribute("value","templates/ftp/ftphost.conf");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FTPShapingTemplateFile")->item(0)) {
            $el = $config->createElement("FTPShapingTemplateFile");
            $el->setAttribute("value","templates/ftp/ftpshaping.sh");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FTPServer")->item(0)->getElementsByTagName("FtpdctlCommand")->item(0)) {
            $el = $config->createElement("FtpdctlCommand");
            $el->setAttribute("value","ftpdctl -s /var/run/proftpd/proftpd.sock {command}");
            $changed=true;
            $config->getElementsByTagName("FTPServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("DavServer")->item(0)) {
            $el = $config->createElement("DavServer");
            $changed=true;
            $module_root->appendChild($el);
        }        
        if (!$config->getElementsByTagName("DavServer")->item(0)->getElementsByTagName("DavFoldersConfigFile")->item(0)) {
            $el = $config->createElement("DavFoldersConfigFile");
            $el->setAttribute("value","/etc/apache2/conf.d/davfolders.conf");
            $changed=true;
            $config->getElementsByTagName("DavServer")->item(0)->appendChild($el);
        }
		$this->davFoldersConfigFile = $config->getElementsByTagName("DavServer")->item(0)->getElementsByTagName("DavFoldersConfigFile")->item(0)->getAttribute("value");
        if (!$config->getElementsByTagName("DavServer")->item(0)->getElementsByTagName("DavFoldersTemplateFile")->item(0)) {
            $el = $config->createElement("DavFoldersTemplateFile");
            $el->setAttribute("value","templates/controller/davfolders.conf");
            $changed=true;
            $config->getElementsByTagName("DavServer")->item(0)->appendChild($el);
        }
        if (!$config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("SambaRenameGroupCommand")->item(0)) {
        	$el = $config->createElement("SambaRenameGroupCommand");
        	$el->setAttribute("value","/usr/bin/net rpc group rename {group} {new_group} -U {credentials}");
        	$changed=true;
        	$config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }
        
        if ($changed)
            $config->save($app->config->user->config_file);        
    }
    
    function checkConfigFiles() {
        global $Objects;
        return 0;
        $shell = $Objects->get("Shell_shell");    
        $app = $Objects->get("Application");
        if (!file_exists($this->remotePath."/etc/WAB2/config/getuseracls.sh")) {
            $fp = fopen($this->remotePath."/etc/WAB2/config/getuseracls.sh","w");
            fwrite($fp,"#!/bin/bash\n");
            fwrite($fp,'user_or_group=$1');fwrite($fp,"\n");
            fwrite($fp,'user=$2');fwrite($fp,"\n");
            fwrite($fp,'shares=$3');fwrite($fp,"\n");
            fwrite($fp,'IFS="~"');fwrite($fp,"\n");
            fwrite($fp,'for i in $shares; do');fwrite($fp,"\n");
            fwrite($fp,'echo $i~$(getfacl -cp $i | grep $user_or_group | grep :$user: | grep -v default | cut -d ":" -f2,3 | cut -d ":" -f2 2>/dev/null)');fwrite($fp,"\n");
            fwrite($fp,"done");
            fclose($fp);
        }
        if (!file_exists($this->remotePath."/etc/exports")) {
            $fp = fopen($this->remotePath."/etc/exports","w");
            fwrite($fp,"");
            fclose($fp);
		}
        if (!file_exists($this->remotePath.$this->smbFirewallFile)) {
            $fp = fopen($this->remotePath.$this->smbFirewallFile,"w");
            fwrite($fp,"");
            fclose($fp);
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." ".$this->smbFirewallFile);
            $shell->exec_command($this->remoteSSHCommand." chmod a+x ".$this->smbFirewallFile);            
            if ($this->remoteSSHCommand=="")
                $shell->exec_command($this->remotSSHCommand." chown ".$app->apacheServerUser." ".$this->rcLocalFile);
            $str = str_replace("exit 0","",file_get_contents($this->remotePath.$this->rcLocalFile));
            $str .= "\n".$this->smbFirewallFile;
            file_put_contents($this->remotePath.$this->rcLocalFile,$str."\nexit 0");
            $shell->exec_command($this->remoteSSHCommand." ".$this->smbFirewallFile);
		}
        if (!file_exists($this->remotePath."/".$this->smbAuditLogFile)) {
            $app = $Objects->get("Application");
            if (!$app->initiated)
                $app->initModules();
            if ($this->remoteSSHCommand=="")
                $shell->exec_command("chown ".$app->apacheServerUser." /etc/rsyslog.conf");
            $strings = file($this->remotePath."/etc/rsyslog.conf");
            $found = false;            
            foreach($strings as $line) {
                if (trim($line)=="local5.notice -".$this->smbAuditLogFile) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $strings[] = "local5.notice -".$this->smbAuditLogFile."\n";
                $fp = fopen($this->remotePath."/etc/rsyslog.conf","w");
                foreach ($strings as $line)
                    fwrite($fp,$line);
                fclose($fp);
                $shell->exec_command($this->remoteSSHCommand." /etc/init.d/rsyslog restart");
            }
            if ($this->remoteSSHCommand=="")
                $shell->exec_command("chown ".$app->apacheServerUser." ".$this->smbAuditLogFile);
            $fp = fopen($this->remotePath."/".$this->smbAuditLogFile,"w");
            fwrite($fp,"");
            fclose($fp);
		}        
        if (!file_exists($this->remotePath."/".$this->snapshotsFolder)) {
            $shell->exec_command($this->remoteSSHCommand." mkdir -p ".$this->snapshotsFolder);
        }
        if ($this->remoteSSHCommand=="") {
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." ".$this->snapshotsFolder);            
            $shell->exec_command($this->remoteSSHCommand." chmod a+x ".$this->snapshotsFolder);
        }
        if (!file_exists($this->shadowCopyEnginePath)) {
            $shell->exec_command($this->remoteSSHCommand." mkdir -p ".$this->shadowCopyEnginePath);
        }
        if ($this->remoteSSHCommand=="") {
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->shadowCopyEnginePath);
        }
        $shell->exec_command($this->remoteSSHCommand." chmod -R a+x ".$this->shadowCopyEnginePath);
        if (!file_exists($this->getAuditDataScript)) {
            file_put_contents($this->getAuditDataScript,file_get_contents($this->getAuditDataScriptTemplate));            
            $shell->exec_command("chown -R ".$app->apacheServerUser." ".$this->getAuditDataScript);
            $shell->exec_command("chown -R ".$app->apacheServerUser." ".$this->crontabFile);
            $shell->exec_command("chmod -R a+x ".$this->getAuditDataScript);
            file_put_contents($this->crontabFile,file_get_contents($this->crontabFile)."\n*/2 * * * * root ".$this->getAuditDataScript); 
            $shell->exec_command("chown -R root ".$this->crontabFile);
            $shell->exec_command("/etc/init.d/cron restart");
        }
        if (!file_exists($this->eraseTrashScript)) {
            file_put_contents($this->eraseTrashScript,file_get_contents($this->eraseTrashScriptTemplate));            
            $shell->exec_command("chown -R ".$app->apacheServerUser." ".$this->eraseTrashScript);
            $shell->exec_command("chmod -R a+x ".$this->eraseTrashScript);
            $shell->exec_command("chown -R ".$app->apacheServerUser." ".$this->crontabFile);
            file_put_contents($this->crontabFile,file_get_contents($this->crontabFile)."\n59 23 * * * root ".$this->eraseTrashScript."\n");            
            $shell->exec_command("chown -R root ".$this->crontabFile);
            $shell->exec_command("/etc/init.d/cron restart");
        }
        if (!file_exists($this->remotePath.$this->fTPUserHomesMountsFile)) {
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." /root");
            $fp=fopen($this->remotePath.$this->fTPUserHomesMountsFile,"w");fwrite($fp,"");fclose($fp);            
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->fTPUserHomesMountsFile);
            $shell->exec_command($this->remoteSSHCommand." chmod -R a+x ".$this->fTPUserHomesMountsFile);
            file_put_contents($this->remotePath.$this->rcLocalFile,file_get_contents($this->rcLocalFile)."\n".$this->fTPUserHomesMountsFile."\n");            
            $shell->exec_command($this->remoteSSHCommand." chown root /root");
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$this->apacheServerUser." ".$this->fTPUserHomesMountsFile);
        }
        if (!file_exists($this->remotePath.$this->fTPFoldersMountsFile)) {
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." /root");
            $fp= fopen($this->remotePath.$this->fTPFoldersMountsFile,"w");fwrite($fp,"");fclose($fp);            
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->fTPFoldersMountsFile);
            $shell->exec_command($this->remoteSSHCommand." chmod -R a+x ".$this->fTPFoldersMountsFile);
            file_put_contents($this->remotePath.$this->rcLocalFile,file_get_contents($this->rcLocalFile)."\n".$this->fTPFoldersMountsFile."\n");
            $shell->exec_command($this->remoteSSHCommand." chown root /root");
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$this->apacheServerUser." ".$this->fTPFoldersMountsFile);
        }
        if (!file_exists($this->remotePath.$this->proFTPdWAB2ConfigFile)) {
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->proFTPdConfigPath);
            file_put_contents($this->remotePath.$this->proFTPdWAB2ConfigFile,file_get_contents($this->proFTPdTemplateConfigFile));
            file_put_contents($this->remotePath.$this->proFTPdConfigFile,file_get_contents($this->remotePath.$this->proFTPdConfigFile)."\n".$this->proFTPdWAB2ConfigFile);
            $shell->exec_command($this->remoteSSHCommand." /etc/init.d/proftpd restart");
        }
        if (!file_exists($this->remotePath.$this->fTPUsersFile)) {
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." /etc/");
            $fp = fopen($this->remotePath.$this->ftpUsersFile,"w");fwrite($fp,"");fclose($fp);
            $shell->exec_command($this->remoteSSHCommand." chown root /etc/");
        }
        $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." ".$this->fTPUsersFile);
        if (!file_exists($this->remotePath.$this->fTPVirtualHostsConfigFile)) {
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." /etc/proftpd/");
            $fp = fopen($this->remotePath.$this->fTPVirtualHostsConfigFile,"w");fwrite($fp,"");fclose($fp);
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$this->apacheServerUser." ".$this->fTPVirtualHostsConfigFile);
        }
        $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." ".$this->fTPVirtualHostsConfigFile);
        if (!file_exists($this->remotePath.$this->fTPShapingConfigFile)) {
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." /root");
            $fp= fopen($this->remotePath.$this->fTPShapingConfigFile,"w");fwrite($fp,"");fclose($fp);
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->fTPShapingConfigFile);
            $shell->exec_command($this->remoteSSHCommand." chmod -R a+x ".$this->fTPShapingConfigFile);
            file_put_contents($this->remotePath.$this->rcLocalFile,file_get_contents($this->rcLocalFile)."\n".$this->fTPShapingConfigFile."\n");
            $shell->exec_command($this->remoteSSHCommand." chown root /root");
            $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->fTPShapingConfigFile);
        }
        $shell->exec_command($this->remoteSSHCommand." chown -R ".$app->apacheServerUser." ".$this->fTPShapingConfigFile);
        if (!file_exists($this->remotePath.$this->davFoldersConfigFile)) {
			$path = explode("/",$this->davFoldersConfigFile);
			array_pop($path);$path = implode("/",$path);
            $shell->exec_command($this->remoteSSHCommand." chown ".$app->apacheServerUser." ".$path);
            $fp = fopen($this->remotePath.$this->davFoldersConfigFile,"w");fwrite($fp,"");fclose($fp);
        }
        //$shell->exec_command("ln -s ".$this->app->remotePath."/"." root 2>/dev/null");
    }

    function init() {
        global $Objects;

        $this->ldap_host = "localhost";
        $this->ldap_port = 389;
        $this->ldap_user = "";
        $this->ldap_password = "";
        $this->ldap_base = "";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
		if ($app->User!="")
        	$this->config = $Objects->get("AdminConfig_".$app->User);
		else
        	$this->config = $Objects->get("AdminConfig_".@$_SERVER["PHP_AUTH_USER"]);
        
        $this->checkConfigOptions();
        
        $this->module = $app->getModuleByClass($this->getId());
        $this->hostnameFile = $app->hostnameFile;
        $this->hostnameCommand = $app->hostnameCommand;
        $this->moduleName = $this->module["name"];
        
        foreach ($this->module as $key=>$value)
        	$this->fields[$key] = $value;
        
        $this->hostsFile = $app->hostsFile;
        $this->rcLocalFile = $app->rcLocalFile;
        $this->crontabFile = $app->crontabFile;
        $this->nmapCommand = $app->nmapCommand;
        
        $shell = $Objects->get("Shell_shell");            
        if ($this->remoteAddress!="")
            if ($shell->exec_command(strtr($app->pingPortTestCommand,array("{address}" => $this->remoteAddress,"{port}" => 22)))==0) {
            $this->remotePath = $app->variablesPath."mounts/".$this->module->getAttribute("name")."/";
            $this->remoteSSHCommand = "ssh root@".$this->remoteAddress;
            if (!file_exists($this->remotePath)) {
                $shell->exec_command($app->makeDirCommand." -p ".$this->remotePath);
                $shell->exec_command($app->chownCommand." ".$app->apacheServerUser." ".$this->remotePath);     
            }
            if (!file_exists($this->remotePath."/etc/hostname")) {
                $shell->exec_command("fusermount -uz ".$this->remotePath);            
                $shell->exec_command($app->sshFsCommand." root@".$this->remoteAddress.":/ ".$this->remotePath." -o uid=33,gid=33,allow_other,follow_symlinks,nonempty");            
            }
        }        
        $this->gatewayIp = $this->gatewayIntegration;
        if ($this->gatewayIntegration!="")
            if ($shell->exec_command(strtr($app->pingPortTestCommand,array("{address}" => $this->gatewayIntegration,"{port}" => 22)))==0) {
            $this->gatewaySSHCommand = "ssh root@".$this->gatewayIntegration;
            $this->gatewayPath = $app->variablesPath."mounts/Bastion/";
            $shell = $Objects->get("Shell_shell");            
            $shell->exec_command("echo \"fusermount -uz ".$this->gatewayPath."\" | at now");
            if (!file_exists($this->gatewayPath)) {
                $shell->exec_command($app->makeDirCommand." -p ".$this->gatewayPath);
                $shell->exec_command($app->chownCommand." ".$app->apacheServerUser." ".$this->gatewayPath);     
            }
            $shell->exec_command($app->sshFsCommand." root@".$this->gatewayIntegration.":/ ".$this->gatewayPath." -o uid=33,gid=33,allow_other,follow_symlinks,nonempty");            
            if (file_exists($this->gatewayPath."etc/lbs_inactive")) {
                $shell->exec_command($app->gatewaySSHCommand." 'mv ".$this->gatewayIntegrationPath."_inactive /etc/lbs'");
                $shell->exec_command($app->gatewaySSHCommand." '".$app->debianNetworkRestartCommand."'");
            }
            if (!file_exists($app->gatewayIntegration."/etc/hostname"))
                $this->reportError("Сервер с адресом ".$this->gatewayIp." не обнаружен !","");            
        }       
        if ($this->docFlowIntegration) {
        	global $modules;
        	$this->docFlowApp = $Objects->get($modules[$this->docFlowIntegration]["class"]);
        	if (!$this->docFlowApp->loaded)
        		$this->docFlowApp->load();
        }
        $this->checkConfigFiles();
        $this->loaded = false;
    }

    function __get($name) {
        global $Objects;
        switch ($name) {
            default:
                if (isset($this->fields[$name]))
                    return $this->fields[$name];
                else
                    return "";
        }
    }

    function getId() {
        return "ControllerApplication_".$this->object_id;
    }

    function load() {
        $this->loaded = true;
    }            
}
?>