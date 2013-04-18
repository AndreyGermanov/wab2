// Массив типов событий элементов
taskbar_loaded = false;
var event_types = new Array;
event_types[0]="onActivate";
event_types[1]="onAfterPrint";
event_types[2]="onBeforePrint";
event_types[3]="onAfterUpdate";
event_types[4]="onBeforeUpdate";
event_types[5]="onErrorUpdate";
event_types[6]="onAbort";
event_types[7]="onBeforeDeactivate";
event_types[8]="onDeactivate";
event_types[9]="onBeforeCopy";
event_types[10]="onBeforeCut";
event_types[11]="onBeforeEditFocus";
event_types[12]="onBeforePaste";
event_types[13]="onBeforeUnload";
event_types[14]="onBlur";
event_types[15]="onBounce";
event_types[16]="onChange";
event_types[17]="onClick";
event_types[18]="onControlSelect";
event_types[19]="onCopy";
event_types[20]="onCut";
event_types[21]="onDblClick";
event_types[22]="onDrag";
event_types[23]="onDragEnter";
event_types[24]="onDragLeave";
event_types[25]="onDragOver";
event_types[26]="onDragStart";
event_types[27]="onDrop";
event_types[28]="onFilterChange";
event_types[29]="onDragDrop";
event_types[30]="onError";
event_types[31]="onFilterChange";
event_types[32]="onFinish";
event_types[33]="onFocus";
event_types[34]="onHelp";
event_types[35]="onKeyDown";
event_types[36]="onKeyPress";
event_types[37]="onKeyUp";
event_types[38]="onLoad";
event_types[39]="OnLoseCapture";
event_types[40]="onMouseDown";
event_types[41]="onMouseEnter";
event_types[42]="onMouseLeave";
event_types[43]="onMouseMove";
event_types[44]="onMouseOut";
event_types[45]="onMouseOver";
event_types[46]="onMouseUp";
event_types[47]="onMove";
event_types[48]="onPaste";
event_types[49]="onPropertyChange";
event_types[50]="onReadyStateChange";
event_types[51]="onReset";
event_types[52]="onResize";
event_types[53]="onResizeEnd";
event_types[54]="onResizeStart";
event_types[55]="onScroll";
event_types[56]="onSelectStart";
event_types[57]="onSelect";
event_types[58]="onSelectionChange";
event_types[59]="onStart";
event_types[60]="onStop";
event_types[61]="onSubmit";
event_types[62]="onUnload";
event_types[63]="onContextMenu";

var event_hashes = new Array;
for (evtype in event_types) {
	if (typeof(event_types[evtype])!="function")
	event_hashes[event_types[evtype].toLowerCase()] = event_types[evtype];
}
// Вспомогательные функции верхнего уровня

function cursorPos(event) {
      var x = y = 0;
      if (document.attachEvent != null) { // Internet Explorer & Opera
            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
      } else if (!document.attachEvent && document.addEventListener) { // Gecko
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
      } else {
            // Do nothing
      }
      return {x:x, y:y};

      return {x:x, y:y};
}

function getTopWindow()		
{
	if (typeof window == "undefined")
		return 0;
	
	if (typeof window.parent == "undefined")		
		return window;
	
    var win = window.parent;
    if (win==null) {
        return window;
    }
    var predwin = null;
    while (win!=null)
    {
        if (win==predwin) break;
        win = window.parent;
        predwin = win;            
    }
    if (win!=null)
        return win.parent;
    else
        return 0;
}

function getWindowManager()
{    
    return globalTopWindow.windowManager;
}

function getElementById(parent,id)
{
    if (parent=='')
        parent = document.documentElement;
//        var elem = parent.getElementById(id);
//        alert(elem);
//        if (elem!=null)
//            return elem;
    
    if (typeof(parent)=="object") {        
		var elems2 = parent.getElementsByTagName('*');
		var counter2=0;
		for (counter2=0;counter2<elems2.length;counter2++)
			if (elems2[counter2].id == id)
				return elems2[counter2];
	}
    return 0;
}

function isStrLess(what,less_then)
{
    if (what==less_then) return false;
    var arr = new Array;
    arr[0] = what.toUpperCase();
    arr[1] = less_then.toUpperCase();
    arr.sort();
    if (arr[0]==what.toUpperCase())
        return true;
    else
        return false;
}

function get_elem_by_id(elem,id)
{
    var obj_id = elem.getAttribute('object');
    if (obj_id == null) obj_id="";else obj_id = obj_id.concat("_");
    var instance_id = elem.getAttribute('instance');
    if (instance_id == null) instance_id="";else instance_id = instance_id.concat("_");
    return document.getElementById(obj_id.concat(instance_id).concat(id));
}

