<?php
    // connexion à la base de données. Renseignez vos propres identifiants
    if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
        if(!isset($_GET['semaine']))$s = date('W', time());
        else if(isset($_GET['semaine'][7]))$s = $_GET['semaine'][6].$_GET['semaine'][7];
        else $s = $_GET['semaine'];
    include('log_bdd.php');
    include('fonction.php');

    $r = $pdo->query('SELECT rdv.date, rdv.durre, elleve.nom as e_nom, elleve.prenom as e_prenom, elleve.classe as e_classe, proph.nom as p_nom, proph.prenom as p_prenom, rdv.lieu, rdv.nom, rdv.couleur FROM rdv, elleve, proph;');
 
    $tabeleves = [];
    $tabeleves[] = ['DATE', 'DURRE', 'NOM ELEVES', 'PRENOM ELEVES', 'CLASSE ELEVES', 'NOM PROFS', 'PRENOM PROFS', 'LIEU', 'OBSERVATION', 'COULEUR'];
    //$tabeleves[] = ['', '', '', '', '', '', '', '', '', ''];
 
    while($rs = $r->fetch(PDO::FETCH_ASSOC)){
        if(date('W', strtotime($rs['date']))==$s){
            $tabeleves[] = [$rs['date'], $rs['durre'],$rs['e_nom'],$rs['e_prenom'],$rs['e_classe'],$rs['p_nom'],$rs['p_prenom'],$rs['lieu'],$rs['nom'],$rs['couleur']];
        }
    }
    $name = "export/export_S".$s.".csv";
    $fichier_csv = fopen($name, "w+");
 
    fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));
    
    foreach($tabeleves as $ligne){
        //$ligne = array_map("utf8_decode", $ligne);
        fputcsv($fichier_csv, $ligne, ";");
    }
 
    fclose($fichier_csv);
    $h = 'Location:'.$name;
    //header($h);
    echo('<a href="'.$name.'">telecharger</a>');
    }

?>