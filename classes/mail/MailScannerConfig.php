<?php
/* 
 * Класс, позволяющий работать с настройками сканера MailScanner.
 *
 * Он позволяет читать и изменять любые настройки файла /etc/MailScanner/MailScanner.conf,
 * а также черного и белого списков адресов /etc/MailScanner/rules/spam.whitelist.rules.
 *
 */
 
class MailScannerConfig extends WABEntity {
    public $options = array();
    public $categories = array();
    public $translations = array();
    public $descriptions = array();
    public $variables = array();
    public $whitelist_rules_array = array();
    public $blacklist_rules_array = array();

    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->skinPath = $app->skinPath;

        $this->template = "templates/mail/MailScannerConfig.html";
        $this->css = $this->skinPath."styles/MailScannerConfig.css";
        $this->handler = "scripts/handlers/mail/MailScannerConfig.js";        
        $this->icon = $this->skinPath."images/Tree/content_filter.png";

        $this->tabs_string = "settings|Основные|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "whitelist|Белый список|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "blacklist|Черный список|".$this->skinPath."images/spacer.gif";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name;
        $this->active_tab = "settings";
        $this->width = "600";
        $this->height = "400";
        $this->overrided = "width,height";
        $this->loaded = false;
        $this->clientClass = "MailScannerTable";
        $this->parentClientClasses = "Entity";        
    }

    function fillTranslations() {
        $this->translations[strtoupper('System settings')] = 'Основные системные параметры';
        $this->translations[strtoupper('Variables')] = 'Переменные';
        $this->translations[strtoupper('Incoming Work Dir Settings')] = 'Параметры каталога для обработки входящей почты';
        $this->translations[strtoupper('Quarantine and Archive Settings')] = 'Параметры карантина и архивирования';
        $this->translations[strtoupper('Processing Incoming Mail')] = 'Параметры обработки входящей почты';
        $this->translations[strtoupper('Virus Scanning and Vulnerability Testing')] = 'Параметры сканирования вирусов и уязвимостей';
        $this->translations[strtoupper('Options specific to Sophos Anti-Virus')] = 'Параметры антивируса Sophos';
        $this->translations[strtoupper('Options specific to ClamAV Anti-Virus')] = 'Параметры антивируса ClamAV';
        $this->translations[strtoupper('Options specific to F-Protd-6 Anti-Virus')] = 'Параметры антивируса F-Protd-6 Anti-Virus';
        $this->translations[strtoupper('Removing/Logging dangerous or potentially offensive content')] = 'Параметры протоколирования и удаления потенциально опасного содержимого';
        $this->translations[strtoupper('Attachment Filename Checking')] = 'Параметры проверки вложенных в письма файлов';
        $this->translations[strtoupper('Reports and Responses')] = 'Параметры отчетов и ответов';
        $this->translations[strtoupper('Changes to Message Headers')] = 'Параметры внесения изменений в заголовки сообщения';
        $this->translations[strtoupper('Notifications back to the senders of blocked messages')] = 'Параметры оповещений для отправителей';
        $this->translations[strtoupper('Changes to the Subject: line')] = 'Параметры внесения изменений в заголовок письма';
        $this->translations[strtoupper('Changes to the Message Body')] = 'Параметры внесения изменений в тело письма';
        $this->translations[strtoupper('Mail Archiving and Monitoring')] = 'Параметры архивирования почты и мониторинга';
        $this->translations[strtoupper('Notices to System Administrators')] = 'Параметры отправки оповещений системным администраторам';
        $this->translations[strtoupper('Spam Detection and Virus Scanner Definitions')] = 'Списки антивирусов и черных списков для спама';
        $this->translations[strtoupper('Spam Detection and Spam Lists (DNS blocklists)')] = 'Параметры обнаружения спама и черных списков';
        $this->translations[strtoupper('Watermarking')] = 'Параметры водяных знаков';
        $this->translations[strtoupper('SpamAssassin')] = 'Параметры спам-фильтра SpamAssassin';
        $this->translations[strtoupper('Custom Spam Scanner Plugin')] = 'Параметры произвольных плагинов для проверки на спам';
        $this->translations[strtoupper('What to do with spam')] = 'Параметры действий со спамом';
        $this->translations[strtoupper('Logging')] = 'Параметры протоколирования';
        $this->translations[strtoupper('Advanced SpamAssassin Settings')] = 'Дополнительные параметры SpamAssassin';
        $this->translations[strtoupper('MCP (Message Content Protection)')] = 'Параметры проверки содержимого сообщений (MCP)';
        $this->translations[strtoupper('Advanced Settings')] = 'Дополнительные параметры';

        $this->translations[strtoupper('%org-name%')] = 'Краткое название организации';
        $this->translations[strtoupper('%org-long-name%')] = 'Полное название организации';
        $this->translations[strtoupper('%web-site%')] = 'Web-сайт организации';
        $this->translations[strtoupper('%etc-dir%')] = 'Путь к конфигурационным файлам';
        $this->translations[strtoupper('%report-dir%')] = 'Путь к файлам отчетов';
        $this->translations[strtoupper('%rules-dir%')] = 'Путь к файлам правил';
        $this->translations[strtoupper('%mcp-dir%')] = 'Путь к файлам, относящимся к защите содержимого сообщений (MCP)';
        $this->translations[strtoupper('MaxChildren')] = 'Максимальное количество дочерних процессов';
        $this->translations[strtoupper('RunAsUser')] = 'Запускать от имени пользователя';
        $this->translations[strtoupper('RunAsGroup')] = 'Запускать от имени группы';
        $this->translations[strtoupper('QueueScanInterval')] = 'Периодичность сканирования входящей почтовой очереди';
        $this->translations[strtoupper('IncomingQueueDir')] = 'Путь к входящей почтовой очереди';
        $this->translations[strtoupper('OutgoingQueueDir')] = 'Путь к исходящей почтовой очереди';
        $this->translations[strtoupper('IncomingWorkDir')] = 'Путь к каталогу временного хранения сканируемых сообщений';
        $this->translations[strtoupper('QuarantineDir')] = 'Путь к карантину';
        $this->translations[strtoupper('PIDfile')] = 'Путь к файлу с идентификатором запущенного процесса спам-фильтра';
        $this->translations[strtoupper('RestartEvery')] = 'Периодичность автоматического перезапуска процесса спам-фильтра';
        $this->translations[strtoupper('MTA')] = 'Тип используемого почтового сервера';
        $this->translations[strtoupper('Sendmail')] = 'Команда, которую спам-фильтр исполняет для отправки своих сообщений';
        $this->translations[strtoupper('Sendmail2')] = 'Команда, которую спам-фильтр исполняет для доставки исходящих очищенных сообщений (для Exim) ';
        $this->translations[strtoupper('IncomingWorkUser')] = 'Пользователь, от имени которого создаются временные файлы';
        $this->translations[strtoupper('IncomingWorkGroup')] = 'Группа, от имени которой создаются временные файлы';
        $this->translations[strtoupper('IncomingWorkPermissions')] = 'Права доступа, устанавливаемые временным файлам';
        $this->translations[strtoupper('IncomingWorkPermissions')] = 'Права доступа, устанавливаемые временным файлам';
        $this->translations[strtoupper('QuarantineUser')] = 'Пользователь, от имени которого файлы записываются в карантин';
        $this->translations[strtoupper('QuarantineGroup')] = 'Группа, от имени которой файлы записываются в карантин';
        $this->translations[strtoupper('QuarantinePermissions')] = 'Права доступа, с которыми файлы записываютсяв карантин';
        $this->translations[strtoupper('MaxUnscannedBytesPerScan')] = 'Максимальное количество байт, которые доставляет каждый процесс спам-фильтра';
        $this->translations[strtoupper('MaxUnsafeBytesPerScan')] = 'Максимальное количество потенциально опасного содержимого в байтах,которые будут распакованы и просканированы в одном процессе сканирования';
        $this->translations[strtoupper('MaxUnscannedMessagesPerScan')] = 'Максимальное количество сообщений, которые доставляет каждый процесс спам-фильтра';
        $this->translations[strtoupper('MaxUnsafeMessagesPerScan')] = 'Максимальное количество потенциально опасных сообщений,которые будут распакованы и просканированы в одном процессе сканирования';
        $this->translations[strtoupper('MaxNormalQueueSize')] = 'Максимальный размер очереди при работе в стандартном режиме';
        $this->translations[strtoupper('ScanMessages')] = 'Сканировать сообщения ?';
        $this->translations[strtoupper('RejectMessage')] = 'Блокировать сообщения ';
        $this->translations[strtoupper('MaximumAttachmentsPerMessage')] = 'Максимальное количество вложений в одном сообщении';
        $this->translations[strtoupper('ExpandTNEF')] = 'Распаковывать вложения TNEF';
        $this->translations[strtoupper('UseTNEFContents')] = 'Как добавлять распакованное TNEF-содержимое в сообщение';
        $this->translations[strtoupper('DeliverUnparsableTNEF')] = 'Доставлять ли TNEF-содержимое, которое невозможно обработать';
        $this->translations[strtoupper('TNEFExpander')] = 'Путь к команде распаковки TNEF';
        $this->translations[strtoupper('TNEFTimeout')] = 'Максимальное количество времени, отводимое распаковщику TNEF для обработки одного сообщения (сек)';
        $this->translations[strtoupper('FileCommand')] = 'Путь к команде file для определения типа файлов';
        $this->translations[strtoupper('FileTimeout')] = 'Максимальное количество времени, выделяемое команде file для обработки одного сообщения (сек)';
        $this->translations[strtoupper('GunzipCommand')] = 'Путь к команде Gunzip';
        $this->translations[strtoupper('GunzipTimeout')] = 'Максимальное количество времени, выделяемое команде gunzip для обработки одного сообщения (сек)';
        $this->translations[strtoupper('UnrarCommand')] = 'Путь к команде unrar';
        $this->translations[strtoupper('UnrarTimeout')] = 'Максимальное количество времени, выделяемое команде unrar для обработки одного сообщения (сек)';
        $this->translations[strtoupper('FindUU-EncodedFiles')] = 'Проверять UU-кодированные файлы';
        $this->translations[strtoupper('MaximumMessageSize')] = 'Максимальный размер сообщения в байтах';
        $this->translations[strtoupper('MaximumAttachmentSize')] = 'Максимальный размер вложения в байтах';
        $this->translations[strtoupper('MinimumAttachmentSize')] = 'Минимальный размер вложения в байтах';
        $this->translations[strtoupper('MaximumArchiveDepth')] = 'Максимальная глубина архива (zip,rar, документов Microsoft Office)';
        $this->translations[strtoupper('FindArchivesByContent')] = 'Определять архивы не по расширению, а по содержимому';
        $this->translations[strtoupper('ZipAttachments')] = 'Сжимать ли вложения ZIP-ом';
        $this->translations[strtoupper('AttachmentsZipFilename')] = 'Имя файла ZIP, в который будут сжаты вложения';
        $this->translations[strtoupper('AttachmentsMinTotalSizeToZip')] = 'Минимальный размер вложений, которые будут сжиматься ZIP-ом';
        $this->translations[strtoupper('AttachmentExtensionsNotToZip')] = 'Расширения файлов вложений, которые не будут сжиматься ZIP-ом';
        $this->translations[strtoupper('AddTextOfDoc')] = 'Вставлять ли содержимое вложений файлов Microsoft Word в сообщение в виде текста';
        $this->translations[strtoupper('Antiword')] = 'Путь к программе antiword, используемой для получение текста из файлов Word';
        $this->translations[strtoupper('AntiwordTimeout')] = 'Максимальное количество времени, выделяемое команде antiword для обработки одного сообщения (сек)';
        $this->translations[strtoupper('VirusScanning')] = 'Проверять письма на вирусы ?';
        $this->translations[strtoupper('VirusScanners')] = 'Используемый антивирус';
        $this->translations[strtoupper('VirusScannerTimeout')] = 'Максимальное количество времени, выделяемое антивирусу для обработки одного сообщения (сек)';
        $this->translations[strtoupper('DeliverDisinfectedFiles')] = 'Пытаться ли вылечивать файлы, в которых обнаружены вирусы и доставлять их';
        $this->translations[strtoupper('SilentViruses')] = 'Типы вирусов в сообщениях, об обнаружении которых спам-фильтр не будет сообщать отправителю';
        $this->translations[strtoupper('StillDeliverSilentViruses')] = 'Доставлять ли сообщения с вирусами указанных выше типов получателю после очистки';
        $this->translations[strtoupper('Non-ForgingViruses')] = 'Имена вирусов, об обнаружении которых спам-фильтр будет сообщать отправителю в любом случае';
        $this->translations[strtoupper('BlockEncryptedMessages')] = 'Блокировать зашифрованные сообщения';
        $this->translations[strtoupper('BlockUnencryptedMessages')] = 'Блокировать не зашифрованные сообщения';
        $this->translations[strtoupper('AllowPassword-ProtectedArchives')] = 'Разрешить архивы, защищенные паролем';
        $this->translations[strtoupper('CheckFilenamesInPassword-ProtectedArchives')] = 'Проверять имена файлов в архивах, защищенных паролем';
        $this->translations[strtoupper('AllowedSophosErrorMessages')] = 'Пропускать файлы со следующими ошибками, которые выдает антивирус Sophos';
        $this->translations[strtoupper('SophosIDEDir')] = 'Каталог (или ссылка), содержащий файлы *.ide для Sophos';
        $this->translations[strtoupper('SophosLibDir')] = 'Каталог (или ссылка), содержащий библиотеки *.so для Sophos';
        $this->translations[strtoupper('MonitorsForSophosUpdates')] = 'Файлы, которые SophosSAVI будет мониторить на предмет изменений в размере для обнаружения факта обновления антивируса';
        $this->translations[strtoupper('MonitorsForClamAVUpdates')] = 'Файлы, которые ClamAVModule будет мониторить на предмет изменений для обнаружения факта обновления антивируса';
        $this->translations[strtoupper('ClamAVmoduleMaximumRecursionLevel')] = 'Максимальный уровень вложенности в архивах';
        $this->translations[strtoupper('ClamAVmoduleMaximumFiles')] = 'Максимальное количество файлов, подаваемое на проверку за один раз';
        $this->translations[strtoupper('ClamAVmoduleMaximumFileSize')] = 'Максимальный размер файла, подаваемого на проверку';
        $this->translations[strtoupper('ClamAVmoduleMaximumCompressionRatio')] = 'Максимальная степень сжатия архива';
        $this->translations[strtoupper('ClamdPort')] = 'Порт, используемый демоном clamd';
        $this->translations[strtoupper('ClamdSocket')] = 'IP-адрес или сокет для связи с clamd';
        $this->translations[strtoupper('ClamdLockFile')] = 'Файл блокировок Clamd';
        $this->translations[strtoupper('ClamdUseThreads')] = 'Использовать потоки при работе clamd';
        $this->translations[strtoupper('ClamAVFullMessageScan')] = 'Проверка сообщений на вирусы целиком';
        $this->translations[strtoupper('FpscandPort')] = 'Номер порта, используемый демоном fpscand';
        $this->translations[strtoupper('DangerousContentScanning')] = 'Проверка опасного содержимого в сообщениях';
        $this->translations[strtoupper('AllowPartialMessages')] = 'Принимать сообщения, содержащие частичные вложения';
        $this->translations[strtoupper('AllowExternalMessageBodies')] = 'Принимать сообщения с телом, которое находится в Интернет';
        $this->translations[strtoupper('FindPhishingFraud')] = 'Включить борьбу с фишингом';
        $this->translations[strtoupper('AlsoFindNumericPhishing')] = 'Обнаруживать ссылки, в которых указаны IP-адреса вместо доменных имен';
        $this->translations[strtoupper('UseStricterPhishingNet')] = 'Включить более строгий режим обнаружения фишинга';
        $this->translations[strtoupper('HighlightPhishingFraud')] = 'Помечать фишинг в письмах';
        $this->translations[strtoupper('PhishingSafeSitesFile')] = 'Файл со списком сайтов, которые не будут проверяться на фишинг';
        $this->translations[strtoupper('PhishingBadSitesFile')] = 'Файл со списком сайтов, ссылки на которые всегда воспринимаются как фишинг';
        $this->translations[strtoupper('CountrySub-DomainsList')] = 'Файл со списком доменов всех стран';
        $this->translations[strtoupper('AllowIFrameTags')] = 'Что делать с тэгами IFRAME в сообщениях';
        $this->translations[strtoupper('AllowIFrameTags')] = 'Что делать с тэгами IFRAME в письмах';        
        $this->translations[strtoupper('AllowFormTags')] = 'Что делать с тэгами FORM в письмах';
        $this->translations[strtoupper('AllowScriptTags')] = 'Что делать с тэгами SCRIPT в письмах';
        $this->translations[strtoupper('AllowWebBugs')] = 'Что делать с маленькими изображениями (тэгами IMG), используемыми в качестве жучков';
        $this->translations[strtoupper('IgnoredWebBugFilenames')] = 'Список имен файлов, которые могут быть как имя файла в URL изображения-жучка';
        $this->translations[strtoupper('KnownWebBugServers')] = 'Список имен серверов (или их частей), о которых известно, что они содержат жучки';
        $this->translations[strtoupper('WebBugReplacement')] = 'Имя файла изображения, на которое будет заменем жучок';
        $this->translations[strtoupper('AllowObjectCodebaseTags')] = 'Что делать с тэгами <Object Codebase ...> или <Object Data=...>';
        $this->translations[strtoupper('ConvertDangerousHTMLToText')] = 'Конвертировать опасный HTML в текст';
        $this->translations[strtoupper('ConvertHTMLToText')] = 'Конвертировать все HTML-сообщения в текст';
        $this->translations[strtoupper('AllowFilenames')] = 'Пропускать следующие имена файлов в качестве вложений';
        $this->translations[strtoupper('DenyFilenames')] = 'Блокировать следующие имена файлов в качестве вложений';
        $this->translations[strtoupper('FilenameRules')] = 'Файл с правилами для имен файлов вложений';
        $this->translations[strtoupper('AllowFiletypes')] = 'Типы файлов, которые фильтр будет пропускать';
        $this->translations[strtoupper('AllowFileMIMETypes')] = 'MIME-типы файлов, которые фильтр будет пропускать';
        $this->translations[strtoupper('DenyFileTypes')] = 'Типы файлов, которые фильтр будет блокировать';
        $this->translations[strtoupper('DenyFileMIMETypes')] = 'MIME-типы файлов, которые фильтр будет блокировать';
        $this->translations[strtoupper('FiletypeRules')] = 'Файл правил для типов файлов';
        $this->translations[strtoupper('QuarantineInfections')] = 'Помещать инфицированные файлы в карантин';
        $this->translations[strtoupper('QuarantineSilentViruses')] = 'Помещать в карантин инфицированные файлы, о которых не сообщалось отправителю';
        $this->translations[strtoupper('QuarantineModifiedBody')] = 'Помещать в карантин тела обезвреженных сообщений';
        $this->translations[strtoupper('QuarantineWholeMessage')] = 'Помещать в карантин все сообщение целиком вместе с инфицированным содержимым';
        $this->translations[strtoupper('QuarantineWholeMessagesAsQueueFiles')] = 'Помещать в карантин все сообщение вместе с "сырыми" файлами очереди ';
        $this->translations[strtoupper('KeepSpamAndMCPArchiveClean')] = 'Не помещать в карантин спам, инфицированный вирусами';
        $this->translations[strtoupper('LanguageStrings')] = 'Путь к файлу строк, используемых для перевода на локальный язык.';
        $this->translations[strtoupper('RejectionReport')] = 'Путь к файлу с текстом, который будет отправляться отправителю при блокировке письма опцией Reject Message';
        $this->translations[strtoupper('DeletedBadContentMessageReport')] = 'Путь к файлу с текстом, который будет отправляться отправителю в случае если из сообщения удалены вложения';
        $this->translations[strtoupper('DeletedBadFilenameMessageReport')] = 'Путь к файлу с текстом, который будет отправляться отправителю в случае если из сообщения были удалены вложения, у которых были плохие имена файлов';
        $this->translations[strtoupper('DeletedVirusMessageReport')] = 'Путь к файлу с текстом, который будет отправляться отправителю в случае если в сообщении был обнаружен вирус';
        $this->translations[strtoupper('DeletedSizeMessageReport')] = 'Путь к файлу с текстом, который будет отправляться отправителю в случае если сообщение имело слишком большой размер';
        $this->translations[strtoupper('StoredBadContentMessageReport')] = 'Путь к файлу с текстом, который будет отправляться пользователю в случае если вложения сообщения были удалены из сообщения и записаны в карантин';
        $this->translations[strtoupper('StoredBadFilenameMessageReport')] = 'Путь к файлу с текстом, который будет отправляться пользователю в случае если вложения были удалены из сообщения и записаны в карантин по причине плохих имен файлов';
        $this->translations[strtoupper('StoredVirusMessageReport')] = 'Путь к файлу с текстом, который будет отправляться пользователю в случае если в сообщении вирус и оно было записано в карантин';
        $this->translations[strtoupper('StoredSizeMessageReport')] = 'Путь к файлу с текстом, который будет отправляться пользователю в случае если сообщение имеет слишком большой размер и было записано в карантин';
        $this->translations[strtoupper('DisinfectedReport')] = 'Путь к файлу с текстом о том, что вложения были обезврежены';
        $this->translations[strtoupper('InlineHTMLSignature')] = 'Путь к файлу с HTML-версией подписи для просканированных и обезвреженных спам-фильтром сообщений';
        $this->translations[strtoupper('InlineTextSignature')] = 'Путь к файлу с текстовой версией подписи для просканированных и обезвреженных спам-фильтром сообщений';
        $this->translations[strtoupper('SignatureImageFilename')] = 'Путь на сервере к файлу с картинкой, которая будет браться с сервера и прикрепляться к подписи в виде вложения';
        $this->translations[strtoupper('SignatureImage<img>Filename')] = 'Имя, которое будет иметь картинка, которая прикрепляется к каждому сообщению и отображается в подписи';
        $this->translations[strtoupper('InlineHTMLWarning')] = 'Путь к файлу с HTML-вариантом текста предупреждения, которое появляеться в сообщениях из которых были удалены вирусы';
        $this->translations[strtoupper('InlineTextWarning')] = 'Путь к файлу с текстом предупреждения, которое появляеться в сообщениях из которых были удалены вирусы';
        $this->translations[strtoupper('SenderContentReport')] = 'Путь к файлу с текстом, который передается отправителю в ответ на письма, содержащие опасное содержимое';
        $this->translations[strtoupper('SenderErrorReport')] = 'Путь к файлу с текстом, который передается отправителю в ответ на письма, содержащие ошибки';
        $this->translations[strtoupper('SenderBadFilenameReport')] = 'Путь к файлу с текстом, который передается отправителю в ответ на письма, содержащие запрещенные имена файлов';
        $this->translations[strtoupper('SenderVirusReport')] = 'Путь к файлу с текстом, который передается отправителю в ответ на письма, содержащие вирусы';
        $this->translations[strtoupper('SenderSizeReport')] = 'Путь к файлу с текстом, который передается отправителю в ответ на письма, имеющие слишком большой размер';
        $this->translations[strtoupper('HideIncomingWorkDir')] = 'Скрывать пути к папкам во всех отчетах, отправляемых антивирусами пользователям';
        $this->translations[strtoupper('IncludeScannerNameInReports')] = 'Включать название антивируса в каждый отчет';
        $this->translations[strtoupper('MailHeader')] = 'Заголовок, добавляемый ко всем письмам';
        $this->translations[strtoupper('SpamHeader')] = 'Заголовок, добавляемый ко всем письмам, которые являются спамом';
        $this->translations[strtoupper('InformationHeader')] = 'Информационный заголовок, добавляемый ко всем письмам';
        $this->translations[strtoupper('SpamScoreHeader')] = 'Заголовок, показывающий количество баллов, которые набрало письмо, являющееся спамом';
        $this->translations[strtoupper('AddEnvelopeFromHeader')] = 'Добавлять заголовок Envelope-From ?';
        $this->translations[strtoupper('AddEnvelopeToHeader')] = 'Добавлять заголовок Envelope-To ?';
        $this->translations[strtoupper('EnvelopeFromHeader')] = 'Заголовок Envelope-From';
        $this->translations[strtoupper('EnvelopeToHeader')] = 'Заголовок Envelope-To';
        $this->translations[strtoupper('IDHeader')] = 'Заголовок с идентификатором письма';
        $this->translations[strtoupper('IPProtocolVersionHeader')] = 'Заголовок с именем протокола (Ipv4 или Ipv6), по которому было передано это письмо';
        $this->translations[strtoupper('SpamScoreCharacter')] = 'Символ, которым отображаются баллы, начисленные письму спам-фильтром';
        $this->translations[strtoupper('SpamScoreNumberInsteadOfStars')] = 'Отображать количество баллов числом, а не символами';
        $this->translations[strtoupper('MinimumStarsIfOnSpamList')] = 'Минимальный балл, при достижении которого письмо попадает в Spam List';
        $this->translations[strtoupper('CleanHeaderValue')] = 'Заголовок для сообщений, очищенных спам-фильтром';
        $this->translations[strtoupper('InfectedHeaderValue')] = 'Заголовок для сообщений, инфицированных вирусом';
        $this->translations[strtoupper('DisinfectedHeaderValue')] = 'Заголовок для сообщений, обезвреженных антивирусом';
        $this->translations[strtoupper('InformationHeaderValue')] = 'Информационный заголовок';
        $this->translations[strtoupper('DetailedSpamReport')] = 'Выдавать подробный отчет о спаме ?';
        $this->translations[strtoupper('IncludeScoresInSpamAssassinReport')] = 'Включать ли в отчет о спаме баллы по каждому критерию, использованному для определения спама';
        $this->translations[strtoupper('AlwaysIncludeSpamAssassinReport')] = 'Хотите ли вы всегда включать отчет о спаме в заголовке, даже если сообщение не является спамом';
        $this->translations[strtoupper('MultipleHeaders')] = 'Как накладывать друг на друга заголовки различных серверов MailScanner, если их используется несколько. ';
        $this->translations[strtoupper('Hostname')] = 'Имя хоста';
        $this->translations[strtoupper('SignMessagesAlreadyProcessed')] = 'Подписывать ли уже обработанные сообщения';
        $this->translations[strtoupper('SignCleanMessages')] = 'Подписывать ли уже очищенные сообщения';
        $this->translations[strtoupper('AttachImageToSignature')] = 'Добавлять картинку в подпись';
        $this->translations[strtoupper('AttachImageToHTMLMessageOnly')] = 'Добавлять картинку в подпись только в HTML-письма';
        $this->translations[strtoupper('AllowMultipleHTMLSignatures')] = 'Разрешать ли дублирование повторное подписывание сообщений';
        $this->translations[strtoupper('DontSignHTMLIfHeadersExist')] = 'Не подписывать если в сообщении есть указанные заголовки';
        $this->translations[strtoupper('MarkInfectedMessages')] = 'Помечать инфицированные сообщения';
        $this->translations[strtoupper('MarkUnscannedMessages')] = 'Помечать не сканированные сообщения';
        $this->translations[strtoupper('UnscannedHeaderValue')] = 'Текст метки для несканированных сообщений';
        $this->translations[strtoupper('RemoveTheseHeaders')] = 'Удалять из сообщения указанные заголовки';
        $this->translations[strtoupper('DeliverCleanedMessages')] = 'Доставлять очищенные письма';
        $this->translations[strtoupper('NotifySenders')] = 'Оповещать отправителей о сообщениях с вирусами и недопустимыми именами файлов';
        $this->translations[strtoupper('NotifySendersOfViruses')] = 'Оповещать отправителей о сообщениях с вирусами';
        $this->translations[strtoupper('NotifySendersOfBlockedFilenamesOrFiletypes')] = 'Оповещать отправителей о сообщениях, заблокированных по причине содержания недопустимых имен или типов файлов';
        $this->translations[strtoupper('NotifySendersOfBlockedSizeAttachments')] = 'Оповещать отправителей о сообщениях, заблокированных по причине слишком маленького или слишком большого размера';
        $this->translations[strtoupper('NotifySendersOfOtherBlockedContent')] = 'Оповещать отправителей о сообщениях, заблокированных по иным причинам';
        $this->translations[strtoupper('NeverNotifySendersOfPrecedence')] = 'Не оповещать отправителей, типы приоритетностей которых перечислены в этом списке';
        $this->translations[strtoupper('ScannedModifySubject')] = 'Как модифицировать тему сообщения, если после его сканирования ничего не обнаружено';
        $this->translations[strtoupper('ScannedSubjectText')] = 'Текст, вставляемый в тему сообщения, если после его сканирования ничего не обнаружено';
        $this->translations[strtoupper('VirusModifySubject')] = 'Как модифицировать тему сообщения, если в нем обнаружен вирус';
        $this->translations[strtoupper('VirusSubjectText')] = 'Текст, вставляемый в тему сообщения, если в нем обнаружен вирус';
        $this->translations[strtoupper('FilenameModifySubject')] = 'Как модифицировать тему сообщения, если в его вложениях обнаружены запрещенные файлы';
        $this->translations[strtoupper('FilenameSubjectText')] = 'Текст, вставляемый в тему сообщения, если в его вложениях обнаружены запрещенные файлы';
        $this->translations[strtoupper('ContentModifySubject')] = 'Как модифицировать тему сообщения, если в его теле обнаружено опасное содержимое';
        $this->translations[strtoupper('ContentSubjectText')] = 'Текст, вставляемый в тему сообщения, если в его теле обнаружено опасное содержимое';
        $this->translations[strtoupper('SizeModifySubject')] = 'Как модифицировать тему сообщения если его размер слишком мал или слишком велик';
        $this->translations[strtoupper('SizeSubjectText')] = 'Текст, вставляемый в тему сообщения, если его размер слишком мал или слишком велик';
        $this->translations[strtoupper('DisarmedModifySubject')] = 'Как модифицировать тему сообщения, если его тело было обезврежено';
        $this->translations[strtoupper('DisarmedSubjectText')] = 'Текст, вставляемый в тему сообщения, если его тело было обезврежено';
        $this->translations[strtoupper('PhishingModifySubject')] = 'Как модифицировать тему сообщения, если сообщение содержит фишинг';
        $this->translations[strtoupper('PhishingSubjectText')] = 'Текст, вставляемый в тему сообщения, если сообщение является фишингом';
        $this->translations[strtoupper('SpamModifySubject')] = 'Как модифицировать тему сообщения, если сообщение является спамом';
        $this->translations[strtoupper('SpamSubjectText')] = 'Текст, вставляемый в тему сообщения, если сообщение является спамом';
        $this->translations[strtoupper('HighScoringSpamModifySubject')] = 'Как модифицировать тему сообщения, если сообщение является спамом с очень высоким баллом';
        $this->translations[strtoupper('HighScoringMCPModifySubject')] = 'Как модифицировать тему сообщения, если сообщение является спамом с очень высоким баллом';
        $this->translations[strtoupper('HighScoringSpamSubjectText')] = 'Текст, вставляемый в тему сообщения, если сообщение является спамом с очень высоким баллом';
        $this->translations[strtoupper('HighScoringMCPSubjectText')] = 'Текст, вставляемый в тему сообщения, если сообщение является спамом с очень высоким баллом';
        $this->translations[strtoupper('WarningIsAttachment')] = 'Присоединять ли предупреждение в виде вложения в письмо';
        $this->translations[strtoupper('AttachmentWarningFilename')] = 'Имя файла вложения, в котором будет предупреждение ';
        $this->translations[strtoupper('AttachmentEncodingCharset')] = 'Кодировка текста в файле вложения, в котором будет предупреждение';
        $this->translations[strtoupper('ArchiveMail')] = 'Куда архивировать почту';
        $this->translations[strtoupper('MissingMailArchiveIs')] = 'Указанное место архивирования является каталогом или файлом';
        $this->translations[strtoupper('SendNotices')] = 'Оповещать локального системного администратора о найденных вирусах и т.д.';
        $this->translations[strtoupper('NoticesIncludeFullHeaders')] = 'Включать ли полные заголовки сообщений в оповещения, отправляемые локальному администратору';
        $this->translations[strtoupper('HideIncomingWorkDirinNotices')] = 'Скрывать ли пути к каталогам в оповещениях, отправляемых локальному администратору';
        $this->translations[strtoupper('NoticeSignature')] = 'Подпись, добавляемая в оповещения локальному администратору';
        $this->translations[strtoupper('NoticesFrom')] = 'От имени какого почтового ящика или пользователя отправлять оповещения';
        $this->translations[strtoupper('NoticesTo')] = 'По какому адресу или какому пользователю отпиавлять оповещения';
        $this->translations[strtoupper('LocalPostmaster')] = 'Адрес локального постмастера, который отображается в поле From: в предупреждающих сообщениях о вирусах, отправляемых пользователям';
        $this->translations[strtoupper('SpamListDefinitions')] = 'Файл со списком имен и соответствующих им сайтов, содержащих черные списки серверов, рассылающих спам';
        $this->translations[strtoupper('VirusScannerDefinitions')] = 'Файл со списком имен антивирусов и команд, вызываемых для каждого из них';
        $this->translations[strtoupper('SpamChecks')] = 'Проверять сообщения на спам';
        $this->translations[strtoupper('SpamList')] = 'Черные списки, используемые при проверке';
        $this->translations[strtoupper('SpamDomainList')] = 'Список черных списков на базе доменов';
        $this->translations[strtoupper('SpamListsToBeSpam')] = 'Количество черных списков, в которые должен попасть отправитель письма, чтобы его письмо считалось спамом';
        $this->translations[strtoupper('SpamListsToReachHighScore')] = 'Количество черных списков, в которые должен попасть отправитель письма, чтобы его письмо считалось спамом c высоким баллом';
        $this->translations[strtoupper('SpamListTimeout')] = 'Количество секунд, выделяемое на проверку отправителя в черном списке';
        $this->translations[strtoupper('MaxSpamListTimeouts')] = 'Максимальное количество таймаутов, после чего черный список считается недоступным';
        $this->translations[strtoupper('SpamListTimeoutsHistory')] = 'Общее количество таймаутов, после чего черный список считается недоступным';
        $this->translations[strtoupper('IsDefinitelyNotSpam')] = 'Белый список адресов, письма с которых никогда не считаются спамом';
        $this->translations[strtoupper('IsDefinitelySpam')] = 'Черный список адресов, письма с которых всегда считаются спамом';
        $this->translations[strtoupper('DefiniteSpamIsHighScoring')] = 'Спам, обнаруженный по черному списку считается спамом с высоким баллом';
        $this->translations[strtoupper('IgnoreSpamWhitelistIfRecipientsExceed')] = 'Игнорировать белый список при обработке сообщений с количеством получателей более ';
        $this->translations[strtoupper('MaxSpamCheckSize')] = 'Максимальный размер сообщения, которое будет проверяться на спам';
        $this->translations[strtoupper('UseWatermarking')] = 'Использовать водяные знаки';
        $this->translations[strtoupper('AddWatermark')] = 'Добавлять водяной знак в каждое письмо';
        $this->translations[strtoupper('CheckWatermarksWithNoSender')] = 'Проверять ли водяные знаки';
        $this->translations[strtoupper('TreatInvalidWatermarksWithNoSenderasSpam')] = 'Воспринимать сообщения с неправильным водяным знаком и без отправителя как';
        $this->translations[strtoupper('CheckWatermarksToSkipSpamChecks')] = 'Проверять водяные знаки для отмены проверок на спам';
        $this->translations[strtoupper('WatermarkSecret')] = 'Секретный ключ, испольуемый для создания водяного знака';
        $this->translations[strtoupper('WatermarkLifetime')] = 'Время жизни водяного знака';
        $this->translations[strtoupper('WatermarkHeader')] = 'Заголовок водяного знака';
        $this->translations[strtoupper('UseSpamAssassin')] = 'Использовать SpamAssassin';
        $this->translations[strtoupper('MaxSpamAssassinSize')] = 'Максимальный размер сообщения, проверяемого SpamAssassin';
        $this->translations[strtoupper('RequiredSpamAssassinScore')] = 'Минимальное количество баллов, необходимое чтобы SpamAssassin считал письмо спамом';
        $this->translations[strtoupper('HighSpamAssassinScore')] = 'Количество баллов, необходимое чтобы SpamAssassin считал письмо спамом с высшим баллом';
        $this->translations[strtoupper('SpamAssassinAutoWhitelist')] = 'Использовать функцию автоматических белых списков SpamAssassin';
        $this->translations[strtoupper('SpamAssassinTimeout')] = 'Количество секунд, выделяемое на проверку одного сообщения с помощью SpamAssassin';
        $this->translations[strtoupper('SpamAssassinTimeoutsHistory')] = 'Количество секунд, выделяемое на проверку одного сообщения с помощью SpamAssassin';
        $this->translations[strtoupper('MaxSpamAssassinTimeouts')] = 'Максимальное количество таймаутов, после которых будет считаться что SpamAssassin недоступен';
        $this->translations[strtoupper('MCPMaxSpamAssassinTimeouts')] = 'Максимальное количество таймаутов, после которых будет считаться что SpamAssassin недоступен';
        $this->translations[strtoupper('MaxSpamAssassinTimeoutsHistory')] = 'Полное количество таймаутов, после которых будет считаться что SpamAssassin недоступен';;
        $this->translations[strtoupper('CheckSpamAssassinIfOnSpamList')] = 'Проверять ли сообщение с помощью SpamAssassin, если его отправитель уже и так находится в одном или нескольких черных списках';
        $this->translations[strtoupper('IncludeBinaryAttachmentsInSpamAssassin')] = 'Сканировать ли двоичные вложения с помощью SpamAssassin';
        $this->translations[strtoupper('SpamScore')] = 'Включать ли в письмо заголовок "Spam Score"';
        $this->translations[strtoupper('CacheSpamAssassinResults')] = 'Кэшировать результаты работы SpamAssassin';
        $this->translations[strtoupper('SpamAssassinCacheDatabaseFile')] = 'Файл, в который будут кэшироваться результаты работы SpamAssassin';
        $this->translations[strtoupper('RebuildBayesEvery')] = 'Перейстраивать базу Бейеса через указанное количество секунд';
        $this->translations[strtoupper('WaitDuringBayesRebuild')] = 'Отключать SpamAssassin на время перестроения базы Бейеса';
        $this->translations[strtoupper('UseCustomSpamScanner')] = 'Использовать свой внешний спам-фильтр';
        $this->translations[strtoupper('MaxCustomSpamScannerSize')] = 'Максимальный размер письма, сканируемого своим спам-фильтром';
        $this->translations[strtoupper('CustomSpamScannerTimeout')] = 'Какое количество времени в секундах выделяется пользовательскому спам-фильтру для обработки сообщения';
        $this->translations[strtoupper('MaxCustomSpamScannerTimeouts')] = 'Максимальное количество таймаутов после которых пользовательскому спам-фильтру будет присвоен стату "недоступен"';
        $this->translations[strtoupper('CustomSpamScannerTimeoutHistory')] = 'Полное количество таймаутов после которых пользовательскому спам-фильтру будет присвоен стату "недоступен"';
        $this->translations[strtoupper('SpamActions')] = 'Действия, которые выполнять с сообщениями, помеченными как спам';
        $this->translations[strtoupper('HighScoringSpamActions')] = 'Действия, которые выполнять с сообщениями, помеченными как спам с высоким баллом';
        $this->translations[strtoupper('NonSpamActions')] = 'Действия, которые выполнять с сообщениями, не помеченными как спам';
        $this->translations[strtoupper('SpamAssassinRuleActions')] = 'Правила, применяемые к сообщениям, помеченным как спам или не как спам';
        $this->translations[strtoupper('SenderSpamReport')] = 'Путь к файлу с текстом, который отправляется отправителю, если письмо является спамом (и в случае если адресат находится в черном списке, и в случае, если SpamAssassin и другой фильтр содержимого посчитал письмо спамом)';
        $this->translations[strtoupper('SenderSpamListReport')] = 'Путь к файлу с текстом, который отправляется отправителю, если он находится в черном списке';
        $this->translations[strtoupper('SenderSpamAssassinReport')] = 'Путь к файлу с текстом, который отправляется отправителю, если письмо является спамом по мнению SpamAssassin';
        $this->translations[strtoupper('InlineSpamWarning')] = 'Путь к файлу с текстом, который добавляется сверху к письму, которое является спамом';
        $this->translations[strtoupper('RecipientSpamReport')] = 'Путь к файлу с текстом, который отправляется получателю, в случае если письмо является спамом';
        $this->translations[strtoupper('EnableSpamBounce')] = 'Отправлять спам обратно отправителям';
        $this->translations[strtoupper('BounceSpamAsAttachment')] = 'Отправлять спам обратно отправителям в виде вложения';
        $this->translations[strtoupper('SyslogFacility')] = 'Раздел syslog';
        $this->translations[strtoupper('LogSpeed')] = 'Нужно ли протоколировать скорость каждой операции';
        $this->translations[strtoupper('LogSpam')] = 'Нужно ли протоколировать спам';
        $this->translations[strtoupper('LogNonSpam')] = 'Нужно ли протоколировать не спам';
        $this->translations[strtoupper('LogPermittedFilenames')] = 'Нужно ли протоколировать все имена файлов, разрешенные правилами имен файлов или только запрещенные.';
        $this->translations[strtoupper('LogPermittedFiletypes')] = 'Нужно ли протоколировать все имена файлов, разрешенные правилами типов файлов или только запрещенные.';
        $this->translations[strtoupper('LogPermittedFileMIMETypes')] = 'Нужно ли протоколировать все имена файлов, разрешенные правилами MIME-типов файлов или только запрещенные.';
        $this->translations[strtoupper('LogSilentViruses')] = 'Нужно ли протоколировать вирусы.';
        $this->translations[strtoupper('LogDangerousHTMLTags')] = 'Нужно ли протоколировать опасные HTML-тэги';
        $this->translations[strtoupper('LogSpamAssassinRuleActions')] = 'Нужно ли протоколировать все действия из параметра "Rule actions"';
        $this->translations[strtoupper('SpamAssassinTemporaryDir')] = 'Путь к папке временных файлов SpamAssassin';
        $this->translations[strtoupper('SpamAssassinUserStateDir')] = 'Путь к пользовательским файлам SpamAssassin';
        $this->translations[strtoupper('SpamAssassinInstallPrefix')] = 'Путь, по которому установлен MailScanner';
        $this->translations[strtoupper('SpamAssassinSiteRulesDir')] = 'Путь к правилам SpamAssassin';
        $this->translations[strtoupper('SpamAssassinLocalRulesDir')] = 'Путь к правилам, которые создаются с помощью sa-update';
        $this->translations[strtoupper('SpamAssassinLocalStateDir')] = 'Путь к папке с файлами состояния SpamAssassin';
        $this->translations[strtoupper('SpamAssassinDefaultRulesDir')] = 'Путь к правилам по умолчанию';
        $this->translations[strtoupper('MCPChecks')] = 'Включать ли проверку MCP';
        $this->translations[strtoupper('FirstCheck')] = 'На что проверять сначала';
        $this->translations[strtoupper('MCPRequiredSpamAssassinScore')] = 'Минимальное количество баллов, необходимое чтобы SpamAssassin считал письмо спамом для MCP';
        $this->translations[strtoupper('MCPHighSpamAssassinScore')] = 'Количество баллов, необходимое чтобы SpamAssassin считал письмо спамом с высшим баллом для MCP';
        $this->translations[strtoupper('MCPErrorScore')] = 'Количество баллов, необходимое чтобы это сообщение считалось ошибкой по MCP';
        $this->translations[strtoupper('MCPHeader')] = 'Заголовок MCP';
        $this->translations[strtoupper('NonMCPActions')] = 'Действия для сообщений, признанных не MCP';
        $this->translations[strtoupper('MCPActions')] = 'Действия для сообщений, признанных MCP';
        $this->translations[strtoupper('HighScoringMCPActions')] = 'Действия для сообщений, признанных MCP с высоким баллом';
        $this->translations[strtoupper('BounceMCPAsAttachment')] = 'Отбрасывать MCP как вложение';
        $this->translations[strtoupper('MCPModifySubject')] = 'Как модифицировать тему сообщения, если в его теле обнаружено MCP';
        $this->translations[strtoupper('MCPSubjectText')] = 'Текст, вставляемый в тему сообщения, если в его теле обнаружено MCP';
        $this->translations[strtoupper('IsDefinitelyNotMCP')] = 'Белый список адресов, которые никогда не являются MCP';
        $this->translations[strtoupper('IsDefinitelyMCP')] = 'Черный список адресов, всегда являющихся MCP';
        $this->translations[strtoupper('DefiniteMCPIsHighScoring')] = 'Считать письма с адресами, находящимися в черном списке MCP спамом с высоким баллом';
        $this->translations[strtoupper('AlwaysIncludeMCPReport')] = 'Всегда включать отчет о MCP';
        $this->translations[strtoupper('DetailedMCPReport')] = 'Включать подробный отчет MCP';
        $this->translations[strtoupper('IncludeScoresInMCPReport')] = 'Включать баллы в отчет о MCP';
        $this->translations[strtoupper('LogMCP')] = 'Протоколировать MCP';
        $this->translations[strtoupper('MCPMaxSpamAssassinSize')] = 'Максимальный размер сообщения, которое будет проверяться на MCP';
        $this->translations[strtoupper('MCPSpamAssassinTimeout')] = 'Время в секундах, выделяемое на проверку сообщения с помощью MCP';
        $this->translations[strtoupper('MCPSpamAssassinPrefsFile')] = 'Путь к файлу настроек SpamAssassin для MCP';
        $this->translations[strtoupper('MCPSpamAssassinUserStateDir')] = 'Путь к пользовательским файлам SpamAssassin для MCP';
        $this->translations[strtoupper('MCPSpamAssassinUserStateDir')] = 'Путь к пользовательским файлам SpamAssassin для MCP';
        $this->translations[strtoupper('MCPSpamAssassinLocalRulesDir')] = 'Путь к локальным правилам SpamAssassin для MCP';
        $this->translations[strtoupper('MCPSpamAssassinDefaultRulesDir')] = 'Путь по умолчанию к правилам SpamAssassin для MCP';
        $this->translations[strtoupper('MCPSpamAssassinInstallPrefix')] = 'Путь по которому установлен MailScanner';
        $this->translations[strtoupper('RecipientMCPReport')] = 'Путь к файлу с отчетом, который отправляется получателю, если письмо содержит проблемы с MCP';
        $this->translations[strtoupper('SenderMCPReport')] = 'Путь к файлу с отчетом, который отправляется отправителю, если письмо содержит проблемы с MCP';
        $this->translations[strtoupper('UseDefaultRulesWithMultipleRecipients')] = 'Использовать правила по умолчанию при обработке писем с несколькими получателями';
        $this->translations[strtoupper('ReadIPAddressFromReceivedHeader')] = 'Ипользовать IP-адрес из заголовка получателя';
        $this->translations[strtoupper('SpamScoreNumberFormat')] = 'Формат числа для количество баллов спама';
        $this->translations[strtoupper('MailScannerVersionNumber')] = 'Версия MailScanner';
        $this->translations[strtoupper('SpamAssassinCacheTimings')] = 'Время, которое различные типы сообщений хранятся в кэше';
        $this->translations[strtoupper('Debug')] = 'Не запускать в режиме демона';
        $this->translations[strtoupper('DebugSpamAssassin')] = 'Запускать SpamAssassin в режиме отладки';
        $this->translations[strtoupper('RunInForeground')] = 'Запускать спам-фильтр в фоне';
        $this->translations[strtoupper('AlwaysLookedUpLast')] = 'Пользовательская функция, которая должна выполнять код, для протоколирования информации о сообщениях в журнал произвольноги типа, такой как SQL';
        $this->translations[strtoupper('AlwaysLookedUpLastAfterBatch')] = 'Пользовательская функция, которая должна выполнять код, для протоколирования информации о сообщениях в журнал произвольноги типа, такой как SQL после пакетной обработки';
        $this->translations[strtoupper('DeliverInBackground')] = 'Доставлять сообщения в фоне';
        $this->translations[strtoupper('DeliveryMethod')] = 'Метод доставки';
        $this->translations[strtoupper('SplitEximSpool')] = 'Разбивать каталоги очереди Exim';
        $this->translations[strtoupper('LockFileDir')] = 'Путь к файлам блокировок спам-фильтра';
        $this->translations[strtoupper('CustomFunctionsDir')] = 'Путь к каталогу с пользовательскими функциями';
        $this->translations[strtoupper('LockType')] = 'Тип блокировки для файлов очереди';
        $this->translations[strtoupper('SyslogSocketType')] = 'Тип сокета syslog';
        $this->translations[strtoupper('AutomaticSyntaxCheck')] = 'Проверять автоматически синтаксис конфигурационных файлов при запуске MailScanner';
        $this->translations[strtoupper('MinimumCodeStatus')] = 'Тип кода, который разрешено использовать в дистрибутиве спам-фильтра';
        $this->translations[strtoupper('AddressBook')] = 'Путь к файлу адресной книги';
        $this->translations[strtoupper('AddressBookTo')] = 'Путь к файлу с адресами отправителей, которым разрешени использовать поля из адресной книги';                

    }

    function fillOptionTypes() {
        $this->options[strtoupper('%org-name%')]["type"] = 'string';
        $this->options[strtoupper('%org-long-name%')]["type"] = 'string';
        $this->options[strtoupper('%web-site%')]["type"] = 'string';
        $this->options[strtoupper('%etc-dir%')]["type"] = 'string';
        $this->options[strtoupper('%report-dir%')]["type"] = 'string';
        $this->options[strtoupper('%rules-dir%')]["type"] = 'string';
        $this->options[strtoupper('%mcp-dir%')]["type"] = 'string';
        $this->options[strtoupper('MaxChildren')]["type"] = 'integer';
        $this->options[strtoupper('RunAsUser')]["type"] = 'string';
        $this->options[strtoupper('RunAsGroup')]["type"] = 'string';
        $this->options[strtoupper('QueueScanInterval')]["type"] = 'integer';
        $this->options[strtoupper('IncomingQueueDir')]["type"] = 'path';
        $this->options[strtoupper('OutgoingQueueDir')]["type"] = 'path';
        $this->options[strtoupper('IncomingWorkDir')]["type"] = 'path';
        $this->options[strtoupper('QuarantineDir')]["type"] = 'path';
        $this->options[strtoupper('PIDfile')]["type"] = 'file';
        $this->options[strtoupper('RestartEvery')]["type"] = 'integer';
        $this->options[strtoupper('MTA')]["type"] = 'list,postfix~sendmail~exim~zmailer|postfix~sendmail~exim~zmailer';
        $this->options[strtoupper('Sendmail')]["type"] = 'file,ruleset';
        $this->options[strtoupper('Sendmail2')]["type"] = 'file,ruleset';
        $this->options[strtoupper('IncomingWorkUser')]["type"] = 'string';
        $this->options[strtoupper('IncomingWorkGroup')]["type"] = 'string';
        $this->options[strtoupper('IncomingWorkPermissions')]["type"] = 'string';
        $this->options[strtoupper('IncomingWorkPermissions')]["type"] = 'string';
        $this->options[strtoupper('QuarantineUser')]["type"] = 'string';
        $this->options[strtoupper('QuarantineGroup')]["type"] = 'string';
        $this->options[strtoupper('QuarantinePermissions')]["type"] = 'string';
        $this->options[strtoupper('MaxUnscannedBytesPerScan')]["type"] = 'string';
        $this->options[strtoupper('MaxUnsafeBytesPerScan')]["type"] = 'string';
        $this->options[strtoupper('MaxUnscannedMessagesPerScan')]["type"] = 'integer';
        $this->options[strtoupper('MaxUnsafeMessagesPerScan')]["type"] = 'integer';
        $this->options[strtoupper('MaxNormalQueueSize')]["type"] = 'integer';
        $this->options[strtoupper('ScanMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('RejectMessage')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('MaximumAttachmentsPerMessage')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('ExpandTNEF')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('UseTNEFContents')]["type"] = 'list,no~add~replace|нет~вставка~замена';
        $this->options[strtoupper('DeliverUnparsableTNEF')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('TNEFExpander')]["type"] = 'file,ruleset';
        $this->options[strtoupper('TNEFTimeout')]["type"] = 'integer';
        $this->options[strtoupper('FileCommand')]["type"] = 'file';
        $this->options[strtoupper('FileTimeout')]["type"] = 'integer';
        $this->options[strtoupper('GunzipCommand')]["type"] = 'file';
        $this->options[strtoupper('GunzipTimeout')]["type"] = 'integer';
        $this->options[strtoupper('UnrarCommand')]["type"] = 'file';
        $this->options[strtoupper('UnrarTimeout')]["type"] = 'integer';
        $this->options[strtoupper('FindUU-EncodedFiles')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('MaximumMessageSize')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('MaximumAttachmentSize')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('MinimumAttachmentSize')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('MaximumArchiveDepth')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('FindArchivesByContent')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('ZipAttachments')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AttachmentsZipFilename')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AttachmentsMinTotalSizeToZip')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AttachmentExtensionsNotToZip')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AddTextOfDoc')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('Antiword')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AntiwordTimeout')]["type"] = 'integer';
        $this->options[strtoupper('VirusScanning')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('VirusScanners')]["type"] = 'list,sophos~sophossavi~mcafee~command~bitdefenter~drweb~kaspersky-4.5~kaspersky~kavdaemonclient~etrust~inoculate~inoculan~nod32~nod32-1.99~f-secure~f-prot~f-prot-6~f-protd-6~panda~rav~antivir~clamav~clamavmodule~clamd~trend~norman~css~avg~vexira~symscanengine~avast~avastd~esets~vba32~generic~none|Sophos~Sophos SAVI~McAfee~Command~BitDefender~Doctor Web~Kaspersky 4.5 и новее~Kaspersky~Kaspersky Daemon Client~E-trust~Inoculate~Inoculan~Eset NOD 32~Eset NOD 32 1.99 и новее~F-Secure~F-Prot-6~F-Protd-6~Panda~RAV~Antivir~ClamAV~ClamAV Module~ClamAV daemon(clamd)~Trend Micro~Norman~Symantec CSS~AVG~Vexira~Symantec Scan Engine~Avast~Avast Daemon(avastd)~Esets~VBA32~Свой собственный~не использовать';
        $this->options[strtoupper('VirusScannerTimeout')]["type"] = 'integer';
        $this->options[strtoupper('DeliverDisinfectedFiles')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SilentViruses')]["type"] = 'listedit,HTML-IFrame~HTML-Codebase~HTML-Script~HTML-Form~Zip-Password~All-Viruses|HTML-IFrame~HTML-Codebase~HTML-Script~HTML-Form~Zip-Password~All-Viruses,null,ruleset';
        $this->options[strtoupper('StillDeliverSilentViruses')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('Non-ForgingViruses')]["type"] = 'string';
        $this->options[strtoupper('BlockEncryptedMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('BlockUnencryptedMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AllowPassword-ProtectedArchives')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('CheckFilenamesInPassword-ProtectedArchives')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AllowedSophosErrorMessages')]["type"] = 'string';
        $this->options[strtoupper('SophosIDEDir')]["type"] = 'path';
        $this->options[strtoupper('SophosLibDir')]["type"] = 'path';
        $this->options[strtoupper('MonitorsForSophosUpdates')]["type"] = 'string';
        $this->options[strtoupper('MonitorsForClamAVUpdates')]["type"] = 'string';
        $this->options[strtoupper('ClamAVmoduleMaximumRecursionLevel')]["type"] = 'integer';
        $this->options[strtoupper('ClamAVmoduleMaximumFiles')]["type"] = 'integer';
        $this->options[strtoupper('ClamAVmoduleMaximumFileSize')]["type"] = 'string';
        $this->options[strtoupper('ClamAVmoduleMaximumCompressionRatio')]["type"] = 'integer';
        $this->options[strtoupper('ClamdPort')]["type"] = 'integer';
        $this->options[strtoupper('ClamdSocket')]["type"] = 'string';
        $this->options[strtoupper('ClamdLockFile')]["type"] = 'file';
        $this->options[strtoupper('ClamdUseThreads')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('ClamAVFullMessageScan')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('FpscandPort')]["type"] = 'integer';
        $this->options[strtoupper('DangerousContentScanning')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AllowPartialMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AllowExternalMessageBodies')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('FindPhishingFraud')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AlsoFindNumericPhishing')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('UseStricterPhishingNet')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('HighlightPhishingFraud')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('PhishingSafeSitesFile')]["type"] = 'file';
        $this->options[strtoupper('PhishingBadSitesFile')]["type"] = 'file';
        $this->options[strtoupper('CountrySub-DomainsList')]["type"] = 'file';
        $this->options[strtoupper('AllowIFrameTags')]["type"] = 'listedit,yes~no~disarm|да~нет~обезвредить,null,ruleset';
        $this->options[strtoupper('AllowIFrameTags')]["type"] = 'listedit,yes~no~disarm|да~нет~обезвредить,null,ruleset';
        $this->options[strtoupper('AllowFormTags')]["type"] = 'listedit,yes~no~disarm|да~нет~обезвредить,null,ruleset';
        $this->options[strtoupper('AllowScriptTags')]["type"] = 'listedit,yes~no~disarm|да~нет~обезвредить,null,ruleset';
        $this->options[strtoupper('AllowWebBugs')]["type"] = 'listedit,yes~no~disarm|да~нет~обезвредить,null,ruleset';
        $this->options[strtoupper('IgnoredWebBugFilenames')]["type"] = 'string,ruleset';
        $this->options[strtoupper('KnownWebBugServers')]["type"] = 'string,ruleset';
        $this->options[strtoupper('WebBugReplacement')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AllowObjectCodebaseTags')]["type"] = 'listedit,yes~no~disarm|да~нет~обезвредить,null,ruleset';
        $this->options[strtoupper('ConvertDangerousHTMLToText')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('ConvertHTMLToText')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AllowFilenames')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DenyFilenames')]["type"] = 'string,ruleset';
        $this->options[strtoupper('FilenameRules')]["type"] = 'file,ruleset';
        $this->options[strtoupper('AllowFiletypes')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AllowFileMIMETypes')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DenyFileTypes')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DenyFileMIMETypes')]["type"] = 'string,ruleset';
        $this->options[strtoupper('FiletypeRules')]["type"] = 'file,ruleset';
        $this->options[strtoupper('QuarantineInfections')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('QuarantineSilentViruses')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('QuarantineModifiedBody')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('QuarantineWholeMessage')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('QuarantineWholeMessagesAsQueueFiles')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('KeepSpamAndMCPArchiveClean')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('LanguageStrings')]["type"] = 'file';
        $this->options[strtoupper('RejectionReport')]["type"] = 'file';
        $this->options[strtoupper('DeletedBadContentMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('DeletedBadFilenameMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('DeletedVirusMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('DeletedSizeMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('StoredBadContentMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('StoredBadFilenameMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('StoredVirusMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('StoredSizeMessageReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('DisinfectedReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('InlineHTMLSignature')]["type"] = 'file,ruleset';
        $this->options[strtoupper('InlineTextSignature')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SignatureImageFilename')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SignatureImage<img>Filename')]["type"] = 'string,ruleset';
        $this->options[strtoupper('InlineHTMLWarning')]["type"] = 'file,ruleset';
        $this->options[strtoupper('InlineTextWarning')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderContentReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderErrorReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderBadFilenameReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderVirusReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderSizeReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('HideIncomingWorkDir')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('IncludeScannerNameInReports')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('MailHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamScoreHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('InformationHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AddEnvelopeFromHeader')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AddEnvelopeToHeader')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('EnvelopeFromHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('EnvelopeToHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('IDHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('IPProtocolVersionHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamScoreCharacter')]["type"] = 'string';
        $this->options[strtoupper('SpamScoreNumberInsteadOfStars')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('MinimumStarsIfOnSpamList')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('CleanHeaderValue')]["type"] = 'string,ruleset';
        $this->options[strtoupper('InfectedHeaderValue')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DisinfectedHeaderValue')]["type"] = 'string,ruleset';
        $this->options[strtoupper('InformationHeaderValue')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DetailedSpamReport')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('IncludeScoresInSpamAssassinReport')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('AlwaysIncludeSpamAssassinReport')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('MultipleHeaders')]["type"] = 'listedit,append~add~replace|Добавить к существующему~Создать новый~Заменить на новый,null,ruleset';
        $this->options[strtoupper('Hostname')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SignMessagesAlreadyProcessed')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SignCleanMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AttachImageToSignature')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AttachImageToHTMLMessageOnly')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AllowMultipleHTMLSignatures')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('DontSignHTMLIfHeadersExist')]["type"] = 'string,ruleset';
        $this->options[strtoupper('MarkInfectedMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('MarkUnscannedMessages')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('UnscannedHeaderValue')]["type"] = 'string,ruleset';
        $this->options[strtoupper('RemoveTheseHeaders')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DeliverCleanedMessages')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('NotifySenders')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NotifySendersOfViruses')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NotifySendersOfBlockedFilenamesOrFiletypes')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NotifySendersOfBlockedSizeAttachments')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NotifySendersOfOtherBlockedContent')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NeverNotifySendersOfPrecedence')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('ScannedModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('ScannedSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('VirusModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('VirusSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('FilenameModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('FilenameSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('ContentModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('ContentSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SizeModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('SizeSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('DisarmedModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('DisarmedSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('PhishingModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('PhishingSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('SpamSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('HighScoringSpamModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('HighScoringSpamSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('WarningIsAttachment')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AttachmentWarningFilename')]["type"] = 'string,ruleset';
        $this->options[strtoupper('AttachmentEncodingCharset')]["type"] = 'string,ruleset';
        $this->options[strtoupper('ArchiveMail')]["type"] = 'string';
        $this->options[strtoupper('MissingMailArchiveIs')]["type"] = 'list,file~directory|файл~каталог';
        $this->options[strtoupper('SendNotices')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NoticesIncludeFullHeaders')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('HideIncomingWorkDirinNotices')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('NoticeSignature')]["type"] = 'string';
        $this->options[strtoupper('NoticesFrom')]["type"] = 'string';
        $this->options[strtoupper('NoticesTo')]["type"] = 'string,ruleset';
        $this->options[strtoupper('LocalPostmaster')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamListDefinitions')]["type"] = 'file';
        $this->options[strtoupper('VirusScannerDefinitions')]["type"] = 'file';
        $this->options[strtoupper('SpamChecks')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SpamList')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamDomainList')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamListsToBeSpam')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('SpamListsToReachHighScore')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('SpamListTimeout')]["type"] = 'integer';
        $this->options[strtoupper('MaxSpamListTimeouts')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('SpamListTimeoutsHistory')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('IsDefinitelyNotSpam')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('IsDefinitelySpam')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('DefiniteSpamIsHighScoring')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('IgnoreSpamWhitelistIfRecipientsExceed')]["type"] = 'listedit,yes~no|да~нет,integer,ruleset';
        $this->options[strtoupper('MaxSpamCheckSize')]["type"] = 'string,ruleset';
        $this->options[strtoupper('UseWatermarking')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AddWatermark')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('CheckWatermarksWithNoSender')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('TreatInvalidWatermarksWithNoSenderasSpam')]["type"] = 'listedit,spam~high-scoring spam~nothing|Спам~Спам с большим количеством баллов~Не воспринимать,integer';
        $this->options[strtoupper('CheckWatermarksToSkipSpamChecks')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('WatermarkSecret')]["type"] = 'string,ruleset';
        $this->options[strtoupper('WatermarkLifetime')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('WatermarkHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('UseSpamAssassin')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('MaxSpamAssassinSize')]["type"] = 'string';
        $this->options[strtoupper('RequiredSpamAssassinScore')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('HighSpamAssassinScore')]["type"] = 'integer,ruleset';
        $this->options[strtoupper('SpamAssassinAutoWhitelist')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SpamAssassinTimeout')]["type"] = 'integer';
        $this->options[strtoupper('MaxSpamAssassinTimeouts')]["type"] = 'integer';
        $this->options[strtoupper('MaxSpamAssassinTimeoutsHistory')]["type"] = 'integer';
        $this->options[strtoupper('CheckSpamAssassinIfOnSpamList')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('IncludeBinaryAttachmentsInSpamAssassin')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SpamScore')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('CacheSpamAssassinResults')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SpamAssassinCacheDatabaseFile')]["type"] = 'file';
        $this->options[strtoupper('RebuildBayesEvery')]["type"] = 'integer';
        $this->options[strtoupper('WaitDuringBayesRebuild')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('UseCustomSpamScanner')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('MaxCustomSpamScannerSize')]["type"] = 'string,ruleset';
        $this->options[strtoupper('CustomSpamScannerTimeout')]["type"] = 'integer';
        $this->options[strtoupper('MaxCustomSpamScannerTimeouts')]["type"] = 'integer';
        $this->options[strtoupper('CustomSpamScannerTimeoutHistory')]["type"] = 'integer';
        $this->options[strtoupper('SpamActions')]["type"] = 'string,ruleset';
        $this->options[strtoupper('HighScoringSpamActions')]["type"] = 'string,ruleset';
        $this->options[strtoupper('NonSpamActions')]["type"] = 'string,ruleset';
        $this->options[strtoupper('SpamAssassinRuleActions')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderSpamReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderSpamListReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderSpamAssassinReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('InlineSpamWarning')]["type"] = 'file,ruleset';
        $this->options[strtoupper('RecipientSpamReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('EnableSpamBounce')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('BounceSpamAsAttachment')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SyslogFacility')]["type"] = 'string';
        $this->options[strtoupper('LogSpeed')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('LogSpam')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('LogNonSpam')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('LogPermittedFilenames')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('LogPermittedFiletypes')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('LogPermittedFileMIMETypes')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('LogSilentViruses')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('LogDangerousHTMLTags')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('LogSpamAssassinRuleActions')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('SpamAssassinTemporaryDir')]["type"] = 'path';
        $this->options[strtoupper('SpamAssassinUserStateDir')]["type"] = 'path';
        $this->options[strtoupper('SpamAssassinInstallPrefix')]["type"] = 'path';
        $this->options[strtoupper('SpamAssassinSiteRulesDir')]["type"] = 'path';
        $this->options[strtoupper('SpamAssassinLocalRulesDir')]["type"] = 'path';
        $this->options[strtoupper('SpamAssassinDefaultRulesDir')]["type"] = 'path';
        $this->options[strtoupper('MCPChecks')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('FirstCheck')]["type"] = 'list,spam~mcp|spam~MCP';
        $this->options[strtoupper('MCPRequiredSpamAssassinScore')]["type"] = 'integer';
        $this->options[strtoupper('MCPHighSpamAssassinScore')]["type"] = 'integer';
        $this->options[strtoupper('MCPErrorScore')]["type"] = 'integer';
        $this->options[strtoupper('MCPHeader')]["type"] = 'string,ruleset';
        $this->options[strtoupper('NonMCPActions')]["type"] = 'string,ruleset';
        $this->options[strtoupper('MCPActions')]["type"] = 'string,ruleset';
        $this->options[strtoupper('HighScoringMCPActions')]["type"] = 'string,ruleset';
        $this->options[strtoupper('BounceMCPAsAttachment')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('MCPModifySubject')]["type"] = 'listedit,no~start~yes|не модифицировать~добавить текст в начало~добавить текст в конец,null,ruleset';
        $this->options[strtoupper('MCPSubjectText')]["type"] = 'string,ruleset';
        $this->options[strtoupper('IsDefinitelyNotMCP')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('IsDefinitelyMCP')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('DefiniteMCPIsHighScoring')]["type"] = 'listedit,yes~no|да~нет,null,ruleset';
        $this->options[strtoupper('AlwaysIncludeMCPReport')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('DetailedMCPReport')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('IncludeScoresInMCPReport')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('LogMCP')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('MCPMaxSpamAssassinSize')]["type"] = 'integer';
        $this->options[strtoupper('MCPSpamAssassinTimeout')]["type"] = 'integer';
        $this->options[strtoupper('MCPSpamAssassinPrefsFile')]["type"] = 'file';
        $this->options[strtoupper('MCPSpamAssassinUserStateDir')]["type"] = 'apth';
        $this->options[strtoupper('MCPSpamAssassinUserStateDir')]["type"] = 'path';
        $this->options[strtoupper('MCPSpamAssassinLocalRulesDir')]["type"] = 'path';
        $this->options[strtoupper('MCPSpamAssassinDefaultRulesDir')]["type"] = 'path';
        $this->options[strtoupper('MCPSpamAssassinInstallPrefix')]["type"] = 'path';
        $this->options[strtoupper('RecipientMCPReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('SenderMCPReport')]["type"] = 'file,ruleset';
        $this->options[strtoupper('UseDefaultRulesWithMultipleRecipients')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('ReadIPAddressFromReceivedHeader')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('SpamScoreNumberFormat')]["type"] = 'string,ruleset';
        $this->options[strtoupper('MailScannerVersionNumber')]["type"] = 'string';
        $this->options[strtoupper('SpamAssassinCacheTimings')]["type"] = 'string';
        $this->options[strtoupper('Debug')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('DebugSpamAssassin')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('RunInForeground')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('AlwaysLookedUpLast')]["type"] = 'listedit,yes~no|да~нет,string';
        $this->options[strtoupper('AlwaysLookedUpLastAfterBatch')]["type"] = 'listedit,yes~no|да~нет,string';
        $this->options[strtoupper('DeliverInBackground')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('DeliveryMethod')]["type"] = 'list,batch~queue|Самостоятельно~С помощью почтовой очереди';
        $this->options[strtoupper('SplitEximSpool')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('LockFileDir')]["type"] = 'file';
        $this->options[strtoupper('CustomFunctionsDir')]["type"] = 'path';
        $this->options[strtoupper('LockType')]["type"] = 'listedit,posix~flock|posix~flock,string,ruleset';
        $this->options[strtoupper('SyslogSocketType')]["type"] = 'string';
        $this->options[strtoupper('AutomaticSyntaxCheck')]["type"] = 'list,yes~no|да~нет';
        $this->options[strtoupper('MinimumCodeStatus')]["type"] = 'list,none~unsupported~alpha~beta~supported|none~unsupported~alpha~beta~supported';
        $this->options[strtoupper('AddressBook')]["type"] = 'file,ruleset';
        $this->options[strtoupper('AddressBookTo')]["type"] = 'string,ruleset';        
    }

    function load() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $configFile = $app->remotePath.$app->mailScannerConfigFile;
        $configTemplateFile = $app->mailScannerConfigTemplateFile;
        $strings = file($configFile);
        $option_values = array();
        foreach ($strings as $line) {
            $parts = explode("=",$line);
            $option_values[trim(strtoupper(str_replace(" ","",$parts[0])))] = @trim(@$parts[1]);
        }
        $strings = file($configTemplateFile);
        $configTemplateFile = $app->mailScannerConfigTemplateFile;
        $strings = file($configTemplateFile);
        $this->categories[count($this->categories)] = "Variables";
        $category = "";
        $descr = array();
        foreach ($strings as $line) {
            if (substr($line,0,4)=='# --') {
                $category = trim(str_replace("#","",$prev_line));
                $this->categories[count($this->categories)] = $category;
            } else {
                if (substr($line, 0,1)=="#") {
                    $descr[count($descr)] = str_replace("#","",$line);
                } else {
                    if (trim($line)=="") {
                        $descr = array();
                    }
                    else {
                        if (substr($line,0,1)=="%") {
                            $opt_arr = explode("=",$line);
                            $option = trim($opt_arr[0]);
                            $value = trim($opt_arr[1]);
                            $this->options[strtoupper(str_replace(" ","",$option))]["value"] = @$option_values[strtoupper(str_replace(" ","",$option))];
                            $this->options[strtoupper(str_replace(" ","",$option))]["name"] = $option;
                            $this->options[strtoupper(str_replace(" ","",$option))]["category"] = "Variables";
                            $this->options[strtoupper(str_replace(" ","",$option))]["description"] = implode("\n",$descr);
                            $this->variables[$option] = $value;

                            $descr = array();
                        }
                        else {
                            $opt_arr = explode("=",$line);
                            $option = trim($opt_arr[0]);
                            $value = trim($opt_arr[1]);
                            $this->options[strtoupper(str_replace(" ","",$option))]["value"] = @$option_values[strtoupper(str_replace(" ","",$option))];
                            $this->options[strtoupper(str_replace(" ","",$option))]["category"] = $category;
                            $this->options[strtoupper(str_replace(" ","",$option))]["name"] = $option;
                            $this->options[strtoupper(str_replace(" ","",$option))]["description"] = implode("\n",$descr);
                            $descr = array();
                        }
                    }
                }
            }
            $prev_line = $line;
        }
        $this->fillOptionTypes();
        $this->loaded = true;
    }

    function save($arguments=null) {
    	if (isset($arguments)) {
    		$this->load();
    		$this->setArguments($arguments);
    		if (!is_array($this->whitelist_rules_array)) {
    			if (!is_object($this->whitelist_rules_array))
    				$this->whitelist_rules_array = json_decode($this->whitelist_rules_array);
    			$this->whitelist_rules_array = (array)$this->whitelist_rules_array;
    		}
    		if (!is_array($this->blacklist_rules_array)) {
    			if (!is_object($this->blacklist_rules_array))
    				$this->blacklist_rules_array = json_decode($this->blacklist_rules_array);
    			$this->blacklist_rules_array = (array)$this->blacklist_rules_array;
    		}
    		if (!is_array($this->options)) {
    			if (!is_object($this->options))
    				$this->options = json_decode($this->options);
    			$this->options = (array)$this->options;
    		}
    	}
        foreach ($this->fields as $key=>$value) {
            if (isset($this->options[$key])) {
                $this->options[$key]["value"] = $value;
            }
        }
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $configFile = $app->remotePath.$app->mailScannerConfigFile;
        if (!file_exists($configFile)) {
            $this->reportError("Конфигурационный файл ".$configFile." не обнаружен !","save");
            return 0;
        }
        $fp = fopen($configFile,"w");
        foreach ($this->options as $value) {
            if (isset($value["name"])) {
                fwrite($fp,$value["name"]." = ". $value["value"]."\n");
            }
        }
        fclose($fp);
       
        $this->spamWhitelistFile = $app->spamWhitelistFile;

        $fp = fopen($app->remotePath.$this->spamWhitelistFile,"w");
        foreach($this->whitelist_rules_array as $value) {
            fwrite($fp,$value."\n");
        }
        fwrite($fp,"FromOrTo: default no\n");
        fclose($fp);

        $this->spamBlacklistFile = $app->spamBlacklistFile;

        $fp = fopen($app->remotePath.$this->spamBlacklistFile,"w");
        foreach($this->blacklist_rules_array as $value) {
            fwrite($fp,$value."\n");
        }
        fwrite($fp,"FromOrTo: default no\n");
        fclose($fp);

        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
        $this->loaded = true;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILSCANNERCONFIG_CHANGED");
    }

    function getId() {
        return "MailScannerConfig_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return "Параметры спам-фильтра";
    }

    function getArgs() {
        $result = parent::getArgs();
        //return $result;
        $str = "<table width='100%' cellpadding='0' cellspacing='5'><tr><td class='inner'>РАЗДЕЛ:</td><td class='inner' width='100%'><select class='wide' id='categorySelect' collection='";
        $values = array();
        $titles = array();
        $this->fillTranslations();
        $this->fillOptionTypes();
        $divs = array();
        foreach ($this->categories as $category) {
            $cnt = count($divs);
            $values[count($values)] = str_replace(" ","",$category."_category");
            $titles[count($titles)] = $this->translations[strtoupper($category)];
            if ($category=="Variables")
                $display="";
            else
                $display = "none";
            $divs[$cnt] = "<table style='display:".$display."' id='".str_replace(" ","",$category)."_category' width='100%' cellpadding='0' cellspacing='5'>\n";
            foreach($this->options as $value) {
                if (!isset($value["category"]))
                    continue;
                if ($value["category"]==$category) {
                    $divs[$cnt] .= "<tr><td colspan='2'>".$this->translations[str_replace(" ","",strtoupper($value["name"]))]."(".@$value["name"].")</td></tr>\n";
                    $divs[$cnt] .= "<tr><td colspan='2'><Control object_class='InputControl' id='".str_replace(" ","",strtoupper($value["name"]))."' type='".@$value["type"]."' must_set='false' width='100%' value='".$value['value']."' input_class='input1' deactivate_class='deactivated'></Control></td></tr>\n";
                }
            }
            $divs[$cnt] .= "</table>\n";
            
        }
        $str .= implode(",",$values)."|".implode(",",$titles)."'></select></td></tr></table>";
        $result["{|settings}"] = $str;
        foreach($divs as $div) {
            $result['{|settings}'] .= $div;
        }
        $result["{variables}"] = "mbox.variables= new Array;\n";
        foreach($this->variables as $key=>$value) {
            $result["{variables}"] .= "mbox.variables['".$key."'] = '".$value."';\n";
        }
        return $result;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>