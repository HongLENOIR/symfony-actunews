<?php


namespace App\Controller;


use App\Entity\Post;
use App\Entity\Category;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class PostController extends AbstractController
{
    /**
     * Formulaire permettant de créer un article
     * @Route("/article/creer-un-article", name="post_create", methods={"GET|POST"})
     * @IsGranted("ROLE_JOURNALIST")
     */
    public function createPost(Request $request, SluggerInterface $slugger )
    {
        #1a. Création d'un nouveau Post
        $post = new Post();

        #1b. Attribution d'un user
        # FIXME Temporaire
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find(1);
        $post->setUser($user);

        #1c. Ajouter de la date de rédaction
        $post->setCreatedAt(new \DateTime());

        #2. Création d'un formulaire avec $post
        $form = $this->createFormBuilder($post)

            #2a. Titre de l'article
            ->add('title', TextType::class)

            #2b. Categorie de l'article (Liste déroulante)
            ->add('category', EntityType::class,[
                    // looks for choices from this entity
                    'class' => Category::class,
                    // uses the User.username property as the visible option string
                    'choice_label' => 'name',
                    // used to render a select box, check boxes or radios
                    // 'multiple' => true,
                    // 'expanded' => true,
                ])
            #2c. Contenu de l'article
            ->add('content',TextareaType::class)

            #2d. Upload Image de l'article
            ->add('featuredImage',FileType::class)

            #2e. Boutton Submit de l'article
            ->add('submit',SubmitType::class)
            #2f. #Permet de récupérer le formulaire généré
        ->getForm();

        #3. Demande à Symfony de récupérer les infos dans la request.
        $form->handleRequest($request);

        #4. Véfifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()){
//            dump($request);
//            dd($post);    pour vérifier les infos

            #4a. TODO Gestion Upload de l'image
            /** @var UploadedFile $featuredImage */
            $featuredImage = $form->get('featuredImage')->getData(); // pour récupérer le champs de l'image

            // this condition is needed because the 'featuredImage' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($featuredImage) {
                $originalFilename = pathinfo($featuredImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename); // slugger pour notre projet comme alias
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $featuredImage->guessExtension();
                    # cette ligne pour que nom de fichier est sécurisé
                // Move the file to the directory where brochures are stored
                try {
                    $featuredImage->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'FeateuredImage' property to store the PDF file name
                // instead of its contents
                #ici on stock dans la BDD le nom de l'image
                $post->setFeaturedImage($newFilename);
            }

            #4b. Génération de l'alias
            $post->setAlias(
                $slugger->slug(
                    $post->getTitle()
                )
            );

            #4c. Sauvegarde dans la BDD
            /**
             * Qu'est ce que le Entity Manager(em)?
             * C'est une classses qui sait comment sauvegarder d'autres classes
             */

            $em =$this->getDoctrine()->getManager(); # Récupération du EM
            $em->persist($post); #Demande pour sauvegarder en BDD $post
            $em->flush(); # On execute la demande

            #4d. Notification / Confirmation
            $this->addFlash('notice','Votre article est en ligne!');

            #4e. Redirection
            return $this->redirectToRoute('default_article', [
             'category'=>$post->getCategory()->getAlias(),
                'alias'=>$post->getAlias(),
                'id'=>$post->getId()
                ]);
        }

        #5. Transmission du formulaire à la vue
        return $this->render('post/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

}