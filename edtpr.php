<?php 

include('fonction.php');
if(isset($_GET['idp'])){
    if($_GET['idp'] == 0 && isset($_GET['key']) && test_id($_GET['key'])){
        header("Location: index.php?key=".$_GET['key']);
    }
    if(!isset($_GET['semaine'])){
        if(isset($_GET['key']) && test_id($_GET['key'])) header("Location: edt.php?idp=".$_GET['idp']."&semaine=".date('W', time())."&key=".$_GET['key']);
        else header("Location: edtpr.php?idp=".$_GET['idp']."&semaine=".date('W', time()));
    }
    include('log_bdd.php');
    //extraction du numero de la semaine
    if(strpos($_GET['semaine'],"-W")!=FALSE){
        if(isset($_GET['semaine'][7])){
            $s = $_GET['semaine'][6].$_GET['semaine'][7];
        }
        else {
            $s = $_GET['semaine'][6];
        }

        if(isset($_GET['key']) && test_id($_GET['key'])){
            if(isset($_GET['id'])){
                header("Location: edtpr.php?idp=".$_GET['idp']."&semaine=".$s."&id=".$_GET['id']."&key=".$_GET['key']);
            }
            else{
                header("Location: edtpr.php?idp=".$_GET['idp']."&semaine=".$s."&key=".$_GET['key']);
            } 
        }
        else{
            if(isset($_GET['id'])){
                header("Location: edtpr.php?idp=".$_GET['idp']."&semaine=".$s."&id=".$_GET['id']);
            }
            else{
                header("Location: edtpr.php?idp=".$_GET['idp']."&semaine=".$s);
            } 
        }    
    }

    //creation de l'objet RDV
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
        public $abs;
        public $date;
        public $date_min;
        public $nom;
        public $durré;
        public $durré_max;
        public $couleur;
        public $id_proph = NULL;
        public $id_eleves;
        public $lieu;

        function __construct($d, $n, $du,$c, $ide, $idp, $l, $id, $abs){
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
            $this->abs[] = $abs;
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
        //echo('ell : '.$rdv_[$id_max]->id." // ");
        foreach($craineau as $crai){
            if((($crai->date_min <= $rdv_[$id_max]->date) and ($rdv_[$id_max]->date < $crai->date_min+60*$crai->durré_max)) or ((($crai->date_min) < ($rdv_[$id_max]->date+60*$rdv_[$id_max]->durré)) and (($rdv_[$id_max]->date+60*$rdv_[$id_max]->durré) <= ($crai->date_min+60*$crai->durré_max)))){
                $crai->id_eleves[]=$rdv_[$id_max]->id_eleves;
                //echo('add : '.$rdv_[$id_max]->id_eleves." // ");
                $crai->abs[]= $res['abs'];
                $crai->date[]=$rdv_[$id_max]->date;
                $crai->durré[]=$rdv_[$id_max]->durré;
                $crai->id_rdv[]=$rdv_[$id_max]->id;
                $crai_exist = true;
                if($crai->date_min>$rdv_[$id_max]->date) $crai->date_min = $rdv_[$id_max]->date;
                if($crai->date_min+$crai->durré_max*60 < $rdv_[$id_max]->date+60*$rdv_[$id_max]->durré) $crai->durré_max = (($rdv_[$id_max]->date+60*$rdv_[$id_max]->durré)-$crai->date_min)/60;
            }
        }
        if($crai_exist == false){
            //echo('create : '.$rdv_[$id_max]->id_eleves." // ");
            $craineau[$craineau_max] = new craineau($rdv_[$id_max]->date, $rdv_[$id_max]->nom, $rdv_[$id_max]->durré, $rdv_[$id_max]->couleur, $rdv_[$id_max]->id_eleves, $rdv_[$id_max]->id_proph, $rdv_[$id_max]->lieu,$rdv_[$id_max]->id, $res['abs']);
            $craineau_max =$craineau_max + 1;
        }
        $id_max = $id_max + 1;
    }
}
?>
<html>
<head>
    <title>EDT-Profs</title>
    <link rel="stylesheet" type="text/css" href="ent.css" />
    <style>
        body{
            margin: 0px;
            padding: 8px;
            background-color: #F4FFF4;
        }
        @media print {
                body * {
                    /*visibility: hidden;*/
                    -webkit-print-color-adjust: exact !important; // not necessary use if colors not visible
                }
                .no_print{
                    visibility: hidden;
                }
                #printBtn {
                    visibility: hidden !important; // To hide 
                }

                #page-wrapper * {
                    visibility: visible; // Print only required part
                    text-align: left;
                    -webkit-print-color-adjust: exact !important;
                }
            }
    </style>
