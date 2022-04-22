<?php
include('fonction.php');
if(isset($_GET['key']) && $_GET['key'] == 'viesco'){
    include('log_bdd.php');

if(isset($_POST['Envoyer'])){
    if(isset($_POST['date'][5])){
        $d = $_POST['date_j']." ".$_POST['date'];
    }
    else{
        $d = $_POST['date_j']." ".$_POST['date'].":00";
    }
    
    $query=$pdo->prepare("UPDATE rdv
        SET
        `abs` = :ab,
        `date` = :d
        WHERE id = ".$_GET['id']);
    $query->bindValue(':ab', $_POST['abs'], PDO::PARAM_INT);
    $query->bindValue(':d', $_POST['date'], PDO::PARAM_STR);
    $query->execute();
    
    if(isset($_POST['notifpr'])){
        notifier_prof($_POST['idp'], $_GET['semaine']);
    }
}

if(isset($_POST['absent'])){
    $dd = $_POST['date_d']." 00:00:00";
    $df = $_POST['date_f']." 23:59:59";
    $q=$pdo->prepare("SELECT * FROM rdv where rdv.date >= :dd and rdv.date <= :df and id_elleve = :ide;");
    $q->bindValue(':dd', $dd, PDO::PARAM_STR);
    $q->bindValue(':ide', $_POST['ide'], PDO::PARAM_INT);
    $q->bindValue(':df', $df, PDO::PARAM_STR);
    $q->execute();
    $recipess = $q->fetchAll();
    foreach ($recipess as $ress){
        $query=$pdo->prepare("UPDATE rdv
        SET
            rdv.date = :d, 
            rdv.abs = :ab
        WHERE 
            rdv.id = :id;");
        $query->bindValue(':d', $ress['date'], PDO::PARAM_STR);
        $query->bindValue(':ab', $_POST['abs'], PDO::PARAM_INT);
        $query->bindValue(':id', $ress['id'], PDO::PARAM_INT);
        $query->execute();

        if(isset($_POST['notifpr'])){
            notifier_prof($ress['id_proph'], $_GET['semaine']);
        }
    }
}

function error($code){
    if ($code == 1062){
        echo("<script type='text/javascript'>alert(\"un cours existe deja pour cette elleves et pour cette heure \");</script>"); 
    }
}

header("Location: edt_general.php?&semaine=".$_GET['semaine']."&id=".$_GET['id']."&key=".$_GET['key']);
}
?>