<?php 
include('fonction.php');
if(isset($_GET['key']) && test_id($_GET['key'])){

    if(!isset($_GET['semaine']))$s = date('W', time());
    else if(isset($_GET['semaine'][7]))$s = $_GET['semaine'][6].$_GET['semaine'][7];
    else $s = $_GET['semaine'];
    include('log_bdd.php');
    // INSERTION rdv : INSERT INTO `rdv` (`id`, `nom`, `date`, `durre`, `couleur`, `id_elleve`, `id_proph`) VALUES (NULL, 'test_rdv_bdd', '2021-12-10 16:00:00', '60', '#FF00FF', '1', '1');
    // Ecriture de la requête
    if(isset($_POST['Envoyer'])){
        $query=$pdo->prepare("INSERT INTO elleve (nom, prenom, mail, classe) VALUES (:n, :p, :m, :c)");
        $query->bindValue(':n', $_POST['nom'], PDO::PARAM_STR);
        $query->bindValue(':p', $_POST['prenom'], PDO::PARAM_STR);
        $query->bindValue(':m', $_POST['mail'], PDO::PARAM_STR);
        $query->bindValue(':c', $_POST['class'], PDO::PARAM_STR);
        $query->execute();
        $query->CloseCursor();
    }

    if(isset($_POST['Modifier'])){
        $query=$pdo->prepare("UPDATE elleve SET nom = :n , prenom = :p , mail = :m, classe = :c WHERE id = :id");
        $query->bindValue(':n', $_POST['nom'], PDO::PARAM_STR);
        $query->bindValue(':p', $_POST['prenom'], PDO::PARAM_STR);
        $query->bindValue(':c', $_POST['class'], PDO::PARAM_STR);
        $query->bindValue(':m', $_POST['mail'], PDO::PARAM_STR);
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();
    }

    if(isset($_POST['Supprimer'])){
        $queryy=$pdo->prepare("DELETE FROM `rdv` WHERE id_elleve = :id");
        $queryy->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $queryy->execute();
        $queryy->CloseCursor();
        $query=$pdo->prepare("DELETE FROM `elleve` WHERE id = :id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();
    }

?>
<html>
<head>
    <title>EDT-Ajouter eleves</title>
    <style type="text/css">
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
            tr{
            background-color: #b9edc3;
        }
        table{
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="all.css" />
    <script>
    </script>
</head>
<body>
<h3>AJOUTER / MODIFIER UNE FICHE ELEVE :</h3>
<form method="post" action="">
        <label for="nom">nom</label> <input type="text"  name="nom" id="nom" value="Nom"/><br />
        <label for="prenom">prenom</label> <input type="text"  name="prenom" id="prenom" value="Prenom"/><br />
        <label for="class">classe</label> <input type="text"  name="class" id="class" value="classe"/><br />
        <label for="mail">mail</label> <input type="mail"  name="mail" id="mail" value="mail@mail.fr"/><br />
        <input type="submit" name="Envoyer" value="Envoyer" />
</form>
</br></br>

<form action="index.php?key=<?php echo($_GET['key']);?>&semaine=<?php echo($s);?>" method="POST">
    <button>RETOUR ACCUEIL</button>
</form>
<?php     

$sqlqueryy = "SELECT * FROM `elleve` ORDER BY `nom`";
$recipesStatementt = $pdo->prepare($sqlqueryy);
$recipesStatementt->execute();
$recipess = $recipesStatementt->fetchAll();
echo('<table cellspacing="0" cellpadding="0">');
echo('<tr style="padding-right: 0vw; width : 30vw" class="head">
        <td>Nom</td>
        <td>Prenom</td>
        <td>Mail</td>
        <td>Classe</td>
    </tr>');
foreach ($recipess as $ress){
    $id='';
    if(isset($_GET['id']) && $_GET['id']==$ress['id']){
        $id = 'id="position"';
    }
    echo('<tr '.$id.'>
            <td onclick="location.href=\'?id='.$ress['id'].'&key='.$_GET['key'].'&semaine='.$s.'#position\'" >'.$ress['nom'].'</td>
            <td onclick="location.href=\'?id='.$ress['id'].'&key='.$_GET['key'].'&semaine='.$s.'#position\'" >'.$ress['prenom'].'</td>
            <td onclick="location.href=\'?id='.$ress['id'].'&key='.$_GET['key'].'&semaine='.$s.'#position\'" >'.$ress['mail'].'</td>
            <td onclick="location.href=\'?id='.$ress['id'].'&key='.$_GET['key'].'&semaine='.$s.'#position\'" >'.$ress['classe'].'</td>
        </tr>');
    if(isset($_GET['id']) && $_GET['id']==$ress['id']){
        echo('<tr>
                <td colspan="3">
                    <form method="post" action="">
                        <label for="nom">nom</label> <input type="text"  name="nom" id="nom" value="'.$ress['nom'].'"/><br />
                        <label for="prenom">prenom</label> <input type="text"  name="prenom" id="prenom" value="'.$ress['prenom'].'"/><br />
                        <label for="class">classe</label> <input type="text"  name="class" id="class" value="'.$ress['classe'].'"/><br />
                        <label for="mail">mail</label> <input type="mail"  name="mail" id="mail" value="'.$ress['mail'].'"/><br />
                        <input type="HIDDEN" name = "id" value="'.$ress['id'].'"/>
                        <input type="submit" name="Modifier" value="Modifier" />
                    </form>
                </td>
                <td>
                    <form method="post" action="">
                        <input type="HIDDEN" name = "id" value="'.$ress['id'].'"/>
                        <input type="submit" name="Supprimer" value="Supprimer" />
                    </form>
                </td>
            </tr>');
    }
}
echo('</table>');
}?>
</body>
</html>
