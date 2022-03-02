<?php
include('fonction.php');
include('log_bdd.php');
if(isset($_GET['key']) && test_id($_GET['key'])){
    if(isset($_GET['idp']) && isset($_GET['semaine'])){
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
            $url_base = "https";
        }
        else{
            $url_base = "http"; 
        }
        $url_base .= "://"; 
        $url_base .= $_SERVER['HTTP_HOST']; 
        $link = $url_base.'/edtpr.php?idp='.$_GET['idp'].'&key='.id_prof($_GET['idp'], $_GET['semaine']).'&semaine=';
        $url = $url_base.'/edtpr.php?idp='.$_GET['idp'].'%26semaine='.$_GET['semaine']."%26key=".id_prof($_GET['idp'], $_GET['semaine']); 
        $qr_path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$url.'%2F&choe=UTF-8';
        $link .= $_GET['semaine'];
        $sqlqueryy = "SELECT * FROM `proph` where `id` = ".$_GET['idp'];
        $recipesStatementt = $pdo->prepare($sqlqueryy);
        $recipesStatementt->execute();
        $recipess = $recipesStatementt->fetchAll();
        foreach ($recipess as $ress){
            $destinataire = $ress['mail'];
        }
        // Pour les champs $expediteur / $copie / $destinataire, séparer par une virgule s'il y a plusieurs adresses
        $expediteur = 'no_reply@no_reply.fr';
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
                        <p>Bonjour,</p>
                        <p> ci-joint, votre lien pour l\'EDT Individualisation de la semaine '.$_GET['semaine'].' :</p>
                        <p><a href="'.$link.'">'.$link.'</a></p>
                        <img  id="page-wrapper" src="'.$qr_path.'" id="logo" style="height: 20vh; float: right; margin-right:5vw;"/>
                        <hr style="
                                height: none;
                                border: none;
                                border-top: 1px dashed grey;"
                        />
                        <p style="color : #888888;">* Retrouvez ici une video tutoriel d\'une minute pour comprendre le fonctionement de l\'ENT : <a href="https://youtu.be/RGdg-TCmldY">https://youtu.be/RGdg-TCmldY  </a></p>
                    
                    </div>';
        if (mail($destinataire, $objet, $message, $headers)) // Envoi du message
        {
            echo 'Votre message a bien été envoyé ';
        }
        else // Non envoyé
        {
            echo "Votre message n'a pas pu être envoyé verifier qu'une adresse mail à ete rensseigner dans le portail/profs";
        }
    }
    ?>
    <form class="no_print" action="edtpr.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($_GET['semaine']);?>&idp=<?php echo($_GET['idp']);?>" method="POST">
        <button>RETOUR</button>
    </form>
<?php
}
?>