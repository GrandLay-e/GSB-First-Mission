<?php
/** @var PdoGsb $pdo */
include("views/v_sommaire.php");
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];

switch ($action) {
    case 'ajouterFrais':
    {
        $numeroVisiteur = $_REQUEST['numeroVisiteur'];
        $moisEngagement = $pdo->getLesMoisDisponibles($idVisiteur);

        /*location("index.php");*/
    }
    case 'saisirFrais':
    {
        $listVisiteurs = $pdo->getIdVisiteurs();
        include("views/v_gestFrais.php");
        break;
    }
/*
    default:{
        include("views/v_gestFrais.php");
        break;
    }*/
}