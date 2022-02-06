<?php 
include("fonction.php");
if(isset($_GET['key']) && test_id($_GET['key'])){
    $key=$_GET['key'];
    if(isset($_GET['semaine'])){
        $semaine=$_GET['semaine'];
    }
    else{
        $semaine = date('W', time());
    }
    $i = 0;
    // Connect to database
    include("log_bdd.php");

    $sqlqueryy = "SELECT MAX(id) FROM `importation`";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    $id_importation = 0;
    foreach ($recipess as $ress){
        $id_importation = $ress[0]+1;
    }
    

    if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
    $file = fopen($fileName, "r");
    if ($_FILES["file"]["size"] > 0) {
        $sqlqueryy = "SELECT MAX(id) FROM `importation`";
        $recipesStatementt = $pdo->prepare($sqlqueryy);
        $recipesStatementt->execute();
        $recipess = $recipesStatementt->fetchAll();
        $id_importation = 0;
        foreach ($recipess as $ress){
            $id_importation = $ress[0]+1;
        }

        $query=$pdo->prepare("INSERT INTO importation (id, date, nom) VALUES (:id, :d, :n)");
        $query->bindValue(':id', $id_importation, PDO::PARAM_INT);
        $query->bindValue(':d', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $query->bindValue(':n',$_POST["name"] , PDO::PARAM_STR);
        $query->execute();
        $query->CloseCursor();

        
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
                $query=$pdo->prepare("INSERT INTO elleve (nom, prenom, classe, id_importation) VALUES (:n, :p, :c, :idi)");
                $query->bindValue(':n', $nom_e, PDO::PARAM_STR);
                $query->bindValue(':p', $prenom_e, PDO::PARAM_STR);
                $query->bindValue(':c', $classe_e, PDO::PARAM_STR);
                $query->bindValue(':idi', $id_importation, PDO::PARAM_INT);
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
                $query=$pdo->prepare("INSERT INTO proph (nom, prenom, id_importation) VALUES (:n, :p, :idi)");
                $query->bindValue(':n', $nom_p, PDO::PARAM_STR);
                $query->bindValue(':p', $prenom_p, PDO::PARAM_STR);
                $query->bindValue(':idi', $id_importation, PDO::PARAM_INT);
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
                $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, date, durre, couleur, lieu, id_importation) VALUES (:rdv, :ide, :date, :durre, :coulleur, :lieu, :idi)");
            }
            else{
                $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, id_proph, date, durre, couleur, lieu, id_importation) VALUES (:rdv, :ide, :idp, :date, :durre, :coulleur, :lieu, :idi)");
                $query->bindValue(':idp', $idp, PDO::PARAM_INT);
            }
            $query->bindValue(':rdv', $observ, PDO::PARAM_STR);
            $query->bindValue(':ide', $ide, PDO::PARAM_INT);
            $query->bindValue(':date', $date, PDO::PARAM_STR);
            $query->bindValue(':durre', $duree, PDO::PARAM_INT);
            $query->bindValue(':coulleur', $couleur, PDO::PARAM_STR);
            $query->bindValue(':lieu', $lieu, PDO::PARAM_STR);
            $query->bindValue(':idi', $id_importation, PDO::PARAM_INT);
            $query->execute();
            
            if($query->errorInfo()[0] != 0000){
                echo('<p style="color: red;font-weight: bold;">erreur d\'ajout de données rdv de '.$prenom_e.' '.$nom_e.' du '.$date.'</p>');
                if($query->errorInfo()[1] == 1062)echo('<p style="color: red;font-weight: bold;">-->'.$prenom_e.' '.$nom_e.' à deja un rendez-vous au même horraire</p>');
                else print_r($query->errorInfo());
            }
            $i++;
            }}
        }
    }
    $id_importation ++;
    }

    if(isset($_POST['Supprimer'])){
        $queryyy=$pdo->prepare("DELETE FROM `rdv` WHERE id_importation = :id");
        $queryyy->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $queryyy->execute();
        $queryyy->CloseCursor();
        $queryy=$pdo->prepare("DELETE FROM `elleve` WHERE id_importation = :id");
        $queryy->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $queryy->execute();
        $queryy->CloseCursor();
        $query=$pdo->prepare("DELETE FROM `proph` WHERE id_importation = :id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();
        $quer=$pdo->prepare("DELETE FROM `importation` WHERE id = :id");
        $quer->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $quer->execute();
        $quer->CloseCursor();
    }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Importation csv</title>
  <link rel="stylesheet" href="all.css" />
  <style type="text/css">
        .head {
            background-color: #006600 !important;
            color: white !important;
            font-size: 1.5vw !important;
            font-family: Arial, "Arial Black", Times, "Times New Roman", serif !important;
            border:1px solid red !important;
            text-align: center !important;
        }
        td{
            border-top: 1px solid black !important;
            font-size: 1.3vw !important;
            border-bottom: 1px solid black !important;
            border-collapse: collapse !important;
            padding-top: 1vw !important;
            padding-bottom: 1vw !important;
            padding-right: 0.3vw !important;
            font-weight: bold !important;
            }
        tr{
            background-color: #b9edc3;
        }
        table{
            width: 100% !important;
        }
    </style>
</head>
<body>
    <form enctype="multipart/form-data" action="" method="post">
        <div class="input-row">
            <label class="col-md-4 control-label">Choisir un fichier CSV</label>
            <input type="file" name="file" id="file" accept=".csv">
            <br />
            <label for="name">Observation : </label> <input type="text"  name="name" id="name" value="Importation <?php echo($id_importation); ?>"/>
            <br />
            <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
            <br />
        </div>
    </form>
    <br/><br/>
    <form action="index.php?key=<?php echo($key);?>&semaine=<?php echo($semaine);?>" method="POST">
        <button>RETOUR ACCUEIL</button>
    </form>
    <?php     
    $sqlqueryy = "SELECT * FROM `importation` ORDER BY `date` desc";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    echo('<table cellspacing="0" cellpadding="0">');
    echo('<tr style="padding-right: 0vw; width : 30vw" class="head">
            <td>Note</td>
            <td>Date</td>
            <td>Action</td>
        </tr>');
    foreach ($recipess as $ress){
        echo('<tr>
                <td>'.$ress['nom'].'</td>
                <td>'.$ress['date'].'</td>
                <td>
                    <form method="post" action="">
                        <input type="HIDDEN" name = "id" value="'.$ress['id'].'"/>
                        <input type="submit" name="Supprimer" value="Supprimer" />
                    </form>
                </td>
            </tr>');
    }
    echo('</table>');
    ?>
</body>
</html>
<?php } ?>