<?php
/**
 * @file      ControleurJeux.php
 * @author    Guilherme Tosin, Marcelo Guzmán
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
        $modeleCategoriesJeux = $this->lireDAO("CategoriesJeux");
        $modeleCommentaireJeux = $this->lireDAO("CommentaireJeux");
        $modeleCategories = $this->lireDAO("Categories");


        
        $donnees["erreur"] = "";

        if (isset($params["action"]))
        {
            switch($params["action"])
            {
                case "afficherJeu" :
                    if (isset($params["JeuxId"]))
                    {
                        $donnees['jeu'] = $modeleJeux->lireJeuParId($params["JeuxId"]);
                        $donnees['images'] = $modeleImages->lireImagesParJeuxId($params["JeuxId"]);
                        $donnees['membre'] = $modeleMembres->obtenirParId($donnees['jeu']->getMembreId());
                        $donnees['plateforme'] = $modelePlateformes->lirePlateformeParId($donnees['jeu']->getPlateformeId());
                        $donnees['categoriesJeu'] = $modeleCategoriesJeux->lireCategoriesParJeuxId($params["JeuxId"]);
                        $donnees['commentaires'] = $modeleCommentaireJeux->toutObtenirParIdJeuxId($params["JeuxId"]);
                        // $donnees['commentaires'] = $modeleCommentaireJeux->lireCommentaireParJeuxId($params["JeuxId"]);
                        foreach ($donnees['commentaires'] as $commentaire){
                            $donnees['commentaires']['membres'][] = $modeleMembres->obtenirParId($commentaire->getMembreId());
                        }
                    }
                    else
                    {
                        $donnees["erreur"] = "Ce jeu n'existe pas.";
                    }
                    $this->afficherVues("jeux", $donnees);
                    break;

                case "afficherJeux" :
                    if(isset($params["JeuxId"]))
                    {
                        $this->afficherVues("accueil", $donnees);
                    }
                    break;

                case "derniers" :

                    $donnees['derniers'] = $modeleJeux->lireDerniersJeux();
                    $donnees['images'] = $modeleImages->lireDerniersImages();


                    $this->afficherVues("accueil", $donnees);
                    
                    break;
                
                case "formAjoutJeux":

                    $donnees['plateforme'] = $modelePlateformes->lireToutesPlateformes();
                    $donnees['jeu'] = $modeleJeux->lireTousLesJeux();
                    $donnees['categories'] = $modeleCategories->lireToutesCategories();

                    $this->afficherVues("ajoutJeux", $donnees);
                    
                    break;

                case "enregistrerJeux":

                    $this->afficherVues("accueil", $donnees);

                case "rechercherJeux":

                    $donnees['filter'] = $this->filtrerJeux($params);
                    $donnees['jeux'] = $modeleJeux->lireTousLesJeux();
                    $donnees['images'] = $modeleImages->toutesImages();
                    $donnees['derniers'] = $modeleJeux->lireDerniersJeux();



                    $this->afficherVues("rechercher", $donnees);

                default :
                    $this->afficherVues("accueil", $donnees);
                    break;
                            }
                        }
                        else
                        {
                            $donnees['derniers'] = $modeleJeux->lireDerniersJeux();
                            $donnees['images'] = $modeleImages->lireDerniersImages();
                            $this->afficherVues("accueil", $donnees);
                        }

                    }

            public function filtrerJeux(array $params) {



                $modeleJeux = $this->lireDAO("Jeux");

                $modeleImages = $this->lireDAO("Images");
                $donnees['images'] = $modeleImages->toutesImages();


                $_POST['action'] = "index.php?Jeux&action=rechercherJeux";

                $filtre = " WHERE jeux_actif = true AND jeux_valide = true";

                if (isset($params["categorie"]) && ($_POST['categorie'] !== '')) {
                    $filtre = " JOIN categorie_jeux cj ON cj.jeux_id = jeux.jeux_id JOIN categorie c ON c.categorie_id = cj.categorie_id WHERE c.categorie_id = '" . $params["categorie"] . "'";
                    $_POST['categorie'] = $params["categorie"];
                }
                else if (!isset($_POST['categorie'])) {
                    $_POST['categorie'] = null;
                }

                if (isset($params["plateforme"]) && ($_POST['plateforme'] !== '')) {
                    $filtre .= ($filtre == "" ? "" : " AND ") . "plateforme_id = '" . $params["plateforme"] . "'";
                    $_POST['plateforme'] = $params["plateforme"];
                }
                else if (!isset($_POST['categorie'])) {
                    $_POST['plateforme'] = null;
                }

                if (isset($params["negotiation"]) && ($_POST['negotiation'] !== '')) {
                    $filtre .= ($filtre == "" ? "" : " AND ") . "location = '" . $params["negotiation"] . "'";
                    $_POST['negotiation'] = $params["negotiation"];
                }
                else {
                    $_POST['negotiation'] = null;
                }
                return $modeleJeux->filtreJeux($filtre);
            }
}