<?php
if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
    $id = $_GET['idrdv'];
    include('log_bdd.php');
    include('fonction.php');

    $sqlqueryy = "DELETE FROM `rdv` where id = ".$id;
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&key=consecteturadipiscingelit");
}
else{
    echo("erreur");
    header("Location: edt.php");
}
?>