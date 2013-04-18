<?php
$events["*"] = array ("name" => "*",
		"title" => "Все события",
		"comment" => "Все события",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["USER_ONLINE"] = array ("name" => "USER_ONLINE",
								 "title" => "Вход пользователя в систему",
								 "comment" => "Пользователь подключился к системе",
								 "params" => array("logonTime"),
								 "collection" => "events",
								 "file" => __FILE__
);

$events["USER_OFFLINE"] = array ("name" => "USER_OFFLINE",
								 "title" => "Выход пользователя из системы",
								 "comment" => "Пользователь отключился от системы",
  								 "params" => array("logonTime"),
								 "collection" => "events",
								 "file" => __FILE__
);

$events["ENTITY_OPENED"] = array ("name" => "ENTITY_OPENED",
		"title" => "Открытие объекта",
		"comment" => "Пользователь открыл объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_ADDED"] = array ("name" => "ENTITY_ADDED",
		"title" => "Создание нового объекта",
		"comment" => "Пользователь создал новый объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_CHANGED"] = array ("name" => "ENTITY_CHANGED",
		"title" => "Изменение объекта",
		"comment" => "Пользователь внес изменения в объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_CLOSED"] = array ("name" => "ENTITY_CLOSED",
		"title" => "Закрытие объекта",
		"comment" => "Пользователь закрыл объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_DELETED"] = array ("name" => "ENTITY_DELETED",
		"title" => "Удаление объекта",
		"comment" => "Пользователь удалил объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_MARK_DELETED"] = array ("name" => "ENTITY_MARK_DELETED",
		"title" => "Пометка объекта на удаление",
		"comment" => "Пользователь пометил на удаление объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_MARK_UNDELETED"] = array ("name" => "ENTITY_MARK_UNDELETED",
		"title" => "Снятие пометки объекта на удаление",
		"comment" => "Пользователь снял пометку удаления объекта `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_REGISTERED"] = array ("name" => "ENTITY_REGISTERED",
		"title" => "Проведение объекта",
		"comment" => "Пользователь провел объект `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_MARK_REGISTERED"] = array ("name" => "ENTITY_MARK_REGISTERED",
		"title" => "Проведение документа",
		"comment" => "Пользователь провел документ `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ENTITY_MARK_UNREGISTERED"] = array ("name" => "ENTITY_MARK_UNREGISTERED",
		"title" => "Отмена проведения документа",
		"comment" => "Пользователь отменил проведение документа `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["APACHEUSER_ADDED"] = array ("name" => "APACHEUSER_ADDED",
		"title" => "Создание пользователя системы управления",
		"comment" => "Пользователь создал пользователя системы управления `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["APACHEUSER_CHANGED"] = array ("name" => "APACHEUSER_CHANGED",
		"title" => "Изменение параметров пользователя системы управления",
		"comment" => "Пользователь изменил параметры пользователя системы управления `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["APACHEUSER_DELETED"] = array ("name" => "APACHEUSER_DELETED",
		"title" => "Удаление пользователя системы управления",
		"comment" => "Пользователь удалил пользователя системы управления `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["USER_BAN"] = array ("name" => "USER_BAN",
		"title" => "Блокировка пользователя системы управления",
		"comment" => "Пользователь заблокировал пользователя системы управления `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ROLE_ADDED"] = array ("name" => "ROLE_ADDED",
		"title" => "Создание роли",
		"comment" => "Пользователь создал роль `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ROLE_CHANGED"] = array ("name" => "ROLE_CHANGED",
		"title" => "Изменение роли",
		"comment" => "Пользователь изменил роль `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ROLE_DELETED"] = array ("name" => "ROLE_DELETED",
		"title" => "Удаление роли",
		"comment" => "Пользователь удалил роль `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["PROFILE_CHANGED"] = array ("name" => "PROFILE_CHANGED",
		"title" => "Изменение параметров профиля",
		"comment" => "Пользователь изменил параметры профиля `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["PROFILE_DELETED"] = array ("name" => "PROFILE_DELETED",
		"title" => "Удаление профиля",
		"comment" => "Пользователь удалил профиль `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_MAKEDIR"] = array ("name" => "FM_MAKEDIR",
		"title" => "Создание каталога",
		"comment" => "Пользователь создал новый каталог",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_RENAME"] = array ("name" => "FM_RENAME",
		"title" => "Переименование файла/каталога",
		"comment" => "Пользователь переименовал файл/каталог",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_COPY"] = array ("name" => "FM_COPY",
		"title" => "Копирование файла/каталога",
		"comment" => "Пользователь скопировал файл/каталог",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_MOVE"] = array ("name" => "FM_MOVE",
		"title" => "Перемещение файла/каталога",
		"comment" => "Пользователь переместил файл/каталог",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_DELETE"] = array ("name" => "FM_DELETE",
		"title" => "Удаление файла/каталога",
		"comment" => "Пользователь удалил файл/каталог",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_UPLOAD"] = array ("name" => "FM_UPLOAD",
		"title" => "Загрузка на сервер файла/каталога",
		"comment" => "Пользователь загрузил на сервер файл/каталог",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["FM_CHANGEPROPERTIES"] = array ("name" => "FM_CHANGEPROPERTIES",
		"title" => "Изменение свойств файлов/каталогов",
		"comment" => "Пользователь изменил свойства файлов/каталогов",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["UPDATE_FILES"] = array ("name" => "UPDATE_FILES",
		"title" => "Обновление состояния файлов",
		"comment" => "Пользователь обновил состояние файлов",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);
?>