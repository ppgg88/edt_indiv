<?php 
include('fonction.php');
if(isset($_GET['key']) && test_id($_GET['key'])){
    $key = $_GET['key'];
    if(!isset($_GET['semaine']))$s = date('W', time());
    else $s = $_GET['semaine'];

if(isset($_POST['Envoyer'])){
    include('log_bdd.php');
    $query=$pdo->prepare($_POST['sql']);
    $query->execute();
    print_r($query);
    while($messages = $query->fetch()){
        echo '<pre>'; print_r($messages); echo '</pre>';
    }
}
?>
    <html>
        <head>
            <meta charset="utf-8" />
            <title>Accueil EDT</title>
            <link rel="stylesheet" href="all.css" />
            <style>
                button{
                    display:block;
                    margin-left: auto;
                    margin-right: auto;
                }
                button.back{
                    width: 15% !important;
                }
                button.reset{
                    width: 25%;
                    margin-top: 9vh;
                }
                .centre{
                    display:block;
                    margin-left: auto;
                    margin-right: auto;
                }
                .sql_req{
                    margin-top: 9vh;
                    width: 25%;
                }
            </style>
        </head>
        <body>
            <form action="./reset.php" method="GET" id="pr" onsubmit="if(confirm('Vous allez suprimer Tout les rendez-vous enregistrer, Etes-vous sur ?')){return true;}else{return false;}">
                <div>
                    <input type="HIDDEN" name = "key" value="<?php echo($key) ?>"/>
                    <button class="reset">Suprimer tout les RDV</button>
                </div>
            </form>
            <form action="" method="POST" id="pr" onsubmit="if(confirm('Vous allez envoyer une requette à la base de donnée, Etes-vous sur ?')){return true;}else{return false;}">
                <div>
                    <input class="centre sql_req" type="text" name="sql"/>
                    <input class="centre" type="submit" name="Envoyer" value="Envoyer la Requête" />
                </div>
            </form>
            </br></br>
            <form class="no_print back" action="index.php?key=<?php echo($key); ?>&semaine=<?php echo($s); ?>" method="POST">
                <button class="back">RETOUR ACCUEIL</button>
            </form>
        </body>
    </html>
<?php } ?>