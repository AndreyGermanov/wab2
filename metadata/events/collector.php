<?php
$events["ADDRBOOK_ADDRESS_ADDED"] = array ("name" => "ADDRBOOK_ADDRESS_ADDED",
		"title" => "Создание записи в адресной книге",
		"comment" => "Пользователь создал запись в адресной книге `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ADDRBOOK_ADDRESS_CHANGED"] = array ("name" => "ADDRBOOK_ADDRESS_CHANGED",
		"title" => "Изменение записи в адресной книге",
		"comment" => "Пользователь изменил запись в адресной книге `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ADDRBOOK_ADDRESS_DELETED"] = array ("name" => "ADDRBOOK_ADDRESS_DELETED",
		"title" => "Удаление записи из адресной книги",
		"comment" => "Пользователь удалил запись из адресной книги `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LDAPADDRBOOK_ADDED"] = array ("name" => "LDAPADDRBOOK_ADDED",
		"title" => "Создание адресной книги LDAP",
		"comment" => "Пользователь создал адресную книгу LDAP `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LDAPADDRBOOK_CHANGED"] = array ("name" => "LDAPADDRBOOK_CHANGED",
		"title" => "Изменение параметров адресной книги LDAP",
		"comment" => "Пользователь изменил параметры адресной книги LDAP `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["LDAPADDRBOOK_DELETED"] = array ("name" => "LDAPADDRBOOK_DELETED",
		"title" => "Удаление адресной книги LDAP",
		"comment" => "Пользователь удалил адресную книгу LDAP `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ADDRBOOK_CHANGED"] = array ("name" => "ADDRBOOK_CHANGED",
		"title" => "Изменение параметров системной адресной книги",
		"comment" => "Пользователь изменил параметры системной адресной книги",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["ADDRBOOK_DEFAULT_FIELDS_CHANGED"] = array ("name" => "ADDRBOOK_DEFAULT_FIELDS_CHANGED",
		"title" => "Изменение общих полей адресной книги",
		"comment" => "Пользователь изменил общие поля адресной книги",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILALIAS_ADDED"] = array ("name" => "MAILALIAS_ADDED",
		"title" => "Создание нового списка рассылки",
		"comment" => "Пользователь создал список рассылки `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILALIAS_CHANGED"] = array ("name" => "MAILALIAS_CHANGED",
		"title" => "Изменение параметров списка рассылки",
		"comment" => "Пользователь изменил параметры списка рассылки `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILALIAS_DELETED"] = array ("name" => "MAILALIAS_DELETED",
		"title" => "Удаление списка рассылки",
		"comment" => "Пользователь удалил список рассылки `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILALIAS_ADDRESS_ADDED"] = array ("name" => "MAILALIAS_ADDRESS_ADDED",
		"title" => "Создание нового адресата списка рассылки",
		"comment" => "Пользователь добавил адресата `<presentation>` в список рассылки",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILALIAS_ADDRESS_CHANGED"] = array ("name" => "MAILALIAS_ADDRESS_CHANGED",
		"title" => "Изменение параметров адресата списка рассылки",
		"comment" => "Пользователь изменил параметры адресата `<presentation>` списка рассылки",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILALIAS_ADDRESS_DELETED"] = array ("name" => "MAILALIAS_ADDRESS_DELETED",
		"title" => "Удаление адресата списка рассылки",
		"comment" => "Пользователь удалил адресата `<presentation>` списка рассылки",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILBOX_ADDED"] = array ("name" => "MAILBOX_ADDED",
		"title" => "Создание нового почтового ящика",
		"comment" => "Пользователь создал почтовый ящик `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILBOX_CHANGED"] = array ("name" => "MAILBOX_CHANGED",
		"title" => "Изменение параметров почтового ящика",
		"comment" => "Пользователь изменил параметры почтового ящика `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILBOX_DELETED"] = array ("name" => "MAILBOX_DELETED",
		"title" => "Удаление почтового ящика",
		"comment" => "Пользователь удалил почтовый ящик `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILDOMAIN_ADDED"] = array ("name" => "MAILDOMAIN_ADDED",
		"title" => "Создание нового почтового домента",
		"comment" => "Пользователь создал почтовый домен `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILDOMAIN_CHANGED"] = array ("name" => "MAILDOMAIN_CHANGED",
		"title" => "Изменение параметров почтового домена",
		"comment" => "Пользователь изменил параметры почтового домена `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILDOMAIN_DELETED"] = array ("name" => "MAILDOMAIN_DELETED",
		"title" => "Удаление почтового домена",
		"comment" => "Пользователь удалил почтовый домен `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILQUEUE_RESEND"] = array ("name" => "MAILQUEUE_RESEND",
		"title" => "Запрос повторной отправки письма из почтовой очереди",
		"comment" => "Пользователь запросил повторную отправку письма из почтовой очереди",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILQUEUE_DELETE"] = array ("name" => "MAILQUEUE_DELETE",
		"title" => "Запрос удаления письма из почтовой очереди",
		"comment" => "Пользователь запросил удаление письма из почтовой очереди",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILQUEUE_REASON"] = array ("name" => "MAILQUEUE_REASON",
		"title" => "Запрос причины задержки письма в почтовой очереди",
		"comment" => "Пользователь запросил причину задержки письма в почтовой очереди",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILSCANNERCONFIG_CHANGED"] = array ("name" => "MAILSCANNERCONFIG_CHANGED",
		"title" => "Изменение параметров фильтра содержимого",
		"comment" => "Пользователь изменил параметры фильтра содержимого",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["MAILSETTINGS_CHANGED"] = array ("name" => "MAILSETTINGS_CHANGED",
		"title" => "Изменение параметров почтового сервера",
		"comment" => "Пользователь изменил параметры почтового сервера",
		"params" => array("logonTime"),
		"collection" => "events",
		"file" => __FILE__
);

$events["REMOTEMAILBOX_ADDED"] = array ("name" => "REMOTEMAILBOX_ADDED",
		"title" => "Создание нового почтового ящика Интернет",
		"comment" => "Пользователь создал почтовый ящик Интернет `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["REMOTEMAILBOX_CHANGED"] = array ("name" => "REMOTEMAILBOX_CHANGED",
		"title" => "Изменение параметров почтового ящика Интернет",
		"comment" => "Пользователь изменил параметры почтового ящика Интернет `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["REMOTEMAILBOX_DELETED"] = array ("name" => "REMOTEMAILBOX_DELETED",
		"title" => "Удаление почтового ящика Интернет",
		"comment" => "Пользователь удалил почтовый ящик Интернет `<presentation>`",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);

$events["REPFILTER_CHANGED"] = array ("name" => "REPFILTER_CHANGED",
		"title" => "Изменение параметров репутационного фильтра",
		"comment" => "Пользователь изменил параметры репутационного фильтра",
		"params" => array("logonTime","object_id"),
		"collection" => "events",
		"file" => __FILE__
);
?>