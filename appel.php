<?php 
    if(isset($_GET['idp']) && isset($_GET['key']) && isset($_GET['id_craineau']) && isset($_GET['semaine'])){
    include('fonction.php');
    include('log_bdd.php');
    class rdv{
        public $id;
        public $date;
        public $nom;
        public $durré;
        public $couleur;
        public $id_proph = NULL;
        public $id_eleves;
        public $lieu;
        public $suite = FALSE;

        function __construct($id, $d, $n, $du,$c, $ide, $idp = NULL, $l){
            $this->id = $id;
            $this->date = $d;
            $this->nom = $n;
            $this->durré = $du;
            $this->couleur = $c;
            $this->id_eleves = $ide;
            $this->id_proph = $idp;
            $this->lieu = $l;
        }
    }
    class craineau{
        public $id_rdv;
        public $date;
        public $date_min;
        public $nom;
        public $durré;
        public $durré_max;
        public $couleur;
        public $id_proph = NULL;
        public $id_eleves;
        public $lieu;

        function __construct($d, $n, $du,$c, $ide, $idp, $l, $id){
            $this->date[] = $d;
            $this->date_min = $d;
            $this->nom = $n;
            $this->durré[] = $du;
            $this->durré_max = $du;
            $this->couleur = $c;
            $this->id_eleves[] = $ide;
            $this->id_rdv[] = $id;
            $this->id_proph = $idp;
            $this->lieu = $l;
        }
    }
    //recuperation des RDV dans la BDD
    $sqlquery = "SELECT * FROM `rdv` WHERE id_proph = ".$_GET['idp']." ORDER BY date";
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    $id_max = 0;
    $craineau_max = 0;
    $craineau = array();
    $rdv_ = array();
    foreach ($recipes as $res){
        $rdv_[$id_max] = new rdv($res['id'],strtotime($res['date']), $res['nom'], $res['durre'], $res['couleur'],$res['id_elleve'], $res['id_proph'], $res['lieu']);
        $crai_exist = false;
        foreach($craineau as $crai){
            if((($crai->date_min >= $rdv_[$id_max]->date) and (($rdv_[$id_max]->date+60*$rdv_[$id_max]->durré) > $crai->date_min)) or ((($crai->date_min+60*$crai->durré_max) >$rdv_[$id_max]->date) and (($rdv_[$id_max]->date+60*$rdv_[$id_max]->durré) > ($crai->date_min+60*$crai->durré_max)))){
                $crai->id_eleves[]=$rdv_[$id_max]->id_eleves;
                $crai->date[]=$rdv_[$id_max]->date;
                $crai->durré[]=$rdv_[$id_max]->durré;
                $crai->id_rdv[]=$rdv_[$id_max]->id;
                $crai_exist = true;
                if($crai->date_min>$rdv_[$id_max]->date) $crai->date_min = $rdv_[$id_max]->date;
                if($crai->date_min+$crai->durré_max*60 < $rdv_[$id_max]->date+60*$rdv_[$id_max]->durré) $crai->durré_max = (($rdv_[$id_max]->date+60*$rdv_[$id_max]->durré)-$crai->date_min)/60;
            }
        }
        if($crai_exist == false){
            $craineau[$craineau_max] = new craineau($rdv_[$id_max]->date, $rdv_[$id_max]->nom, $rdv_[$id_max]->durré, $rdv_[$id_max]->couleur, $rdv_[$id_max]->id_eleves, $rdv_[$id_max]->id_proph, $rdv_[$id_max]->lieu,$rdv_[$id_max]->id);
            $craineau_max =$craineau_max + 1;
        }
        $id_max = $id_max + 1;
    }

    //validation de l'apelle, abs = 1 present, 0 abs, 2 excuser
    if(isset($_POST['Envoyer'])){
        foreach ($craineau[$_GET['id_craineau']]->id_rdv as $id){
            $sqlquery = "SELECT * FROM `rdv` WHERE id = ".$id;
            $recipesStatement = $pdo->prepare($sqlquery);
            $recipesStatement->execute();
            $recipes = $recipesStatement->fetchAll();
            foreach ($recipes as $res){
                if(isset($_POST[$res['id_elleve']])){
                    $query=$pdo->prepare("UPDATE rdv SET abs = 1 WHERE id = :id");
                    $query->bindValue(':id', $res['id'], PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();
                }
                elseif($res['abs']!=2){
                    $query=$pdo->prepare("UPDATE rdv SET abs = 0 WHERE id = :id");
                    $query->bindValue(':id', $res['id'], PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();
                }
            }
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <form method="post" action="">
            <?php 
            foreach($craineau[$_GET['id_craineau']]->id_eleves as $ide){
                $sqlquery = "SELECT * FROM `elleve` WHERE id = ".$ide;
                $recipesStatement = $pdo->prepare($sqlquery);
                $recipesStatement->execute();
                $recipes = $recipesStatement->fetchAll();
                foreach ($recipes as $res){
                    echo('<div><input type="checkbox" id="'.$res['id'].'" name="'.$res['id'].'" value="'.$res['id'].'" checked><label for="'.$res['id'].'">'.$res['nom'].' '.$res['prenom'].'</label></div>');
                }
            }
            ?>
            <input type="submit" name="Envoyer" value="Envoyer" />
        </form>
    </body>
</html>

<?php } ?>