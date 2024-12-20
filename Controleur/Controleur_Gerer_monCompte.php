<?php


use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_Compte_Administration_Gerer;
use App\Vue\Vue_Connexion_Formulaire_client;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Structure_BasDePage;
use App\Vue\Vue_Structure_Entete;
use App\Vue\Vue_Utilisateur_Changement_MDP;
use function App\Fonctions\CalculComplexiteMdp;


switch ($action) {
    case "changerMDP":
        //Il a cliqué sur changer Mot de passe. Cas pas fini
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->setMenu(new Vue_Menu_Administration($_SESSION["idCategorie_utilisateur"]));
        $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("", "Gerer_monCompte"));
        break;
    case "submitModifMDP":
        //il faut récuperer le mdp en BDD et vérifier qu'ils sont identiques
        $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParId($_SESSION["idUtilisateur"]);
        if ($_REQUEST["AncienPassword"] == $utilisateur["motDePasse"])
        {
            //on vérifie si le mot de passe de la BDD est le même que celui rentré
            if ($_REQUEST["NouveauPassword"] == $_REQUEST["ConfirmPassword"]) {
                $cmdp = CalculComplexiteMdp($_REQUEST["NouveauPassword"]);
                if ($cmdp < 90)
                    {
                            $Vue->setEntete(new Vue_Structure_Entete());
                            $Vue->setMenu(new Vue_Menu_Administration($_SESSION["idCategorie_utilisateur"]));
                            $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("<label><b>Votre mot de passe n'est pas assez complexe: " . $cmdp . " bits, il doit dépasser les 90 bits.<br> Les critères de val </b></label><br>
                                          <ul><li>- Minuscule </li>
                                              <li>- Majuscule </li>
                                              <li>- Chiffre   </li>
                                              <li>- Caractères spéciaux</li>
                                              <li>Longueur minimal de 15 caractères</li></ul>", "Gerer_monCompte"));
                    }
                else{
                    $Vue->setEntete(new Vue_Structure_Entete());
                    $Vue->setMenu(new Vue_Menu_Administration($_SESSION["idCategorie_utilisateur"]));
                    Modele_Utilisateur::Utilisateur_Modifier_motDePasse($_SESSION["idUtilisateur"], $_REQUEST["NouveauPassword"]);
                    $Vue->addToCorps(new Vue_Compte_Administration_Gerer("<label><b>Votre mot de passe a bien été modifié</b></label>"));
                    // Dans ce cas les mots de passe sont bons, il est donc modifier
                }
            } else {
                $Vue->setEntete(new Vue_Structure_Entete());
                $Vue->setMenu(new Vue_Menu_Administration($_SESSION["idCategorie_utilisateur"]));
                $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("<label><b>Les nouveaux mots de passe ne sont pas identiques</b></label>", "Gerer_monCompte"));
            }
        } else {
            $Vue->setEntete(new Vue_Structure_Entete());
            $Vue->setMenu(new Vue_Menu_Administration($_SESSION["idCategorie_utilisateur"]));
            $Vue->addToCorps(new Vue_Utilisateur_Changement_MDP("<label><b>Vous n'avez pas saisi le bon mot de passe</b></label>", "Gerer_monCompte"));
        }
        break;
    case  "SeDeconnecter":
        //L'utilisateur a cliqué sur "se déconnecter"
        session_destroy();
        unset($_SESSION);
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());
        break;
    default:
        //Cas par défaut: affichage du menu des actions.
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->setMenu(new Vue_Menu_Administration($_SESSION["idCategorie_utilisateur"]));
        $Vue->addToCorps(new Vue_Compte_Administration_Gerer());
        break;
}

$Vue->setBasDePage(new Vue_Structure_BasDePage());