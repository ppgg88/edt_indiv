<?php 
    include('fonction.php');
    include('log_bdd.php');
    if(isset($_GET['idp']) && isset($_GET['key']) && isset($_GET['id_craineau']) && isset($_GET['semaine']) && (test_id($_GET['key']) || test_id_prof($_GET['idp'], $_GET['semaine'], $_GET['key']))){
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

    //validation de l'apelle, abs = 1 present, -1 abs, 2 excuser
    if(isset($_POST['Envoyer'])){
        foreach ($craineau[$_GET['id_craineau']]->id_rdv as $id){
            $sqlquery = "SELECT * FROM `rdv` WHERE id = ".$id;
            $recipesStatement = $pdo->prepare($sqlquery);
            $recipesStatement->execute();
            $recipes = $recipesStatement->fetchAll();
            foreach ($recipes as $res){
                if(isset($_POST[$res['id_elleve']])){
                    $query=$pdo->prepare("UPDATE `rdv` SET `date`= :d, `abs` = 1 WHERE id = :id");
                    $query->bindValue(':id', $res['id'], PDO::PARAM_INT);
                    $query->bindValue(':d', $res['date'], PDO::PARAM_STR);
                    $query->execute();
                    $query->CloseCursor();
                }
                elseif($res['abs']!=2){
                    $query=$pdo->prepare("UPDATE `rdv` SET `date`= :d, `abs` = -1 WHERE id = :id");
                    $query->bindValue(':id', $res['id'], PDO::PARAM_INT);
                    $query->bindValue(':d', $res['date'], PDO::PARAM_STR);
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
    <style>
        #stat{
            display: inline;
            font-size: 4vh;
            margin-left: 2vh;
        }
        body{
            margin: 0px;
            padding: 8px;
            background-color: #F4FFF4;
        }
        /* The container */
        .container {
            display: inline-block;
            position: relative;
            padding-left: 10vw;
            margin-bottom: 1vh;
            cursor: pointer;
            font-size: 4vh;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 4vh;
            width: 4vh;
            background-color: #eee;
        }

        /* On mouse-over, add a grey background color */
        .container:hover input ~ .checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .container input:checked ~ .checkmark {
            background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .container input:checked ~ .checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .container .checkmark:after {
            left: 0.5vh;
            top: 0.5vh;
            width: 2vh;
            height: 2vh;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .btn{
            font-size: 3vh;
        }
    </style>
    </head>
    <body>
        <form method="post" action="">
            <?php 
            foreach($craineau[$_GET['id_craineau']]->id_rdv as $idr){

                $sqlqueryy = "SELECT * FROM `rdv` WHERE id = ".$idr;
                $recipesStatementt = $pdo->prepare($sqlqueryy);
                $recipesStatementt->execute();
                $recipess = $recipesStatementt->fetchAll();
                foreach ($recipess as $ress){
                    $ide = $ress['id_elleve'];
                    $sqlquery = "SELECT * FROM `elleve` WHERE id = ".$ide;
                    $recipesStatement = $pdo->prepare($sqlquery);
                    $recipesStatement->execute();
                    $recipes = $recipesStatement->fetchAll();
                    foreach ($recipes as $res){
                        $exc = '';
                        $abs = '';
                        $ch = '';
                        if($ress['abs'] == 2){
                            $exc = 'Excuser';
                            $ch = 'disabled="disabled"';
                        }
                        elseif($ress['abs'] == -1)$abs = 'Absent';
                        else $ch = 'checked';
                        echo('<div><label class="container" for="'.$res['id'].'">'.$res['nom'].' '.$res['prenom'].'<input type="checkbox" id="'.$res['id'].'" name="'.$res['id'].'" value="'.$res['id'].'"'.$ch.'><span class="checkmark"></span> </label><p id="stat" style="color: orange;">'.$exc.' </p><p id="stat" style="color: red;">'.$abs.' </p></div>');
                    }
                }
            }
            ?>
            <input class="btn" type="submit" name="Envoyer" value="Envoyer" />
        </form>
        </br></br></br>
        <form class="no_print" action="edtpr.php?idp=<?php echo($_GET['idp']); ?>&key=<?php echo($_GET['key']); ?>&semaine=<?php echo($_GET['semaine']); ?>" method="POST">
        <button class="btn">RETOUR EDT PROFS</button>
    </form>
    </body>
</html>

<?php } ?>