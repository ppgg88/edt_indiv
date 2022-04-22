<?php
header('Content-type:application/json');
if(isset($_GET['semaine'])){

    $ladate = new DateTime();
    $ladate->setISOdate(strftime("%Y"), $_GET['semaine']);
    $s = date_format($ladate, 'Y-m-d');
    $ladate = strftime("%Y-%M-%d", $ladate->getTimestamp());
    $d = new DateTime('Monday this week '.$s);
    $start = date_format($d, 'Y-m-d');
    $d->add(new DateInterval('P7D'));
    $end = date_format($d, 'Y-m-d');
    $ide = "";
    $idp = "";
    if(isset($_GET['ide'])) $ide = "and elleve.id = ".$_GET['ide'];
    if(isset($_GET['idp'])) $idp = "and proph.id = ".$_GET['idp'];
    $pdo = new PDO('mysql:host=mysql-edt-individualisation.alwaysdata.net;dbname=edt-individualisation_edt;charset=utf8', "258160_paul" , "roville-2022");
    $sqlquery = "SELECT rdv.id, rdv.nom, rdv.date, rdv.durre, rdv.couleur, rdv.lieu, elleve.nom as e_nom, elleve.prenom as e_prenom, elleve.classe as e_classe, elleve.id as ide ,proph.nom as p_nom, proph.prenom as p_prenom, proph.id as idp, abs FROM rdv, elleve, proph WHERE rdv.id_elleve = elleve.id and rdv.id_proph = proph.id and rdv.date >= :dd and rdv.date < :df ".$ide." ".$idp." order by date, proph.nom, elleve.nom;";
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->bindValue(':dd', $start, PDO::PARAM_STR);
    $recipesStatement->bindValue(':df', $end, PDO::PARAM_STR);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    echo(json_encode($recipes));
}
?>