<?php

$groups["webEntityMain"] = array (
	"file" => __FILE__,
	"name" => "webEntityMain",
	"collection" => "groups",
	"title" => "Основное",
	"fields" => array("name","Text","title")
);

$groups["webEntityAdvanced"] = array (
	"file" => __FILE__,
	"name" => "webEntityAdvanced",
	"collection" => "groups",
	"title" => "Дополнительно",
	"fields" => array("sysname","sortOrder","parent","static","isPublic","isPage","author","createdTime","modifyTimeAuthors","siteId")	
);

$groups["webEntitySEO"] = array (
		"file" => __FILE__,
		"name" => "webEntitySEO",
		"collection" => "groups",
		"title" => "Продвижение",
		"fields" => array("htmlTitle","htmlDescription","htmlKeywords","htmlMeta","htmlEncoding","htmlHeader")
);

$groups["webEntitySettings"] = array (
		"file" => __FILE__,
		"name" => "webEntitySettings",
		"collection" => "groups",
		"title" => "Настройки",
		"fields" => array("childClass","userTemplate","childUserTemplate","adminTemplate","childAdminTemplate","sortFields","childSortFields",
						   "fieldList","childFieldList","entityImage","groupEntityImage","childEntityImage","childGroupEntityImage","width","height")
);

$groups["webEntityComment"] = array (
		"file" => __FILE__,
		"name" => "webEntityComment",
		"collection" => "groups",
		"title" => "Описание",
		"fields" => array("comment")
);

$groups["webEntityPersistedFields"] = array (
		"file" => __FILE__,
		"name" => "webEntityPersistedFields",
		"collection" => "groups",
		"title" => "Описания полей",
		"fields" => array("persistedFields","childPersitedFields")
);

$groups["webEntityCacheDeps"] = array (
		"file" => __FILE__,
		"name" => "webEntityCacheDeps",
		"collection" => "groups",
		"title" => "Кэширование",
		"fields" => array("cacheDeps","childCacheDeps")
);

$groups["webEntityRights"] = array (
		"file" => __FILE__,
		"name" => "webEntityRights",
		"collection" => "groups",
		"title" => "Права доступа",
		"fields" => array("accessDeniedPage")
);

$groups["webEntity"] = array (
	"file" => __FILE__,
	"name" => "webEntity",
	"collection" => "groups",
	"title" => "Раздел Web-сайта",
	"groups" => array("webEntityMain","webEntityAdvanced","webEntitySEO","webEntitySettings","webEntityComment","webEntityPersistedFields","webEntityCacheDeps","webEntityRights")		
);

$fields["static"] = array (
	"file" => __FILE__,
	"name" => "static",
	"collection" => "fields",
	"base" => "booleanField",
	"params" => array (
		"title" => "Статическая страница"
	)		
);

$fields["Text"] = array (
	"file" => __FILE__,
	"name" => "Text",
	"collection" => "fields",
	"base" => "textField",
	"params" => array(
		"title" => "Текст",
		"control_type" => "tinyMCE",
		"width" => "100%",
		"height" => "100%"
	)		
);

$fields["webEntityParent"] = array("type" => "entity",
	"params" => array("type" => "entity",
			"className" => "*WebEntity*",
			"tableClassName" => "EntityDataTable",
			"show_float_div" => "true",
			"classTitle" => "Разделы",
			"editorType" => "WABWindow",
			"title" => "Относится к",
			"condition" => "@parent IS NOT EXISTS",
			"parentEntity" => "WebEntity_WebServerApplication_Docs_"),
	"name" => "webEntityParent",
	"collection" => "fields",
	"file" => __FILE__
);

$fields["accessDeniedPage"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "*WebEntity*",
				"tableClassName" => "EntityDataTable",
				"show_float_div" => "true",
				"classTitle" => "Разделы",
				"editorType" => "WABWindow",
				"title" => "Страница запрета доступа",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "WebEntity_WebServerApplication_Docs_"),
		"name" => "accessDeniedPage",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["isPublic"] = array (
	"file" => __FILE__,
	"name" => "isPublic",
	"collection" => "fields",
	"base" => "booleanField",
	"params" => array (
			"title" => "Опубликовано"
	)
);

$fields["isPage"] = array (
		"file" => __FILE__,
		"name" => "isPage",
		"collection" => "fields",
		"base" => "booleanField",
		"params" => array (
			"title" => "Является Web-страницей"
		)
);

$fields["htmlTitle"] = array (
		"file" => __FILE__,
		"name" => "htmlTitle",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "TITLE"
		)
);

$fields["htmlDescription"] = array (
		"file" => __FILE__,
		"name" => "htmlDescription",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "DESCRIPTION"
		)
);

$fields["htmlKeywords"] = array (
		"file" => __FILE__,
		"name" => "htmlKeywords",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "KEYWORDS"
		)
);

$fields["htmlMeta"] = array (
		"file" => __FILE__,
		"name" => "htmlMeta",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "META"
		)
);

$fields["htmlEncoding"] = array (
		"file" => __FILE__,
		"name" => "htmlEncoding",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Кодировка"
		)
);

$fields["htmlHeader"] = array (
		"file" => __FILE__,
		"name" => "htmlHeader",
		"collection" => "fields",
		"base" => "textField",
		"params" => array (
				"title" => "Текст в заголовке HEAD"
		)
);

$fields["childClass"] = array (
		"file" => __FILE__,
		"name" => "childClass",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Класс дочернего элемента"
		)
);

