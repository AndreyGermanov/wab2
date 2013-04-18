<?php
$codes["secondsToDuration"] = 
array
(
	"file" => __FILE__,
	"comment" => "Алгоритм принимает в ка",
	"metaTitle" => "Алгоримт преобразует число секунд в удобочитаемую продолжительность времени",
	"params" => array
	(
		"seconds" => "1000",
		"returnType" => "string"
	),
	"code" => "\$seconds = \$input[\"seconds\"];
if (!isset(\$input[\"returnType\"]))
	\$returnType = \"string\";
else
	\$returnType = \$input[\"returnType\"];
\$resultArray = array();
\$resultString = \"\";
if (floor(\$seconds/86400)>0) {
	\$resultArray[\"days\"] = floor(\$seconds/86400);
	\$resultString .= floor(\$seconds/86400).\" д. \";
	\$seconds = \$seconds - \$resultArray[\"days\"]*86400;
}
if (floor(\$seconds/3600)>0) {
	\$resultArray[\"hours\"] = floor(\$seconds/3600);
	\$resultString .= floor(\$seconds/3600).\" ч. \";
	\$seconds = \$seconds - \$resultArray[\"hours\"]*3600;
}
if (floor(\$seconds/60)>0) {
	\$resultArray[\"minutes\"] = floor(\$seconds/60);
	\$resultString .= floor(\$seconds/60).\" м. \";
	\$seconds = \$seconds - \$resultArray[\"minutes\"]*60;
}
if (\$seconds>0) {
	\$resultArray[\"seconds\"] = \$seconds;
	\$resultString .= \$seconds.\" с. \";
}

if (\$returnType==\"string\")
	return \$resultString;
else
	return \$resultArray;"
);

$codes["getUserActivityTime"] = 
array
(
	"file" => __FILE__,
	"comment" => "",
	"metaTitle" => "Получает время активности пользователя либо в виде строки, либо в виде массива, либо в виде секунд",
	"params" => array
	(
		"user" => "",
		"returnType" => "seconds"
	)
);

$codes["isUserActive"] = 
array
(
	"file" => __FILE__,
	"comment" => "",
	"metaTitle" => "Определяет, активен ли переданный пользователь в данный момент",
	"params" => array
	(
		"user" => ""
	)
);

$codeGroups["utils"] = 
array
(
	"metaTitle" => "Утилиты",
	"file" => __FILE__,
	"name" => "utils",
	"collection" => "codeGroups",
	"fields" => array
	(
		"secondsToDuration",
		"getUserActivityTime",
		"isUserActive"
	)
);
?>