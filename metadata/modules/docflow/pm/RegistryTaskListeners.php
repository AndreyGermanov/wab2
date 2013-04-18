<?php
$models["RegistryTaskListeners"] = array("metaTitle" => "Заинтересованные лица по задаче",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryTaskListeners",
												   "listener" => "referenceUser",
												   "task" => "documentTask",
												   "firm" => "firm"
);
?>