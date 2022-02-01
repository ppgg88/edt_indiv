<html>
<head>
    <title>EDT-Lien S:<?php echo($_GET['semaine']);?></title>
    <style>
        .noa{
            text-decoration: none;
            color: black;
        }
        .lien{
            margin-left: 5vw;
        }
    </style>
</head>
<body>
<?php 
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

    if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
        if(strpos($_GET['semaine'],"-W")!=FALSE){
            if(isset($_GET['semaine'][7])){
                $s = $_GET['semaine'][6].$_GET['semaine'][7];
            }
            else {
                $s = $_GET['semaine'][6];
            }
            header("Location: all_edt.php?semaine=".$s."&key=consecteturadipiscingelit");
        }
        include('log_bdd.php');
        echo("<p> - EDT ELLEVES</p>");
        $sqlq = "SELECT * FROM `elleve`";
        $recipesStat = $pdo->prepare($sqlq);
        $recipesStat->execute();
        $reci = $recipesStat->fetchAll();
        foreach ($reci as $re){
            $url = $url_base.'/edt.php?ide='.$re['id'].'&semaine='.$_GET['semaine'];
            echo('<a class="noa" href="'.$url.'"><p class="lien">'.$re['prenom'].' '.$re['nom'].' : '.$url.'</p></a>');
        }
    }
    else{
        echo('<h1>merci d\'utiliser le lien de connexion</h1>');
    }
 
    if(isset($_GET['key']) && $_GET['key'] == "consecteturadipiscingelit"){
    ?>
    <form action="index.php?key=consecteturadipiscingelit" method="POST">
        <button>RETOUR ACCUEIL</button>
    </form>
    <?php     
    }
    ?>


</body>
</html>