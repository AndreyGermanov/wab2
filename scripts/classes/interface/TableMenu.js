/** Класс, преобразующий таблицу с вложенными таблицами в интерактивное меню
 * 
 * У таблицы самого меню и у всех таблиц вложенных подменю должны быть установлены
 * идентификаторы (id), а также атрибут type="menu". В таблицах меню должен быть тэг
 * <tbody>. У всех таблиц подменю должен отличаться z-index от обычного (например style="z-index:2").
 * У всех таблиц подменю должен быть установлен стиль position:absolute.
 * 
 * При щелчке по элементу меню генерируется событие MENU_ITEM_CLICKED. Это просиходит
 * только в случае, если у этого элемента есть идентификатор (id).
 *  
 * ЛВА Конструктор Web-приложений 2
 * (С) 2013 ООО "ЛВА". Все права защищены.
 * http://www.lvacompany.ru
 * 
 * @author andrey
 * @time 05.03.2013 02:45
 */
var TableMenu = Class.create(Entity, {

	/** Функция инициализации меню
	 * 
	 * Принимает узел таблицы и запускает
	 * функцию build, которая собирает меню
	 * на базе этой таблицы
	 */
	init: function(tbl) {
		this.active_menus = new Array;
		this.table = tbl;
		this.showEvent = "onclick";
		this.build(this.table);
	},
	
	/** Функция сборки меню из таблицы
	 * 
	 * Размечает таблицу под меню
	 * 
	 * @param table
	 * @returns
	 */
	build: function(table) {
		var tbody = table.getElementsByTagName("TBODY");
		tbody = tbody[0];
		var cells = new Array;
		var rows = tbody.childNodes;
		var i=0;
		var i1=0;
		var i3=0;
		var found = false;
		for (i=0;i<rows.length;i++) {
			if (rows[i].nodeType!=1)
				continue;			
			var cols = rows[i].childNodes;
			cells[i3] = new Array;
			i2=0;
			for (i1=0;i1<cols.length;i1++) {
				if (cols[i1].nodeType!=1)
					continue;
				cells[i3][i2] = cols[i1];
				i2++;
			}
			i3++;
		}
		for (i=0;i<cells.length;i++) {
			for (i1=0;i1<cells[i].length;i1++) {
				if (table.id!=null)
					cells[i][i1].setAttribute("parentmenu",table.id);
				if (cells[i][i1].getAttribute("object")==null)
					cells[i][i1].setAttribute("object",this.object_id);
				if (cells[i][i1+1]!=null) {
					var node = cells[i][i1+1];
					var cellContent = node.childNodes;
					found = false;
					var i2=0;
					for (i2=0;i2<cellContent.length;i2++) {						
						if (cellContent[i2].nodeType==1 && cellContent[i2]!=null && cellContent[i2].getAttribute("type")=="menu") {
							found = true;
							break;
						}
					}
					if (found) {
						if (cellContent[i2]!=null) {
							cells[i][i1].setAttribute("submenu",cellContent[i2].id);
							if (cells[i][i1].getAttribute("parentmenu")!=null)
								cellContent[i2].setAttribute("parentmenu",cells[i][i1].getAttribute("parentmenu"));
							cellContent[i2].setAttribute("object",this.object_id);
							cellContent[i2].style.display = 'none';
							if (this.showEvent=="onmouseover") {
								if (cellContent[i2]["addEventListener"]!=null)
									cellContent[i2].addEventListener("mouseout",this.onMouseOut);
								else 
									cellContent[i2].attachEvent("onmouseout",this.onMouseOut);
							}
							this.build(cellContent[i2]);
						}
					}
				}
				if (cells[i+1]!=null && cells[i+1][i1]!=null) {
					var node = cells[i+1][i1];
					var cellContent = node.childNodes;
					found = false;
					for (i2=0;i2<cellContent.length;i2++) {
						if (cellContent[i2].nodeType==1 && cellContent[i2]!=null && cellContent[i2].getAttribute("type")=="menu") {
							found = true;
							break;
						}
					}
					if (found) {
						if (cellContent[i2]!=null) {
							cells[i][i1].setAttribute("submenu",cellContent[i2].id);
							if (cells[i][i1].getAttribute("parentmenu")!=null)
								cellContent[i2].setAttribute("parentmenu",cells[i][i1].getAttribute("parentmenu"));
							cellContent[i2].setAttribute("object",this.object_id);
							cellContent[i2].style.display = 'none';
							if (this.showEvent=="onmouseover") {
								if (cellContent[i2]["addEventListener"]!=null)
									cellContent[i2].addEventListener("mouseout",this.onMouseOut);
								else 
									cellContent[i2].attachEvent("onmouseout",this.onMouseOut);
							}
							this.build(cellContent[i2]);
						}
					}
				}
				var event = new Array;
				event["target"] = cells[i][i1];
				if (cells[i][i1]["addEventListener"]!=null) {
					cells[i][i1].addEventListener(this.showEvent.replace(/on/,""), this.processMenuItem);
					if (this.showEvent=="onclick")
						cells[i][i1].addEventListener("mouseover",this.onMouseOver);
					else
						cells[i][i1].addEventListener("click",this.onClick);
				}
				else {
					cells[i][i1].attachEvent(this.showEvent, this.processMenuItem);
					if (this.showEvent=="onclick")
						cells[i][i1].attachEvent("onmouseover",this.onMouseOver);
					else
						cells[i][i1].attachEvent("onclick",this.onClick);
				}
				cells[i][i1].setAttribute("object",this.object_id);
			}
		}
	},
	
	/** Обработчик выбора элемента меню
	 * 
	 * Если выбранный элемент содержит подменю, то отображает это подменю,
	 * иначе генерирует событие MENU_ITEM_HOVER, возникающее при наведении мышью,
	 * а также MENU_ITEM_CLICKED при нажатии мышью по элементу. Это происходит только
	 * если у элемента есть идентификатор (id).
	 * 
	 */
	processMenuItem: function(event) {
		var elem = eventTarget(event);
		if (elem==null)
			elem = event.srcElement;
		var obj = $O(elem.getAttribute("object"),"");
		if (obj==null)
			obj=this;
		var menu = $(elem.getAttribute("submenu"));
		if (menu!=null) {			
		    event.cancelBubble = true;
			menu.style.display = '';
			var o=null;
			for (o in obj.active_menus) {
				if (typeof obj.active_menus[o] != "function") {
					if (obj.active_menus[o].getAttribute("parentmenu")==menu.getAttribute("parentmenu") && obj.active_menus[o] != menu) {
						obj.active_menus[o].style.display = 'none';
						obj.hideSubMenus(obj.active_menus[o]);
					}	
				}	
			}	
			obj.active_menus[menu.id] = menu;
			if (elem.id!=null && elem.id!="") {
				if (obj.showEvent=="onclick")
					obj.raiseEvent("MENU_ITEM_CLICKED",$Arr("object_id="+obj.object_id+",item_id="+elem.id));
				obj.raiseEvent("MENU_ITEM_HOVER",$Arr("object_id="+obj.object_id+",item_id="+elem.id));
			}
		} else {
			if ($(elem.getAttribute("parentmenu"))!=null) {
				if (elem.id!=null && elem.id!="") {
					if (obj.showEvent=="onclick")
						obj.raiseEvent("MENU_ITEM_CLICKED",$Arr("object_id="+obj.object_id+",item_id="+elem.id));
					obj.raiseEvent("MENU_ITEM_HOVER",$Arr("object_id="+obj.object_id+",item_id="+elem.id));
				}
				obj.hideSubMenus($(elem.getAttribute("parentmenu")));
			}
		}		
	},
	
	/**
	 * Обработчик события onmouseover, который вызывается при наведении
	 * мышью на элемент меню.
	 * Генерирует событие MENU_ITEM_HOVER, на которое могут отвечать другие 
	 * объекты, или даже сам этот объект.
	 */
	onMouseOver: function(event) {
		var elem = eventTarget(event);
		if (elem==null)
			elem = event.srcElement;
		var obj = $O(elem.getAttribute("object"),"");
		if (elem.id!=null && elem.id!="")
			obj.raiseEvent("MENU_ITEM_HOVER",$Arr("object_id="+obj.object_id+",item_id="+elem.id));		
	},
	
	/**
	 * Обработчик события onmouseover, который вызывается при уведении
	 * мыши с элемента меню.
	 * Генерирует событие MENU_ITEM_OUT, на которое могут отвечать другие 
	 * объекты, или даже сам этот объект.
	 */
	onMouseOut: function(event) {
	    event.cancelBubble = true;
		var elem = eventTarget(event);
		if (elem==null)
			elem = event.srcElement;
		var obj = $O(elem.getAttribute("object"),"");
		while (elem.tagName!="TABLE") {
			elem = elem.parentNode;
		}
		elem.style.display = 'none';
		if (elem.id!=null && elem.id!="") {
			obj.raiseEvent("MENU_ITEM_OUT",$Arr("object_id="+obj.object_id+",item_id="+elem.id));
		}
	},	
	
	/**
	 * Обработчик события onclick, который вызывается при щелчке
	 * по объекту.
	 * Генерирует событие MENU_ITEM_CLIKED, на которое могут отвечать другие 
	 * объекты, или даже сам этот объект.
	 */
	onClick: function(event) {
		var elem = eventTarget(event);
		if (elem==null)
			elem = event.srcElement;
		var obj = $O(elem.getAttribute("object"),"");
		if (elem.id!=null && elem.id!="")
			obj.raiseEvent("MENU_ITEM_CLICKED",$Arr("object_id="+obj.object_id+",item_id="+elem.id));		
	},
	
	/**
	 * Функция скрывает все подменю указанного меню
	 * 
	 * Если меню не указано, скрывает все подменю объекта.
	 */
	hideSubMenus: function(submenu) {
		var obj=null;
		if (submenu!=null) {
			obj = $O(submenu.getAttribute("object"),"");
		}
		else
			obj = this;
		if (obj!=null) {
			var o=null;
			for (o in obj.active_menus) {
				if (typeof obj.active_menus[o] != "function") {
					if (submenu!=null && submenu.getAttribute("id")!=null) {
						if (obj.active_menus[o].getAttribute("parentmenu")==submenu.getAttribute("id")) {
							obj.active_menus[o].style.display = 'none';
							obj.hideSubMenus(obj.active_menus[o]);
						}
					} else {
						obj.active_menus[o].style.display = 'none';
						delete obj.active_menus[o];
					}
				}	
			}
		}
	},
	
	/**
	 * Функция скрывает все меню в системе
	 */
	hideAllMenus: function(event) {
		var o=null;
		for (o in objects.objects) {
			if (typeof objects.objects[o] != "function") {
				var className = o.split("_").shift();
				if (className=="TableMenu")
					objects.objects[o].hideSubMenus();
			}
		}
	}
});