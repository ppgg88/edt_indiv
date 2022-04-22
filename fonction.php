<?php

function select_elleves($sellected_id = NULL, $class="", $num = ''){
    include("log_bdd.php");
    echo('<select name="ide'.$num.'" class="s_eleves '.$class.'"><option value=0>--Elèves--</option>');
    $sqlqueryy = "SELECT * FROM `elleve` order by `nom`";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    foreach ($recipess as $ress){
        if($sellected_id == $ress['id']){
            echo('<option selected="selected" value='.$ress['id'].'>'.$ress['nom'].' '.$ress['prenom'].'</option>');
        }
        else{
            echo('<option value='.$ress['id'].'>'.$ress['nom'].' '.$ress['prenom'].'</option>');
        }
    }
    echo('</select>');
}

function select_profs($sellected_id = NULL, $class=""){
    include("log_bdd.php");
    echo('<select class="s_profs '.$class.'" name="idp"><option value=0>--Profs--</option>');
    $sqlqueryy = "SELECT * FROM `proph` order by `nom`";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    foreach ($recipess as $ress){
        if($sellected_id == $ress['id']){
            echo('<option selected="selected" value='.$ress['id'].'>'.$ress['nom'].' '.$ress['prenom'].'</option>');
        }
        else{
            echo('<option value='.$ress['id'].'>'.$ress['nom'].' '.$ress['prenom'].'</option>');
        }
    }
    echo('</select>');
}

function select_classe(){
    include("log_bdd.php");
    echo('<select class="s_classe" name="classe"><option value="no">--classe--</option>');
    $sqlqueryy = "SELECT distinct classe FROM `elleve` order by `classe`";
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    foreach ($recipess as $ress){
        echo('<option value='.str_replace(' ','',$ress['classe']).'>'.$ress['classe'].'</option>');
    }
    echo('</select>');
}


function day($n){
    if($n==1) return("Lundi");
    if($n==2) return("Mardi");
    if($n==3) return("Mercredi");
    if($n==4) return("Jeudi");
    if($n==5) return("Vendredi");
    if($n==6) return("Samedi");
    if($n==7) return("Dimanche");
    
}

function test_id($id){
    if($id == "consecteturadipiscingelit") return True;
    else return false;
}

function id_prof($id_proph, $n_semaine){
    return($id_proph*$id_proph+$n_semaine*$n_semaine);
}
function test_id_prof($id_proph, $n_semaine, $id_test){
    if(id_prof($id_proph, $n_semaine) == $id_test) return True;
    else return false;
}

function notifier_prof($id_proph, $n_semaine){
    include("log_bdd.php");
    $sqlqueryy = "SELECT * FROM `proph` WHERE `id` = ".$id_proph;
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    foreach ($recipess as $ress){
        $id = $ress['id'];
        $nom_prof = $ress['nom'];
        $prenom_prof = $ress['prenom'];
        $mail_prof = $ress['mail'];
    }
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
        $url_base = "https";
    }
    else{
        $url_base = "http"; 
    }
    $url_base .= "://"; 
    $url_base .= $_SERVER['HTTP_HOST']; 
    $link = $url_base.'/edtpr.php?idp='.$id.'&key='.id_prof($id, $n_semaine).'&semaine='.$n_semaine;
    $url = $url_base.'/edtpr.php?idp='.$id.'%26semaine='.$n_semaine."%26key=".id_prof($id, $n_semaine); 
    $qr_path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$url.'%2F&choe=UTF-8';
    $destinataire = $mail_prof;
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
                    <p>Bonjour,</p>
                    <p>Une modification a ete effectuee sur votre emploi du temps d\'individualisation, vous pouvez le consulter via le lien suivant :</p>
                    <p><a href="'.$link.'">'.$link.'</a></p>
                    <img  id="page-wrapper" src="'.$qr_path.'" id="logo" style="height: 20vh; float: right; margin-right:5vw;"/>
                </div>';
    echo($message);
    if (mail($destinataire, $objet, $message, $headers)) // Envoi du message
    {
        echo '<p>Votre message a bien été envoyé à '.$res['prenom'].' '.$res['nom'].'</p>';
    }
    else
    {
        echo '<p>Votre message n\'a pas pu être envoyé à '.$res['prenom'].' '.$res['nom'].'. verifier qu\'une adresse mail à ete rensseigner dans le portail/profs</p>';
    }
}

function notifier_eleve($id_eleve, $n_semaine){
    include("log_bdd.php");
    $sqlqueryy = "SELECT * FROM `elleve` WHERE `id` = ".$id_eleve;
    $recipesStatementt = $pdo->prepare($sqlqueryy);
    $recipesStatementt->execute();
    $recipess = $recipesStatementt->fetchAll();
    foreach ($recipess as $ress){
        $id = $ress['id'];
        $nom_eleve = $ress['nom'];
        $prenom_eleve = $ress['prenom'];
        $mail_eleve = $ress['mail'];
    }
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
        $url_base = "https";
    }
    else{
        $url_base = "http"; 
    }
    $url_base .= "://"; 
    $url_base .= $_SERVER['HTTP_HOST']; 
    $link = $url_base.'/edt.php?ide='.$id.'&semaine='.$n_semaine;
    $url = $url_base.'/edt.php?ide='.$id.'%26semaine='.$n_semaine; 
    $qr_path = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$url.'%2F&choe=UTF-8';
    $destinataire = $mail_eleve;
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
                    <p>Bonjour,</p>
                    <p>Une modification a ete effectuee sur votre emploi du temps d\'individualisation, vous pouvez le consulter via le lien suivant :</p>
                    <p><a href="'.$link.'">'.$link.'</a></p>
                    <img  id="page-wrapper" src="'.$qr_path.'" id="logo" style="height: 20vh; float: right; margin-right:5vw;"/>
                </div>';
    echo($message);
    if (mail($destinataire, $objet, $message, $headers)) // Envoi du message
    {
        echo '<p>Votre message a bien été envoyé à '.$res['prenom'].' '.$res['nom'].'</p>';
    }
    else
    {
        echo '<p>Votre message n\'a pas pu être envoyé à '.$res['prenom'].' '.$res['nom'].'. verifier qu\'une adresse mail à ete rensseigner dans le portail/profs</p>';
    }
}
?>