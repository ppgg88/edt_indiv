<?php
include('log_bdd.php');
include('fonction.php');

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

if(!(isset($_GET['key']) && test_id($_GET['key']))) header("Location: edt.php");

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
    public $qr_url;

    function __construct($id, $d, $n, $du,$c, $ide, $idp = NULL, $l, $qr){
        $this->id = $id;
        $this->date = $d;
        $this->nom = $n;
        $this->durré = $du;
        $this->couleur = $c;
        $this->id_eleves = $ide;
        $this->id_proph = $idp;
        $this->lieu = $l;
        $this->qr_url = $qr;
    }
}

function getStartAndEndDate($week, $year) {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $ret['week_start'] = $dto->format('d/m/Y');
    $dto->modify('+6 days');
    $ret['week_end'] = $dto->format('d/m/Y');
    return $ret;
  }

if(isset($_GET['semaine'])){
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
                header("Location: edt_full.php?semaine=".$s."&key=".$_GET['key']);
            }
            else{
                header("Location: edt_full.php?semaine=".$s."&key=".$_GET['key']);
            } 
        }
        else{
            if(isset($_GET['id'])){
                header("Location: edt_full.php?semaine=".$s);
            }
            else{
                header("Location: edt_full.php?semaine=".$s);
            } 
        }    
    }

    //creation de l'objet RDV
    
    //recuperation des RDV dans la BDD
?>
<html>
<head>
    <title>EDT-Elèves</title>
    <link rel="stylesheet" type="text/css" href="ent.css" />
    <style>
                #npc{
            display : inline-block;
            width : 83vw!important;
        }
        h3{
            text-align : center;
            margin:0px;
        }
        #nom_prenom{
            text-align : left;
            margin-top : 1vh;
            margin-bottom : 1vh;
        }
        tr.nom{
            height : 3vh!important;
        }
        td.nom{
            height : 3vh!important;
        }
        .titre_indiv{
            COLOR: #2FC900;
            width: 83vw;
        }
        .soustitre_indiv{
            width: 83vw;
            margin-top: 0px;
            background-color: #2FC901;
            padding: 0.5vh;
            color: #FFFFFF;
        }
        .logo_a{
            width: 10vw;
            float: right;
            margin-right: 2vw;
            margin-bottom: -3vh;
        }
        .head{
            background-color: #2FC901;
            color: #FFFFFF;
            padding: 0.5vh;
            width: 75vw;
            margin-top: 0px;
            font-weight: bold;
        }
        .body{
            border: 1px solid black;
            border-collapse: collapse;
            height: 6vh;
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
                div{
                    page-break-inside: avoid;
                }
            }
    </style>
</head>
<body>
    
