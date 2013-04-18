<?php
/**
 * Класс предназначен для того чтобы просканировать сеть и вернуть список
 * всех компьютеров, которые в нее входят. Данные возвращаются в виде массива,
 * состоящего из строк с разделителями:
 *
 * Обнаруженный MAC-адрес, Обнаруженный IP-адрес, IP-адрес в базе, имя-хоста
 *
 * Первые два значения возвращает утилита nmap.
 *
 * Затем производится поиск в списке уже заведенных компьютеров подсети по
 * MAC-адресу и возвращается IP-адрес этого компьютера и имя хоста из базы.
 *
 * Если IP-адрес в базе и имя хоста пустые, значит был обнаружен новый компьютер,
 * который нужно добавить в базу.
 *
 * Если IP-адрес в базе и обнаруженный IP-адрес совпадают, значит был обнаружен
 * компьютер, который уже был заведен.
 *
 * Если IP-адрес в базе отличается от обнаруженного IP-адреса, то это говорит об
 * ошибке, это говорит о том, что компьютер с таким MAC-адресом не использует
 * IP-адрес, который находится в базе, а прописал себе свой собственный адрес или
 * получил его из другого источника. Нужно исправить ситуацию на этом компьютере
 * и пересканировать сеть.
 *
 * Если не обнаружено MAC-адреса, значит компьютер, который сканируется - сам
 * сервер, который сканирует.
 *
 * @author andrey
 */
class ScanNetworkTable extends WABEntity {

    public $scanResult;

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->subnet = $this->name;
        $this->width = "98%";
        $this->height = "";
        $this->networkScanned = false;
        
        $this->template = "templates/interface/Table.html";
        $this->handler = "scripts/handlers/controller/ScanNetworkTable.js";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->css = $this->skinPath."styles/Table.css";
        $this->hostnameCommand = $app->hostnameCommand;
        $this->clientClass = "ScanNetWorkTable";
        $this->parentClientClasses = "Entity";        
    }

    function getHostsTable() {
        $hosts_table = array();
        global $Objects;
        $subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$this->subnet);
        $subnet->loadHosts();
        $hosts = $subnet->hosts;
        foreach ($hosts as $host) {
            $hosts_table[strtoupper($host->hw_address)] = $host->fixed_address."|".$host->name;
        }
        return $hosts_table;
        
    }

    function scanNetwork() {
        // Определение NETBIOS-имени хоста
        // nmblookup -A 192.168.0.101 | grep "<ACTIVE>" | head -1 | fmt -us | sed -e "s/\t//g" | cut -d " " -f1
        
        global $Objects;
        $app = $Objects->get($this->module_id);
        $dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$dhcpServer->loaded)
            $dhcpServer->load();
        $shell = $Objects->get("Shell_helix");
        if ($app->remoteSSHCommand!="")
            $result = str_replace("\n","|",$shell->exec_command($app->remoteSSHCommand." '".$app->nmapCommand." ".$this->subnet."/".$this->mask."'"));
        else
            $result = str_replace("\n","|",$shell->exec_command($app->nmapCommand." ".$this->subnet."/".$this->mask));
        $result = preg_split("/Interesting ports /U",$result);
        $hosts_table = $this->getHostsTable();

        array_shift($result);
        $matches = array();
        $scan_ip = ""; $mac_address = ""; $ip_in_base = ""; $host_name = "";$host_in_base="";
        foreach ($result as $line) {
            $scan_ip = ""; $mac_address = ""; $ip_in_base = ""; $host_name = "";$host_in_base="";
            preg_match("/on (.*):|/U",$line,$matches);
            if (isset($matches[1])) {
                $scan_ip = $matches[1];
            } else
                continue;
            preg_match("/MAC Address: (.*) /U",$line,$matches);
            if (isset($matches[1])) {
                $mac_address = $matches[1];
            }
            else
                $host_name = exec($this->hostnameCommand);
            
            if (isset($hosts_table[$mac_address])) {
                $in_base_array = explode("|",$hosts_table[$mac_address]);
                $ip_in_base = $in_base_array[0];
                $host_in_base = $in_base_array[1];
            }
            $host_name = str_replace("\n","",$shell->exec_command(str_replace("{host}",$scan_ip,$dhcpServer->scanNetbiosNameCommand)));
            $this->scanResult[count($this->scanResult)] = $mac_address.",".$scan_ip.",".$ip_in_base.",".$host_name.",".$host_in_base;
            $scan_ip = ""; $mac_address = ""; $ip_in_base = ""; $host_name = "";
        }
        $this->networkScanned = true;
    }

    function getArgs() {
        $result = parent::getArgs();
        $subnet_mask_arr = explode(".",$this->mask);
        $res = "";
        foreach ($subnet_mask_arr as $sub) {
            $res .= base_convert($sub,10,2);
        }
        $res = str_replace("0","",$res);
        $this->mask = strlen($res);
        if (!$this->networkScanned)
            $this->scanNetwork();
        $result["{network_table}"] = implode("|",$this->scanResult);
        return $result;

    }

    function load() {
        parent::load();
    }

    function getId() {
        return get_class($this)."_".$this->module_id."_".$this->subnet;
    }
}
?>