function get_elem_id(elem)
{
    var obj_id = elem.getAttribute('object');
    if (obj_id == null) obj_id="";else obj_id = obj_id.concat("_");
    var instance_id = elem.getAttribute('instance');
    if (instance_id == null) instance_id="";else instance_id = instance_id.concat("_");
    var id = elem.id;
    return id.toString().replace(obj_id, "").replace(instance_id,"");
}

function get_full_elem_id(elem,id)
{
    var obj_id = elem.getAttribute('object');
    if (obj_id == null) obj_id="";else obj_id = obj_id.concat("_");
    var instance_id = elem.getAttribute('instance');
    if (instance_id == null) instance_id="";else instance_id = instance_id.concat("_");
    var result = obj_id;
    return result.concat(instance_id).concat(id);
}

function getClientId(id)
{
    if (id!=null)
        return id.replace(/\./g,"_").replace(/@/g,"_").replace(/\//g,"_").replace(/\ /g,"_");
    else
        return 0;
}

function getServerId(id)
{
    var elem = $I(id);
    if (elem!=null)
    {
        var object_id = elem.getAttribute("object");
        var instance_id = elem.getAttribute("instance");
        var js_object_id = object_id.replace(".","_").replace("@","_");
        return id.replace(js_object_id,object_id).replace(instance_id,"");
    }
    else
        return 0;
}

function execCommand(command) {
    var params = new Array;
    params[params.length] = "$object->exec_command('"+command+"');";
        
    new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: "Shell_Exec",
                         init_string: params.join(";")+";"},
            onSuccess: function(transport) {
                var result = trim(transport.responseText);
                execCommand(command,result);
            }
        });
    return result;
}

function trimleft(str)
{
    var stri = str;
    if (stri == null)
        return 0;
    var result = "";
    var counter=0;
    while (counter<stri.length) {
        if (stri[counter]!=" ")
            break;
        counter++;
    }
    var counter1=0;
    for (counter1=counter;counter1<stri.length;counter1++) {
            result = result+stri[counter1];
    }
    return result;
}

function trimright(str)
{
    var result = "";
    var stri = str;
    if (stri=="")
        return 0;
    var counter=stri.length-1;
    while (counter>-1) {
        if (stri[counter]!=" ")
            break;
        counter--;
    }
    
    var counter1=0;
    for (counter1=0;counter1<=counter;counter1++) {
            result = result+stri[counter1];
    }
    return result;
}

function trim(str)
{
    var stri = str;
    var result = trimleft(stri);
    //result = trimright(result);
    return result;
}

function onTinyMCETextChanged(inst) {
	var arr = new Array;
    if (inst.id == null) {
        arr = inst.split("_");
        instid = inst;
    }
    else {
        arr = inst.id.split("_");
        instid = inst.id;
    }
    arr.pop();
    var obj = arr.join("_");
    var ev = new Array;
    if (typeof($O(obj,''))!='undefined') {
        ev.target = $O(obj,'').node;
        $O(obj,'').onChange(ev);
    }
}

function openKCFinder(field_name, url, type, win) {
    if (type=="image")
        type="images";
    if (type=="file")
        type="files";
    tinyMCE.activeEditor.windowManager.open({
        file: '/tools/kcfinder/browse.php?opener=tinymce&type=' + type+'&lng=ru',
        title: 'KCFinder',
        width: 700,
        height: 500,
        resizable: "yes",
        inline: false,
        close_previous: "no",
        popup_css: false
    }, {
        window: win,
        input: field_name
    });
    return false;
}

