<?php
include('fonction.php');
if(isset($_GET['key']) && test_id($_GET['key'])){

    if(isset($_POST['send'])){
        include('log_bdd.php');

        $head = $_POST['head'];
        $body = $_POST['body'];

        $ladate = new DateTime();
        $ladate->setISOdate(strftime("%Y"), $_GET['semaine']);
        $s = date_format($ladate, 'Y-m-d');
        $ladate = strftime("%Y-%M-%d", $ladate->getTimestamp());
        $d = new DateTime('Monday this week '.$s);
        $start = date_format($d, 'Y-m-d');
        $d->add(new DateInterval('P7D'));
        $end = date_format($d, 'Y-m-d');

        $sql = "SELECT DISTINCT rdv.id_proph as id, proph.mail as mail, proph.nom as nom, proph.prenom as prenom FROM rdv, proph where rdv.id_proph = proph.id and rdv.date >= :dd and rdv.date < :df";
        $query=$pdo->prepare($sql);
        $query->bindValue(':dd', $start, PDO::PARAM_STR);
        $query->bindValue(':df', $end, PDO::PARAM_STR);
        $query->execute();
        $recipes = $query->fetchAll();
        foreach ($recipes as $res)
        {
            $h = $head;
            $b = $body;
            $h = str_replace("%prenom%", $res['prenom'], $h);
            $h = str_replace("%nom%", $res['nom'], $h);
            $h = str_replace("%semaine%", $_GET['semaine'], $h);
            $b = str_replace("%prenom%", $res['prenom'], $b);
            $b = str_replace("%nom%", $res['nom'], $b);
            $b = str_replace("%semaine%", $_GET['semaine'], $b);
            echo('<p>'.$h." ".$b."</p>");
            //pour chaque proph trouver à partir de son id et de son mail

            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
                $url_base = "https";
            }
            else{
                $url_base = "http"; 
            }
            $url_base .= "://"; 
            $url_base .= $_SERVER['HTTP_HOST']; 
            $link = $url_base.'/edtpr.php?idp='.$res['id'].'&key='.id_prof($res['id'], $_GET['semaine']).'&semaine=';
            $url = $url_base.'/edtpr.php?idp='.$res['id'].'%26semaine='.$_GET['semaine']."%26key=".id_prof($res['id'], $_GET['semaine']); 
            $qr_path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$url.'%2F&choe=UTF-8';
            $link .= $_GET['semaine'];
            $destinataire = $res['mail'];
            // Pour les champs $expediteur / $copie / $destinataire, séparer par une virgule s'il y a plusieurs adresses
            $expediteur = 'no_reply@edt-individualisation.fr';
            $copie = '';
            $copie_cachee = '';
            $objet = 'EDT Individualisation'; // Objet du message
            $headers  = 'MIME-Version: 1.0' . "\n"; // Version MIME
            $headers .= 'Content-type: text/html; charset=ISO-8859-1'."\n"; // l'en-tete Content-type pour le format HTML
            $headers .= 'Reply-To: '.$expediteur."\n"; // Mail de reponse
            $headers .= 'From: "EDT-Individualisation"<'.$expediteur.'>'."\n"; // Expediteur
            $headers .= 'Delivered-to: '.$destinataire."\n"; // Destinataire
            $headers .= 'Cc: '.$copie."\n"; // Copie Cc
            $headers .= 'Bcc: '.$copie_cachee."\n\n"; // Copie cachée Bcc        
            $message = '<div style="width: 100%; text-align: left; font-weight: bold">
                            <p>'.$h.'</p>
                            <p>'.$b.'</p>
                            <p><a href="'.$link.'">'.$link.'</a></p>
                            <img  id="page-wrapper" src="'.$qr_path.'" id="logo" style="height: 20vh; float: right; margin-right:5vw;"/>
                            <hr style="
                                    height: none;
                                    border: none;
                                    border-top: 1px dashed grey;"
                            />
                            <p style="color : #888888;">* Retrouvez ici une video tutoriel d\'une minute pour comprendre le fonctionement de l\'ENT : <a href="">...</a></p>
                        
                        </div>';
            //echo($message);
            if (mail($destinataire, $objet, $message, $headers)) // Envoi du message
            {
                echo '<p>Votre message a bien été envoyé à '.$res['prenom'].' '.$res['nom'].'</p>';
            }
            else
            {
                echo '<p>Votre message n\'a pas pu être envoyé à '.$res['prenom'].' '.$res['nom'].'. verifier qu\'une adresse mail à ete rensseigner dans le portail/profs</p>';
            }
        }
        $query->CloseCursor();
    }
?>
<html>
    <head>
        <style>
            .head{
                display: block;
                width : 100%;
            }
            .body{
              display: block;
               width : 100%;
                
            }
        </style>
    </head>
    <body>
        <h4>envoie Groupe : </h4>
        <form action="" method="POST" onsubmit="if(confirm('Vous allez envoyer les liens par mail, Etes-vous sur ?')){return true;}else{return false;}">
            <?php 
            $head = 'Bonjour %prenom% %nom%,';
            $corp = 'ci-joint, votre lien pour l\'EDT Individualisation de la semaine %semaine% :';
            ?>
            <textarea class="head" name="head"><?php echo($head); ?></textarea>
            <textarea class="body" name="body"><?php echo($corp); ?></textarea>
            <input class="centre sql_req_btn" type="submit" name="send" value="Envoyer les Mails" />
        </form>
        <form class="no_print" action="edt_full_pr.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>" method="POST">
            <button>RETOUR</button>
        </form>
    </body>
    <!-- 
        $nom : Nom du Profs
        $prenom : Prenom du Profs
        $semaine : Semaine
    -->
</html>

<?php } ?>