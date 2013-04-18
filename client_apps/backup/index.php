<?php
/**
 * ЛВА Конструктор WEB-приложений 2, версия 1.1.05, Конфигурация: "Контроллер"
 * Модуль просмотра резервных копий подсистемы теневого копирования.
 * ---------------------------------------------------------------------------
 * 
 * Модуль выполнен в виде отдельного Web-приложения, которое не должно быть 
 * в составе Панели управления сервером. Обычно приложение размещается на 
 * отдельном виртуальном хосте и может быть  доступно пользователям без 
 * авторизации, однако для его работы требуется связь с модулями "ЛВА 
 * Конструктор Web-приложений": в корне необходима ссылка на файл boot.php, 
 * ссылки на каталоги skins, classes и utils. Само приложение состоит из
 * двух файлов: index.php - само приложение, template.html - шаблон HTML-
 * разметки, в соответствии с которым приложение формирует вывод на экран.
 * Также в состав приложения входит папка mimetypes с изображениями mime-
 * типов файлов, которые используются в интерфейсе.
 * 
 * ---------------------------------------------------------------------------
 * (С) 2012 ООО "ЛВА". Все права защищены.
 * Данная программа зависит от платформы "ЛВА Конструктор Web-приложений 2",
 * может использоваться только совместно с этой платформой и на таких же
 * условиях.
 **/

	// Отключаем сообщения об ошибках
