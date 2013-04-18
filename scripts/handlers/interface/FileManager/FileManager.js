//include scripts/handlers/core/WABEntity.js
entity.viewForm = '{viewForm}';
entity.path = '{path}';
entity.rootPath = '{rootPath}';
entity.useCase ='{useCase}';
entity.user = '{appUser}';
entity.onLoad();
entity.selectedFiles = new Array;
entity.filesCount = 0;
entity.backupsViewerPath = '{backupsViewerPath}';
entity.fileUploadId = '{fileUploadId}';
if ((entity.useCase=="selectPath" || entity.useCase=="selectFile") && entity.viewForm!="list") {
    if ($I(entity.node.id+"_selectButton")!=0) {
        $I(entity.node.id+"_selectButton").style.display = '';
    }
}

if (entity.useCase=='fileUpload')
	entity.ftpUser = '{ftpUserName}';

if (entity.viewForm=="list")
	entity.buildTable();
else {
	if (entity.role["fmCanUpload"]!="false") {	
		entity.fileUpload = $O(entity.fileUploadId,'');
		entity.fileUpload.init();
		entity.fileUpload.swf.settings["file_dialog_start_handler"]    = entity.uploadDialogStartHandler;
		entity.fileUpload.swf.settings["file_dialog_complete_handler"] = entity.uploadDialogCompleteHandler;
		entity.fileUpload.swf.settings["upload_start_handler"]         = entity.uploadStartHandler;
		entity.fileUpload.swf.settings["upload_success_handler"]       = entity.uploadSuccessHandler;
		entity.fileUpload.swf.settings["upload_complete_handler"]      = entity.uploadCompleteHandler;
		entity.fileUpload.swf.settings["upload_progress_handler"]      = entity.uploadProgressHandler;
		entity.fileUpload.swf.settings["upload_error_handler"]         = entity.uploadErrorHandler;
		entity.fileUpload.swf.customSettings["parent_object_id"] = entity.fileUpload.object_id;
	}
}