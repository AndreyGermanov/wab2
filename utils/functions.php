<?php
// Функция возвращает второй вариант, если оба варианта установлено или первый, если установлен только второй.
function oneOfTwo($one,$two) {
    if (isset($two))
        return $two;
    else
        return $one;
}

// Возвращает из строки $string с разделителями $delimiter массив, у которого
// ключи равны значениям
function getHashFromString($string,$delimiter=" ") {
    $arr = explode($delimiter,$string);
    $result = array();
    foreach ($arr as $value) {
        $result[$value] = $value;
    }
    return $result;
}

// Преобразование числа в IP-адрес
function int2ip($i) {
   $d[0]=(int)($i/256/256/256);
   $d[1]=(int)(($i-$d[0]*256*256*256)/256/256);
   $d[2]=(int)(($i-$d[0]*256*256*256-$d[1]*256*256)/256);
   $d[3]=$i-$d[0]*256*256*256-$d[1]*256*256-$d[2]*256;
   return "$d[0].$d[1].$d[2].$d[3]";
}

// Преобразование IP-адреса в число
function ip2int($ip) {
   $a=explode(".",$ip);
   return $a[0]*256*256*256+$a[1]*256*256+$a[2]*256+$a[3];
}

function l10n($phrase) {
    global $l10n;
    if (isset($l10n) and is_array($l10n)) {
        if (isset($l10n[strtoupper($phrase)])) 
            return $l10n[strtoupper($phrase)];
        else
            return $phrase;            
    } 
    return $phrase;
}

// Сравнивает содержимое двух каталогов и выводит список полных путей к файлам,
// которые отличаются
function diffDirs($dir1,$dir2,$result=array()) {
    if (is_dir($dir1)) {
        if ($dh = opendir($dir1)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!=".." and $file!=".AppleDouble" and $file!=".AppleDB" and $file != ".AppleDesktop") {
                    if (is_dir($dir1."/".$file) and $file[0]!="@") {
                        $result = diffDirs($dir1."/".$file,$dir2."/".$file,$result);
                    } else if (is_file($dir1."/".$file)) {
                        if (file_exists($dir1."/".$file) and !file_exists($dir2."/".$file)) {
                            echo $dir1."/".$file."\n";
                            $result[] = $dir1."/".$file;
                        }
                        else {
                            if ((@filemtime($dir1."/".$file)!=@filemtime($dir2."/".$file)) or (@filesize($dir1."/".$file)!=@filesize($dir2."/".$file))) {
                                $result[] = $dir1."/".$file;
                                echo $dir1."/".$file."\n";
                            }
                        }                            
                    }
                }
            }
            closedir($dh);
        }
    }
    return $result;
}

// Получаем начало дня из временной метки
function getBeginOfDay($time) {
	//return $time;
    $arr = getdate($time);
    return mktime(0,0,0,$arr["mon"],$arr["mday"],$arr["year"]);
}

// Получаем конец дня из временной метки
function getEndOfDay($time) {
	//return $time;
    $arr = getdate($time);
    return mktime(23,59,59,$arr["mon"],$arr["mday"],$arr["year"]);
}

function getPrintBlocks($tpl) {
	$arr = explode("\n",$tpl);
	$result = array();
	$current_block = "";
	foreach ($arr as $line) {
		$matches = array();
		if (preg_match("/\<\!\-\- block\=(.*) \-\-\>/",$line,$matches)) {
			$current_block = $matches[1];
			$result[$current_block] = "";
			continue;
		}
		if ($current_block!="") {
			$result[$current_block].=$line."\n";
		}
	}
	return $result;
}

function linkPath($path) {
    // Формируем представление переданного пути так, чтобы он мог
    // фигурировать в гиперссылках
    $linkPath = $path;
    $linkPath = str_replace("%","%25",$linkPath);
    $linkPath = str_replace('~','%7e',$linkPath);
    $linkPath = str_replace("#","%23",$linkPath);
    $linkPath = str_replace("//","/",$linkPath);
    return $linkPath;
}

function getClientClass($class_name,$dir="") {
	$class_name = str_replace("/","",str_replace("?","",$class_name));
	if ($dir=="")
		$dir = "scripts/classes/";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file!="." and $file!="..") {
					if (is_dir($dir."/".$file)) {
						$result=getClientClass($class_name,$dir."/".$file);
						if (file_exists($result)) {
							return str_replace("//","/",$result);
						}
					} else {
						if (trim(str_replace(".js","",$file))==$class_name) {
							$result=$dir."/".$file;
							return str_replace("//","/",$result);
						}
					}
				}
			}
			closedir($dh);
		}
	}
}
 
/**
 *  ФУНКЦИИ ДЛЯ РАБОТЫ С МЕТАДАННЫМИ
 */

/** 
 * Функция возвращает код PHP для создания массива
 * для переданного массива $array
 * 
 * @param массив $array
 * @param смещение $ident
 * @return string
 */
