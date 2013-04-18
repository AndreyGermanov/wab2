<?php
$events["DHCPHOST_ADDED"] = array ("name" => "DHCPHOST_ADDED",
		"title" => "Создание нового хоста",
		"comment" => "Пользователь создал хост `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPHOST_CHANGED"] = array ("name" => "DHCPHOST_CHANGED",
		"title" => "Изменение параметров хоста",
		"comment" => "Пользователь изменил настройки хоста `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPHOST_DELETED"] = array ("name" => "DHCPHOST_DELETED",
		"title" => "Удаление хоста",
		"comment" => "Пользователь удалил хост `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPSUBNET_ADDED"] = array ("name" => "DHCPSUBNET_ADDED",
		"title" => "Создание подсети",
		"comment" => "Пользователь создал подсеть `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPSUBNET_CHANGED"] = array ("name" => "DHCPSUBNET_CHANGED",
		"title" => "Изменение настроек подсети",
		"comment" => "Пользователь изменил настройки подсети `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPSUBNET_DELETED"] = array ("name" => "DHCPSUBNET_DELETED",
		"title" => "Удаление подсети",
		"comment" => "Пользователь удалил подсеть `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPSERVER_CHANGED"] = array ("name" => "DHCPSERVER_CHANGED",
		"title" => "Изменение настроек Сетевого центра",
		"comment" => "Пользователь изменил настройки Сетевого центра",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DHCPSERVER_RESTARTED"] = array ("name" => "DHCPSERVER_RESTARTED",
		"title" => "Перезапуск служб Сетевого центра",
		"comment" => "Пользователь перезапустил службы Сетевого центра",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FILESERVER_CHANGED"] = array ("name" => "FILESERVER_CHANGED",
		"title" => "Изменение настроек Файлового сервера",
		"comment" => "Пользователь изменил настройки Файлового сервера",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FILESERVER_RESTARTED"] = array ("name" => "FILESERVER_RESTARTED",
		"title" => "Перезапуск служб Файлового сервера",
		"comment" => "Пользователь перезапустил службы Файлового сервера",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["DOCFLOWINTEGRATOR_CHANGED"] = array ("name" => "DOCFLOWINTEGRATOR_CHANGED",
		"title" => "Изменение параметров интеграции с Бизнес-сервером",
		"comment" => "Пользователь изменил параметры интеграции с Бизнес-сервером",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["GATEWAYINTEGRATOR_CHANGED"] = array ("name" => "GATEWAYINTEGRATOR_CHANGED",
		"title" => "Изменение параметров интеграции с Интернет-шлюзом",
		"comment" => "Пользователь изменил параметры интеграции с Интернет-шлюзом",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILINTEGRATOR_CHANGED"] = array ("name" => "MAILINTEGRATOR_CHANGED",
		"title" => "Изменение параметров интеграции с почтовым сервером",
		"comment" => "Пользователь изменил параметры интеграции с почтовым сервером",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FILESHARE_DELETED"] = array ("name" => "FILESHARE_DELETED",
		"title" => "Удаление общей папки",
		"comment" => "Пользователь удалил общую папку `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["OBJECTGROUP_ADDED"] = array ("name" => "OBJECTGROUP_ADDED",
		"title" => "Создание группы объектов",
		"comment" => "Пользователь создал группу объектов `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["OBJECTGROUP_CHANGED"] = array ("name" => "OBJECTGROUP_CHANGED",
		"title" => "Изменение параметров группы объектов",
		"comment" => "Пользователь изменил параметры группы объектов `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["OBJECTGROUP_DELETED"] = array ("name" => "OBJECTGROUP_DELETED",
		"title" => "Удаление группы объектов",
		"comment" => "Пользователь удалил группу объектов `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FILESHARE_ADDED"] = array ("name" => "FILESHARE_ADDED",
		"title" => "Создание общей папки",
		"comment" => "Пользователь создал общую папку `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FILESHARE_CHANGED"] = array ("name" => "FILESHARE_CHANGED",
		"title" => "Изменение параметров общей папки",
		"comment" => "Пользователь изменил параметры общей папки `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["GROUP_ADDED"] = array ("name" => "GROUP_ADDED",
		"title" => "Создание группы пользователей",
		"comment" => "Пользователь создал группу пользователей `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["GROUP_CHANGED"] = array ("name" => "GROUP_CHANGED",
		"title" => "Изменение параметров группы пользователей",
		"comment" => "Пользователь изменил параметры группы пользователей `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["GROUP_DELETED"] = array ("name" => "GROUP_DELETED",
		"title" => "Удаление группы пользователей",
		"comment" => "Пользователь удалил группу пользователей `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LVMSNAPSHOT_CREATE"] = array ("name" => "LVMSNAPSHOT_CREATE",
		"title" => "Создание снимка",
		"comment" => "Пользователь создал новый снимок",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LVMSNAPSHOT_REMOVE"] = array ("name" => "LVMSNAPSHOT_REMOVE",
		"title" => "Удаление снимка",
		"comment" => "Пользователь удалил снимок",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LVMSNAPSHOT_REMOVEALL"] = array ("name" => "LVMSNAPSHOT_REMOVEALL",
		"title" => "Удаление всех снимков",
		"comment" => "Пользователь удалил все снимки",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LVMSNAPSHOT_RESIZE"] = array ("name" => "LVMSNAPSHOT_RESIZE",
		"title" => "Изменение размера снимка",
		"comment" => "Пользователь изменил размер снимка",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["SHADOWCOPY_CHANGED"] = array ("name" => "SHADOWCOPY_CHANGED",
		"title" => "Изменение параметров теневого копирования данных",
		"comment" => "Пользователь изменил параметры теневого копирования данных",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["CONTROLLER_SETTINGS_CHANGED"] = array ("name" => "CONTROLLER_SETTINGS_CHANGED",
		"title" => "Изменение основных параметров системы",
		"comment" => "Пользователь изменил основные параметры системы",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["USER_ADDED"] = array ("name" => "USER_ADDED",
		"title" => "Создание пользователя",
		"comment" => "Пользователь создал пользователя `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["USER_CHANGED"] = array ("name" => "USER_CHANGED",
		"title" => "Изменение параметров пользователя",
		"comment" => "Пользователь изменил параметры пользователя `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["USER_DELETED"] = array ("name" => "USER_DELETED",
		"title" => "Удаление пользователя",
		"comment" => "Пользователь удалил пользователя `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FTPSERVER_CHANGED"] = array ("name" => "FTPSERVER_CHANGED",
		"title" => "Изменение настроек FTP-сервера",
		"comment" => "Пользователь изменил настройки FTP-сервера",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);