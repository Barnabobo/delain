<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");
if (!isset($db))
    include "verif_connexion.php";
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">
<head>
    <title>Repaire voleur</title>
</head>
<body>
<div class="bordiv">
    <?php
    define("VOL_NIV1_COD", "84");
    define("VOL_NIV2_COD", "85");
    define("VOL_NIV3_COD", "86");
    define("SEUIL_NIV2", "60");
    define("SEUIL_NIV3", "70");
    define("COUT_VOL_BR", "200");
    define("COUT_VOL_PA", "4");
    // on regarde si le joueur est bien sur un lieu
    $erreur = 0;
    if (!$db->is_lieu($perso_cod)) {
        echo("<p>Erreur ! Vous n'êtes pas sur un lieu !!!");
        $erreur = 1;
    }
    if ($erreur == 0) {
        $tab_lieu = $db->get_lieu($perso_cod);
        if ($tab_lieu['type_lieu'] != 6) {
            $erreur = 1;
            echo("<p>Erreur ! Vous n'êtes pas sur un lieu !!!");
        }
    }

    if ($erreur == 0) {
        $tab_temple = $db->get_lieu($perso_cod);
        $nom_lieu = $tab_temple['nom'];
        $type_lieu = $tab_temple['libelle'];

        $valeur_comp_niv1 = 0;
        $valeur_comp_niv2 = 0;
        $valeur_comp_niv3 = 0;

        $db = new base_delain;
        $req_comp = "select pcomp_modificateur from perso_competences ";
        $req_comp = $req_comp . "where pcomp_perso_cod = $perso_cod ";
        $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
        $req_comp = $req_comp . "and pcomp_pcomp_cod = " . VOL_NIV1_COD;
        $db->query($req_comp);
        if ($db->next_record()) {
            $valeur_comp_niv1 = $db->f("pcomp_modificateur");
        } else {
            $valeur_comp_niv1 = 0;
        }
        $req_comp = "select pcomp_modificateur from perso_competences ";
        $req_comp = $req_comp . "where pcomp_perso_cod = $perso_cod ";
        $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
        $req_comp = $req_comp . "and pcomp_pcomp_cod = " . VOL_NIV2_COD;
        $db->query($req_comp);
        if ($db->next_record()) {
            $valeur_comp_niv2 = $db->f("pcomp_modificateur");
        } else {
            $valeur_comp_niv2 = 0;
        }
        $req_comp = "select pcomp_modificateur from perso_competences ";
        $req_comp = $req_comp . "where pcomp_perso_cod = $perso_cod ";
        $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
        $req_comp = $req_comp . "and pcomp_pcomp_cod = " . VOL_NIV3_COD;
        $db->query($req_comp);
        if ($db->next_record()) {
            $valeur_comp_niv3 = $db->f("pcomp_modificateur");
        } else {
            $valeur_comp_niv3 = 0;
        }
        $req_perso = "select perso_po,perso_pa,perso_niveau from perso where perso_cod = $perso_cod ";
        $db->query($req_perso);
        $db->next_record();
        $nb_or = $db->f("perso_po");
        $nb_pa = $db->f("perso_pa");
        $cout = COUT_VOL_BR * $db->f("perso_niveau");
        $coutpa = COUT_VOL_PA;
        // TRAITEMENT DES ACTIONS
        if (isset($_POST['methode'])) {
            //
            switch ($methode) {
                case 'insert_comp_lvl_1':
                    if (($valeur_comp_niv1 == 0) && ($valeur_comp_niv2 == 0) && ($valeur_comp_niv3 == 0)) {
                        if ($nb_or < $cout) {
                            $erreur = 1;
                            ?><p><strong>Vous n'avez pas assez d'argent dans votre bourse</strong></p><?php
                        }
                        if ($nb_pa < $coutpa) {
                            $erreur = 1;
                            ?><p><strong>Vous n'avez pas assez de PAs disponibles</strong></p><?php
                        }
                        if ($erreur == 0) {
                            $req_or_pa = "update perso set perso_po = perso_po - $cout,perso_pa = perso_pa - $coutpa  where perso_cod = $perso_cod ";
                            $db->query($req_or_pa);
                            $req_comp = "insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) "
                                . "values($perso_cod," . VOL_NIV1_COD . ",25)";
                            $db->query($req_comp);
                            $valeur_comp_niv1 = 25;
                        }
                    } else {
                        ?><p>Vous avez déjà la compétence</p><?php
                    }
                    break;
                case 'update_comp_lvl_2':
                    if (($valeur_comp_niv1 >= SEUIL_NIV2) && ($valeur_comp_niv2 == 0) && ($valeur_comp_niv3 == 0)) {
                        if ($nb_or < ($cout * 2)) {
                            $erreur = 1;
                            ?><p><strong>Vous n'avez pas assez d'argent dans votre bourse</strong></p><?php
                        }
                        if ($nb_pa < ($coutpa * 2)) {
                            $erreur = 1;
                            ?><p><strong>Vous n'avez pas assez de PAs disponibles</strong></p><?php
                        }
                        if ($erreur == 0) {
                            $req_or_pa = "update perso set perso_po = perso_po - (2*$cout),perso_pa = perso_pa - (2*$coutpa)  where perso_cod = $perso_cod ";
                            $db->query($req_or_pa);
                            $req_comp = "update perso_competences set pcomp_pcomp_cod = " . VOL_NIV2_COD
                                . " WHERE pcomp_perso_cod = $perso_cod and pcomp_pcomp_cod = " . VOL_NIV1_COD;
                            $db->query($req_comp);
                            $valeur_comp_niv2 = $valeur_comp_niv1;
                            $valeur_comp_niv1 = 0;
                        }
                    } else {
                        ?><p>Vous avez déjà la compétence</p><?php
                    }
                    break;
                case 'update_comp_lvl_3':
                    if (($valeur_comp_niv2 >= SEUIL_NIV3) && ($valeur_comp_niv3 == 0)) {
                        if ($nb_or < ($cout * 3)) {
                            $erreur = 1;
                            ?><p><strong>Vous n'avez pas assez d'argent dans votre bourse</strong></p><?php
                        }
                        if ($nb_pa < ($coutpa * 3)) {
                            $erreur = 1;
                            ?><p><strong>Vous n'avez pas assez de PAs disponibles</strong></p><?php
                        }
                        if ($erreur == 0) {
                            $req_or_pa = "update perso set perso_po = perso_po - (3*$cout),perso_pa = perso_pa - (3*$coutpa)  where perso_cod = $perso_cod ";
                            $db->query($req_or_pa);
                            $req_comp = "update perso_competences set pcomp_pcomp_cod = " . VOL_NIV3_COD
                                . " WHERE pcomp_perso_cod = $perso_cod and pcomp_pcomp_cod = " . VOL_NIV2_COD;
                            $db->query($req_comp);
                            $valeur_comp_niv3 = $valeur_comp_niv2;
                            $valeur_comp_niv2 = 0;
                        }
                    } else {
                        ?><p>Vous avez déjà la compétence</p><?php
                    }
                    break;

            }
        }


        ?>
        <p><strong><?php echo("$nom_lieu</strong> - $type_lieu"); ?> <br>

        Niv 1 = <?php echo $valeur_comp_niv1 ?><br>
        Niv 2 = <?php echo $valeur_comp_niv2 ?><br>
        Niv 3 = <?php echo $valeur_comp_niv3 ?><br>
        <?php
        if (($valeur_comp_niv1 == 0) && ($valeur_comp_niv2 == 0) && ($valeur_comp_niv3 == 0)) {
            ?>
            <p>Ainsi donc tu viens ici pour la première fois, petit. Je peux te faire profiter de mon expérience,
                moyennant
                bien sur une petite compensation financière.</p>
            <form name="upgrade_voleur" method="post">
                <input type="hidden" name="methode" value="insert_comp_lvl_1">
                <input type="submit" value="Apprendre le vol lvl 1 (<?php echo $coutpa; ?> PA, <?php echo $cout; ?>Br)">
            </form>
            <?php
        } else if (($valeur_comp_niv1 < SEUIL_NIV2) && ($valeur_comp_niv2 == 0) && ($valeur_comp_niv3 == 0)) {
            ?>
            <p>Tu n'es pas encore assez doué pour passer à l'étape suivante, mais je ne peux plus t'entraîner, tu dois
                apprendre par toi-même.</p>
            <?php
        } else if (($valeur_comp_niv2 == 0) && ($valeur_comp_niv3 == 0)) {
            ?>
            <p>Ah ! Il semblerait que tu ne sois pas tout à fait irrécupérable, si tu veux tu peux apprendre quelques
                techniques plus avancées. Moyennant bien sur une petite compensation financière. </p>
            <form name="upgrade_voleur" method="post">
                <input type="hidden" name="methode" value="update_comp_lvl_2">
                <input type="submit"
                       value="Apprendre le vol lvl 2 (<?php echo 2 * $coutpa; ?> PA, <?php echo 2 * $cout; ?>Br)">
            </form>
            <?php
        } else if (($valeur_comp_niv2 < SEUIL_NIV3) && ($valeur_comp_niv3 == 0)) {
            ?>
            <p>Reviens quand tu seras un peu plus doué.</p>
            <?php
        } else if (($valeur_comp_niv3 == 0)) {
            ?>
            <p>Bien voilà un élève prometteur, tu pourras faire encore mieux si tu perfectionne encore ta technique avec
                quelques trucs supplémentaires </p>
            <form name="upgrade_voleur" method="post">
                <input type="hidden" name="methode" value="update_comp_lvl_3">
                <input type="submit"
                       value="Apprendre le vol lvl 3 (<?php echo 3 * $coutpa; ?> PA, <?php echo 3 * $cout; ?>Br)">
            </form>
            <?php
        } else { ?>
            <p> Je n'ai plus rien à t'apprendre ... Pour le moment. <br></p>
            <?php
        }
        ?>
        <?php
    }
    ?>
</div>
</body>
</html>
