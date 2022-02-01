<?php 
include('log_bdd.php');

if(isset($_POST['Envoyer'])){
    if(isset($_POST['date'][5])){
        $d = $_POST['date_j']." ".$_POST['date'];
    }
    else{
        $d = $_POST['date_j']." ".$_POST['date'].":00";
    }
    
    $query=$pdo->prepare("UPDATE rdv
        SET `nom` = :rdv,
        `id_elleve` = :ide, 
        `id_proph` = :idp, 
        `date` = :dates, 
        `durre` = :durre, 
        `couleur` = :coulleur, 
        `lieu` = :lieu 
        WHERE id = ".$_GET['id']);
    $query->bindValue(':rdv', $_POST['rdv'], PDO::PARAM_STR);
    $query->bindValue(':ide', $_POST['ide'], PDO::PARAM_INT);
    if($_POST['idp'] == 0){
        $query->bindValue(':idp', null, PDO::PARAM_INT);
    }
    else{
        $query->bindValue(':idp', $_POST['idp'], PDO::PARAM_INT);
    }
    $query->bindValue(':dates', $d, PDO::PARAM_STR);
    $query->bindValue(':durre', $_POST['durre'], PDO::PARAM_INT);
    $query->bindValue(':coulleur', $_POST['coulleur'], PDO::PARAM_STR);
    $query->bindValue(':lieu', $_POST['lieu'], PDO::PARAM_STR);
    $query->execute();
    echo($query->errorInfo()[0]);
    echo($query->errorInfo()[1]);
    echo($query->errorInfo()[2]);
    
}

function error($code){
    if ($code == 1062){
        echo("<script type='text/javascript'>alert(\"un cours existe deja pour cette elleves et pour cette heure \");</script>"); 
    }
}

header("Location: edtpr.php?idp=".$_POST['idp']."&semaine=".$_GET['semaine']."&id=".$_GET['id']);
?>