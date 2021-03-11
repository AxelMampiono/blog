<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     *@Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig',[
            'title'=>"Bienvenue sur le blog Symphony",
            'age'=>25
        ]);
    }
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        /*
            Pour selectionner des données dans une table SQL, nous devons absolument avoir accès à la classe Repository de l'entité correspondante 
            Un Repository est une classe permettant uniquement d'executer des requetes de selection en BDD (SELECT)
            Nous devons donc accéder au repository de l'netité Article au sein de notre controller  

            On appel l'ORM doctrine (getDoctrine()), puis on importe le repositoritory de la classe Article grace à la méthode getRepository()
            $repo est un objet issu de la classe ArticleRepository
            cet objet contient des méthodes permettant d'executer des requetes de selections
            findAll() : méthode issue de la classe ArticleRepository permettant de selectionner l'ensemble de la table SQL 'Article'
        */

        //$repo=$this->getDoctrine()->getRepository(Article::class);
        dump($repo);
        $articles=$repo->findAll();//=SELECT
        dump($articles);
        return $this->render('blog/index.html.twig', [
            'title' => 'Listes des articles',
            'article'=>$articles// on envoie sur le template, les articles selectionnés en BDD afin de pouvoir les afficher dynamiquement sur le template à l'aide du langage Twig

        ]);
    }
    /**
     * detaild'un article
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $articleCreate = null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$articleCreate)
        {
            $articleCreate = new Article;
        }
        // la classe Request de Symfony permet de véhiculer les données des superglobales PHP ($_POST, $_FILES, $_COOKIE, $_SESSION)
        // $request est un objet issu de la classe Request injecté en dependance de la méthode create()
        // $request permet de stocker les données des superglobales, la propriété $request->request permet de stocker les données véhiculées par un formulaire ($_POST), ici on compte si il y a données qui ont été saisie dans la formulaire

        /*if($request->request->count()>0)
        {
            // Pour insérer dans la table Article, nous devons instancier un objet issu de la classe entité Article, qui est lié à la table SQL Article

            $articleCreate = new Article;
            // On rensigne tout les setteurs de l'objet avec en arguments les données du formulaire ($_POST)

            $articleCreate->setTitle($request->request->get('title'))
                          ->setContent($request->request->get('content'))
                          ->setImage($request->request->get('image'))
                          ->setCreateAt(new \DateTime);
            // On fait appel au manager afin de pouvoir executer une insertion en BDD

            $manager->persist($articleCreate);// on prépare et garde en mémoire l'insertion

            $manager->flush();// on execute l'insertion

            // Après l'insertion, on redirige l'internaute vers le détail de l'article qui vient d'être inséré en BDD
            // Cela correspond à la route 'blog_show', mais c'est une route paramétrée qui attend un ID dans l'URL
            // En 2ème argument de redirectToRoute, nous transmettons l'ID de l'article qui vient d'être inséré en BDD

            return $this->redirectToRoute('blog_show', [
                'id'=>$articleCreate->getId()
            ]);
        }*/
        //
        //$articleCreate->setTitle("Titer")
        //            ->setContent("Contenu");
        /*$form=$this->createFormBuilder($articleCreate)
                   ->add('title')
                   ->add('content')
                   ->add('image')
                   ->getForm();*/
        $form=$this->createForm(ArticleFormType::class, $articleCreate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if(!$articleCreate->getId())
            {
                $articleCreate->setCreatedAt(new \DateTime);
            }

            

            $manager->persist($articleCreate);
            
            $manager->flush();
            return $this->redirectToRoute('blog_show',[
                'id'=>$articleCreate->getId()
            ]);

        }
        return $this->render('blog/create.html.twig', [
            'formArticle'=>$form->createView(),
            'editMode'=>$articleCreate->getId()
        ]);
    }
    /**
     * detaild'un article
     * @Route("/blog/{{id}}", name="blog_show")
     */
    public function show(Article $article): Response
    {
        //$repoArticle=$this->getDoctrine()->getRepository(Article::class);
        //$article=$repoArticle->find($id);
        return $this->render('blog/show.html.twig',[
            'articligeTwig'=>$article
        ]
    );
        
    }
/*
        En fonction de la route paramétrée {id} et de l'injection de dépendance $article, Symfony voit que l'on besoin d'un article de la BDD par rapport à l'ID transmit dans l'URL, il est donc capable de recupérer l'ID et de selectionner en BDD l'article correspondant et de l'envoyer directement en argument de la méthode show(Article $article)
        Tout ça grace à des ParamConverter qui appel des convertisseurs pour convertir les paramètres de l'objet. Ces objets sont stockés en tant qu'attribut de requete et peuvent donc être injectés an tant qu'argument de méthode de controller
    */


    
}
