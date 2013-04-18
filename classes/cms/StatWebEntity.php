<?php
/**
 * Класс для сбора и отображения статистики посещений сайта
 *
 * @author andrey
 */
class StatWebEntity extends WebEntity {

    function construct($params) {
        parent::construct($params);

        // Период хранения статистики
        $this->statPeriod = 30;

        // Создаем таблицу статистики, если таковой еще нет
        if (!$this->adapter->connected)
                $this->adapter->connect();
        if ($this->adapter->connected) {
            @$this->adapter->dbh->exec("CREATE TABLE stats (entityId BIGINT NOT NULL,clientIP INTEGER NOT NULL, referrer VARCHAR(255),requestURI VARCHAR(255), userAgent VARCHAR(255),requestTime BIGINT)");
            @$this->adapter->dbh->exec("CREATE INDEX entityId ON stats (entityId)");
            @$this->adapter->dbh->exec("CREATE INDEX clientIP ON stats (clientIP)");
            @$this->adapter->dbh->exec("CREATE INDEX referred ON stats (referrer)");
            @$this->adapter->dbh->exec("CREATE INDEX requestURI ON stats (requestURI)");
            @$this->adapter->dbh->exec("CREATE INDEX userAgent ON stats (userAgent)");
            @$this->adapter->dbh->exec("CREATE INDEX requestTime ON stats (requestTime)");
        }
        $this->clientClass = "StatWebEntity";
        $this->parentClientClasses = "WebEntity~Entity";        
    }

    function getArgs() {
        if (!$this->asAdminTemplate) {
            if (!$this->adapter->connected)
                $this->adapter->connect();

            // Перед отображением счетчика пишем в базу статистическую информацию о подключении
            if ($this->adapter->connected) {
                $stat = $this->adapter->dbh->prepare("INSERT INTO stats (entityId,clientIP,referrer,requestURI,userAgent,requestTime) VALUES(:entityId,:clientIP,:referrer,:requestURI,:userAgent,:requestTime)");
                $entityId = $this->name;
                $stat->bindParam(":entityId",$entityId);
                $stat->bindParam(":clientIP",ip2int($_SERVER["REMOTE_ADDR"]));
                $stat->bindParam(":referrer",$_SERVER["HTTP_REFERRER"]);
                $stat->bindParam(":requestURI",$_SERVER["REQUEST_URI"]);
                $stat->bindParam(":userAgent",$_SERVER["HTTP_USER_AGENT"]);
                $stat->bindParam(":requestTime",$_SERVER["REQUEST_TIME"]);
                $stat->execute();
            }

            // Получаем основные статистические показатели

            // Количество хитов за день
            $stat = $this->adapter->dbh->prepare("SELECT COUNT(clientIP) as rows FROM stats WHERE entityId=:entityId AND requestTime>=:timeStart AND requestTime<=:timeEnd AND requestURI=:requestURI");
            $entityId = $this->name;
            $stat->bindParam(":entityId",$entityId);
            $stat->bindParam(":timeStart",getBeginOfDay(time()));
            $stat->bindParam(":timeEnd",getEndOfDay(time()));
            $stat->bindParam(":requestURI",$_SERVER["REQUEST_URI"]);
            $stat->execute();
            $res = $stat->fetchAll();
            if (count($res)>0)
                $this->todayHits = $res[0]["rows"];
            else
                $this->todayHits = 0;

            // Количество хостов за день
            $stat = $this->adapter->dbh->prepare("SELECT DISTINCT clientIP as rows FROM stats WHERE entityId=:entityId AND requestTime>=:timeStart AND requestTime<=:timeEnd AND requestURI=:requestURI");
            $entityId = $this->name;
            $stat->bindParam(":entityId",$entityId);
            $stat->bindParam(":timeStart",getBeginOfDay(time()));
            $stat->bindParam(":timeEnd",getEndOfDay(time()));
            $stat->bindParam(":requestURI",$_SERVER["REQUEST_URI"]);
            $stat->execute();
            $res = $stat->fetchAll();
            if (count($res)>0)
                $this->todayHosts = count($res);
            else
                $this->todayHosts = 0;

            // Количество хитов за все время
            $stat = $this->adapter->dbh->prepare("SELECT COUNT(clientIP) as rows FROM stats WHERE entityId=:entityId AND requestURI=:requestURI");
            $entityId = $this->name;
            $stat->bindParam(":entityId",$entityId);
            $stat->bindParam(":requestURI",$_SERVER["REQUEST_URI"]);
            $stat->execute();
            $res = $stat->fetchAll();
            if (count($res)>0)
                $this->allHits = $res[0]["rows"];
            else
                $this->allHits = 0;            
        }
        $result = parent::getArgs();
        return $result;
    }
}
?>