function openKCFinder_singleFile(item,type) {
    window.KCFinder = {};
    window.KCFinder.callBack = function(url) {
        window.KCFinder = null;
        var obj = $I(item).getAttribute("object");
        $O(obj,"").setValue(url);
    };
    
    var leftPosition = (screen.availWidth-600)/2;
    var topPosition = (screen.availHeight-500)/2;
    var params = new Object;
    params["url"] = '/tools/kcfinder/browse.php?type='+type+"&lng=ru";
    params["hook"] = "setParams";
    //getWindowManager().show_window("Window_FrameWindow"+item.replace(/_/g,""),"FrameWindow_"+item,params,$I(item).getAttribute("object"),item,null,true);
    window.open('/tools/kcfinder/browse.php?type='+type+"&lng=ru",args,"dialogWidth:600px; dialogHeight:500px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
}

// Проверяет, является ли переданный параметр ip синтаксически корректным IP-адресом
function check_ip(ip) {
    if (ip.match(/^([0-9]{1,3}\.){3}[0-9]{1,3}$/)==null) {
        return false;
    }
    else {
        var ip_arr = ip.split(".");
        if (parseInt(ip_arr[0])!=ip_arr[0] || parseInt(ip_arr[1])!=ip_arr[1] || parseInt(ip_arr[2])!=ip_arr[2] || parseInt(ip_arr[3])!=ip_arr[3]) {
            return false;
        }
        if (ip_arr[0]>255 || ip_arr[1]>255 || ip_arr[2]>255 || ip_arr[3]>255) {
            return false;
        }
    }
    return true;
}

// Проверяет корректность ввода MAC-адреса
function check_mac(mac) {
    if (mac.match(/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/)==null) {
        return false;
    }
    return true;
}

// Проверяет корректность ввода номера порта
function check_port(port) {
    if (port.match(/^[0-9]{1,5}$/)==null)
        return false;
    if (port<=0)
        return false;
    return true;
}

// Двигает элемент списка SELECT с указанным индексом вверх или вниз
function move_select_item(index,select_item,direction) {
    if (direction=="up") {
        if (index<0)
            return 0;
        var swp = select_item.options[index];
        select_item.add(swp,select_item.options[index-1]);
    }
    else {
        if (index>select_item.options.length-1)
            return 0;
        var swp = select_item.options[index];
        select_item.add(swp,select_item.options[index+2]);
    }
}

// Перемещает указанный элемент из одного списка в другой
function move_select_item_to_list(index,list1,list2) {
    var item = list1.options[index];
    list2.add(item,null);
//    list1.remove(index);
}

function cloneObject(o) {
 if(!o || 'object' !== typeof o)  {
   return o;
 }
 var p, v, c=new Array;
 p=0;
 for(p in o) {
 if(o.hasOwnProperty(p)) {
  v = o[p];
  if(v && ('object' === typeof v || 'array' === typeof v)) {
    c[p] = cloneObject(v);
  }
  else {
    c[p] = v;
  }
 }
}
 return c;
}

function array_merge()
{
     var res = {};
     for (var i = 0; i < arguments.length; i++)
         for (id in arguments[i]) if (typeof(arguments[i][id])=='object' || typeof(arguments[i][id])=='array') res[id]=array_merge(res[id], arguments[i][id]); else res[id]=arguments[i][id];
     return res;
}

Array.prototype.swap = function (a, b) {
    if (this[a] && this[b]) {
        var c = this[a];
        this[a] = this[b];
        this[b] = c;
    }
    return this;
};

Array.prototype.toObject = function() {
	var result = new Object;
	var c=null;
	for (c in this) {
		if (typeof this[c] != "function") {
			if (typeof this[c] == "object" && this[c].length!=null)
				result[c] = this[c].toObject();
			else
				result[c] = this[c];
		}
	}
	return result;
};

Array.prototype.indexOf = function(value) {
    var c=0;
    for (c in this) {
    	if (typeof this[c] == "function")
    		continue;
        if (this[c]==value)
            return c;
    }
    return -1;
};

Array.prototype.indexOf2 = function(value) {
    var c=0;
    for (c=0;c<this.length;c++) {
        if (this[c]==value)
            return c;
    }
    return -1;
};

Array.prototype.diff = function(value) {
    var result = new Array;
    var v=null;
    for (v in value) {
        if (typeof value[v] == "function")
            continue;
        if (this.indexOf(value[v])==-1)
            result[result.length] = value[v];
    }
    return result;
};

Array.prototype.diff2 = function(value) {
    var result = new Array;
    var i=0;
    for (i=0;i<value.length;i++) {
        if (this.indexOf2(value[i])==-1)
            result[result.length] = value[i];
    }
    return result;
};

Array.prototype.intersect = function(value) {
    var result = new Array;
    var v=null;
    for (v in value) {
        if (typeof value[v] == "function")
            continue;
        if (this.indexOf(value[v])!=-1)
            result[result.length] = value[v];
    }
    return result;
};

Array.prototype.intersect2 = function(value) {
    var result = new Array;
    var v=0;
    for (v=0;v<value.length;v++) {
        if (this.indexOf2(value[v])!=-1)
            result[v] = value[v];
    }
    return result;
};

Array.prototype.union = function(value) {
    var result0 = this.intersect(value);
    var result1 = this.diff(value);
    var result2 = value.diff(this);
    var result = new Array;
    var v=null;
    for (v in result0) {
        if (typeof result0[v] == "function")
            continue;
        result[result.length] = result0[v];
    }
    for (v in result1) {
        if (typeof result1[v] == "function")
            continue;
        result[result.length] = result1[v];
    }
    for (v in result2) {
        if (typeof result2[v] == "function")
            continue;
        result[result.length] = result2[v];
    }
    return result;
};

function objToArr(obje) {
	var result = new Array;
	var o=null;
	for (o in obje) {
		if (typeof obje[o] != "function") {
			result[o] = obje[o];
		}
	}
	return result;
}

function cursorPos(event) {
      var x = y = 0;
      if (document.attachEvent != null) { 
            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
      } else if (!document.attachEvent && document.addEventListener) {
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
      } else {
            
      }
      return {x:x, y:y};

      return {x:x, y:y};
}   
 
function getScrollY() 
{
    var scrollY = 0;    
    if (typeof window.pageYOffset == "number") {
        scrollY = window.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {
        scrollY = document.documentElement.scrollTop;
    }  else if (document.body && document.body.scrollTop) {
        scrollY = document.body.scrollTop; 
    } else if (window.scrollY) {
        scrollY = window.scrollY;
    }            
    return scrollY;
}        

// Преобразование IP-адреса в число
function ip2int(ip) {
   var a=ip.split(".");
   return parseInt(a[0])*256*256*256+parseInt(a[1])*256*256+parseInt(a[2])*256+parseInt(a[3]);
}

function l10n(phrase) {
    if (l10n_dict!=null) {
        if (l10n_dict[phrase]!=null)
            return l10n_dict[phrase];
    }
    return phrase;
}

function getNextArrayIndex(arr1,idx) {
	var pred_idx = null;
	var o=null;
	for (o in arr1) {
		if (typeof arr1[o] == "function")
			continue;
		if (pred_idx==idx)
			return o;
		pred_idx=o;
	}
	return null;
}

function getPrevArrayIndex(arr1,idx) {
	var pred_idx = null;
	var o=null;
	for (o in arr1) {
		if (typeof arr1[o] == "function")
			continue;
		if (o==idx)
			return pred_idx;
		pred_idx=o;
	}
	return null;
}

function destroyChildren(node)
{
  while (node.firstChild)
      node.removeChild(node.firstChild);
}

function getObjectData(srcObj) {
	var result = new Object;
	var o=null;
	for (o in srcObj) {
		if (typeof srcObj[o] != "function" && typeof srcObj[o]!= "object") {
			result[o] = srcObj[o];
		}
	}
	return result;
}

function processEvent(event) {
	$O(eventTarget(event).getAttribute("object"),"")[event["func"]](event);
}

function addEvent(element, type, handler) {
	// присвоение каждому обработчику события уникального ID
	if (!handler.$$guid) handler.$$guid = addEvent.guid++;
	// создание хэш-таблицы видов событий для элемента
	if (!element.events) element.events = {};
	// создание хэш-таблицы обработчиков событий для каждой пары
	// элемент-событие
	var handlers = element.events[type];
	if (!handlers) {
			handlers = element.events[type] = {};
			// сохранение существующего обработчика события
			// (если он существует)
			if (element["on" + type]) {
				handlers[0] = element["on" + type];
			}
	}
	// сохранение обработчика события в хэш-таблице
	handlers[handler.$$guid] = handler;
	// назначение глобального обработчика события для выполнения
	// всей работы
	element["on" + type] = handleEvent;
};
	
// счетчик, используемый для создания уникальных ID
addEvent.guid = 1;
	
function removeEvent(element, type, handler) {
	// удаление обработчика события из хэш-таблицы
	if (element.events && element.events[type]) {
	delete element.events[type][handler.$$guid];
	}
};
	
function handleEvent(event) {
	var returnValue = true;
	// захват объекта события (IE использует глобальный объект события)
	event = event || fixEvent(window.event);
	// получение ссылки на хэш-таблицу обработчиков событий
	var handlers = this.events[event.type];
	// выполнение каждого обработчика события
	for (var i in handlers) {
		this.$$handleEvent = handlers[i];
		if (this.$$handleEvent(event) === false) {
				returnValue = false;
		}
	}
	return returnValue;
};

// Добавление к объекту события IE некоторых "упущенных" методов
function fixEvent(event) {
	// добавление стандартных методов событий W3C
	event.preventDefault = fixEvent.preventDefault;
	event.stopPropagation = fixEvent.stopPropagation;
	return event;
};

fixEvent.preventDefault = function() {
	this.returnValue = false;
};
	
fixEvent.stopPropagation = function() {
	this.cancelBubble = true;
};

function eventTarget(event) {
	if (event["element"]!=null)
		return event.element();
	else
		return event.target;
};

var globalTopWindow = getTopWindow();
var topWindow = globalTopWindow;
var wm = getWindowManager;
current_context_menu = null;
arra = new Array;
blur_object = null;
blur_item = null;
blur_error = "";