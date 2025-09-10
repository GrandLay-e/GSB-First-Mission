<!-- Division pour le sommaire -->
<nav class="menuLeft">
    <ul class="menu-ul">
        <li class="menu-item"><a href="index.php"> <button> retour </button></a></li>

        <li class="menu-item">
            Visiteur :<br>
            <?php echo $_SESSION['prenom'] . "  " . $_SESSION['nom'] ?>
        </li>

        <li class="menu-item">
            <a href="index.php?uc=ajoutFrais&action=saisirFrais" title="Ajouter Frais"> <button> Ajouter Frais </button></a>
        </li>
        <li class="menu-item">
            <a href="index.php?uc=etatFrais&action=selectionnerMois" title="Consultation de mes fiches de frais"> <button> Mes
                fiches de frais </button> </a>
        </li>
        <li class="menu-item">
            <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter"> <button> Déconnexion</button> </a>
        </li>
    </ul>
</nav>
<section class="content">


