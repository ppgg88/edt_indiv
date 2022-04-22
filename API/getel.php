<?php
header('Content-type:application/json');

$pdo = new PDO('mysql:host=mysql-edt-individualisation.alwaysdata.net;dbname=edt-individualisation_edt;charset=utf8', "258160_paul" , "roville-2022");
$sqlquery = "SELECT * from elleve order by nom";
$recipesStatement = $pdo->prepare($sqlquery);
$recipesStatement->execute();
$recipes = $recipesStatement->fetchAll();

echo(json_encode($recipes));
?>