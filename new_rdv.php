<?php
    include('fonction.php');
    if(isset($_GET['key']) && test_id($_GET['key'])){
        
        if(!isset($_GET['nbe'])){
            header("location: new_rdv.php?key=".$_GET['key']."&semaine=".$_GET['semaine']."&nbe=1");
        }

        if(isset($_POST['addeleve'])){
            $nbe = $_GET['nbe'];
            $nbe++;
            header("location: new_rdv.php?key=".$_GET['key']."&semaine=".$_GET['semaine']."&nbe=".$nbe);

            $date_j = "";
            $elleve = "";
            $heure = "";
            $idp = "";
            $durre = "";
            $rdv = "";
            $lieu = "";
            $nrepeat = "";

            for($k=0; $k<$nbe; $k++){
                if(isset($_POST['idell'.$k])){
                    $elleve .= '&idell'.$k.'='.$_POST['idell'.$k];
                }
            }
            if(isset($_POST['date_j']) && $_POST['date_j'] != ""){
                 $date_j .= '&date_j='.$_POST['date_j'];
            }
            if(isset($_POST['date']) && $_POST['date'] != ""){
                 $heure .= '&heure='.$_POST['date'];
            }
            if(isset($_POST['idp']) && $_POST['idp'] != 0){
                 $idp .= '&idp='.$_POST['idp'];
            }
            if(isset($_POST['durre']) && $_POST['durre'] != 0){
                 $durre .= '&durre='.$_POST['durre'];
            }
            if(isset($_POST['rdv']) && $_POST['rdv'] != ""){
                 $rdv .= '&rdv='.$_POST['rdv'];
            }
            if(isset($_POST['lieu']) && $_POST['lieu'] != ""){
                 $lieu .= '&lieu='.$_POST['lieu'];
            }
            if(isset($_POST['coulleur'])){
                $coulleur = '&coulleur='.str_replace('#', 'Z', $_POST['coulleur']);
            }
            if(isset($_POST['nrepeat']) && $_POST['nrepeat'] != ""){
                $nrepeat .= '&nrepeat='.$_POST['nrepeat'];
            }
            header("location: new_rdv.php?key=".$_GET['key']."&semaine=".$_GET['semaine']."&nbe=".$nbe.$elleve.$date_j.$heure.$idp.$durre.$rdv.$lieu.$coulleur.$nrepeat);
        }

        if(isset($_POST['mineleve'])){
            $nbe = $_GET['nbe'];
            $nbe--;

            $date_j = "";
            $elleve = "";
            $heure = "";
            $idp = "";
            $durre = "";
            $rdv = "";
            $lieu = "";
            $nrepeat = "";

            for($k=0; $k<$nbe; $k++){
                if(isset($_POST['idell'.$k])){
                    $elleve .= '&idell'.$k.'='.$_POST['idell'.$k];
                }
            }
            if(isset($_POST['date_j']) && $_POST['date_j'] != ""){
                 $date_j .= '&date_j='.$_POST['date_j'];
            }
            if(isset($_POST['date']) && $_POST['date'] != ""){
                 $heure .= '&heure='.$_POST['date'];
            }
            if(isset($_POST['idp']) && $_POST['idp'] != 0){
                 $idp .= '&idp='.$_POST['idp'];
            }
            if(isset($_POST['durre']) && $_POST['durre'] != 0){
                 $durre .= '&durre='.$_POST['durre'];
            }
            if(isset($_POST['rdv']) && $_POST['rdv'] != ""){
                 $rdv .= '&rdv='.$_POST['rdv'];
            }
            if(isset($_POST['lieu']) && $_POST['lieu'] != ""){
                 $lieu .= '&lieu='.$_POST['lieu'];
            }
            if(isset($_POST['coulleur'])){
                $coulleur = '&coulleur='.str_replace('#', 'Z', $_POST['coulleur']);
            }
            if(isset($_POST['nrepeat']) && $_POST['nrepeat'] != ""){
                $nrepeat .= '&nrepeat='.$_POST['nrepeat'];
            }
            header("location: new_rdv.php?key=".$_GET['key']."&semaine=".$_GET['semaine']."&nbe=".$nbe.$elleve.$date_j.$heure.$idp.$durre.$rdv.$lieu.$coulleur.$nrepeat);
        }

        if(isset($_GET['s']))$s = $_GET['s'];
        else $s = $_GET['semaine'];

        if(isset($_POST['Envoyer'])){
            include('log_bdd.php');
            for($i=1;$i<=53;$i++)$week[$i] = 0;
            $strlen = strlen($_POST['nrepeat']);
            $x = 0;
            for($i=0;$i<$strlen;$i++){
                if($_POST['nrepeat'][$i] == ';'){
                    $week[$x] = true;
                    $x = 0;
                }
                else{
                    $x = 10*$x + $_POST['nrepeat'][$i];
                }
            }
            if($x != 0)$week[$x] = true;
            $dd = new DateTime($_POST['date_j']." ".$_POST['date'].":00");
            $d = $dd->format('Y-m-d H:i:s');
            $week[date('W', strtotime($d))] = 1;

            $dddd = new DateTime("01/01/".date('Y', strtotime($d))." ".$_POST['date'].":00");
            $ddd = $dddd->format('Y-m-d H:i:s');
            $j = date('N', strtotime($ddd));
            if($j != 1){
                $j = 7 - $j;
                $ddd = date('Y-m-d H:i:s', strtotime("+".$j." day", strtotime($ddd)));
            };
            $j = date('N', strtotime($d));
            $ddd = date('Y-m-d H:i:s', strtotime("+".$j." day", strtotime($ddd)));
            for($s = 1; $s<=53; $s++){
                if($week[$s] == 1){
                    for($k=0; $k<$_GET['nbe']; $k++){
                        $repeat = $_POST['nrepeat'];
                        if($_POST['idp']  == 0){
                            $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, date, durre, couleur, lieu) VALUES (:rdv, :ide, :date, :durre, :coulleur, :lieu)");
                        }
                        else{
                            $query=$pdo->prepare("INSERT INTO rdv (nom, id_elleve, id_proph, date, durre, couleur, lieu) VALUES (:rdv, :ide, :idp, :date, :durre, :coulleur, :lieu)");
                            $query->bindValue(':idp', $_POST['idp'], PDO::PARAM_INT);
                        }
                        $query->bindValue(':rdv', $_POST['rdv'], PDO::PARAM_STR);
                        $query->bindValue(':ide', $_POST['idell'.$k], PDO::PARAM_INT);
                        $query->bindValue(':date', $ddd, PDO::PARAM_STR);
                        $query->bindValue(':durre', $_POST['durre'], PDO::PARAM_INT);
                        $query->bindValue(':coulleur', $_POST['coulleur'], PDO::PARAM_STR);
                        $query->bindValue(':lieu', $_POST['lieu'], PDO::PARAM_STR);
                        $query->execute();

                    }
                }
                $ddd = date('Y-m-d H:i:s', strtotime("+ 7 day", strtotime($ddd)));
            }
            $nbe = $_GET['nbe'];
            $date_j = "";
            $elleve = "";
            $heure = "";
            $idp = "";
            $durre = "";
            $rdv = "";
            $lieu = "";
            $coulleur = "";
            for($k=0; $k<$nbe; $k++){
                if(isset($_POST['idell'.$k])){
                    $elleve .= '&idell'.$k.'='.$_POST['idell'.$k];
                }
            }
            if(isset($_POST['date_j']) && $_POST['date_j'] != ""){
                $date_j .= '&date_j='.$_POST['date_j'];
            }
            if(isset($_POST['date']) && $_POST['date'] != ""){
                    $heure .= '&heure='.$_POST['date'];
            }
            if(isset($_POST['idp']) && $_POST['idp'] != 0){
                    $idp .= '&idp='.$_POST['idp'];
            }
            if(isset($_POST['durre']) && $_POST['durre'] != 0){
                    $durre .= '&durre='.$_POST['durre'];
            }
            if(isset($_POST['rdv']) && $_POST['rdv'] != ""){
                    $rdv .= '&rdv='.$_POST['rdv'];
            }
            if(isset($_POST['lieu']) && $_POST['lieu'] != ""){
                    $lieu .= '&lieu='.$_POST['lieu'];
            }
            if(isset($_POST['coulleur'])){
                $coulleur = '&coulleur='.str_replace('#', 'Z', $_POST['coulleur']);
            }
            header("location: new_rdv.php?key=".$_GET['key']."&semaine=".$_GET['semaine']."&idel=".$_POST['idell0']."&s=".date('W', strtotime($d))."&nbe=".$nbe.$elleve.$date_j.$heure.$idp.$durre.$rdv.$lieu.$coulleur);
        }

    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8" />
            <link rel="stylesheet" href="all.css" />
            <link rel="stylesheet" type="text/css" href="ent.css" />
            <style>
                input.modif, select.modif{
                width:16vw;
                }
                label.modif{
                    display:inline-block;
                    width:7vw;
                    text-align:right;
                }
                tr{
                background-color: #F4FFF4;
                }
                #edt_view{
                    width:65vw;
                    height: 80vh;
                }
                input, select{
                    margin-bottom: 0.5vh;
                }
                .head{
                    margin-left: 2vw;
                }
            </style>
            <title>Nouveau RDV</title>
        </head>
        <body>
            <h3 class="head" style = "display: inline-block;">AJOUTER UN RENDEZ-VOUS :</h3>
            <form style = "display: inline-block;" action="index.php?key=<?php echo($_GET['key']); ?>&semaine=<?php echo($_GET['semaine']); ?>" method="POST">
                <button>RETOUR ACCUEIL</button>
            </form>
            <table>
                <tr>
                    <td>
                        <form method="POST" action="">
                                <?php 
                                for($i=0; $i<$_GET['nbe'];$i++){
                                    echo('<label class="modif" for="ide'.$i.'"> élève '.($i+1).' : </label>');
                                    if(isset($_GET['idell'.$i])) $ide = $_GET['idell'.$i];
                                    else $ide = 0;
                                    select_elleves($ide, "modif", 'll'.$i);
                                    echo('<br/>');
                                }
                                ?>
                                <input class="modif" type="submit" name="addeleve" value="Ajouter Elève" /><br/><br/>
                                <?php 
                                if($_GET['nbe']>1){
                                    echo('<input class="modif" type="submit" name="mineleve" value="suprimer Elève" /><br/><br/>');
                                }
                                ?>
                                <label class="modif" for="date_j">Date</label> <input class="modif" type="date"  name="date_j" id="date_j" value="<?php if(isset($_GET['date_j']))echo($_GET['date_j']);?>"/><br />
                                <label class="modif" for="date">Heure</label> <input class="modif" type="time"  name="date" id="date" value="<?php if(isset($_GET['heure']))echo($_GET['heure']);?>"/><br />
                                <label class="modif" for="durre"> Durée</label> <input class="modif" type="number"  name="durre" id="durre" value="<?php if(isset($_GET['durre']))echo($_GET['durre']);?>"/><br />
                                <label class="modif" for="idp"> Encadrant : </label>
                                    <?php if(isset($_GET['idp'])){
                                        select_profs($_GET['idp'], "modif"); 
                                    }
                                    else {
                                        select_profs(-1, "modif"); 
                                    } ?><br />
                                <label class="modif" for="rdv">Observation/Détail</label> <input class="modif" type="text"  name="rdv" id="rdv" value="<?php if(isset($_GET['rdv'])) echo($_GET['rdv']);?>"/><br />
                                <label class="modif" for="lieu">Lieu</label> <input class="modif" type="texte"  name="lieu" id="lieu" value="<?php if(isset($_GET['lieu'])) echo($_GET['lieu']);?>"/><br />
                                <label class="modif" for="coulleur">Couleur</label><!-- <input type="color"  name="coulleur" id="coulleur" value="#000000"/><br />-->
                                <select class="modif" name="coulleur">
                                    <option value=0>--couleur--</option>
                                    <option style="background:#9BD9EE;" value='#9BD9EE' <?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'Z9BD9EE'){echo(' selected="selected" ');}?>>CDR</option>
                                    <option style="background:#7CCB06;" value='#7CCB06'<?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'Z7CCB06'){echo(' selected="selected" ');}?>>Pépinière</option>
                                    <option style="background:#ADFF2F;" value='#ADFF2F'<?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'ZADFF2F'){echo(' selected="selected" ');}?>>Serres</option>
                                    <option style="background:#DF9FDF;" value='#DF9FDF'<?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'ZDF9FDF'){echo(' selected="selected" ');}?>>Individualisation</option>
                                    <option style="background:#DBE2D0;" value='#DBE2D0'<?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'ZDBE2D0'){echo(' selected="selected" ');}?>>Cours prof</option>
                                    <option style="background:#F3E768;" value='#F3E768'<?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'ZF3E768'){echo(' selected="selected" ');}?>>Arexhor</option>
                                    <option style="background:#FD9BAA;" value='#FD9BAA'<?php if(isset($_GET['coulleur']) && $_GET['coulleur'] == 'ZFD9BAA'){echo(' selected="selected" ');}?>>A confirmer</option>
                                </select></br>
                                <label class="modif" for="nrepeat">Numéros des semaines à répéter</label><input placeholder="separer chaque numéro par un ;" class="modif" type="text" id="nrepeat" name="nrepeat" value="<?php if(isset($_GET['nrepeat']))echo($_GET['nrepeat']);?>"><br />
                                <input class="modif" type="submit" name="Envoyer" value="Envoyer" />
                        </form>
                    </td>
                    <td>
                        <?php 
                        if(!isset($_GET['idel'])){
                            $ide=-1;
                        }
                        else{
                            $ide=$_GET['idel'];
                        }?>

                        <iframe id="edt_view"
                            title="edt_view"
                            src="edt.php?semaine=<?php echo($s);?>&view=0&ide=<?php echo($ide);?>&key=<?php echo($_GET['key']);?>">
                        </iframe>
                    </td>
                </tr>
            </table>
        </body>
    </html>
<?php } ?>