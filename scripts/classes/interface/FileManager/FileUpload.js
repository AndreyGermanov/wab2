FileUpload = Class.create(Entity, {
	init: function(params) {
		this.swf = new SWFUpload({upload_url: "index.php",
		   flash_url: "tools/SWFUpload/swfupload.swf",
		   button_placeholder_id: this.buttonId,
		   button_image_url: this.buttonURL,
		   button_width: this.buttonWidth,
		   button_height: this.buttonHeight,
		   button_text: this.buttonText,
		   button_text_style: this.buttonTextStype,
		   button_action: SWFUpload.BUTTON_ACTION.SELECT_FILE
			});
	}
});