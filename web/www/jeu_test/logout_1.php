<?php include G_CHE . 'ident.php';
include 'classes.php';
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
if($normal_auth)
	$auth->logout();
session_destroy();
header('Location: ' . $type_flux.G_URL);
?>

