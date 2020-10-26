<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     *  Page / Action : Accueil
     */
    public function index()
    {
        # Récupérer les 6 derniers articles de la BDD ordre décroissant
        #->getRepository(XXX::class): L'entitiée ou je souhaite récupérer les données
        #->findBy(): Récupère les données selon plusieurs critères
        #->findOneBy(): Récupère un enregistrement selon plusieurs critères
        #->findAll(): Récupère toutes les données de la table
        #->find(id): Récupère une donnée via son ID
        $posts = $this->getDoctrine()
            ->getRepository(Post::class) #entity pour récupérer les info
            ->findBy([], ['id'=>'DESC'], 6);
        #premier [] c'est la critère, ici on n'a pas de critère pour mettre dedans



        # Transmettre à la vue
        return $this->render('default/index.html.twig', ['posts'=>$posts]);
    }
    # toutes les fonctions disponibles grace a l'AbstractController. En POO, le mot clé 'extends' permet mettre en place de héritage de la class (ici AbstractController parent, enfant DefaultController), la meme principe, grace à l'heritage, $this accede tous les propriétés (public et protected) de class
    #si public, accéder les propriétés des parents et les enfants, si private ou protected, interdit de classe enfant accéder les propriétés de class des parents
    #toutes les classes abstract, pas vocation d'étre instantiable en raison d'etre hérité, juste pour aider ou soutenir d'autres class, une classe d'héritage qu'un classe abstract, pas plusieurs en meme temps! mais en serie d'heritage, A par B, B héritage de C
    #symfony créer une protected function render (...) pour rendu la vue twig


    /**
     *  Page / Action : Contact
     */

    public function contact()
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     *  Page / Action : Categorie
     *  Permet d'afficher les articles d'une catégorie
     * @Route("/{alias}", name="default_category", methods={"GET"})
     * name de la route, convention: controller_action pour facilier trouver dans user controller!
     */

    public function category($alias)
    {
        # Récupération de la catégorie via son alias dans l'URL
        $category =$this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['alias'=>$alias]);
        # trouver un category dans tous les Répository

        /**
         * Grâce à la relation entre Post et Category
         * (OneToMany), je suis en mesure de récupérer les articles de la catégorie
         */
        $posts = $category->getPosts();

        return $this->render('default/category.html.twig',['posts'=>$posts]);
    }

        /**
         *  Page / Action : Article
         *  Permet d'afficher un article du site
         * @Route("/{category}/{alias}_{id}.html", name="default_article", methods={"GET"})
         * method comme GET ou POST d'autoriser pour la route
         * si l'on veut récupérer les variables (propriétés), mets dans la fonction sans l'ordre mais avec le meme nom
         */
        public
        function post($id)
        {

            #3 parametre: categorie, alias(le titre d'article), _id.html
            # Récupérer l'article via son ID
            $post =$this->getDoctrine()
                ->getRepository(Post::class)
                ->find($id);

            # URL: https://localhost:8000/politique/couvre-feu-quand-la-situation-sanitaire-s-ameliorera-t-elle_14155614.html
            return $this->render('default/post.html.twig', ['post'=>$post]);
        }

    }