$fields["sortFields"] = array (
		"file" => __FILE__,
		"name" => "sortFields",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Поля сортировки"
		)
);

$fields["childSortFields"] = array (
		"file" => __FILE__,
		"name" => "childSortFields",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Поля сортировки дочерних элементов"
		)
);

$fields["fieldList"] = array (
		"file" => __FILE__,
		"name" => "fieldList",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Список полей таблицы"
		)
);

$fields["childFieldList"] = array (
		"file" => __FILE__,
		"name" => "childFieldList",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Список полей таблицы дочерних элементов"
		)
);

$fields["entityImage"] = array (
		"file" => __FILE__,
		"name" => "entityImage",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Изображение элемента"
		)
);

$fields["childEntityImage"] = array (
		"file" => __FILE__,
		"name" => "childEntityImage",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Изображение дочерних элементов"
		)
);

$fields["groupEntityImage"] = array (
		"file" => __FILE__,
		"name" => "groupEntityImage",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Изображение группы элементов"
		)
);

$fields["childGroupEntityImage"] = array (
		"file" => __FILE__,
		"name" => "childGroupEntityImage",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Изображение группы дочерних элементов"
		)
);

$fields["width"] = array (
		"file" => __FILE__,
		"name" => "width",
		"collection" => "fields",
		"base" => "integerField",
		"params" => array (
				"title" => "Ширина"
		)
);

$fields["height"] = array (
		"file" => __FILE__,
		"name" => "height",
		"collection" => "fields",
		"base" => "integerField",
		"params" => array (
				"title" => "Высота"
		)
);

$fields["comment"] = array (
		"file" => __FILE__,
		"name" => "comment",
		"collection" => "fields",
		"base" => "textField",
		"params" => array (
				"title" => "Описание"
		)
);

$fields["userTemplate"] = array("type" => "entity",
	"params" => array("type" => "entity",
			"className" => "*WebTemplate*",
			"tableClassName" => "EntityDataTable",
			"show_float_div" => "true",
			"classTitle" => "Шаблоны",
			"editorType" => "WABWindow",
			"title" => "Шаблон просмотра",
			"condition" => "@parent IS NOT EXISTS",
			"parentEntity" => "WebTemplate_WebServerApplication_Docs_"),
	"name" => "userTemplate",
	"collection" => "fields",
	"file" => __FILE__
);

$fields["childUserTemplate"] = array("type" => "entity",
	"params" => array("type" => "entity",
			"className" => "*WebTemplate*",
			"tableClassName" => "EntityDataTable",
			"show_float_div" => "true",
			"classTitle" => "Шаблоны",
			"editorType" => "WABWindow",
			"title" => "Шаблон просмотра дочерних элементов",
			"condition" => "@parent IS NOT EXISTS",
			"parentEntity" => "WebTemplate_WebServerApplication_Docs_"),
	"name" => "childUserTemplate",
	"collection" => "fields",
	"file" => __FILE__
);

$fields["adminTemplate"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "*WebTemplate*",
				"tableClassName" => "EntityDataTable",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны",
				"editorType" => "WABWindow",
				"title" => "Шаблон администрирования",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "WebTemplate_WebServerApplication_Docs_"),
		"name" => "adminTemplate",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["childAdminTemplate"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "*WebTemplate*",
				"tableClassName" => "EntityDataTable",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны",
				"editorType" => "WABWindow",
				"title" => "Шаблон администрирования дочерних элементов",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "WebTemplate_WebServerApplication_Docs_"),
		"name" => "childAdminTemplate",
		"collection" => "fields",
		"file" => __FILE__
);

$models["WebEntity"] = array (
	"file" => __FILE__,
	"metaTitle" => "Раздел Web-сайта",
	"collection" => "models",
	"groups" => array("webEntityMain","webEntityAdvanced","webEntitySEO","webEntitySettings","webEntityComment","webEntityPersistedFields","webEntityCacheDeps","webEntityRights"),		
	"name" => "WebEntity",
	"sysname" => "systemName",
	"title" => "title",
	"static" => "static",
	"Text" => "Text",
	"parent" => "webEntityParent",
	"isPublic" => "isPublic",
	"isPage" => "isPage",
	"htmlTitle" => "htmlTitle",
	"htmlDescription" => "htmlDescription",
	"htmlKeywords" => "htmlKeywords",
	"htmlMeta" => "htmlMeta",
	"htmlEncoding" => "htmlEncoding",
	"htmlHeader" => "htmlHeader",
	"childClass" => "childClass",
	"userTemplate" => "userTemplate",
	"childUserTemplate" => "childUserTemplate",
	"adminTemplate" => "adminTemplate",
	"childAdminTemplate" => "childAdminTemplate",
	"sortFields" => "sortFields",
	"childSortFields" => "childSortFields",
	"fieldList" => "fieldList",
	"childFieldList" => "childFieldList",
	"entityImage" => "entityImage",
	"groupEntityImage" => "groupEntityImage",
	"childEntityImage" => "childEntityImage",
	"childGroupEntityImage" => "childGroupEntityImage",
	"author" => "authorName",
	"createdTime" => "stringField",
	"modifyTimeAuthors" => "textField",
	"width" => "width",
	"height" => "height",
	"comment" => "comment",
	"childPersistedFields" => "textField",
	"persistedFields" => "textField",
	"cacheDeps" => "textField",
	"childCacheDeps" => "textField",
	"siteId" => "integerField",
	"accessDeniedPage" => "accessDeniedPage"
);