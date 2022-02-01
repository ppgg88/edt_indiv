<?php 
include('log_bdd.php');

// INSERTION rdv : INSERT INTO `rdv` (`id`, `nom`, `date`, `durre`, `couleur`, `id_elleve`, `id_proph`) VALUES (NULL, 'test_rdv_bdd', '2021-12-10 16:00:00', '60', '#FF00FF', '1', '1');
// Ecriture de la requête
    if(isset($_POST['Envoyer'])){
        $query=$pdo->prepare("INSERT INTO proph (nom, prenom) VALUES (:n, :p)");
        $query->bindValue(':n', $_POST['nom'], PDO::PARAM_STR);
        $query->bindValue(':p', $_POST['prenom'], PDO::PARAM_STR);
        $query->execute();
        $query->CloseCursor();
    }
?>

<html>
<head>
    <title>EDT-Ajouter profs</title>
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
        table{
            width: 40% !important;
        }
    </style>
</head>
<body>
<?php 
if(isset($_GET['key'])){
    if($_GET['key'] == "consecteturadipiscingelit"){
?>
<h3>AJOUTER UN PROF :</h3>
<form method="post" action="">
        <label for="nom">nom</label> <input type="text"  name="nom" id="nom" value="Nom"/><br />
        <label for="prenom">prenom</label> <input type="text"  name="prenom" id="prenom" value="Prenom"/><br />
        <!--<label for="class">classe</label> <input type="text"  name="class" id="class" value="classe"/><br /> -->
        <input type="submit" name="Envoyer" value="Envoyer" />
</form>
</br></br></br>
<form action="index.php?key=consecteturadipiscingelit" method="POST">
    <button>RETOUR ACCUEIL</button>
</form>
<?php     
}}

$sqlqueryy = "SELECT * FROM `proph`";
$recipesStatementt = $pdo->prepare($sqlqueryy);
$recipesStatementt->execute();
$recipess = $recipesStatementt->fetchAll();
echo('<table cellspacing="0" cellpadding="0">');
echo('<tr style="padding-right: 0vw; width : 30vw" class="head">
        <td>Nom</td>
        <td>Prenom</td>
    </tr>');
foreach ($recipess as $ress){
    echo('<tr>
            <td>'.$ress['nom'].'</td>
            <td>'.$ress['prenom'].'</td>
        </tr>');
}
echo('</table>');

?>
</body>
</html>