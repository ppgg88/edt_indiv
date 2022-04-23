<?php
include 'fonction.php';
include 'log_bdd.php';
if(isset($_GET['key']) && test_id($_GET['key'])){
    $dd = $_POST['date_d']." 00:00:00";
    $df = $_POST['date_f']." 23:59:59";
    $q=$pdo->prepare("SELECT * FROM rdv where rdv.date >= :dd and rdv.date <= :df and id_proph = :idp;");
    $q->bindValue(':dd', $dd, PDO::PARAM_STR);
    $q->bindValue(':idp', $_POST['idp'], PDO::PARAM_INT);
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
        if(isset($_POST['notife'])){
            if(!isset($temp[$ress['id_elleve']])){
                notifier_eleve($ress['id_elleve'], $_GET['semaine']);
                $temp[$ress['id_elleve']] = 1;
            }
        }
        
    }
    if(isset($_POST['notifpr'])){
        notifier_prof($_POST['idp'], $_GET['semaine']);
    }
    
}
header('Location: edtpr.php?key='.$_GET['key'].'&semaine='.$_GET['semaine'].'&idp='.$_GET['idp'].'&abs=1');


?>