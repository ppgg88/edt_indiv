<?php 
    include('log_bdd.php');
    include('fonction.php');
    if(isset($_GET['semaine']) == false){
        $s = date('W', time());
        if(isset($_GET['key'])){
            header("Location: edt_general.php?semaine=".$s."&key=".$_GET['key']); 
        }
        else header("Location: edt_general.php?semaine=".$s); 
    }
    if(isset($_GET['semaine'])){
        if(strpos($_GET['semaine'],"-W")!=FALSE){
            if(isset($_GET['semaine'][7])){
                $s = $_GET['semaine'][6].$_GET['semaine'][7];
            }
            else {
                $s = $_GET['semaine'][6];
            }
            if(isset($_GET['key'])){
                header("Location: edt_general.php?semaine=".$s."&key=".$_GET['key']);
            }
            else{
                header("Location: edt_general.php?semaine=".$s);  
            }    
        }
    }

    class rdv{
        public $id;
        public $date;
        public $nom;
        public $durée;
        public $couleur;
        public $nom_prof = NULL;
        public $prenom_prof = NULL;
        public $id_prof;
        public $nom_eleve;
        public $prenom_eleve;
        public $classe_eleve;
        public $id_eleve;
        public $lieu;
        public $suite = FALSE;

        public $abs;

        function __construct($id, $d, $n, $du,$c, $nprof = NULL, $pprof = NULL, $neleve, $peleve, $celeve, $l, $ide, $idp, $abs){
            $this->id = $id;
            $this->date = $d;
            $this->nom = $n;
            $this->durée = $du;
            $this->couleur = $c;
            $this->nom_eleve = $neleve;
            $this->prenom_eleve = $peleve;
            $this->classe_eleve = $celeve;
            $this->nom_prof = $nprof;
            $this->prenom_prof = $pprof;
            $this->lieu = $l;
            $this->id_eleve = $ide;
            $this->id_prof = $idp;
            $this->abs = $abs;

        }
    }

    $sqlquery = "SELECT rdv.id, rdv.nom, rdv.date, rdv.durre, rdv.couleur, rdv.lieu, elleve.nom as e_nom, elleve.prenom as e_prenom, elleve.classe as e_classe, elleve.id as ide ,proph.nom as p_nom, proph.prenom as p_prenom, proph.id as idp, abs FROM rdv, elleve, proph WHERE rdv.id_elleve = elleve.id and rdv.id_proph = proph.id order by date, proph.nom, elleve.nom;";
    $recipesStatement = $pdo->prepare($sqlquery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
    $index_rdv  = 0;
    $rdv = array();
    foreach ($recipes as $res)
    {
        $rdv[$index_rdv] = new rdv($res['id'], strtotime($res['date']), $res['nom'], $res['durre'], $res['couleur'], $res['p_nom'], $res['p_prenom'], $res['e_nom'], $res['e_prenom'], $res['e_classe'], $res['lieu'], $res['ide'], $res['idp'], $res['abs']);
        $index_rdv++;
    } 
    //echo($rdv[10]->durée)
    //echo(date("d/m/Y H:i", strtotime(" +".$rdv_[$k]->durré."minutes", $rdv_[$k]->date))
?>
<html>
    <head>
        <title>EDT-General</title>
        <link rel="stylesheet" href="all.css" />
        <style>
            input.modif, select.modif{
                width:20vw;
                margin-bottom: 0.5vh;
            }
            label.modif{
                display: inline-block;
                width: 7vw;
                text-align: right;
                font-size: small;
            }
            .centre{
                display: block;
                margin-left: auto;
                margin-right: auto;
            }
            a{
                text-decoration: none;
                text-align: center;
                color: black; 
            }
            #trash{
                display:block;
                margin-left:auto;
                margin-right:auto;
            }
            form.modif{
                margin-top: 5vh;
                margin-bottom: 0px;
                margin-left: 0.5vw;
            }
            table{
                width: 100% !important;
            }
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
            .content{
                padding-left: 1.1vw !important;
            }
            div.close {
                position: relative;
                height: inherit;
            }
            img.close {
                position: relative;
                right: 0px;
                margin-left: 100%;
            }
            
            a.close{
                position: relative;
                right: 0px;
                display: block;
            }

            @media print {
                body * {
                    /*visibility: hidden;*/
                    -webkit-print-color-adjust: exact !important; // not necessary use if colors not visible
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.0/jspdf.umd.min.js"></script>
        <script src="js/html2canvas.js"></script>
    </head>
    <body>
        <!-- Impression -->
        <h4 onclick="window.print();"> Print </h4>

        <!-- Selection de la semaine -->
        <form action="" method="get" style="display: inline-block;">
            <label for="semaine">Quelle Semaine ?</label> <input type="week"  name="semaine" id="semaine" value="<?php echo(date('Y', time())."-W".$_GET['semaine']); ?>"require/>
            <?php if(isset($_GET['key'])) echo('<input type="HIDDEN" name="key" value="'.$_GET['key'].'"/>'); ?>
            <button>Validé</button>
        </form>
        
        <!-- retour à l'accueil si key check -->
        <?php if(isset($_GET['key']) && test_id($_GET['key'])){ ?>
            <form class="no_print" style ="display: inline-block; margin-left :7vw" action="index.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST">
                <button>RETOUR ACCUEIL</button>
            </form>
        <?php }  ?>

        <!-- reinitialiser filtre(s) si besoin -->
        <?php   
        if(isset($_GET['ide'])||isset($_GET['idp'])||isset($_GET['classe'])){ ?>
            <form action="" method="get" style ="display: inline-block; margin-left :7vw">
                <input type="HIDDEN" name="semaine" value="<?php echo($_GET['semaine']);?>"/>
                <?php if(isset($_GET['key'])){
                    echo('<input type="HIDDEN" name = "key" value="'.$_GET['key'].'"/>'); 
                }?>
                <button>Reinitialiser les filtres</button>
            </form>
        <?php } ?>    
        <img src="icon/roville_logo.png" id="logo" style="height: 20vh !important; float: right !important; margin-right:5vw !important;"/>
        <?php 
        if(isset($_GET['semaine'])){
        echo("<h4> Semaine ".$_GET['semaine']."</h4>"); ?>

        <table cellspacing="0" cellpadding="0">
        <tr>
                <td colspan="2" class="head" style="padding-right: 0vw; width : 30vw">
                    Date & Heure
                    <div>
                        <form action="" method="get">
                            <input type="date" name="date" value="<?php echo(date('Y-m-d', time()));?>"/>
                            <input type="HIDDEN" name="semaine" value="<?php echo($_GET['semaine']);?>"/>
                            <?php 
                            if(isset($_GET['classe'])) echo('<input type="HIDDEN" name="classe" value="'.$_GET['classe'].'"/>');
                            if(isset($_GET['ide'])) echo('<input type="HIDDEN" name="ide" value="'.$_GET['ide'].'"/>');
                            if(isset($_GET['idp'])) echo('<input type="HIDDEN" name="idp" value="'.$_GET['idp'].'"/>');
                            if(isset($_GET['key'])) echo('<input type="HIDDEN" name = "key" value="'.$_GET['key'].'"/>');
                            ?>
                            <button>Validé</button>
                        </form>
                    </div>
                </td>
                <td class="head" style="width : 14vw">
                    <form action="" method="get">
                        <div>
                            <?php select_elleves();?>
                            <input type="HIDDEN" name="semaine" value="<?php echo($_GET['semaine']);?>"/>
                            <?php if(isset($_GET['key'])) echo('<input type="HIDDEN" name = "key" value="'.$_GET['key'].'"/>');
                            if(isset($_GET['date'])) echo('<input type="HIDDEN" name = "date" value="'.$_GET['date'].'"/>'); ?>
                            <button>Validé</button>
                        </div>
                    </form>
                </td>
                <td class="head" style="width : 14vw">
                    <form action="" method="GET">
                        <div>
                            <?php select_classe(); ?>
                            <input type="HIDDEN" name="semaine" value="<?php echo($_GET['semaine']);?>"/>
                            <?php if(isset($_GET['key'])) echo('<input type="HIDDEN" name = "key" value="'.$_GET['key'].'"/>');
                            if(isset($_GET['date'])) echo('<input type="HIDDEN" name = "date" value="'.$_GET['date'].'"/>');
                            if(isset($_GET['idp'])) echo('<input type="HIDDEN" name = "idp" value="'.$_GET['idp'].'"/>'); ?>
                            <button>Validé</button>
                        </div>
                    </form>
                </td>
                <td class="head" style="width : 14vw">
                    Lieu
                </td>
                <td class="head" style="width : 14vw">
                <form action="" method="get">
                        <div>
                            <?php select_profs(); ?>
                            <input type="HIDDEN" name="semaine" value="<?php echo($_GET['semaine']);?>"/>
                            <?php if(isset($_GET['classe'])){
                                echo('<input type="HIDDEN" name="classe" value="'.$_GET['classe'].'"/>');
                            }
                            if(isset($_GET['key'])) echo('<input type="HIDDEN" name = "key" value="'.$_GET['key'].'"/>');
                            if(isset($_GET['date'])) echo('<input type="HIDDEN" name = "date" value="'.$_GET['date'].'"/>'); ?>
                            <button>Validé</button>
                        </div>
                    </form>
                </td>
                <td class="head" style="width : 14vw">
                    Observations
                </td>
                <td class="head" style="width : 4vw">
                    Abs
                </td>
            </tr>
        <?php
            for($i = 0; $i<$index_rdv; $i++){ 
                if(date("W", $rdv[$i]->date) == $_GET['semaine']){
                    if((isset($_GET['ide']) && $rdv[$i]->id_eleve == $_GET['ide']) || isset($_GET['ide']) == FALSE || $_GET['ide'] == 0){
                        if((isset($_GET['idp']) && $rdv[$i]->id_prof == $_GET['idp']) || isset($_GET['idp']) == FALSE || $_GET['idp'] == 0){ 
                            if((isset($_GET['classe']) && str_replace(' ','',$rdv[$i]->classe_eleve) == $_GET['classe']) || isset($_GET['classe']) == FALSE || $_GET['classe'] == 'no'){
                                if((isset($_GET['date']) && $_GET['date'] == date("Y-m-d", $rdv[$i]->date)) || isset($_GET['date']) == FALSE){ ?>
                                <tr>
                                    <?php
                                    $lien_rdv = '';
                                    if(isset($_GET['key']) && (test_id($_GET['key']) || $_GET['key'] == "viesco"))$lien_rdv = 'onclick="location.href=\'?semaine='.$_GET['semaine'].'&id='.$rdv[$i]->id.'&key='.$_GET['key'].'#position\'"';
                                    //else $lien_rdv = '';
                                    $color = "";
                                    $deco = "";
                                    if($rdv[$i]->abs == -1){
                                        $color = "red";
                                        $deco = "bold";
                                    }
                                    elseif($rdv[$i]->abs == 2){
                                        $color = "#CC8822";
                                        $deco = "bold";
                                    }
                                    else{
                                        $color = "black";
                                        $deco = "normal";
                                    }

                                    ?>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="padding-right: 0vw !important; background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo(day(date("N", $rdv[$i]->date))." ".date("d/m", $rdv[$i]->date));?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo(date("H:i", $rdv[$i]->date)."-".date("H:i", strtotime(" +".$rdv[$i]->durée."minutes", $rdv[$i]->date)));?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo($rdv[$i]->nom_eleve." ".($rdv[$i]->prenom_eleve)[0]);?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo($rdv[$i]->classe_eleve);?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo($rdv[$i]->lieu);?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo($rdv[$i]->nom_prof." ".($rdv[$i]->prenom_prof)[0]);?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php echo($rdv[$i]->nom);?>
                                    </td>
                                    <td <?php echo($lien_rdv); ?> class = "content" style="background-color: <?php echo($rdv[$i]->couleur);?> !important; color:<?php echo($color);?> !important">
                                        <?php 
                                        if($rdv[$i]->abs == -1) echo("Absent");
                                        elseif($rdv[$i]->abs == 1)echo("Present");
                                        elseif($rdv[$i]->abs == 2)echo("Annulé");
                                        elseif($rdv[$i]->abs == 4)echo("Formateur Abs");
                                        else echo("NR"); ?>
                                    </td>
                                </tr>
                                <?php

                                if(isset($_GET['id']) && isset($_GET['key']) && test_id($_GET['key']) && $_GET['id'] == $rdv[$i]->id){
                                    //requette sql de recherche du rdv dans la bdd :
                                    $sqlquery = "SELECT * FROM rdv WHERE id =".$_GET['id'];
                                    $recipesStatement = $pdo->prepare($sqlquery);
                                    $recipesStatement->execute();
                                    $recipes = $recipesStatement->fetchAll();
                                    foreach ($recipes as $res){ ?>
                                        <tr>
                                            <td colspan="3" id="position">
                                                <form class="modif" class="no_print" method="post" action="update_general.php?id=<?php echo($_GET['id']); ?>&semaine=<?php echo($_GET['semaine']); if(test_id($_GET['key'])){echo("&key=".$_GET['key']);}?>">
                                                    <label class="modif" for="rdv">Nom du RDV</label> <input class="modif" type="text"  name="rdv" id="rdv" value="<?php echo($res['nom']);?>"/><br />
                                                    <label class="modif" for="ide">Eleves</label>
                                                    <?php select_elleves($res['id_elleve'], "modif"); ?>
                                                    <br />
                                                    <label class="modif" for="idp">Prof</label>
                                                    <?php select_profs($res['id_proph'], "modif"); ?>
                                                    <br />
                                                    <label class="modif" for="date_j">Date</label> <input class="modif" type="date"  name="date_j" id="date_j" value="<?php echo(date('Y-m-d', strtotime($res['date'])));?>"/><br />
                                                    <label class="modif" for="date">Heure</label> <input class="modif" type="time"  name="date" id="date" value="<?php echo(date('H:i:s', strtotime($res['date'])));?>"/><br />
                                                    <label class="modif" for="durre">Durée</label> <input class="modif" type="number"  name="durre" id="durre" value="<?php echo($res['durre']);?>"/><br />
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
                                                        <option value='3' <?php if($res['abs']==0) echo('selected="selected"'); ?>>Non Renseigné</option>
                                                        <option value='-1' <?php if($res['abs']==-1) echo('selected="selected"'); ?>>Absent</option>
                                                        <option value='1' <?php if($res['abs']==1) echo('selected="selected"'); ?>>Present</option>
                                                        <option value='2' <?php if($res['abs']==2) echo('selected="selected"'); ?>>Annulé</option>
                                                        <option value='4' <?php if($res['abs']==4) echo('selected="selected"'); ?>>Formateur Absent</option>
                                                    </select><br />
                                                    <label class="modif" for="notifpr">Notifier Prof</label><input class="modif" type="checkbox" name="notifpr" value="notifpr"></br>
                                                    <label class="modif" for="notife">Notifier Elève</label><input class="modif" type="checkbox" name="notife" value="notife"></br>
                                                    <input class="modif centre" style="margin-top:1vh;" type="submit" name="Envoyer" value="Envoyer" />
                                                </form>
                                            </td>
                                            <td colspan="3">
                                                <form class="modif" class="no_print" method="post" action="update_general.php?id=<?php echo($_GET['id']); ?>&semaine=<?php echo($_GET['semaine']); if(test_id($_GET['key'])){echo("&key=".$_GET['key']);}?>">
                                                    <input type="HIDDEN" name = "ide" value="<?php echo($res['id_elleve']); ?>"/>
                                                    <label class="modif" for="abs">Absence</label>
                                                    <select class="modif" name="abs">
                                                        <option value='3' <?php if($res['abs']==0) echo('selected="selected"'); ?>>Non Renseigné</option>
                                                        <option value='-1' <?php if($res['abs']==-1) echo('selected="selected"'); ?>>Absent</option>
                                                        <option value='1' <?php if($res['abs']==1) echo('selected="selected"'); ?>>Present</option>
                                                        <option value='2' <?php if($res['abs']==2) echo('selected="selected"'); ?>>Annulé</option>
                                                    </select><br />
                                                    <label class="modif" for="date_d">Date Debut</label> <input class="modif" type="date"  name="date_d" id="date_d" value="<?php echo(date('Y-m-d', strtotime($res['date'])));?>"/><br />
                                                    <label class="modif" for="date_f">Date Fin</label> <input class="modif" type="date"  name="date_f" id="date_f" value="<?php echo(date('Y-m-d', strtotime($res['date'])));?>"/><br />
                                                    <label class="modif" for="notifpr">Notifier Prof</label><input class="modif" type="checkbox" name="notifpr" value="notifpr"></br>
                                                    <label class="modif" for="notife">Notifier Elève</label><input class="modif" type="checkbox" name="notife" value="notife"></br>
                                                    <input class="modif centre" style="margin-top:1vh;" type="submit" name="absent" value="Declarer Absent" />
                                                </form>
                                            </td>
                                            <td colspan="1" style="height: 100%;">
                                                <div class="close">
                                                    <a class="close" href = "<?php echo("edt_general.php?semaine=".$_GET['semaine']);if(test_id($_GET['key'])){echo("&key=".$_GET['key']);}?>"><img class="close" src="icon/close.png" style="height : 5vh;"/></a>
                                                    <a onclick="if(confirm('Vous allez suprimer le rendez-vous, Etes-vous sur ?')){return true;}else{return false;}" href = "<?php echo("edt_supr_general.php?semaine=".$_GET['semaine']."&idrdv=".$_GET['id']."&key=".$_GET['key']);?>"><p style="margin-top:30%;">Supprimer</p><img id="trash" src="icon/trash.png" style="height : 8vh;"/></a>
                                                </div>
                                            </td>
                                        </tr>
            
                                        <?php }  }
                                        
                                                
                                        if(isset($_GET['id']) && isset($_GET['key']) && $_GET['key']=="viesco" && $_GET['id'] == $rdv[$i]->id){
                                            //requette sql de recherche du rdv dans la bdd :
                                            $sqlquery = "SELECT * FROM rdv WHERE id =".$_GET['id'];
                                            $recipesStatement = $pdo->prepare($sqlquery);
                                            $recipesStatement->execute();
                                            $recipes = $recipesStatement->fetchAll();
                                            foreach ($recipes as $res){ ?>
                                                <tr>
                                                    <td colspan="4" id="position">
                                                        <form class="modif" class="no_print" method="post" action="update_general_abs.php?id=<?php echo($_GET['id']); ?>&semaine=<?php echo($_GET['semaine']."&key=".$_GET['key']);?>">
                                                            <label class="modif" for="abs">Statut Absence</label>
                                                            <select class="modif" name="abs">
                                                                <option value='3' <?php if($res['abs']==0) echo('selected="selected"'); ?>>Non Renseigner</option>
                                                                <option value='-1' <?php if($res['abs']==-1) echo('selected="selected"'); ?>>Absent</option>
                                                                <option value='1' <?php if($res['abs']==1) echo('selected="selected"'); ?>>Present</option>
                                                                <option value='2' <?php if($res['abs']==2) echo('selected="selected"'); ?>>Annuler</option>
                                                            </select><br />
                                                            <label class="modif" for="notifpr">Notifier Prof</label><input class="modif" type="checkbox" name="notifpr" value="notifpr"></br>
                                                            <label class="modif" for="notife">Notifier Elève</label><input class="modif" type="checkbox" name="notife" value="notife"></br>
                                                            <input type="HIDDEN" name = "idp" value="<?php echo($res['id_proph']); ?>"/>
                                                            <input type="HIDDEN" name = "ide" value="<?php echo($res['id_elleve']); ?>"/>
                                                            <input class="modif" type="HIDDEN"  name="date" id="date" value="<?php echo($res['date']);?>"/><br />
                                                            <input class="modif centre" style="margin-top:1vh;" type="submit" name="Envoyer" value="Envoyer" />
                                                        </form>
                                                    </td>
                                                    <td colspan="3">
                                                        <form class="modif" class="no_print" method="post" action="update_general_abs.php?id=<?php echo($_GET['id']); ?>&semaine=<?php echo($_GET['semaine']."&key=".$_GET['key']);?>">
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
                                                            <label class="modif" for="notifpr">Notifier Prof</label><input class="modif" type="checkbox" name="notifpr" value="notifpr"></br>
                                                            <label class="modif" for="notife">Notifier Elève</label><input class="modif" type="checkbox" name="notife" value="notife"></br>
                                                            <input class="modif centre" style="margin-top:1vh;" type="submit" name="absent" value="Declarer Absent" />
                                                        </form>
                                                        <a class="close" href = "<?php echo("edt_general.php?semaine=".$_GET['semaine']."&key=".$_GET['key']);?>"><img class="close" src="icon/close.png" style="height : 5vh;"/></a>
                                                    </td>
                                                </tr>

<?php }  } }   }   }   }   }   }   }            
         ?>
        </table>
        </br>
    </body>
</html>