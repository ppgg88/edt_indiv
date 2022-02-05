<?php
    include('fonction.php');
    if(isset($_GET['key']) && test_id($_GET['key'])){
    if(isset($_POST['Envoyer'])){
        include('log_bdd.php');
        $repeat = $_POST['nrepeat'];
        $dd = new DateTime($_POST['date_j']." ".$_POST['date'].":00");
        while($repeat>=1){
            $d = $dd->format('Y-m-d H:i:s');
            if($_POST['idp']  == 0){
                $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, date, durre, couleur, lieu) VALUES (:rdv, :ide, :date, :durre, :coulleur, :lieu)");
            }
            else{
                $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, id_proph, date, durre, couleur, lieu) VALUES (:rdv, :ide, :idp, :date, :durre, :coulleur, :lieu)");
                $query->bindValue(':idp', $_POST['idp'], PDO::PARAM_INT);
            }
            $query->bindValue(':rdv', $_POST['rdv'], PDO::PARAM_STR);
            $query->bindValue(':ide', $_POST['ide'], PDO::PARAM_INT);
            $query->bindValue(':date', $d, PDO::PARAM_STR);
            $query->bindValue(':durre', $_POST['durre'], PDO::PARAM_INT);
            $query->bindValue(':coulleur', $_POST['coulleur'], PDO::PARAM_STR);
            $query->bindValue(':lieu', $_POST['lieu'], PDO::PARAM_STR);
            $query->execute();
            date_add($dd, date_interval_create_from_date_string('1 weeks'));
            $repeat = $repeat-1;
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Nouveau RDV</title>
    </head>
    <body>
        <h3>AJOUTER UN RENDEZ-VOUS :</h3>
        <form method="post" action="">
                <label for="ide"> élève : </label><?php select_elleves(); ?><br />
                <label for="date_j">Date</label> <input type="date"  name="date_j" id="date_j" value=""/><br />
                <label for="date">Heure</label> <input type="time"  name="date" id="date" value=""/><br />
                <label for="durre"> Durée</label> <input type="number"  name="durre" id="durre" value=""/><br />
                <label for="idp"> Encadrant : </label><?php select_profs(); ?><br />
                <label for="rdv">Observation/Détail</label> <input type="text"  name="rdv" id="rdv" value=""/><br />
                <label for="lieu">Lieu</label> <input type="texte"  name="lieu" id="lieu" value=""/><br />
                <label for="coulleur">Couleur</label><!-- <input type="color"  name="coulleur" id="coulleur" value="#000000"/><br />-->
                <select name="coulleur">
                    <option value=0>--couleur--</option>
                    <option style="background:#9BD9EE;" value='#9BD9EE'>CDR</option>
                    <option style="background:#7CCB06;" value='#7CCB06'>Pépinière</option>
                    <option style="background:#ADFF2F;" value='#ADFF2F'>Serres</option>
                    <option style="background:#DF9FDF;" value='#DF9FDF'>Individualisation</option>
                    <option style="background:#DBE2D0;" value='#DBE2D0'>Cours prof</option>
                    <option style="background:#F3E768;" value='#F3E768'>Arexhor</option>
                    <option style="background:#FD9BAA;" value='#FD9BAA'>A confirmer</option>
                </select></br>
                <label for="nrepeat">Nombre de semaines à répeter</label><input type="number" id="nrepeat" name="nrepeat" min="1" max="52" value="1"><br />
                <input type="submit" name="Envoyer" value="Envoyer" />
        </form>
        <br/><br/><br/>
        <form action="index.php?key=<?php echo($_GET['key']); ?>" method="POST">
            <button>RETOUR ACCUEIL</button>
        </form>
    </body>
</html>
<?php } ?>