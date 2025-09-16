<?php
/** @var PdoGsb $pdo */
include("views/v_sommaire.php");
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
$listVisiteurs = $pdo->getIdVisiteurs();
switch ($action) {
    
    //En cas de validation du formulaire de saisie de frais
    case 'ajouterFrais':
    {
        //----------------------------------------------------------------------------------//
        //Récupération des données du formulaire
        $numeroVisiteur = $_REQUEST['numeroVisiteur'];
        $mois = $_REQUEST['anneeEngagement'] . $_REQUEST['moisEngagement'];
        $repas = $_REQUEST['repasFrais'];
        $nuitee = $_REQUEST['nuiteeFrais'];
        $etape = $_REQUEST['etapeFrais'];
        $km = $_REQUEST['kmFrais'];


        //----------------------------------------------------------------------------------//
        //Récupération des prix des frais forfaitaires
        $prixRepas = $pdo->getPrix('REP') * $repas;
        $prixNuitee = $pdo->getPrix('NUI') * $nuitee;
        $prixEtape = $pdo->getPrix('ETP') * $etape;
        $prixKm = $pdo->getPrix('KM') * $km;

        //----------------------------------------------------------------------------------//
        //prix total des frais (dans fiche frais)
        $montantTotal = $prixRepas + $prixNuitee + $prixEtape + $prixKm;
        //----------------------------------------------------------------------------------//
        $nbJustificatifs = $repas + $nuitee + $etape + $km;

        //Vérification des champs
        //----------------------------------------------------------------------------------//
        $infosFIcheFrais = array($numeroVisiteur, $mois, $repas, $nuitee, $etape, $km);
        foreach ($infosFIcheFrais as $info){
            if (empty($info)){
                ajouterErreur("Tous les champs doivent être remplis");
                include("views/v_erreurs.php");
                include("views/v_gestFrais.php");
                break;
            }
        }

        //----------------------------------------------------------------------------------//
        //Vérification si une fiche de frais pour ce visiteur et ce mois existe déjà
        $etatFrais = $pdo->getEtat($numeroVisiteur, $mois);
        echo $etatFrais . '<br>';
        if(isset($etatFrais) && ($etatFrais != null)){
            if($etatFrais == 'CL'){
                // S'il existe et est close
                ajouterErreur("Une fiche de frais pour ce visiteur et ce mois est déjà validée, vous ne pouvez pas la modifier");
                include("views/v_erreurs.php");
                include("views/v_gestFrais.php");
            }else{
                //existe et est ouvert, on met à jour
                $dateModif = date('Y-m-d');
                $pdo->ajouterFicheFrais($numeroVisiteur, $mois, $nbJustificatifs, $montantTotal, $dateModif, 'CR', true);
                $pdo->ajouterLigneFraisForfait($numeroVisiteur, $mois, $repas, $nuitee, $etape, $km, true);
                include("views/v_validationFrais.php");
                include("views/v_gestFrais.php");
                echo "Update fiche frais + update lignes frais";
            }
        }else{
            //N'existe pas, on crée la fiche et on ajoute les lignes
            $dateModif = date('Y-m-d');
            $pdo->ajouterFicheFrais($numeroVisiteur, $mois, $nbJustificatifs, $montantTotal, $dateModif, 'CR', false);
            $pdo->ajouterLigneFraisForfait($numeroVisiteur, $mois, $repas, $nuitee, $etape, $km, false);
            include("views/v_validationFrais.php");
            include("views/v_gestFrais.php");
            echo "Insert fiche frais + ajout lignes frais";
        }
        break;
    }

    //----------------------------------------------------------------------------------//
    //En cas d'affichage du formulaire de saisie de frais depuis le sommaire
    case 'saisirFrais':
    {

        include("views/v_gestFrais.php");
        break;
    }
/*
    default:{
        include("views/v_gestFrais.php");
        break;
    }*/
}