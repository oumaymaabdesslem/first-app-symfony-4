<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {

       $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit",name="blog_edit")
     */
     public function create(Article $article= null,Request $request ,EntityManagerInterface $manager){

         if(!$article) {
             $article = new Article();
         }
         $form = $this->createForm(ArticleType::class,$article);
         $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()){
               if(!$article->getId()) {
                   $article->setCreatedAt(new \DateTime());
               }
               $manager->persist($article);
               $manager->flush();

               return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);

           }



        return $this->render('blog/create.html.twig',[
            'formArticle'=>$form->createView(),
            'editArticle'=>$article->getId() !==null

        ]);
     }


    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public  function  show(Article $article,EntityManagerInterface $manager,Request $request){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form ->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                     ->setArticle($article);


            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id'=>$article->getId()]);
        }
        return $this->render('blog/show.html.twig',[
            'article'=>$article,
            'formComment'=>$form->createView()
        ]);
    }
}
