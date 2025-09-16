<?php
/** @var PdoGsb $pdo */
include("views/v_sommaire.php");
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];

$infosVisiteurs = $pdo->getInfosVisiteurs();
$listIdVisiteurs = array_keys($infosVisiteurs);
switch ($action) {
    //En cas de validation du formulaire de saisie de frais
    case 'ajouterFrais':
    {
        $errorMessage = "Tous les champs doivent être remplis";
        //----------------------------------------------------------------------------------//
        //Récupération des données du formulaire
        if (
            isset($_REQUEST['numeroVisiteur']) &&
            isset($_REQUEST['anneeEngagement']) &&
            isset($_REQUEST['moisEngagement']) &&
            isset($_REQUEST['repasFrais']) &&
            isset($_REQUEST['nuiteeFrais']) &&
            isset($_REQUEST['etapeFrais']) &&
            isset($_REQUEST['kmFrais'])
        ) {
            $numeroVisiteur = $_REQUEST['numeroVisiteur'];
            $mois = $_REQUEST['anneeEngagement'] . $_REQUEST['moisEngagement'];
            $repas = $_REQUEST['repasFrais'];
            $nuitee = $_REQUEST['nuiteeFrais'];
            $etape = $_REQUEST['etapeFrais'];
            $km = $_REQUEST['kmFrais'];
        } else {
            ajouterErreur($errorMessage);
            include("views/v_erreurs.php");
            include("views/v_ajoutFrais.php");
            break;
        }

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


        //----------------------------------------------------------------------------------//
        //Vérification si une fiche de frais pour ce visiteur et ce mois existe déjà
        $etatFrais = $pdo->getEtat($numeroVisiteur, $mois);
        if(isset($etatFrais) && ($etatFrais != null)){
            if($etatFrais === 'CR'){
                //existe et est ouvert, on met à jour
                $dateModif = date('Y-m-d');
                $pdo->ajouterFicheFrais($numeroVisiteur, $mois, $nbJustificatifs, $montantTotal, $dateModif, 'CR', true);
                $pdo->ajouterLigneFraisForfait($numeroVisiteur, $mois, $repas, $nuitee, $etape, $km, true);
                include("views/v_validationFrais.php");
                include("views/v_ajoutFrais.php");
            }else{                
                
                // S'il existe et est close
                ajouterErreur("Une fiche de frais pour ce visiteur et ce mois est déjà validée, vous ne pouvez pas la modifier");
                include("views/v_erreurs.php");
                include("views/v_ajoutFrais.php");
            }
        }else{
            //N'existe pas, on crée la fiche et on ajoute les lignes
            $dateModif = date('Y-m-d');
            $pdo->ajouterFicheFrais($numeroVisiteur, $mois, $nbJustificatifs, $montantTotal, $dateModif, 'CR', false);
            $pdo->ajouterLigneFraisForfait($numeroVisiteur, $mois, $repas, $nuitee, $etape, $km, false);
            include("views/v_validationFrais.php");
            include("views/v_ajoutFrais.php");
        }
        break;
    }

    //----------------------------------------------------------------------------------//
    //En cas de click sur le bouton (ajouter frais)
    case 'saisirFrais':
    {

        include("views/v_ajoutFrais.php");
        break;
    }

    default:{
        include("views/v_accueil.php");
        break;
    }
}