function getArrayCode($array,$ident="",$explode=true) {
	$str  = "array\n";
	$str .= $ident."(\n";
	$strings = array();
	$arr = array('$' => '\$', '"' => '\"');
	foreach ($array as $key=>$value) {
		if (!is_array($value)) {
			if (gettype($key)=="string") {				
				if ($key!="file")
					$strings[] = $ident."\t".'"'.$key.'" => "'.strtr($value,$arr).'"';
				else
					$strings[] = $ident."\t".'"file" => __FILE__';
			}
			else
				$strings[] = $ident."\t".'"'.strtr($value,$arr).'"';
		}
		else
			if ($explode or !isset($value["collection"]))
				$strings[] = $ident."\t".'"'.$key.'" => '.getArrayCode($value,$ident."\t",$explode);
			else {
				if (isset($value["name"]))
					$strings[] = $ident."\t".'"'.$key.'" => $'.$value["collection"].'["'.$value["name"].'"]';
				else
					$strings[] = $ident."\t".'"'.$key.'" => $'.$value["collection"];
			}
				
	}	
	$str .= implode(",\n",$strings)."\n".$ident.")";
	return $str;
}

/**
 * Функция возвращает элементы массива $array1, которые отличаются от элементов
 * массива $array2. Каждый такой элемент возвращается в формате
 * 
 * ['ключ']['ключ1']['ключ2'] = 'значение'
 * 
 * @param массив $array1
 * @param массив $array2
 * @param строка $path
 * @return array
 */
function getDiffArrayItems($array1,$array2,$path="") {
	$result = array();
	$arr = array();// array("'","\'");
	foreach ($array1 as $key=>$value) {
		if ($key=="file" or $key=="md_name" or $key=="_empty_" or $key=="settings")
			continue;
		if (is_object($value))
			$value = (array)$value;
		if (!is_array($value)) {			
			if (@$array2[$key]!=$array1[$key])
				$result[$path."['".$key."']"] = $path."['".$key."']='".strtr($value,$arr)."';";
		} else {
			if (@$value["name"] == @$array2[$key]["name"] and @$value["collection"] == @$array2[$key]["collection"])
				$result = array_merge($result,getDiffArrayItems($value,@$array2[$key],$path."['".$key."']"));
			else 
				$result[$path."['".$key."']"] = $path."['".$key."']=\$".@$value["collection"]."['".@$value["name"]."'];";				
		}
	}
	return $result;
}

/**
 * Функция возвращает список отличий массива1 от массива2
 * в формате
 * 
 * $array_name['ключ']['ключ1']['ключ2'] = 'значение';
 * .
 * .
 * unset($array_name['ключ']['ключ1']['ключ2']);
 * 
 * @param строка $array_name
 * @param массив $array1
 * @param массив $array2
 * @return string
 */
function getDiffArray($array_name,$array1,$array2) {
	$set_array = getDiffArrayItems($array1,$array2);
	$unset_array = getDiffArrayItems($array2,$array1);
	$unset_array = array_diff_key($unset_array,$set_array);
	$strings = array();
	foreach ($set_array as $value)
		$strings[] = '$'.$array_name.$value;
	foreach ($unset_array as $key=>$value) {
		$strings[] = "unset(".'$'.$array_name.$key.");";
	}
	return implode("\n",$strings);
}

/** 
 * Функция возвращает массив метаданных,
 * описания которых хранятся в указанном файле
 * 
 * Формат каждой строки
 * 
 * $result['класс-метаданных']['ключ'] = 'значение';
 * 
 * @param строка $file
 * @return array
 */
function getMetadataInFile($file) {
	global $metadata_classes;
	$result = array();
	foreach($metadata_classes as $class) {
		if (isset($GLOBALS[$class]["file"])) {
			if ($GLOBALS[$class]["file"]==$file) {				
				$result[$class] = $GLOBALS[$class];				
			}
		} else {			
			foreach($GLOBALS[$class] as $key=>$value) {
				if (@$value["file"]==$file) {
					$result[$class][$key] = $value;
				}
			}
		}
	}
	return $result;
}

function getMetadataString($array,$explode=true) {
	
	$strings = array();
	foreach($array as $class=>$item) {
		if (isset($item["file"])) {
			$strings[] = '$'.$class.' = '."\n".getArrayCode($item,"",$explode);
		} else {
			foreach ($item as $key=>$value) {
				$strings[] = '$'.$class.'["'.$key.'"] = '."\n".getArrayCode($value,"",$explode);
			}
		}
	}
	return implode(";\n\n",$strings).";";
	
}

function mergeArrays($array1,$array2) {
	if (count($array1)==0)
		return $array2;
	if (count($array2)==0)
		return $array1;
	$array1 = (array)$array1;
	$array2 = (array)$array2;
	foreach ($array2 as $key=>$value) {
		if (is_object($value))
			$value = (array)$value;
		if (!is_array($value))
			$array1[$key] = $value;
		else {
			if (!isset($array1[$key]))
				$array1[$key] = array();
			$array1[$key] = mergeArrays($array1[$key],$value);
		}
	}
	return $array1;
}

