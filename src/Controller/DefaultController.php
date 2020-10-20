<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     *  Page / Action : Accueil
     */
    public function index()
    {
        return new Response('<h1>Page Accueil</h1>');
    }

    /**
     *  Page / Action : Contact
     */

    public function contact()
    {
        return new Response('<h1>Page Contact</h1>');
    }

    /**
     *  Page / Action : Categorie
     *  Permet d'afficher les articles d'une catégorie
     * @Route("/{alias}", name="default_category", methods={"GET"})
     * name de la route, convention: controller_action pour facilier trouver dans user controller!
     */

    public function category($alias)
    {

        return new Response("<h1>Page $alias</h1>");

    }
    /**
     *  Page / Action : Article
     *  Permet d'afficher un article du site
     * @Route("/{category}/{alias}_{id}.html", name="default_article", methods={"GET"})
     * method comme GET ou POST d'autoriser pour la route
     * si l'on veut récupérer les variables (propriétés), mets dans la fonction sans l'ordre mais avec le meme nom
     */
    public function article($id, $category, $alias)
    {
        # URL: https://localhost:8000/politique/couvre-feu-quand-la-situation-sanitaire-s-ameliorera-t-elle_14155614.html
        #3 parametre: categorie, alias(le titre d'article), _id.html
        return new Response("<h1>Page Article</h1>");
    }

}


