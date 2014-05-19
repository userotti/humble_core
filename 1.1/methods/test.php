<?php
/*

Caldoza Engine ------------------------

File	:	test.php
Created	: 	2013-12-03

*/
//print_r($db);

$users = $db->get_results("SELECT * FROM `categories`");

return $users;

?>