</head>
<body>
    <?php
    if(isset($_GET['key']) and (test_id_prof($_GET['idp'], $_GET['semaine'], $_GET['key']) or test_id($_GET['key']))){
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        {
            $url_base = "https";
        }
        else
        {
            $url_base = "http"; 
        }  
        $url_base .= "://"; 
        $url_base .= $_SERVER['HTTP_HOST']; 
        $url = $url_base.'/edtpr.php?idp='.$_GET['idp'].'%26semaine='.$_GET['semaine']."%26key=".id_prof($_GET['idp'], $_GET['semaine']); 
        $qr_path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$url.'%2F&choe=UTF-8';
        echo('<img  id="page-wrapper" src="'.$qr_path.'" id="logo" style="height: 20vh; float: right; margin-right:5vw; margin-top:5vh;"/>');
    }
    ?> 
    <img  id="page-wrapper" src="icon/roville_logo.png" id="logo" style="height: 20vh; float: right; margin-right:5vw; margin-top:5vh;"/>
    <!-- SELECTION DE L'AFFICHAGE -->
<?php 
if(isset($_GET['key']) && test_id($_GET['key'])){ ?>
    <h4 onclick="window.print();" class="no_print" style="background: #ADFF2F; display: inline-block; padding: 1vh;"> Imprimer </h4>

    <form class="no_print" action="" method="get">
    <div>
        <label for="ide">De qui voulez vous afficher l'EDT ?</label>
        <?php select_profs($_GET['idp']); ?>
        <label for="semaine">quelle semaine ?</label> <input type="week"  name="semaine" id="semaine" value="<?php echo(date('Y', time())."-W".date('W', time())); ?>"require/>
        <input type="HIDDEN" name="key" value="<?php echo($_GET['key']);?>"/>
        <button>Validé</button>
    </form>
    <form class="no_print" action="index.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST">
        <button>RETOUR ACCUEIL</button>
    </form>
    <form class="no_print" action="mail.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>&idp=<?php echo($_GET['idp']);?>" method="POST">
        <button>ENVOYER PAR MAIL</button>
    </form>
<?php } ?>

    <!-- EMPLOIE DU TEMPS -->
