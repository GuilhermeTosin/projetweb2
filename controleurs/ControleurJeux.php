<?php
/**
 * @file      ControleurJeux.php
 * @author    Chunliang He, Guilherme Tosin, Jansy López, Marcelo Guzmán
 * @version   1.0.0
 * @date      Septembre 2018
 * @brief     Définit la classe pour le controleur jeux
 * @details   Cette classe définit les différentes activités concernant aux jeux
 */

class ControleurJeux extends BaseControleur
    /**
     * @brief   Méthode qui sera appelée par les controleurs
     * @details Méthode abstraite pour traiter les "cases" des contrôleurs
     * @param   [array] $params La chaîne de requête URL ("query string") captée par le Routeur.php
     * @return  L'acces aux vues,aux données et aux différents messages pour ce contrôleur.
     */
{
    public function index(array $params)
    {
        $modeleJeux = $this->lireDAO("Jeux");
        $modeleImages = $this->lireDAO("Images");
        $modeleMembres = $this->lireDAO("Membres");
        $modelePlateformes = $this->lireDAO("Plateformes");

        $donnees['jeux'] = $modeleJeu->lireJeuParId($params["JeuxId"]);

        if (isset($params["action"]))
        {
            switch($params["action"])
            {
                case "afficherJeu" :
                    $donnees['jeu'] = $modeleJeux->lireJeuParId($params["JeuxId"]);
                    $donnees['images'][] = $modeleImages->lireImagesParJeuxId($params["JeuxId"]);
                    $donnees['membre'] = $modeleMembres->obtenirParId($donnees['jeux']->getMembreId());
                    $donnees['plateforme'] = $modelePlateformes->lirePlateformeParId($donnees['jeux']->getPlateformeId());
                    // $donnees['plateformes'] = $modelePlateformes->lireToutesPlateformes();
                    $this->afficherVues("jeux", $donnees);

                case "afficherJeux" :
                    if(isset($params["JeuxId"]))
                    {
                        $this->afficherVues("accueil", $donnees);
                    }
                    break;

                case "derniers" :
                    $donnees['derniers'] = $modeleJeu->lireDerniersJeux();
                    $donnees['derniers'] = $modeleJeu->lireDerniersJeux();
                    $this->afficherVues("accueil", $donnees);
                    break;

                default :
                    $this->afficherVues("accueil", $donnees);
                    break;
            }
        }
        else
        {
            $this->afficherVues("accueil", $donnees);
        }

    }

    // public function afficherJeu(array $params)
}