<?php

namespace App\Controller;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AuthorFormType;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends AbstractController
{
    
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/showauthor/{name}',name:'show_author')]
    public function showAuthor($name): Response
    {
        return $this->render('Author/show.html.twig', [
            'name' => $name
        ]);
    }
    #[Route('/list',name:'list')]
    public function list(): Response{
     
       $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>'taha.hussein@gmail.com', 'nb_books' => 300),);
            return $this->render('Author/list.html.twig',[
                'authors'=>$authors
            ]);
    }
    #[Route('/authorDetails/{id}',name:'detail')]
    public function auhtorDetails($id): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>'taha.hussein@gmail.com', 'nb_books' => 300),);
       $authorId=$id-1;
            $author=$authors[$authorId];
        return $this->render('Author/detailAuthor.html.twig', [
            'author' => $author
        ]);
    }
    #[Route('/author_show',name: 'show')]
    public function showAuthors2(AuthorRepository $repo) : Response {
        $list=$repo->findAll();
        return $this->render('Author/listAuthors.html.twig', [
            'list' => $list
        ]);
    }
    #[Route ("/authorAdd",name:"author.add")]
    public function addAuthor(EntityManagerInterface $entityManager){
        $Author= new Author();
        $Author ->setuserName("John Doe");
        $Author ->setEmail("Doe@gmail.com");
        $Author ->setNbBooks(0);
        $entityManager->persist($Author);
        $entityManager->flush();
        return new Response('saved new author '.$Author->getUsername());
    }
    #[Route("/author/add",name:"formAdd")]
    public function formAdd(Request $request,EntityManagerInterface $entityManager){
        $Author= new Author();
        $Author->setNbBooks(0);
        $form=$this->createForm(AuthorFormType::class, $Author);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $Author= $form->getData();
            $entityManager->persist($Author);
            $entityManager->flush();
            return $this->redirectToRoute("show");
        }
        return $this->render("author/authorform.html.twig",[
            "form"=>$form->createView()
        ]);
    }
    #[Route("author/edit/{id}",name:"edit_author")]
    public function editAuthor(Request $request,AuthorRepository $repo, EntityManagerInterface $entityManager, int $id){  
       $Author=$repo->find($id);
       $form=$this->createForm(AuthorFormType::class, $Author);
       $form->handleRequest($request);
       if($form->isSubmitted()&& $form->isValid()){
        $Author= $form->getData();
        $entityManager->persist($Author);
        $entityManager->flush();
        return $this->redirectToRoute("show");
    }
    return $this->render("author/authorEdit.html.twig",[
        "form"=>$form->createView()
    ]);
    }
    #[Route("/delete/{id}",name:"delete_author")]
    public function deleteAuthor(AuthorRepository $repo, EntityManagerInterface $entityManager, int $id){
        $Author=$repo->find($id);
        $entityManager->remove($Author);
        $entityManager->flush();
        return $this->redirectToRoute("show");
    }
     #[Route("/author/email",name:"emailAsc")]
     public function listEmailAsc(AuthorRepository $repo){
        $authors= $repo->showEmailAsc();
        return $this->render("author/listEmailAsc.html.twig",["list"=>$authors]);
     }

}
