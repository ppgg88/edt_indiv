<?php

function select_elleves($sellected_id = NULL, $class=""){
    include("log_bdd.php");
    echo('<select name="ide" class="s_eleves '.$class.'"><option value=0>--Elèves--</option>');
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

?>