#!/usr/bin/perl -w
# Скрипт управления ротацией снапшотов.
# Author: Nevorotin Vadim aka Malamut
# Лицензия: GPLv3
 
use 5.010;
use Getopt::Long;	# Для разбора опций
 
# Библиотека с необходимыми функциями
require "{shadowCopyEnginePath}/libsnapshot.pm";
 
########################################
# Параметры тома для ротации снапшотов #
########################################
 
# Группа томов
$vg = '{shadowCopyVgName}';
# Логический том
$lv = '{shadowCopyLvName}';
# Точка монтирования
$path = '{snapshotsFolder}';
 
# Количество поддерживаемых снапшотов
$count = {snapshotsCount};
 
# Начальный размер снапшота, Gb
$sn_size = {snapshotSize};
# Предел заполнения до ресайза, %
$sn_limit = 80;
# Шаг увеличения снапшота при переполнении, Gb
$sn_add = 3;
 
#########################################
 
$clear = 0;
$rotate = 0;
$remount = 0;
$checksize = 0;

Getopt::Long::Configure ("bundling");			# Конфигурирование getopt дабы воспринимать склейку коротких аргументов
GetOptions(
	"clear|c" => \$clear,				# Удалить все снапшоты
	"rotate|r" => \$rotate,				# Провести ротацию
	"remount|m" => \$remount,			# Перемонтировать имеющиеся снапшоты
	"checksize|s" =>\$checksize,
	"help|h" => \$help);				# Помощь же
 
if (@ARGV or $help) {
	die "Usage: snapshots.pl [--clear|--rotate|--remount]\n\t-c = --clear\n\t-r = --rotate\n\t-m = --remount\n";
} elsif ($clear) {
	removeAllSnapshots($lv, $vg, $path);
} elsif ($rotate) {
	snapshotsRotate($lv, $vg, $path, $count, $sn_size, $sn_limit, $sn_add);
} elsif ($remount) {
	snapshotsRemount($lv, $vg, $path);
} elsif ($checksize) {
	%snapshots = getActive($lv,$vg); 
	foreach (@snapshots) {
		checkSize($_, $vg, $sn_limit, $sn_add);
	}
} else {
	die "Usage: snapshots.pl [--clear|--rotate|--remount]\n\t-c = --clear\n\t-r = --rotate\n\t-m = --remount\n";
}
