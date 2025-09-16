<?php
/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsbfrais';
    private static $user = 'root';
    private static $mdp = '';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     * @return null L'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     * @param $login
     * @param $mdp
     * @return mixed L'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $req = "select id, nom, prenom from visiteur where login='$login' and mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    /**
     * Transforme une date au format français jj/mm/aaaa vers le format anglais aaaa-mm-jj
     * @param $madate au format  jj/mm/aaaa
     * @return la date au format anglais aaaa-mm-jj
     */
    public function dateAnglaisVersFrancais($maDate)
    {
        @list($annee, $mois, $jour) = explode('-', $maDate);
        $date = "$jour" . "/" . $mois . "/" . $annee;
        return $date;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments
     * La boucle foreach ne peut être utilisée ici, car on procède
     * à une modification de la structure itérée - transformation du champ date-
     * @param $idVisiteur
     * @param $mois 'sous la forme aaaamm
    * @return array 'Tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $req = "select * from lignefraishorsforfait where idvisiteur ='$idVisiteur' 
		and mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            //Gestion des dates
            @list($annee, $mois, $jour) = explode('-', $date);
            $dateStr = "$jour" . "/" . $mois . "/" . $annee;
            $lesLignes[$i]['date'] = $dateStr;
        }
        return $lesLignes;
    }


    /**
     * Retourne les mois pour lesquels, un visiteur a une fiche de frais
     * @param $idVisiteur
     * @return array 'Un tableau associatif de clé un mois - aaaamm - et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $req = "select mois from  fichefrais where idvisiteur ='$idVisiteur' order by mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donn�
     * @param $idVisiteur
     * @param $mois 'sous la forme aaaamm
    * @return mixed 'Un tableau avec des champs de jointure entre une fiche de frais et la ligne d'�tat
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $req = "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id 
			where fichefrais.idVisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    public function getIdVisiteurs()
    {
        $req = "select id from visiteur";
        $res = PdoGsb::$monPdo->query($req);
        $lesIds = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $id = $laLigne['id'];
            $lesIds[] = $id;
            $laLigne = $res->fetch();
        }
        return $lesIds;
    }

    //vérifier sur une date pour un utilisateur est deja saisie
   /* public function CheckClosedInputs($idVisiteur, $date){
        $req = "select * from fichefrais where mois = :mois and idVisiteur = :idVisiteur and idEtat = 'CL'";
        $res = PdoGsb::$monPdo->prepare($req);
        $res->bindParam("mois", $date);
        $res->bindParam("idVisiteur", $idVisiteur);
        $res->execute();
        $laLigne = $res->fetch();
        return $laLigne;
    }*/

    //Mettre à joute une fiche de frais
    public function updateFicheFrais($idVisiteur, $mois, $nbJustificatif, $montant, $dateModif, $idEtat){
        $req = "UPDATE fichefrais SET nbjustificatifs = :nbjustif,
                                       montantValide = (montantValide+:montant), 
                                       dateModif = :date, 
                                       idEtat = :idEtat
                                       WHERE mois = :mois AND idVisiteur = :idVisiteur";

        $stmt = PdoGsb::$monPdo->prepare($req);
        $stmt->bindParam(':nbjustif', $nbJustificatif);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':date', $dateModif);
        $stmt->bindParam(':idEtat', $idEtat);
        $stmt->bindParam(':mois', $mois);
        $stmt->bindParam(':idVisiteur', $idVisiteur);
        $stmt->execute();
    }

    public function ajouterFicheFrais($idVisiteur, $mois, $nbJustificatif, $montant, $dateModif, $idEtat, $update = false)
    {
        if($update){
            $req = "UPDATE fichefrais SET nbjustificatifs = :nbJustificatif,
                                       montantValide = (montantValide+:montant), 
                                       dateModif = :dateModif, 
                                       idEtat = :idEtat
                                       WHERE mois = :mois AND idVisiteur = :idVisiteur";
        }else {
            $req = "INSERT INTO fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat) 
                VALUES (:idVisiteur, :mois, :nbJustificatif, :montant, :dateModif, :idEtat)";
        }

        $stmt = PdoGsb::$monPdo->prepare($req);
        $stmt->bindParam(':idVisiteur', $idVisiteur);
        $stmt->bindParam(':mois', $mois);
        $stmt->bindParam(':nbJustificatif', $nbJustificatif);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':dateModif', $dateModif);
        $stmt->bindParam(':idEtat', $idEtat);
        $stmt->execute();
    }

    public function ajouterLigneFraisForfait($idVisiteur, $mois, $repasFrais, $nuiteeFrais, $etapeFrais, $kmFrais, $update = false)
    {
        if($update){
            $req = "UPDATE lignefraisforfait SET quantite = (quantite +:repasFrais) WHERE idVisiteur = :idVisiteur AND mois = :mois AND idFraisForfait = 'REP';
                    UPDATE lignefraisforfait SET quantite = (quantite +:nuiteeFrais) WHERE idVisiteur = :idVisiteur AND mois = :mois AND idFraisForfait = 'NUI';
                    UPDATE lignefraisforfait SET quantite = (quantite +:etapeFrais) WHERE idVisiteur = :idVisiteur AND mois = :mois AND idFraisForfait = 'ETP';
                    UPDATE lignefraisforfait SET quantite = (quantite +:kmFrais) WHERE idVisiteur = :idVisiteur AND mois = :mois AND idFraisForfait = 'KM';";
        }else{
            $req = "INSERT INTO lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite) VALUES
                (:idVisiteur, :mois, 'REP', :repasFrais),
                (:idVisiteur, :mois, 'NUI', :nuiteeFrais),
                (:idVisiteur, :mois, 'ETP', :etapeFrais),
                (:idVisiteur, :mois, 'KM', :kmFrais)";
        }
        $stmt = PdoGsb::$monPdo->prepare($req);
        $stmt->bindParam(':idVisiteur', $idVisiteur);
        $stmt->bindParam(':mois', $mois);
        $stmt->bindParam(':repasFrais', $repasFrais);
        $stmt->bindParam(':nuiteeFrais', $nuiteeFrais);
        $stmt->bindParam(':etapeFrais', $etapeFrais);
        $stmt->bindParam(':kmFrais', $kmFrais);
        $stmt->execute();
    }

    public function getPrix($idFrais)
    {
        $req = "select montant from fraisforfait where id = '$idFrais'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch(PDO::FETCH_ASSOC);
        return $laLigne['montant'];
    }

    public function getEtat($idVisiteur, $mois) {
        $req = "SELECT idEtat FROM fichefrais WHERE mois = ? AND idVisiteur = ?";
        $stmt = PdoGsb::$monPdo->prepare($req);
        $stmt->bindParam(1, $mois);
        $stmt->bindParam(2, $idVisiteur);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false || !isset($result['idEtat'])) {
            return null;}
        return $result['idEtat'];
    }
}

