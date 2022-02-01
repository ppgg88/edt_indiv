<?php
if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
    include('log_bdd.php');
    include('fonction.php');

    $sqlqueryy = "DELETE FROM `rdv`;";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();

    header("Location: index.php?key=consecteturadipiscingelit");
}
?>