function cleanText($text) {
	$args = array("\r" => "", "\n" => "", "\t" => "", "  " => "");
	return strtr($text,$args);
	
}

function saveMetadataFile($file) {
	$str = "<?php\n".getMetadataString(getMetadataInFile($file))."\n?>";
	file_put_contents($file,$str);	
}

function getMetadataFieldParamsString($fieldName) {
	if (isset($GLOBALS["fields"][$fieldName]) and isset($GLOBALS["fields"][$fieldName]["params"])) {
		$params = $GLOBALS["fields"][$fieldName]["params"];
		return json_encode($params);
	}
	return "";
}

function execAlgo($name,$params) {
	global $Objects;
	$algo = $Objects->get("MetadataObjectCode_Module_Name_100_".$name);
	return $algo->exec($params);
}

function getObjectsIndexes($list) {
	$result = array();
	foreach ($list as $value)
		if (is_object($value))
			$result[$value->getId()] = $value->getId();
		else
			$result[$value] = $value;
	return $result;
}

function trimSpaces($string) {
	while (1==1) {
		$result = str_replace("  "," ",$string);
		if ($result==str_replace("  "," ",$string))
			break;
	}
	return $result;
}

$url_encode = array("!" => "%21", '"' => "%22", '#' => "%23", '$' => '%24', '%' => '%25', '&' => '%26', "'" => '%27', '(' => '%28', ')' => '%29',
               '*' => '%2a', '+' => '%2b', ','=> '%2c','-' => '%2d', '.'=>'%2e', '/' => '%2f', '`' => '%60', ':' => '%3a', ';' => '%3b', '<' => '%3c',
               '=' => '%3d', '>' => '%3e', '?' => '%3f', '@' => '%40', '[' => '%5b', "\\" => '%5c', ']' => '%5d', '^' => '%5e', '_' => '%5f');
               
function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array(
        array('копейка' ,'копейки' ,'копеек',	 1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );

    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { 
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; 
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));

            $out[] = $hundred[$i1];
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; 
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3];
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        }
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); 
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); 
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function getFmPath($path) {
	
	global $Objects;
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();	
		
	// Разбор переданного пути к файлу	
	$path = strip_tags(trim($path));
	
	// Сохранение содержимого пути до того, как начинать преобразования
	// и анализ
	$origPath = $path;
		
	// Получение информации о сервере и о его общих папках
	if ($app->defaultModule=="MystixController") {
		$fileServer = $Objects->get("FileServer_ControllerApplication_Network_Shares");
		$fileServer->load();
		$fileServer->loadShares();
	
		// Корень, в котором хранятся общие папки
		$shares_root = $fileServer->shares_root;
	
		// Общая папка по умолчанию, которая есть на каждом сервере
		$share = "files";
	} else {
		$moduleClass = $app->user->modules[$app->user->config['appconfig']['defaultModule']]['class'];
		$fm = $Objects->get("FileManager_".$moduleClass."_files");
		$fm->setRole();
		$shares_root = $fm->role["rootPath"];
		$share = "";
	}
	
	// Разбираем переданный путь к файлу, в зависимости от типа просмотра.
	if ($path!="") {
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
			if (isset($fileServer)) {
				$fileShare = $fileServer->containsPath($share);
				if (!$fileShare)
					exit;
			}

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
			if (isset($fileServer)) {
				$fileShare = $fileServer->containsPath($share);
				if (!$fileShare)
					exit;
			}
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
			if (isset($fileServer)) {
				$fileShare = $fileServer->containsPath($share);
				if (!$fileShare)
					exit;
			}

			// Указываем дальнейшему скрипту, что тип переданного пути
			// определен
			$finished = true;
		}
			
		// Теперь можно убрать все лишнее навсегда
		$path = str_replace("\\","/",$path);
		$path = str_replace("//","/",$path);
			
		// Проверяем 5-й вариант
		if (@$path{0}=="/") {
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
			 
	}
	
	// Формируем полный путь к файлу или папке на сервере
	$fullPath = @str_replace("//","/",$shares_root."/".$share."/".$path);
	
	return $fullPath;	
}

function getParams($str) {
	$arr = explode(",",$str);
	$result = array();
	foreach ($arr as $value) {
		$parts = explode("=",$value);
		$result[trim($parts[0])] = trim($parts[1]);
	}
	return $result;
}

function objectToArray($obj) {
	$obj = (array)$obj;
	foreach ($obj as $key=>$value) {
		if (is_object($value)) {
			$value = (array)$value;
			$obj[$key] = objectToArray($value);
		} else
			$obj[$key] = $value;
	}
	return $obj;	
}
?>