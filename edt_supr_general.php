<?php
include('fonction.php');
if(isset($_GET['key']) && test_id($_GET['key'])){
    $id = $_GET['idrdv'];
    include('log_bdd.php');

    $sqlqueryy = "DELETE FROM `rdv` where id = ".$id;
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $classe = "";
    $date = "";
    $idpp = "";
    $idee = "";
    if(isset($_GET['classe'])){
        $classe = "&classe=".$_GET['classe'];
    }
    if(isset($_GET['date'])){
        $date = "&date=".$_GET['date'];
    }
    if(isset($_GET['idp'])){
        $idpp = "&idp=".$_GET['idp'];
    }
    if(isset($_GET['ide'])){
        $idee = "&ide=".$_GET['ide'];
    }

    header("Location: edt_general.php?&semaine=".$_GET['semaine']."&key=".$_GET['key'].$classe.$date.$idpp.$idee);
}
else{
    echo("erreur");
    header("Location: edt.php");
}
?>