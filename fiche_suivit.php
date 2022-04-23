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
function sort_by_date($a, $b) {
    $a = $a->date;
    $b = $b->date;
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}
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
        <form class="no_print" action="edt_full.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST" style="margin-left : 7vw; display: inline-block;">
            <button>RETOUR EDT</button>
        </form>
        <form class="no_print" action="index.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST" style="margin-left : 7vw; display: inline-block;">
            <button>RETOUR ACCUEIL</button>
        </form>
    <?php } 
    $jour = array(null, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $jourfr = array(null, "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
    $sqlqueryyy = "SELECT * FROM `elleve`";
    $rStatementt = $pdo->prepare($sqlqueryyy);
    $rStatementt->execute();
    $r = $rStatementt->fetchAll();
    foreach ($r as $el){
        $get_ide = $el['id'];
        $rdv_ = null;
        $sqlquery = "SELECT * FROM `rdv` WHERE id_elleve = ".$get_ide." ORDER BY date ";
        $recipesStatement = $pdo->prepare($sqlquery);
        $recipesStatement->execute();
        $recipes = $recipesStatement->fetchAll();
        $id_max = 0;
        $url = $url_base.'/edt.php?ide='.$get_ide;
        foreach ($recipes as $res)
        {
            $rdv_[$id_max] = new rdv($id_max,strtotime($res['date']), $res['nom'], $res['durre'], $res['couleur'],$res['id_elleve'], $res['id_proph'], $res['lieu'],$url );
            usort($rdv_, 'sort_by_date');
            $id_max = $id_max + 1;
        }

        $id_s = 0;
        for($l=0;$l<$id_max;$l++){
            if(date('W', $rdv_[$l]->date) == $_GET['semaine']) $id_s = $id_s + 1;
        }
        if($id_s>0){
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
                    }
                }
            }
            echo('</table></div>');
        }
}

?>
</br>
</body>