<!-- SELECTION DE L'AFFICHAGE -->
    <h4 onclick="window.print();" class="no_print" style="background: #ADFF2F; display: inline-block; padding: 1vh;"> Imprimer </h4>
    <?php if(isset($_GET['key']) && test_id($_GET['key'])){ ?>
        <form class="no_print" action="index.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST" style="margin-left : 7vw; display: inline-block;">
            <button>RETOUR ACCUEIL</button>
        </form>
        <form class="no_print" action="edt_full.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST" style="margin-left : 7vw; display: inline-block;">
            <button>RETOUR EDT</button>
        </form>
        <form class="no_print" action="fiche_suivit.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST" style="margin-left : 7vw; display: inline-block;">
            <button>Fiche De Suivit</button>
        </form>
    <?php } ?>
    <?php if(isset($_GET['qr']) && $_GET['qr'] == 'off'){ ?>
        <form class="no_print" action="" method="get" style="margin-left : 7vw; display: inline-block;">
            <input type="HIDDEN"  name="semaine" id="semaine" value="<?php echo($_GET['semaine']); ?>"/>
            <input type="HIDDEN" name="key" value="<?php echo($_GET['key']);?>"/>
            <input type="HIDDEN" name="qr" value="on"/>
            <button>Afficher les QR codes</button>
        </form>
    <?php }else{ ?>
        <form class="no_print" action="" method="get" style="margin-left : 7vw; display: inline-block;">
            <input type="HIDDEN"  name="semaine" id="semaine" value="<?php echo($_GET['semaine']); ?>"/>
            <input type="HIDDEN" name="key" value="<?php echo($_GET['key']);?>"/>
            <input type="HIDDEN" name="qr" value="off"/>
            <button>Cacher les QR codes</button>
        </form>
<?php }
    $sqlqueryyy = "SELECT * FROM `elleve`";
    $rStatementt = $pdo->prepare($sqlqueryyy);
    $rStatementt->execute();
    $r = $rStatementt->fetchAll();
    foreach ($r as $el){
        $get_ide = $el['id'];

        $sqlquery = "SELECT * FROM `rdv` WHERE id_elleve = ".$get_ide." ORDER BY `rdv`.`date` ASC";
        $recipesStatement = $pdo->prepare($sqlquery);
        $recipesStatement->execute();
        $recipes = $recipesStatement->fetchAll();
        $id_max = 0;
        $url = $url_base.'/edt.php?ide='.$get_ide;
        foreach ($recipes as $res)
        {
            $rdv_[$id_max] = new rdv($res['id'],strtotime($res['date']), $res['nom'], $res['durre'], $res['couleur'],$res['id_elleve'], $res['id_proph'], $res['lieu'],$url );
            $id_max = $id_max + 1;
        }
        $id_s = 0;
        for($l=0;$l<$id_max;$l++){
            if(date('W', $rdv_[$l]->date) == $_GET['semaine']) $id_s = $id_s + 1;
        }
        if($id_s>0){
        echo("<div style=\"margin-top:6vh;page-break-before:always\" >");
        $url = $url_base.'/edt.php?ide='.$get_ide.'%26src=qr';
        $qr_path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$url.'%2F&choe=UTF-8';
        echo('<img  id="page-wrapper" src="icon/roville_logo.png" id="logo" style="height: 20vh; float: right; margin-right:5vw; margin-top:0vh;"/>');
        if(!(isset($_GET['qr']) && $_GET['qr'] == 'off')){
            echo('<img  id="page-wrapper" src="'.$qr_path.'" style="height: 20vh; float: right; margin-right:5vw; margin-top:0vh;"/>');
        }
        echo('<h3 id="nom_prenom"  id="page-wrapper" style=" padding-top : 8vh;">'.$el['prenom'].' '.$el['nom'].' '.$el['classe'].'</h3>');
        echo('<h3 id="nom_prenom"  id="page-wrapper">Semaine du '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_start'].' au '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_end'].'</h3>');
    
        //afichage de l'edt
    echo("<table  id=\"page-wrapper\">");
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
                    echo("<td onclick=\"location.href='?ide=".$get_ide."&semaine=".$_GET['semaine']."&key=".$_GET['key']."'\" class=\"time\" rowspan=\"60\">".$b."h-".($b+1)."h</td>");
                }
            }
            $test = FALSE;
            for ($k = 0; isset($rdv_[$k]->nom); $k++) {
                //echo($heure." // ".date("H:i", $rdv_[$k]->date)."\n");
                if(date("l", $rdv_[$k]->date) == $jour[$i+1] && date("H:i", $rdv_[$k]->date) == $heure && $rdv_[$k]->id_eleves == $get_ide&& date('W', $rdv_[$k]->date) == $_GET['semaine']){
                $a = "ROWSPAN=\"".($rdv_[$k]->durré)."\"";
                echo("<td ".$a." style=\"background-color:".$rdv_[$k]->couleur.";border : 1px solid black !important;\">"); ?>
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
                echo "<td onclick=\"test-False:location.href='?ide=".$get_ide."&semaine=".$_GET['semaine']."&time=".$heure."&jour=".$jour[$i+1]."&key=".$_GET['key']."'\">";
            }
            else if($pass_day[$i]!=0) $pass_day[$i] = $pass_day[$i]-1;
            echo "</td>";
        }
        echo "</tr>";
    }
echo('</table></div>');

