<?php 
include('fonction.php');
if(isset($_GET['ide'])){
    if($_GET['ide'] == 0 && isset($_GET['key']) && test_id($_GET['key'])){
        header("Location: index.php?key=".$_GET['key']);
    }
    if(!isset($_GET['semaine'])){
        if(isset($_GET['key']) && test_id($_GET['key'])) header("Location: edt.php?ide=".$_GET['ide']."&semaine=".date('W', time())."&key=".$_GET['key']);
        else header("Location: edt.php?ide=".$_GET['ide']."&semaine=".date('W', time()));
    }
    include('log_bdd.php');
    //extraction du numero de la semaine :
    if(strpos($_GET['semaine'],"-W")!=FALSE){
        if(isset($_GET['semaine'][7])){
            $s = $_GET['semaine'][6].$_GET['semaine'][7];
        }
        else {
            $s = $_GET['semaine'][6];
        }

        if(isset($_GET['key']) && test_id($_GET['key'])){
            if(isset($_GET['id'])){
                header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$s."&id=".$_GET['id']."&key=".$_GET['key']);
            }
            else{
                header("Location: edt.php?ide=".$_GET['ide']."&semaine=".$s."&key=".$_GET['key']);
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
        public $abs;

        function __construct($id, $d, $n, $du,$c, $ide, $idp = NULL, $l, $abs){
            $this->id = $id;
            $this->date = $d;
            $this->nom = $n;
            $this->durré = $du;
            $this->couleur = $c;
            $this->id_eleves = $ide;
            $this->id_proph = $idp;
            $this->lieu = $l;
            $this->abs = $abs;
        }
    }
    
    //recuperation des RDV dans la BDD
    $sqlquery = "SELECT * FROM `rdv` WHERE id_elleve = ".$_GET['ide'];
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    $id_max = 0;
    foreach ($recipes as $res)
    {
        $rdv_[$id_max] = new rdv($res['id'],strtotime($res['date']), $res['nom'], $res['durre'], $res['couleur'],$res['id_elleve'], $res['id_proph'], $res['lieu'], $res['abs']);
        $id_max = $id_max + 1;
    }

?>
<html>
<head>
    <title>EDT-Elèves</title>
    <link rel="stylesheet" href="all.css" />
    <link rel="stylesheet" type="text/css" href="ent.css" />
    <style>
        input.modif, select.modif{
            width:20vw;
        }
        label.modif{
            display:inline-block;
            width:7vw;
            text-align:right;
        }
        a{
            text-decoration: none;
            font-weight: bold ;
            text-align: center;
            color: black; 
        }
        #trash{
            display:block;
            margin-left:auto;
            margin-right:auto;
        }
        body{
            margin: 0px;
            padding: 8px;
            <?php if(isset($_GET['view']) && $_GET['view']==0){?>
            overflow-y: hidden; 
            <?php } ?>
        }
        tr{
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
    <?php if(!(isset($_GET['view'])) || $_GET['view']==1){ ?><img  id="page-wrapper" src="icon/roville_logo.png" id="logo" style="height: 20vh; float: right; margin-right:5vw; margin-top:5vh;"/>
    <!-- SELECTION DE L'AFFICHAGE -->
<?php
if(isset($_GET['key']) && test_id($_GET['key'])){ ?>
    <h4 onclick="window.print();" class="no_print" style="background: #ADFF2F; display: inline-block; padding: 1vh;"> Imprimer </h4>

    <form class="no_print" action="" method="get">
        <label for="ide">De qui voulez vous afficher l'EDT ?</label>
        <?php select_elleves($_GET['ide']); ?>
        <label for="semaine">quelle semaine ?</label> <input type="week"  name="semaine" id="semaine" value="<?php echo(date('Y', time())."-W".$_GET['semaine']); ?>"require/>
        <input type="HIDDEN" name="key" value="<?php echo($_GET['key']);?>"/>
        <button>Validé</button>
    </form>
    <form class="no_print" action="index.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST">
        <button>RETOUR ACCUEIL</button>
    </form>
<?php } 
}?>

    <!-- modification eventuel de l'EDT -->
<?php 
if(isset($_GET['id']) && isset($_GET['key']) && test_id($_GET['key'])){
    //requette sql de recherche du rdv dans la bdd :
    $sqlquery = "SELECT * FROM rdv WHERE id =".$_GET['id'];
        $recipesStatement = $pdo->prepare($sqlquery);
        $recipesStatement->execute();
        $recipes = $recipesStatement->fetchAll();
        foreach ($recipes as $res){ ?>
        <table>
            <tr>
                <td>
                    <form style="margin-top:1vh;" class="no_print" method="post" action="update.php?id=<?php echo($_GET['id']); ?>&semaine=<?php echo($_GET['semaine']); if(test_id($_GET['key'])){echo("&key=".$_GET['key']);}?>">
                        <label class="modif" for="rdv">Nom du RDV</label> <input class="modif" type="text"  name="rdv" id="rdv" value="<?php echo($res['nom']);?>"/><br />
                        <label class="modif" for="ide">Eleves</label>
                        <?php select_elleves($res['id_elleve'], "modif"); ?>
                        <br />
                        <label class="modif" for="idp">Prof</label>
                        <?php select_profs($res['id_proph'], "modif"); ?>
                        <br />
                        <label class="modif" for="date_j">Date</label> <input class="modif" type="date"  name="date_j" id="date_j" value="<?php echo(date('Y-m-d', strtotime($res['date'])));?>"/><br />
                        <label class="modif" for="date">Heure</label> <input class="modif" type="time"  name="date" id="date" value="<?php echo(date('H:i:s', strtotime($res['date'])));?>"/><br />
                        <label class="modif" for="durre">Durre</label> <input class="modif" type="number"  name="durre" id="durre" value="<?php echo($res['durre']);?>"/><br />
                        <label class="modif" for="lieu">Lieu</label> <input class="modif" type="texte"  name="lieu" id="lieu" value="<?php echo($res['lieu']);?>"/><br />
                        <label class="modif" for="coulleur">Couleur</label>
                        <select class="modif" name="coulleur">
                            <option style="background:#9BD9EE;" value='#9BD9EE' <?php if(strtoupper($res['couleur'])=='#9BD9EE') echo('selected="selected"'); ?>>CDR</option>
                            <option style="background:#7CCB06;" value='#7CCB06' <?php if(strtoupper($res['couleur'])=='#7CCB06') echo('selected="selected"'); ?>>Pépinière</option>
                            <option style="background:#ADFF2F;" value='#ADFF2F' <?php if(strtoupper($res['couleur'])=='#ADFF2F') echo('selected="selected"'); ?>>Serres</option>
                            <option style="background:#DF9FDF;" value='#DF9FDF' <?php if(strtoupper($res['couleur'])=='#DF9FDF') echo('selected="selected"'); ?>>Individualisation</option>
                            <option style="background:#DBE2D0;" value='#DBE2D0' <?php if(strtoupper($res['couleur'])=='#DBE2D0') echo('selected="selected"'); ?>>Cours prof</option>
                            <option style="background:#F3E768;" value='#F3E768' <?php if(strtoupper($res['couleur'])=='#F3E768') echo('selected="selected"'); ?>>Arexhor</option>
                            <option style="background:#FD9BAA;" value='#FD9BAA' <?php if(strtoupper($res['couleur'])=='#FD9BAA') echo('selected="selected"'); ?>>A confirmer</option>
                        </select><br />
                        <label class="modif" for="abs">Statut Absence</label>
                        <select class="modif" name="abs">
                            <option value='3' <?php if($res['abs']==0) echo('selected="selected"'); ?>>Non Renseigner</option>
                            <option value='-1' <?php if($res['abs']==-1) echo('selected="selected"'); ?>>Absent</option>
                            <option value='1' <?php if($res['abs']==1) echo('selected="selected"'); ?>>Present</option>
                            <option value='2' <?php if($res['abs']==2) echo('selected="selected"'); ?>>Annuler</option>
                        </select><br />
                        <input class="modif" style="margin-top:1vh;" type="submit" name="Envoyer" value="Envoyer" />
                        <a style="margin-top:2vh;" href = "<?php echo("edt.php?ide=".$_GET['ide']."&semaine=".$_GET['semaine']);if(test_id($_GET['key'])){echo("&key=".$_GET['key']);}?>"><img src="icon/close.png" style="height : 5vh;"/></a>
                    </form>
                </td>
                <td>
                    <form class="modif" class="no_print" method="post" action="update.php?id=<?php echo($_GET['id']); ?>&semaine=<?php echo($_GET['semaine']); if(test_id($_GET['key'])){echo("&key=".$_GET['key']);}?>">
                        <input type="HIDDEN" name = "ide" value="<?php echo($res['id_elleve']); ?>"/>
                        <label class="modif" for="abs">Absence</label>
                        <select class="modif" name="abs">
                            <option value='3' <?php if($res['abs']==0) echo('selected="selected"'); ?>>Non Renseigner</option>
                            <option value='-1' <?php if($res['abs']==-1) echo('selected="selected"'); ?>>Absent</option>
                            <option value='1' <?php if($res['abs']==1) echo('selected="selected"'); ?>>Present</option>
                            <option value='2' <?php if($res['abs']==2) echo('selected="selected"'); ?>>Annuler</option>
                        </select><br />
                        <label class="modif" for="date_d">Date Debut</label> <input class="modif" type="date"  name="date_d" id="date_d" value="<?php echo(date('Y-m-d', strtotime($res['date'])));?>"/><br />
                        <label class="modif" for="date_f">Date Fin</label> <input class="modif" type="date"  name="date_f" id="date_f" value="<?php echo(date('Y-m-d', strtotime($res['date'])));?>"/><br />
                        <input style="margin-top:1vh;" class="modif" type="submit" name="absent" value="Declarer Absent" />
                    </form>
                </td>
                <td>
                    <a onclick="if(confirm('Vous allez suprimer le rendez-vous, Etes-vous sur ?')){return true;}else{return false;}" href = "<?php echo("edt_supr.php?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&idrdv=".$_GET['id']."&key=".$_GET['key']);?>">
                        <p>Supprimer</p>
                        <img id="trash" src="icon/trash.png" style="height : 8vh;"/>
                    </a>
                </td>
            </tr>
        </table>
<?php } } ?>

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
    $sqlquery = "SELECT * FROM `elleve` where id = ".$_GET['ide'];
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    foreach ($recipes as $res)
    {
        if(!(isset($_GET['view'])) || $_GET['view']==1){echo('<h3 id="nom_prenom"  id="page-wrapper">'.$res['prenom'].' '.$res['nom'].' '.$res['classe'].'</h3>');}
    }
    if(!(isset($_GET['view'])) || $_GET['view']==1)echo('<h3 id="nom_prenom"  id="page-wrapper">Semaine du '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_start'].' au '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_end'].'</h3>');
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
            for ($k = 0; isset($rdv_[$k]->nom); $k++) {
                //echo($heure." // ".date("H:i", $rdv_[$k]->date)."\n");
                if(date("l", $rdv_[$k]->date) == $jour[$i+1] && date("H:i", $rdv_[$k]->date) == $heure && $rdv_[$k]->id_eleves == $_GET['ide']&& date('W', $rdv_[$k]->date) == $_GET['semaine']){
                $a = "ROWSPAN=\"".($rdv_[$k]->durré)."\"";
                if(isset($_GET['key']) && test_id($_GET['key'])){
                    echo("<td ".$a." onclick=\"location.href='?ide=".$_GET['ide']."&semaine=".$_GET['semaine']."&id=".$rdv_[$k]->id."&key=".$_GET['key']."'\" style=\"background-color:".$rdv_[$k]->couleur."; border : 1px solid black !important;\">");
                }
                else{
                    echo("<td ".$a." style=\"background-color:".$rdv_[$k]->couleur.";border : 1px solid black !important;\">");
                }?>
                <p style="font-weight: bold;">
                <?php echo($rdv_[$k]->lieu); ?>
                </p>
                <p>
                <?php echo($heure." / ".date("H:i", strtotime(" +".$rdv_[$k]->durré."minutes", $rdv_[$k]->date))); ?>
                </p>
                    <?php 
                    echo(($rdv_[$k]->nom));
                    $sqlquery = "SELECT * FROM `proph` WHERE `id` = ".($rdv_[$k]->id_proph);
                    $recipesStatement = $pdo->prepare($sqlquery);
                    $recipesStatement->execute();
                    $recipes = $recipesStatement->fetchAll();
                    foreach ($recipes as $res){
                        echo("<p>".$res['prenom'][0]." ".$res['nom']."</p>");
                    }
                    if($rdv_[$k]->abs == -1) echo('<p style="color:red">Absent</p>');
                    elseif($rdv_[$k]->abs == 2) echo('<p style="color:#CC8822">Annuler</p>');     
                    $test = TRUE;
                    $rdv_[$k]->durré = $rdv_[$k]->durré - 1;
                    if($rdv_[$k]->durré != 0){
                        $pass_day[$i] = $rdv_[$k]->durré;
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

<?php }
else{
    echo('<h1>merci d\'utiliser le lien de connexion</h1>');
}
?>