var AuthFormWebEntity = Class.create(InputWebEntity, {
	
	afterSave: function() {
		location.href = "index.php";
	}
});