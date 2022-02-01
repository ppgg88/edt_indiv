<?php 

if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
    $i = 0;
    // Connect to database
    include("log_bdd.php");
    if (isset($_POST["import"])) {

    $fileName = $_FILES["file"]["tmp_name"];
    
    if ($_FILES["file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $d=$column[0];
            if($d[0]!='d'){
            $d = str_replace('janvier', 'january 2022', $d);
            $d = str_replace('février', 'february 2022', $d);
            $d = str_replace('mars', 'march 2022', $d);
            $d = str_replace('avril', 'april 2022', $d);
            $d = str_replace('mai', 'may 2022', $d);
            $d = str_replace('juin', 'june 2022', $d);
            $d = str_replace('juillet', 'july 2022', $d);
            $d = str_replace('août', 'august 2022', $d);
            $d = str_replace('septembre', 'september 2022', $d);
            $d = str_replace('octobre', 'october 2022', $d);
            $d = str_replace('novembre', 'november 2022', $d);
            $d = str_replace('décembre', 'december 2022', $d);

            $d = str_replace('lundi ', '', $d);
            $d = str_replace('mardi ', '', $d);
            $d = str_replace('mercredi ', '', $d);
            $d = str_replace('jeudi ', '', $d);
            $d = str_replace('vendredi ', '', $d);
            
            $date = date('Y-m-d H:i:s', strtotime($d));
            $duree = $column[1];
            $nom_e = trim($column[2]);
            $prenom_e = trim($column[3]);
            $classe_e = trim($column[4]);
            $nom_p = trim($column[5]);
            $prenom_p = trim($column[6]);
            $lieu = trim($column[8]);
            $observ = trim($column[7]);
            $couleur = trim($column[9]);

            if(!ctype_digit($duree)){
                echo("<p>erreur avec la duree du rendez-vous de ". $prenom_e." ".$nom_e." du ".$date."</p>");
            }
            elseif(strlen($couleur)!=7){
                echo("<p>problemme avec le code couleur du rendez-vous de ". $prenom_e." ".$nom_e." du ".$date."</p>");
            }
            else{
            $sqlqueryy = "SELECT id FROM `elleve` where UPPER(nom) = UPPER(\"".$nom_e."\") and UPPER(prenom) =UPPER(\"".$prenom_e."\")";
            $recipesStatementt = $pdo->prepare($sqlqueryy);
            $recipesStatementt->execute();
            $recipess = $recipesStatementt->fetchAll();
            $ide = 0;
            foreach ($recipess as $ress){
                $ide = $ress['id'];
            }

            if(isset($nom_p[1])){
                $sqlqueryy = "SELECT id FROM `proph` where UPPER(nom) = UPPER(\"".$nom_p."\") and UPPER(prenom) =UPPER(\"".$prenom_p."\")";
                $recipesStatementt = $pdo->prepare($sqlqueryy);
                $recipesStatementt->execute();
                $recipess = $recipesStatementt->fetchAll();
                $idp = 0;
                foreach ($recipess as $ress){
                    $idp = $ress['id'];
                }
            }
            else{
                $idp=-1;
            }
            
            if($ide == 0){
                $query=$pdo->prepare("INSERT INTO elleve (nom, prenom, classe) VALUES (:n, :p, :c)");
                $query->bindValue(':n', $nom_e, PDO::PARAM_STR);
                $query->bindValue(':p', $prenom_e, PDO::PARAM_STR);
                $query->bindValue(':c', $classe_e, PDO::PARAM_STR);
                $query->execute();
                $query->CloseCursor();
                
                $sqlqueryy = "SELECT id FROM `elleve` where nom =\"".$nom_e."\" and prenom =\"".$prenom_e."\"";
                $recipesStatementt = $pdo->prepare($sqlqueryy);
                $recipesStatementt->execute();
                $recipess = $recipesStatementt->fetchAll();
                $ide = 0;
                foreach ($recipess as $ress){
                    $ide = $ress['id'];
                }
                echo('<p>ajout élève : '.$prenom_e.' '.$nom_e.' classe : '.$classe_e.'</p>');
            }
            if($idp == 0){
                $query=$pdo->prepare("INSERT INTO proph (nom, prenom) VALUES (:n, :p)");
                $query->bindValue(':n', $nom_p, PDO::PARAM_STR);
                $query->bindValue(':p', $prenom_p, PDO::PARAM_STR);
                $query->execute();
                $query->CloseCursor();
                
                $sqlqueryy = "SELECT id FROM `proph` where nom =\"".$nom_p."\" and prenom =\"".$prenom_p."\"";
                $recipesStatementt = $pdo->prepare($sqlqueryy);
                $recipesStatementt->execute();
                $recipess = $recipesStatementt->fetchAll();
                $idp = 0;
                foreach ($recipess as $ress){
                    $idp = $ress['id'];
                }
                echo('<p>ajout prof : '.$prenom_p.' '.$nom_p.'</p>');
            }

            if($idp  == -1){
                $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, date, durre, couleur, lieu) VALUES (:rdv, :ide, :date, :durre, :coulleur, :lieu)");
            }
            else{
                $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, id_proph, date, durre, couleur, lieu) VALUES (:rdv, :ide, :idp, :date, :durre, :coulleur, :lieu)");
                $query->bindValue(':idp', $idp, PDO::PARAM_INT);
            }
            $query->bindValue(':rdv', $observ, PDO::PARAM_STR);
            $query->bindValue(':ide', $ide, PDO::PARAM_INT);
            $query->bindValue(':date', $date, PDO::PARAM_STR);
            $query->bindValue(':durre', $duree, PDO::PARAM_INT);
            $query->bindValue(':coulleur', $couleur, PDO::PARAM_STR);
            $query->bindValue(':lieu', $lieu, PDO::PARAM_STR);
            $query->execute();
            
            if($pdo->errorInfo()[0] != 0000){
                echo("<p>".$pdo->errorInfo()[0]."</p>");
                echo('<p>erreur d\'ajout de données</p>');
            }
            $i++;
            }}
        }
    }
    }

?>
<!DOCTYPE html>
<html>
<head>
  <title>Importation csv</title>
</head>
<body>
    <form enctype="multipart/form-data" action="" method="post">
        <div class="input-row">
            <label class="col-md-4 control-label">Choisir un fichier CSV</label>
            <input type="file" name="file" id="file" accept=".csv">
            <br />
            <br />
            <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
            <br />
        </div>
    </form>
    <br/><br/>
    <form action="index.php?key=consecteturadipiscingelit" method="POST">
        <button>RETOUR ACCUEIL</button>
    </form>
</body>
</html>
<?php } ?>