echo('<div style="page-break-before:always">');
echo('<img  id="page-wrapper" src="icon/roville_logo.png" class="logo_a"/>');
echo('<h1 class="titre_indiv">DISPOSITIF D’INDIVIDUALISATION - S'.$_GET['semaine'].'</h1>');
echo('<h2 class="soustitre_indiv">DOCUMENT DE SUIVI - ACCOMPAGNEMENT</h2>');
echo('<table id="npc"><tr id="npc" class="nom"><td class="nom" style="width : 27.6vw!important;"><h3>Nom : '.$el['nom'].'</h3></td><td class="nom" style="width : 27.6vw!important;"><h3>Prénom : '.$el['prenom'].'</h3></td><td class="nom" style="width : 27.6vw!important;"><h3>Classe : '.$el['classe'].'</h3></td>');
echo('</tr></table>');
echo('<h3 id="nom_prenom"  id="page-wrapper">Semaine n°'.$_GET['semaine'].' du '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_start'].' au '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_end'].'</h3>');
//afichage de l'edt
echo("<table  id=\"page-wrapper\">");
echo('<tr>
        <td class="head" colspan="2" style="width: 16vw !important;">
            Date - Heure
        </td>
        <td class="head"  colspan="4" style="width:40vw !important;">
            Points-Abordés / Travail réalisé
        </td>
        <td class="head" colspan="2" style="width:11vw !important;">
            Ressenti apprenant
        </td>
        <td class="head" style="width:11vw !important;">
            Signatures apprenant
        </td>
        <td class="head" style="width:11vw !important;">
            Accompagnant(Nom et signature)
        </td>
    </tr>');
    $i = 0;
    $m = 0;
    foreach($rdv_ as $rdv){
        $date_end = strtotime(date("Y-m-d H:i:s", $rdv->date)."+ {$rdv->durré} minutes");
        $sqlquery = "SELECT * FROM `proph` WHERE id = ".$rdv->id_proph;
        $recipesStatement = $pdo->prepare($sqlquery);
        $recipesStatement->execute();
        $recipes = $recipesStatement->fetchAll();
        foreach ($recipes as $res)
        {
            $nom = $res['nom'];
            $prenom = $res['prenom'];
        }
        if(date('W', $rdv->date) == $_GET['semaine']){
            if($i == 11){
                echo('</table>');
                echo('</div><div style="page-break-before:always">');
                echo('<img  id="page-wrapper" src="icon/roville_logo.png" class="logo_a"/>');
                echo('<h1 class="titre_indiv">DISPOSITIF D’INDIVIDUALISATION - S'.$_GET['semaine'].'</h1>');
                echo('<h2 class="soustitre_indiv">DOCUMENT DE SUIVI - ACCOMPAGNEMENT</h2>');
                echo('<table id="npc"><tr id="npc" class="nom"><td class="nom" style="width : 27.6vw!important;"><h3>Nom : '.$el['nom'].'</h3></td><td class="nom" style="width : 27.6vw!important;"><h3>Prénom : '.$el['prenom'].'</h3></td><td class="nom" style="width : 27.6vw!important;"><h3>Classe : '.$el['classe'].'</h3></td>');
                echo('</tr></table>');
                echo('<h3 id="nom_prenom"  id="page-wrapper">Semaine n°'.$_GET['semaine'].' du '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_start'].' au '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_end'].'</h3>');
                echo("<table  id=\"page-wrapper\">");
                echo('<tr>
                        <td class="head" colspan="2" style="width: 16vw !important;">
                            Date - Heure
                        </td>
                        <td class="head"  colspan="4" style="width:40vw !important;">
                            Points-Abordés / Travail réalisé
                        </td>
                        <td class="head" colspan="2" style="width:11vw !important;">
                            Ressenti apprenant
                        </td>
                        <td class="head" style="width:11vw !important;">
                            Signatures apprenant
                        </td>
                        <td class="head" style="width:11vw !important;">
                            Accompagnant(Nom et signature)
                        </td>
                    </tr>');
                $i = 0;
            }
            echo('<tr class="body">');
            echo('<td class="body" colspan="2" style="width: 16vw !important;">'.$jourfr[date('N', $rdv->date)].' '.date('d/m H:i', $rdv->date).'-'.date("H:i", $date_end).'</td>');
            echo('<td class="body" colspan="4" style="width: 40vw !important;"></td>');
            echo('<td class="body" colspan="2" style="width: 11vw !important;">
                    <img  id="page-wrapper" src="icon/ressentit.png" style="width:10vw;"/>
                </td>');
            echo('<td class="body" style="width: 11vw !important;"></td>');
            echo('<td class="body" style="width: 11vw !important;"><p style=" margin-bottom: 3vh !important; margin-top: 0.5vh !important;">'.$prenom[0].'. '.$nom.'</p></td>');
            echo('</tr>');
            $i = $i + 1;
            $m = $m + 1;
        }
    }

    echo('</table></div>');

    if ($m >= 12 && $m < 23){
        echo('<div style="page-break-before:always">');
        echo('<img  id="page-wrapper" src="icon/roville_logo.png" class="logo_a"/>');
        echo('<h1 class="titre_indiv">DISPOSITIF D’INDIVIDUALISATION - S'.$_GET['semaine'].'</h1>');
        echo('<h2 class="soustitre_indiv">DOCUMENT DE SUIVI - ACCOMPAGNEMENT</h2>');
        echo('<table id="npc"><tr id="npc" class="nom"><td class="nom" style="width : 27.6vw!important;"><h3>Nom : '.$el['nom'].'</h3></td><td class="nom" style="width : 27.6vw!important;"><h3>Prénom : '.$el['prenom'].'</h3></td><td class="nom" style="width : 27.6vw!important;"><h3>Classe : '.$el['classe'].'</h3></td>');
        echo('</tr></table>');
        echo('<h3 id="nom_prenom"  id="page-wrapper">Semaine n°'.$_GET['semaine'].' du '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_start'].' au '.getStartAndEndDate($_GET['semaine'], date('Y', time()))['week_end'].'</h3>');
        echo("<table  id=\"page-wrapper\">");
        echo('<tr>
                <td class="head" colspan="2" style="width: 16vw !important;">
                    Date - Heure
                </td>
                <td class="head"  colspan="4" style="width:40vw !important;">
                    Points-Abordés / Travail réalisé
                </td>
                <td class="head" colspan="2" style="width:11vw !important;">
                    Ressenti apprenant
                </td>
                <td class="head" style="width:11vw !important;">
                    Signatures apprenant
                </td>
                <td class="head" style="width:11vw !important;">
                    Accompagnant(Nom et signature)
                </td>
            </tr>');
        echo('</table></div>');
    }

    }


}
}
?>
</br>
</body>