<table  id="page-wrapper">
<?php
    function getStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $dto->setISODate($year, $week*1);
        $ret['week_start'] = $dto->format('d/m/Y');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('d/m/Y');
        return $ret;
      }
    if(isset($_GET['abs']) && $_GET['abs'] == 1){
        echo('<form class="modif" class="no_print" method="post" action="update_prof_abs.php?semaine='.$_GET['semaine']."&key=".$_GET['key'].'&idp='.$_GET['idp'].'">
                <input type="HIDDEN" name = "idp" value="'.$_GET['idp'].'"/>
                <label class="modif" for="abs">Absence : </label>
                <select class="modif" name="abs">
                    <option value=\'4\'>Absent</option>
                    <option value=\'3\'>Present</option>
                </select><br />
                <label class="modif" for="date_d">Date Debut</label> <input class="modif" type="date"  name="date_d" id="date_d"/><br />
                <label class="modif" for="date_f">Date Fin</label> <input class="modif" type="date"  name="date_f" id="date_f"/><br />
                <label class="modif" for="notifpr">Notifier Prof</label><input class="modif" type="checkbox" name="notifpr" value="notifpr"></br>
                <input class="modif centre" style="margin-top:1vh;" type="submit" name="absent" value="Declarer Absent" /><br />
            </form>');
            echo('<form class="modif" class="no_print" method="post" action="edtpr.php?semaine='.$_GET['semaine']."&key=".$_GET['key'].'&idp='.$_GET['idp'].'">
                <input class="modif centre" style="margin-top:1vh;" type="submit" name="absent" value="Cacher Absence" />
            </form>');
            
    }
    else{
        echo('<form class="modif" class="no_print" method="post" action="edtpr.php?semaine='.$_GET['semaine']."&key=".$_GET['key'].'&idp='.$_GET['idp'].'&abs=1">
                <input class="modif centre" style="margin-top:1vh;" type="submit" name="absent" value="Afficher Absence" />
            </form>');
    }
    //affichage du nom est prenom
    $sqlquery = "SELECT * FROM `proph` where id = ".$_GET['idp'];
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    foreach ($recipes as $res)
    {
        echo('<h3 id="nom_prenom"  id="page-wrapper">'.$res['prenom'].' '.$res['nom'].'</h3>');
    }
    echo('<h3 id="nom_prenom"  id="page-wrapper">Semaine du '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_start'].' au '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_end'].'</h3>');
    //afichage de l'edt
    $actuel = NULL;
    $min = 8 * 60; //8h
    $max = 19 * 60; //19h
    $jour = array(null, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $jourfr = array(null, "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
    $pass_day = array(0,0,0,0,0);
    echo "<tr id=\"page-wrapper\"><th>Heure</th>";
    for($x = 1; $x < 6; $x++)
        echo "<th>".$jourfr[$x]."</th>";
    echo "</tr id=\"page-wrapper\">";
    for($j = $min; $j < $max; $j += 1) {
        if(($j % 60) == 0) $border = "style=\"border-top: 1px solid black !important;\"";
        else $border = "";
        echo ("<tr id=\"page-wrapper\" ".$border.">");
        for($i = 0; $i < 5; $i++) {
            if($i == 0) {
                $b = intdiv($j, 60);
                $c = $j%60;
                if($b<10)$b = "0".$b;
                if($c<10)$c = "0".$c;
                $heure = $b.":".$c;
                if(substr($heure,-3,3) == ":00"){
                    echo("<td class=\"time\" rowspan=\"60\">".$b."h-".($b+1)."h</td>");
                }
            }
            $test = FALSE;
            for ($k = 0; isset($craineau[$k]->nom); $k++) {
                //echo($heure." // ".date("H:i", $rdv_[$k]->date)."\n");
                if(date("l", $craineau[$k]->date_min) == $jour[$i+1] && date("H:i", $craineau[$k]->date_min) == $heure && $craineau[$k]->id_proph == $_GET['idp']&& date('W', $craineau[$k]->date_min) == $_GET['semaine']){
                $a = "ROWSPAN=\"".($craineau[$k]->durré_max)."\"";
                if(isset($_GET['key']) && (test_id($_GET['key']) || test_id_prof($_GET['idp'], $_GET['semaine'], $_GET['key']))){
                    echo("<td ".$a." onclick=\"location.href='appel.php?idp=".$_GET['idp']."&semaine=".$_GET['semaine']."&key=".$_GET['key']."&id_craineau=".$k."'\" style=\"background-color:".$craineau[$k]->couleur."; border : 1px solid black !important;\">");
                }
                else{
                    echo("<td ".$a." style=\"background-color:".$craineau[$k]->couleur.";border : 1px solid black !important;\">");
                }?>
                <p style="margin-bottom:1vh!important; font-weight: bold;">
                <?php echo($craineau[$k]->lieu); ?>
                </p>
                <?php
                        foreach($craineau[$k]->id_eleves as $ell){
                           
                            $sqlquery = "SELECT * FROM `elleve` WHERE `id` = ".$ell;
                            $color = '';
                            $recipesStatement = $pdo->prepare($sqlquery);
                            $recipesStatement->execute();
                            $recipes = $recipesStatement->fetchAll();
                            foreach ($recipes as $res){
                                $index = array_search($res['id'], $craineau[$k]->id_eleves);
                                if($craineau[$k]->abs[$index]==-1)$color = 'red';
                                elseif($craineau[$k]->abs[$index]==2)$color = '#CC8822 ';
                                elseif($craineau[$k]->abs[$index]==4)$color = '#ff8b00 ';
                                else $color = 'black';
                                $date_end = strtotime(date("Y-m-d H:i:s", $craineau[$k]->date[$index])."+ {$craineau[$k]->durré[$index]} minutes");
                                echo("<p style='font-size: calc(0.7vh + 0.3vw)!important; color:".$color."!important;'><b>".$res['prenom'][0]." ".$res['nom']." ".$res['classe']." </b>".date("H:i", $craineau[$k]->date[$index])."-".date("H:i", $date_end)."</p>");
                            }
            
                        }   
                    $test = TRUE;
                    $craineau[$k]->durré_max = $craineau[$k]->durré_max -1;
                    if($craineau[$k]->durré_max != 0){
                        $pass_day[$i] = $craineau[$k]->durré_max;
                    }
                }
            }
            if($test == FALSE && $pass_day[$i]==0){
                echo "<td>";
            }
            else if($pass_day[$i]!=0) $pass_day[$i] = $pass_day[$i]-1;
            echo "</td>";
        }
        echo "</tr>";
    }
?>
</table>
</br>
</body>