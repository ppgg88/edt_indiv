<?php
    include('fonction.php');
    if(isset($_GET['key']) && test_id($_GET['key'])){

        if(isset($_GET['ide']))$ide = $_GET['ide'];
        else $ide = -1;
        if(isset($_GET['s']))$s = $_GET['s'];
        else $s = $_GET['semaine'];

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
                header("location: new_rdv.php?key=".$_GET['key']."&semaine=".$_GET['semaine']."&ide=".$_POST['ide']."&s=".date('W', strtotime($d)));
            }
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
                                <label class="modif" for="ide"> élève : </label><?php select_elleves($ide, "modif"); ?><br />
                                <label class="modif" for="date_j">Date</label> <input class="modif" type="date"  name="date_j" id="date_j" value=""/><br />
                                <label class="modif" for="date">Heure</label> <input class="modif" type="time"  name="date" id="date" value=""/><br />
                                <label class="modif" for="durre"> Durée</label> <input class="modif" type="number"  name="durre" id="durre" value=""/><br />
                                <label class="modif" for="idp"> Encadrant : </label><?php select_profs(-1, "modif"); ?><br />
                                <label class="modif" for="rdv">Observation/Détail</label> <input class="modif" type="text"  name="rdv" id="rdv" value=""/><br />
                                <label class="modif" for="lieu">Lieu</label> <input class="modif" type="texte"  name="lieu" id="lieu" value=""/><br />
                                <label class="modif" for="coulleur">Couleur</label><!-- <input type="color"  name="coulleur" id="coulleur" value="#000000"/><br />-->
                                <select class="modif" name="coulleur">
                                    <option value=0>--couleur--</option>
                                    <option style="background:#9BD9EE;" value='#9BD9EE'>CDR</option>
                                    <option style="background:#7CCB06;" value='#7CCB06'>Pépinière</option>
                                    <option style="background:#ADFF2F;" value='#ADFF2F'>Serres</option>
                                    <option style="background:#DF9FDF;" value='#DF9FDF'>Individualisation</option>
                                    <option style="background:#DBE2D0;" value='#DBE2D0'>Cours prof</option>
                                    <option style="background:#F3E768;" value='#F3E768'>Arexhor</option>
                                    <option style="background:#FD9BAA;" value='#FD9BAA'>A confirmer</option>
                                </select></br>
                                <label class="modif" for="nrepeat">Nombre de semaines à répeter</label><input class="modif" type="number" id="nrepeat" name="nrepeat" min="1" max="52" value="1"><br />
                                <input class="modif" type="submit" name="Envoyer" value="Envoyer" />
                        </form>
                    </td>
                    <td>
                        <iframe id="edt_view"
                            title="edt_view"
                            src="http://edt-indiv/edt.php?semaine=<?php echo($s);?>&view=0&ide=<?php echo($ide);?>">
                        </iframe>
                    </td>
                </tr>
            </table>
        </body>
    </html>
<?php } ?>