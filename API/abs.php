<?php
if(isset($_GET['id']) && isset($_GET['val'])){
    $pdo = new PDO('mysql:host=mysql-edt-individualisation.alwaysdata.net;dbname=edt-individualisation_edt;charset=utf8', "258160_paul" , "roville-2022");
    $id = $_GET['id'];
    $abs = $_GET['val'];
    $sqlquery = "SELECT * FROM `rdv` WHERE id = ".$id;
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    foreach ($recipes as $res){
        $query=$pdo->prepare("UPDATE `rdv` SET `date`= :d, `abs` = :ab WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':ab', $abs, PDO::PARAM_INT);
        $query->bindValue(':d', $res['date'], PDO::PARAM_STR);
        $query->execute();
        $query->CloseCursor();
    }
}
?>