<?php
include('fonction.php');

if(isset($_GET['key']) && test_id($_GET['key'])){
    $id = $_GET['idrdv'];
    include('log_bdd.php');

    $sqlqueryy = "DELETE FROM `rdv` where id = ".$id;
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&key=".$_GET['key']);
}
else{
    echo("erreur");
    header("Location: edt.php");
}
?>