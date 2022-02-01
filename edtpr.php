<?php 
if(isset($_GET['ide'])){
    if($_GET['ide'] == 0 && isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
        header("Location: index.php?key=consecteturadipiscingelit");
    }
    if(!isset($_GET['semaine'])){
        if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit") header("Location: edt.php?ide=".$_GET['ide']."&semaine=".date('W', time())."&key=consecteturadipiscingelit");
        else header("Location: edt.php?ide=".$_GET['ide']."&semaine=".date('W', time()));
    }
    include('log_bdd.php');
    include('fonction.php');

    //extraction du numero de la semaine
    if(strpos($_GET['semaine'],"-W")!=FALSE){
        if(isset($_GET['semaine'][7])){
            $s = $_GET['semaine'][6].$_GET['semaine'][7];
        }
        else {
            $s = $_GET['semaine'][6];
        }

        if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
            if(isset($_GET['id'])){
                header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$s."&id=".$_GET['id']."&key=consecteturadipiscingelit");
            }
            else{
                header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$s."&key=consecteturadipiscingelit");
            } 
        }
        else{
            if(isset($_GET['id'])){
                header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$s."&id=".$_GET['id']);
            }
            else{
                header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$s);
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
    
    //recuperation des RDV dans la BDD
    $sqlquery = "SELECT * FROM `rdv` WHERE id_proph = ".$_GET['idp'];
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    $id_max = 0;
    foreach ($recipes as $res)
    {
        $rdv_[$id_max] = new rdv($res['id'],strtotime($res['date']), $res['nom'], $res['durre'], $res['couleur'],$res['id_elleve'], $res['id_proph'], $res['lieu']);
        $id_max = $id_max + 1;
    }

    //creation des craineaux horraires
    class craineau{
        public $id;
        public $date;
        public $nom;
        public $durré;
        public $couleur;
        public $id_proph = NULL;
        public $id_eleves;
        public $lieu;
        public $suite = FALSE;
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
    <img  id="page-wrapper" src="icon/roville_logo.png" id="logo" style="height: 20vh; float: right; margin-right:5vw; margin-top:5vh;"/>
    <!-- SELECTION DE L'AFFICHAGE -->
<?php 
if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){ ?>
    <h4 onclick="window.print();" class="no_print" style="background: #ADFF2F; display: inline-block; padding: 1vh;"> Imprimer </h4>

    <form class="no_print" action="" method="get">
    <div>
        <label for="ide">De qui voulez vous afficher l'EDT ?</label>
        <?php select_profs($_GET['idp']); ?>
        <label for="semaine">quelle semaine ?</label> <input type="week"  name="semaine" id="semaine" value="<?php echo(date('Y', time())."-W".date('W', time())); ?>"require/>
        <input type="HIDDEN" name="key" value="consecteturadipiscingelit"/>
        <button>Validé</button>
    </div>
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
                    echo("<td onclick=\"location.href='?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&key=consecteturadipiscingelit'\" class=\"time\" rowspan=\"60\">".$b."h-".($b+1)."h</td>");
                }
            }
            $test = FALSE;
            for ($k = 0; isset($rdv_[$k]->nom); $k++) {
                //echo($heure." // ".date("H:i", $rdv_[$k]->date)."\n");
                if(date("l", $rdv_[$k]->date) == $jour[$i+1] && date("H:i", $rdv_[$k]->date) == $heure && $rdv_[$k]->id_eleves == $_GET['ide']&& date('W', $rdv_[$k]->date) == $_GET['semaine']){
                $a = "ROWSPAN=\"".($rdv_[$k]->durré)."\"";
                if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
                    echo("<td ".$a." onclick=\"location.href='?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&id=".$rdv_[$k]->id."&key=consecteturadipiscingelit'\" style=\"background-color:".$rdv_[$k]->couleur."; border : 1px solid black !important;\">");
                }
                else{
                    echo("<td ".$a." style=\"background-color:".$rdv_[$k]->couleur.";border : 1px solid black !important;\">");
                }?>
                <p style="font-weight: bold;">
                <?php echo($rdv_[$k]->nom); ?>
                </p>
                <p>
                <?php echo($heure." / ".date("H:i", strtotime(" +".$rdv_[$k]->durré."minutes", $rdv_[$k]->date))); ?>
                </p>
                    <?php 
                    echo(($rdv_[$k]->lieu));
                    $sqlquery = "SELECT * FROM `proph` WHERE `id` = ".($rdv_[$k]->id_proph);
                    $recipesStatement = $pdo->prepare($sqlquery);
                    $recipesStatement->execute();
                    $recipes = $recipesStatement->fetchAll();
                    foreach ($recipes as $res){
                        echo("<p>".$res['prenom'][0]." ".$res['nom']."</p>");
                    }            
                    $test = TRUE;
                    $rdv_[$k]->durré = $rdv_[$k]->durré - 1;
                    if($rdv_[$k]->durré != 0){
                        $pass_day[$i] = $rdv_[$k]->durré;
                    }
                }
            }
            if($test == FALSE && $pass_day[$i]==0){
                echo "<td onclick=\"test-False:location.href='?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&time=".$heure."&jour=".$jour[$i+1]."&key=consecteturadipiscingelit'\">";
            }
            else if($pass_day[$i]!=0) $pass_day[$i] = $pass_day[$i]-1;
            echo "</td>";
        }
        echo "</tr>";
    }
?>
</table>
</br>
<?php 
if(isset($_GET['key'])){
    if($_GET['key'] == "consecteturadipiscingelit"){
?>
<form class="no_print" action="index.php?key=consecteturadipiscingelit" method="POST">
    <button>RETOUR ACCUEIL</button>
</form>
<?php     
}}
?>
</body>

<?php }
else{
    echo('<h1>merci d\'utiliser le lien de connexion</h1>');
}
?>