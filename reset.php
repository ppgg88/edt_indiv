<?php
include('fonction.php');
if(isset($_GET['key']) && test_id($_GET['key'])){
    include('log_bdd.php');
    $sqlqueryy = "DELETE FROM `rdv`;";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();

    header("Location: parametre.php?key=".$_GET['key']);
}
?>