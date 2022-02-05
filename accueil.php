<?php
include('fonction.php');

if(isset($_GET['semaine'])){
    if(strpos($_GET['semaine'],"-W")!=FALSE){
        if(isset($_GET['semaine'][7])){
            $s = $_GET['semaine'][6].$_GET['semaine'][7];
        }
        else {
            $s = $_GET['semaine'][6];
        }

        if(isset($_GET['key']) && test_id($_GET['key'])){
            $key=$_GET['key'];
            header("Location: accueil.php?semaine=".$s."&key=".$key);
        } 
    }
    $semaine = $_GET['semaine'];
}
else{
    $semaine = date('W', time());
}
if(isset($_GET['key']) && test_id($_GET['key'])){
$key=$_GET['key'];
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="accueil.css" />
        <title>Accueil EDT</title>
        <script>
            function portail() {
                let p_ell = document.getElementById("p_ell");
                let p_pr = document.getElementById("p_pr");
                let e_ell = document.getElementById("e_ell");
                let e_pr = document.getElementById("e_pr");
                let i_ell = document.getElementById("i_ell");
                let i_pr = document.getElementById("i_pr");
                if(p_ell.style.display == "none"){
                    p_ell.style.display = "inline-block";
                    p_pr.style.display = "inline-block";
                }
                else{
                    p_ell.style.display = "none";
                    p_pr.style.display = "none";
                }
                e_ell.style.display = "none";
                e_pr.style.display = "none";
                i_ell.style.display = "none";
                i_pr.style.display = "none";
            }
            function edt() {
                let p_ell = document.getElementById("p_ell");
                let p_pr = document.getElementById("p_pr");
                let e_ell = document.getElementById("e_ell");
                let e_pr = document.getElementById("e_pr");
                let i_ell = document.getElementById("i_ell");
                let i_pr = document.getElementById("i_pr");
                p_ell.style.display = "none";
                p_pr.style.display = "none";
                if(e_ell.style.display == "none"){
                    e_ell.style.display = "inline-block";
                    e_pr.style.display = "inline-block";
                }
                else{
                    e_ell.style.display = "none";
                    e_pr.style.display = "none";
                }
                i_ell.style.display = "none";
                i_pr.style.display = "none";
            }
            function imp() {
                let p_ell = document.getElementById("p_ell");
                let p_pr = document.getElementById("p_pr");
                let e_ell = document.getElementById("e_ell");
                let e_pr = document.getElementById("e_pr");
                let i_ell = document.getElementById("i_ell");
                let i_pr = document.getElementById("i_pr");
                p_ell.style.display = "none";
                p_pr.style.display = "none";
                e_ell.style.display = "none";
                e_pr.style.display = "none";
                if(i_ell.style.display == "none"){
                    i_ell.style.display = "inline-block";
                    i_pr.style.display = "inline-block";
                }
                else{
                    i_ell.style.display = "none";
                    i_pr.style.display = "none";
                }
            }

            function nothing() {
                let p_ell = document.getElementById("p_ell");
                let p_pr = document.getElementById("p_pr");
                let e_ell = document.getElementById("e_ell");
                let e_pr = document.getElementById("e_pr");
                let i_ell = document.getElementById("i_ell");
                let i_pr = document.getElementById("i_pr");
                p_ell.style.display = "none";
                p_pr.style.display = "none";
                e_ell.style.display = "none";
                e_pr.style.display = "none";
                i_ell.style.display = "none";
                i_pr.style.display = "none";
            }
        </script>
    </head>
    <body onload="nothing();">
        <div class= "head" id="head" onclick="nothing();">
            <img id="logo" src="icon/roville_logo.png"/>
            <h1 class="title">Emplois du temps Individualisation</h1>
            <form method="get" action="" class="select_week">
                <input type="HIDDEN" name = "key" value="consecteturadipiscingelit"/>
                <label for="semaine" class="select_week">Semaine ?</label> <input class="select_week" type="week"  name="semaine" id="semaine" value="<?php echo(date('Y', time())."-W".$semaine); ?>" onChange="this.form.submit();" require/>
            </form>
        </div>
        <div class="main">
            <table class="main">
                <tr class="main">
                    <td class="main">
                        <a href="import_data.php?key=<?php echo($key);?>"><img onclick="nothing()" src="icon/cercle_importer.png" class="import main"/></a>
                    </td>
                    <td class="main">
                        <a href="export_csv.php?key=<?php echo($key);?>&semaine=<?php echo($semaine);?>"><img onclick="nothing()" src="icon/cercle_exporter.png" class="export main"/></a>
                    </td>
                    <td class="main">
                        <a href="parametre.php?key=<?php echo($key);?>"><img onclick="nothing()" src="icon/cercle_parametres.png" class="param main"/></a>
                    </td>
                </tr>
                <tr class="main">
                    <td class="main">
                        <a href="all_edt.php?key=<?php echo($key);?>&semaine=<?php echo($semaine);?>"><img onclick="nothing()" src="icon/cercle_liens.png" class="lien main"/></a>
                    </td>
                    <td class="main">
                        <a href="edt_general.php?key=<?php echo($key);?>&semaine=<?php echo($semaine);?>"><img onclick="nothing()" src="icon/cercle_edt_general.png" class="ge main"/></a>
                    </td>
                    <td class="main">
                        <a href="new_rdv.php?key=<?php echo($key);?>"><img onclick="nothing()" src="icon/cercle_rdv.png" class="rdv_new main"/></a>
                    </td>
                </tr>
                <tr class="main">
                    <td class="main">
                        <img onclick="portail();event.stopPropagation();" src="icon/cercle_portail.png" id="portail_img" class="portail main"/>
                    </td>
                    <td class="main">
                        <img onclick="edt();event.stopPropagation();" src="icon/cercle_edt_individuel.png" class="edt main"/>
                    </td>
                    <td class="main">
                        <img onclick="imp();event.stopPropagation();" src="icon/cercle_imprimer.png" class="imp main"/>
                    </td>
                </tr>
            </table>

            <a href="add_e.php?key=<?php echo($key); ?>"><img onclick="" src="icon/cercle.png" id="p_ell" class="ell_portail"/></a>
            <a href="add_p.php?key=<?php echo($key); ?>"><img onclick="" src="icon/cercle.png" id="p_pr" class="pr_portail"/></a>

            <form method="get" action="edt.php" id="e_ell" class="ell_edt">
                <?php select_elleves(); ?></br>
                <input type="image" onclick="" src="icon/cercle.png" class="ell_edt"/>
            </form>
            <form method="get" action="edtpr.php" id="e_pr" class="pr_edt">
                <?php select_profs(); ?></br>
                <input type="image" onclick="" src="icon/cercle.png" class="pr_edt"/>
            </form>
            <a href="edt_full.php?key=<?php echo($key); ?>&semaine=<?php echo($semaine); ?>"><img onclick="" src="icon/cercle.png" id="i_ell" class="ell_imp"/></a>
            <a href="edt_full_pr.php?key=<?php echo($key); ?>&semaine=<?php echo($semaine); ?>"><img onclick="" src="icon/cercle.png" id="i_pr" class="pr_imp"/></a>

        </div>
    </body>
</html>

<?php } ?>