#	error_reporting(0);        

	// Подключаемся к панели управления от имени пользователя admin
	include_once "boot.php";
	$app = $Objects->get("Application");
	$app->User = "admin";
	if (!$app->initiated)
		$app->initModules();
	
	// Загрузка шаблона разметки страницы
	$blocks = getPrintBlocks(file_get_contents("template.html"));

	// Получение информации о текущей странице
	if (isset($_GET["page"]))
		$page = $_GET["page"];
	else
		$page = 1;

	// Количество элементов на странице
	$itemsPerPage = 20;
	
	// Разбор переданного пути к файлу
	
	// Если ничего не передано, значит путь пустой
	if (!isset($_GET["path"])) 
		$path = "";
	else 
		$path = strip_tags(trim($_GET["path"]));

	// Сохранение содержимого пути до того, как начинать преобразования
	// и анализ
	$origPath = $path;
	
	// Определяем режим просмотра. Их может быть два:
	// 1. backup - режим просмотра списка всех резервных копий файла/папки
	// 2. path - режим просмотра содержимого резервной копии папки
	//
	// Если режим просмотра не передан, то устанавливаем
	// его в backup       
	if (isset($_GET["viewMode"]))
		$viewMode = $_GET["viewMode"];
	else
		$viewMode = "backup";

	// Получение информации о сервере и о его общих папках
	$fileServer = $Objects->get("FileServer_ControllerApplication_Network_Shares");
	$fileServer->load();
	$fileServer->loadShares();
	
	// Корень, в котором хранятся общие папки
	$shares_root = $fileServer->shares_root;
	
	// Общая папка по умолчанию, которая есть на каждом сервере
	$share = "files";
	
	// Разбираем переданный путь к файлу, в зависимости от типа просмотра.        
	if ($path!="") {
		// Если тип просмотра "backup"
		if ($viewMode=="backup") {
	
			// Путь может быть передан в следующих формах:
			//  1. \\Имя-сервера\имя-общей-папки\<путь-к-файлу> - UNC-путь (по виндовому)
			//  2. Z:\<путь-к-файлу> - по виндовому с использованием сетевого диска
			//  3. smb|afp|sftp://<имя-пользователя>@<имя-сервера>/<имя-общей-папки>/<путь-к-файлу> - по линуксовому
			//  4. /<корень-общих-папок>/<общая-папка>/<путь-к-файлу> - по нормальному
			//  5. /общая-папка/путь-к-файлу - еще один правильный способ, используется текущий сервер
			//  6. \путь-к-файлу - просто путь к файлу, при условии что общая папка на сервере всего одна

			// Определение варианта

			// Проверяем 1-й вариант
			if (strpos($path, '\\\\')!==FALSE) {
				// Приведем путь к стандартному линуксовому
				$path = str_replace('\\\\','',$path);
				$path = str_replace('\\','/',$path);

				// Разделим путь на составные части
				$pathArray = explode("/",$path);
				// Имя сервера
				$hostname = array_shift($pathArray);
				// Имя общей папки
				$share = array_shift($pathArray);
				// Путь к файлу
				$path = implode("/",$pathArray);

				// Если указанной общей папки нет на сервере, значит просто выходим
				$fileShare = $fileServer->containsPath($share);
				if (!$fileShare)
					exit;

				// Указываем дальнейшему скрипту, что тип переданного пути
				// определен
				$finished = true;
			}
			// Проверяем 2-й вариант
			if (!isset($finished) and $path{1}==":") {                    
				// Убираем все лишнее из пути
				$pathArray = explode(":",$path);
				array_shift($pathArray);
				$path = implode(":",$pathArray);
				$path = str_replace("\\","/",$path);
				
				// Так как не указано имя общей папки, будем исходить
				// из общей папки "по умолчанию"
				$share = "files";
				
				// Указываем дальнейшему скрипту, что тип переданного пути
				// определен
				$finished = true;
			} else if (strpos($path,":")!==FALSE) {
			    $path = "/".str_replace(":","/",$path);
			}
			
			// Проверяем 3-й вариант
			if (!isset($finished) and strpos($path,"@")!=FALSE) {
			    // Убираем все лишнее из пути
			    $arr = explode("@",$path);
			    array_shift($arr);
			    $path = implode("@",$arr);
			    $path = str_replace($shares_root,"",$path);
			    // Разделим путь на составные части
			    $pathArray = explode("/",$path);
			    // Имя сервера
			    $hostname = array_shift($pathArray);
			    // Имя общей папки
			    $share = array_shift($pathArray);
			    // Путь к файлу
			    $path = implode("/",$pathArray);
			    // Если указанной общей папки нет на сервере, значит просто выходим
			    $fileShare = $fileServer->containsPath($share);
			    if (!$fileShare)
				exit;
			    // Указываем дальнейшему скрипту, что тип переданного пути
			    // определен
			    $finished = true;
			}
			
    			if (!isset($finished) and strpos($path,"/smb///")!==FALSE) {
        			$path = str_replace("/smb///","",$path);
        			$arr = explode("/",$path);
        			array_shift($arr);
        			$path ="/".implode("/",$arr);
    			}

    			if (!isset($finished) and strpos($path,"/file///")!==FALSE) {
        			$path = str_replace("/file///","",$path);
        			$arr = explode("/",$path);
        			array_shift($arr);
        			$path ="/".implode("/",$arr);
    			}
			
			// Проверяем 4-й вариант
			if (!isset($finished) and strpos($path,$shares_root)!==FALSE) {
				// Убираем все лишнее из пути
				$path = str_replace(str_replace("//","/",$shares_root."/"),"",$path);
				$path = str_replace("\\","/",$path);

				// Разделим путь на составные части
				$pathArray = explode("/",$path);
				// Имя общей папки
				$share = array_shift($pathArray);
				// Путь к файлу
				$path = implode("/",$pathArray);

				// Если указанной общей папки нет на сервере, значит просто выходим
				$fileShare = $fileServer->containsPath($share);
				if (!$fileShare)
					exit;

				// Указываем дальнейшему скрипту, что тип переданного пути
				// определен
				$finished = true;
			}
			
			// Теперь можно убрать все лишнее навсегда
			$path = str_replace("\\","/",$path);
			$path = str_replace("//","/",$path);
			
			// Проверяем 5-й вариант
			if ($path{0}=="/") {
				$path = substr($path,1);
			}
			if (!isset($finished)) {
				$pathArray = explode("/",$path);
				
				// Разделим путь на составные части
				$pathArray = explode("/",$path);
				// Имя общей папки
				$share = array_shift($pathArray);
				
				// Если указанной общей папки нет на сервере, значит просто выходим
				$fileShare = $fileServer->containsPath($share);
				if ($fileShare) {
					// Путь к файлу
					$path = implode("/",$pathArray);
					
					// Указываем дальнейшему скрипту, что тип переданного пути
					// определен
					$finished = true;
				}
		   }               
		   // Иначе считаем, что это 6-й вариант
		   if (!isset($finished)) {
			   $share = "files";
			   
				// Указываем дальнейшему скрипту, что тип переданного пути
				// определен
				$finished = true;
		   }
		} else {
			// Если режим просмотра равен "path", значит в качестве
			// пути передан путь к каталогу архива с резервными
			// копиями. Такой путь может передавать только сам скрипт,
			// а не пользователь, поэтому формат такого пути всего один:
			// 
			// /имя-общей-папки/@GMT-<дата-резервной-копии>/путь-к-файлу
			//
			// Именно его и нужно разбирать

			// Разбираем путь на составные части
			$pathArray = explode("/",$path);
			// Имя общей папки
			$share = array_shift($pathArray);
			// Время создания
			$dateTime = str_replace("@GMT-","",array_shift($pathArray));
			
			// Путь к файлу
			$path = implode("/",$pathArray);

			// Формируем время создания резервной копии в Российском представлении
			$datetimeArr = explode("-",$dateTime);
			$dateArr = explode(".",$datetimeArr[0]);
			$timeArr = explode(".",$datetimeArr[1]);
			$dateTimeRus = implode(".",array_reverse($dateArr))." ".implode(":",$timeArr);
		}            
	}
	
	// Формируем полный путь к файлу или папке на сервере
	if ($viewMode=="backup")
		$fullPath = @str_replace("//","/",$shares_root."/".$share."/".$path);
	else
		$fullPath = @str_replace("//","/",$shares_root."/".$share."/@GMT-".$dateTime."/".$path);
		
	// Формируем заголовочную часть страницы
	// Если находимся в режиме "path", то поле ввода оставляем пустым,
	// иначе отображаем ровно то, что запрашивал пользователь
	if ($viewMode=="path")
		$tplPath = "";
	else
		$tplPath = $origPath;
	
	// Формируем строку навигации в зависимости от режима просмотра.
	$pathArray = explode("/",$path);
	$linksArray = array();
	$str = "";
	foreach($pathArray as $value) {
		$str.="/".$value;
		if ($viewMode=="path") 
			$linksArray[] = "<a href='?viewMode=path&path=".$share."/@GMT-".$dateTime."/".linkPath($str)."'>".$value."</a>";
		else
			$linksArray[] = "<a href='?path=".$shares_root."/".$share."/".linkPath($str)."'>".$value."</a>";
	}
	$currentPath = str_replace("//","/",implode("/",$linksArray));
	
	// Формируем строку, описывающую текущий режим просмотра.
	// Также, в зависимости от режима просмотра выбирается режим видимости
	// колонки "Все резервные копии данного файла/каталога". Если
	// режим просмотра уже и так "backup", то эта колонка не отображается
	// С режимом видимости даты резервной копии все строго наоборот
	if ($viewMode=="backup") {
		$viewModeString = "Режим просмотра: все резервные копии файла/каталога";
		$displayAllBackups = "none";
		$displayDate = "";
	}
	else {
		$viewModeString = "Режим просмотра: содержимое резервной копии каталога от ".@$dateTimeRus;
		$displayAllBackups = "";
		$displayDate = "none";
	}
		
	// Записываем заголовок страницы в буфер
	$out = strtr($blocks["header"],array("{tplPath}" => str_replace("//","/",$tplPath),
										 "{viewModeString}" => $viewModeString));
	

	if ($path=="") {
		echo $out;
		exit;
	}

	// Формируем массив отображаемых каталогов и файлов в зависимости от режима просмотра
	$result = array();        
	// Выбираем исходный каталог, содержимое которого будем отображать
	if ($viewMode=="backup")
		$dir = $shares_root."/".$share;        
	else {
		$dir = $shares_root."/".$share."/@GMT-".$dateTime."/".$path;
		$date = $dateTime;
	}
	// Если в каталог можно войти
	if ($dh = opendir($dir)) {
		// получаем в цикле все файлы из этого каталога
		while (($file = readdir($dh)) !== false) {
			// пропускаем ненужные файлы
			if ($file=="." or $file=="..")
				continue;
			// Для режима просмотра "backup" отбираем все резервные копии
			// переданного в пути файла или каталога, находящиеся в 
			// подкаталогах @GMT-<дата-резервной-копии>, находящихся
			// в корне общей папки
			if ($viewMode=="backup") {
				// Формируем полный пути к текущему файлу или каталогу
				$fname = str_replace("//","/",$dir."/".$file."/".$path);
				if (substr($fname,-1)=="/")
					$fname = substr($fname,0,strlen($fname)-1);
				// Выделяем название файла или каталога из пути
				$title = array_pop(explode("/",$fname));
				if (strpos($file,"@GMT-")!==FALSE) {
					if (file_exists($fname)) {
						$date = str_replace("@GMT-","",$file);
						// Формируем время резервной копии в российском представлении
						$datetimeArr = explode("-",$date);
						$dateArr = explode(".",$datetimeArr[0]);
						$timeArr = explode(".",$datetimeArr[1]);
						$dateTimeRus = implode(".",array_reverse($dateArr))." ".implode(":",$timeArr);
						// Если это файл
						if (!is_dir($dir."/".$file."/".$path)) {
							// Определяем изображение, отображаемое для этого файла в первом столбце таблицы
							if (file_exists("mimetypes/".str_replace("/","-",@mime_content_type($fname).".png")))
									$img = "mimetypes/".str_replace("/","-",@mime_content_type($fname).".png");
							else
									$img = "mimetypes/text-plain.png";
							// Формируем ссылку на резервную копию файла
							$link = "root".linkPath($shares_root."/".$share."/".$file."/".$path);
							// Формируем ключ массива для сортировки
							$key = $date."_".strtoupper($title);
							// Класс CSS-спецификации для строк с файлами
							$rowClass = "cell";
							// Если есть текущая версия данной резервной копии, формируем ссылку
							// на нее
							if (file_exists($fullPath))
								$currentVersion = "<a href='root".linkPath($fullPath)."'>".$title."</a>";
							else
								$currentVersion = "Нет";
							// Формируем ссылку для списка всех резервных копий данного файла
							$allBackups = "<a href='?path=".linkPath($fullPath)."'>".$title."</a>";
						} else {
							// Если это каталог,
							// то изображение будет папкой
							$img = "mimetypes/folder.png";
							// Ссылка будет переводить в режим отображения "path", будет показывать содержимое
							// резервной копии этой папки за текущую дату
							$link = "?viewMode=path&path=".linkPath(str_replace("//","/",$share."/".$file."/".$path));
							// ключ для сортировки
							$key = "......".$date."_".strtoupper($title);
							// класс CSS-спецификации для строк с каталогами
							$rowClass = "cell";
							// для каталогов ссылка на текущую версию файла не формируется
							$currentVersion = "Каталог";
							// Формируем ссылку на все резервные копии данного каталога, хранящиеся на сервере
							$allBackups = "<a href='?path=".linkPath($fullPath)."'>".$title."</a>";
						}                            
					}                                                
				}
			} else {
				// в режиме отображения "path" просто показываем все файлы и папки, находящиеся в переданном в качестве параметра
				// path каталога
				$fname = str_replace("//","/",$dir."/".$file);
				if (substr($fname,-1)=="/")
					$fname = substr($fname,0,strlen($fname)-1);
				$title = array_pop(explode("/",$fname));
				if (file_exists($fname)) {
					// если это файл
					if (!is_dir($dir."/".$file)) {
							// Определяем изображение, отображаемое для этого файла в первом столбце таблицы
						if (file_exists("mimetypes/".str_replace("/","-",@mime_content_type($fname).".png")))
								$img = "mimetypes/".str_replace("/","-",@mime_content_type($fname).".png");
						else
								$img = "mimetypes/text-plain.png";
						// Формируем ссылку на резервную копию файла
						$link = "root".linkPath($fname);
						// Формируем ключ массива для сортировки
						$key = $date."_".strtoupper($title);
						// Класс CSS-спецификации для строк с файлами
						$rowClass = "cell";
						// Если есть текущая версия данной резервной копии, формируем ссылку
						// на нее
						if (file_exists($shares_root."/".$share."/".$path."/".$file))
							$currentVersion = "<a href='root".linkPath($shares_root."/".$share."/".$path."/".$file)."'>".$title."</a>";
						else
							$currentVersion = "Нет";
						// Формируем ссылку для списка всех резервных копий данного файла
						$allBackups = "<a href='?path=".linkPath($shares_root."/".$share."/".$path."/".$file)."'>".$title."</a>";
					} else {
						// Если это каталог,
						// то изображение будет папкой
						$img = "mimetypes/folder.png";
						// Ссылка будет переводить в режим отображения "path", будет показывать содержимое
						// резервной копии этой папки за текущую дату
						$link = "?viewMode=path&path=".linkPath($share."/@GMT-".$dateTime."/".$path."/".$file);
						// ключ для сортировки
						$key = "......".$date."_".strtoupper($title);
						// класс CSS-спецификации для строк с каталогами
						$rowClass = "cell";
						// для каталогов ссылка на текущую версию файла не формируется
						$currentVersion = "Каталог";
						// Формируем ссылку на все резервные копии данного каталога, хранящиеся на сервере
						$allBackups = "<a href='?path=".linkPath($shares_root."/".$share."/".$path."/".$file)."'>".$title."</a>";
					}                            
				}                                                
			}
			// Добавляем в массив файлов и каталогов строку, заполняя ее вычисленными параметрами
			if (@$rowClass!="") {
				$result[$key]["img"] = $img;
				$result[$key]["link"] = $link;
				$result[$key]["title"] = strip_tags($title);
				$result[$key]["rowClass"] = $rowClass;
				$result[$key]["backupDate"] = $dateTimeRus;
				$result[$key]["currentVersion"] = $currentVersion;
				$result[$key]["allBackups"] = $allBackups;
			}
		}
	}
	// Формируем строки таблицы из массива и добавляем их в буфер
	ksort($result);

    // Формируем и выводим панель страниц
	$pages = ceil(count($result)/$itemsPerPage);
	$page_nav = "";
	if ($pages>1) {
		$page_nav .= $blocks["page_nav_start"];
		for ($pageNum=1;$pageNum<=$pages;$pageNum++) {
			if ($pageNum!=$page)
				$page_nav .= strtr($blocks["page_nav_page"],array("{pageNumber}" => $pageNum, "{pageLink}" => "path=".@$_GET["path"]."&viewMode=".$viewMode."&page=".$pageNum))."&nbsp;";
			else
				$page_nav .= strtr($blocks["page_nav_selected_page"],array("{pageNumber}" => $pageNum))."&nbsp;";
		}
		if ($page!="All")
			$page_nav .= strtr($blocks["page_nav_page"],array("{pageNumber}" => "Все", "{pageLink}" => "path=".@$_GET["path"]."&viewMode=".$viewMode."&page=All"));
		else
			$page_nav .= strtr($blocks["page_nav_selected_page"],array("{pageNumber}" => "Все"));
		$page_nav .= $blocks["page_nav_end"];
	}
	$out .= $page_nav;

	// Выводим заголовок таблицы
	$out .= strtr($blocks["table_header"],array("{displayAllBackups}" => $displayAllBackups,"{displayDate}" => $displayDate,"{currentPath}" => str_replace("//","/",$currentPath)));

	// Вычисляем, какие строки должны попасть на данную страницу
	if (isset($page) and $page!="All") {
			$result = array_slice($result,($page-1)*$itemsPerPage,$itemsPerPage);
	}

	// Выводим строки таблицы в буфер
	foreach ($result as $row) {
		$out .= strtr($blocks["row"],array("{img}" => $row["img"], "{link}" => $row["link"],"{title}" =>$row["title"], "{rowClass}" => $row["rowClass"], 
										   "{backupDate}" => $row["backupDate"],"{currentVersion}" => $row["currentVersion"],"{allBackups}" => $row["allBackups"],
										   "{displayAllBackups}" => $displayAllBackups,
										   "{displayDate}" => $displayDate));
	}

	// Выводим подвал таблицы в буфер
	$out .= $blocks["table_footer"];
	// Дублируем панель навигации под таблицей
	$out .= $page_nav;
	// Выводим подвал в буфер
	$out .= $blocks["footer"];
	
	// Выводим содержимое буфера на экран
	echo $out;
?>
