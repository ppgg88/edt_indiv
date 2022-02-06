<?php
    // connexion à la base de données. Renseignez vos propres identifiants
    include('fonction.php');
    if(isset($_GET['key']) && test_id($_GET['key'])){
        if(!isset($_GET['semaine']))$s = date('W', time());
        else if(isset($_GET['semaine'][7]))$s = $_GET['semaine'][6].$_GET['semaine'][7];
        else $s = $_GET['semaine'];

        include('log_bdd.php');

        $r = $pdo->query('SELECT rdv.date, rdv.durre, elleve.nom as e_nom, elleve.prenom as e_prenom, elleve.classe as e_classe, proph.nom as p_nom, proph.prenom as p_prenom, rdv.lieu, rdv.nom, rdv.couleur, rdv.abs FROM rdv, elleve, proph WHERE rdv.id_elleve = elleve.id and rdv.id_proph = proph.id ORDER BY rdv.date');

        $tabeleves = [];
        $tabeleves[] = ['DATE', 'DURRE', 'NOM ELEVES', 'PRENOM ELEVES', 'CLASSE ELEVES', 'NOM PROFS', 'PRENOM PROFS', 'LIEU', 'OBSERVATION', 'COULEUR', 'ABS'];
        //$tabeleves[] = ['', '', '', '', '', '', '', '', '', ''];
    
        while($rs = $r->fetch(PDO::FETCH_ASSOC)){
            if(date('W', strtotime($rs['date']))==$s){
                if($rs['abs'] == -1) $abs = "Absent";
                elseif($rs['abs'] == 2) $abs = "Anuller";
                elseif($rs['abs'] == 1) $abs = "Present";
                else $abs = "NR";
                $tabeleves[] = [$rs['date'], $rs['durre'],$rs['e_nom'],$rs['e_prenom'],$rs['e_classe'],$rs['p_nom'],$rs['p_prenom'],$rs['lieu'],$rs['nom'],$rs['couleur'], $abs];
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

    ?>

    <html>
        <head>
            <link rel="stylesheet" href="all.css" />
            <style>
                h3{
                    text-align: center;
                    margin-top:9vh;
                }
                button{
                    display:block;
                    margin-left: auto;
                    margin-right: auto;
                }
                button.back{
                    width: 15% !important;
                }
            </style>
        </head>
        <body>
            <?php echo('<a href="'.$name.'"><h3>Télecharger le Fichier<h3></a>'); ?>
            <form action="index.php?key=<?php echo($_GET['key']); ?>&semaine=<?php echo($s); ?>" method="POST">
                <button class="back">RETOUR ACCUEIL</button>
            </form>
        </body>
    </html>
<?php } ?>