<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BookType;
use App\Form\SearchBookType;
use App\Repository\AuthorRepository;


use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/book_show/published',name: 'showPublished')]
    public function showpublished(BookRepository $repo) : Response {
       /* $list=$repo->findBy([
            'published'=> 'true']
        );*/
        $list = $repo->createQueryBuilder('b')
    ->select('b')
    ->Join('b.author', 'a')
    
    ->where('b.published = :published')
    ->setParameter('published', true)
    ->orderBy('a.username', 'ASC') 
    ->getQuery()
    ->getResult();

        $numPublished=count($list);
        $numUnpublished=count($repo->findBy(['published'=>false,]));
        return $this->render('book/list.html.twig', [
            'list' => $list, 'published'=>$numPublished,'unpublished'=>$numUnpublished
        ]);
    }
    #[Route("/book/add",name:"formBook")]
    public function formAdd(Request $request,EntityManagerInterface $entityManager){
        $Book= new Book();
        $form=$this->createForm(BookType::class, $Book);
        $form->add("save",SubmitType ::class);
        $title = 'Add Book';
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $author = $Book->getAuthor();
            if($author instanceof Author){
               $author->setNbBooks($author->getNbBooks()+1);
            }
            $Book->setPublished(true);
            $Book= $form->getData();
            $entityManager->persist($Book);
            $entityManager->flush();
            return $this->redirectToRoute("showPublished");
        }
        return $this->render("book/bookform.html.twig",[
            "form"=>$form->createView(),
            'title'=> $title
        ]);
    }
    #[Route("book/delete/{ref}",name:"delete_book")]
    public function deleteBook(BookRepository $repo, EntityManagerInterface $entityManager, int $ref){
        $Book=$repo->find($ref);

        $entityManager->remove($Book);
        $entityManager->flush();
        return $this->redirectToRoute("showPublished");
    }
    #[Route("/edit/{ref}",name:"edit_book")]
    public function editAuthor(Request $request,BookRepository $repo, EntityManagerInterface $entityManager, int $ref){  
        $Book= $repo->find($ref);
        $form=$this->createForm(BookType::class, $Book);
        $form->add('published');
       $form->add("update",SubmitType::class);
       $title = 'Update Book';
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
           
        
            $Book= $form->getData();
            $entityManager->persist($Book);
            $entityManager->flush();
            return $this->redirectToRoute("showPublished");
        }
        return $this->render("book/bookform.html.twig",[
            "form"=>$form->createView(),
            'title'=> $title
        ]);
    }
    #[Route('/show/book/{ref}',name: 'show_book')]
    public function showBook(BookRepository $repo,int $ref) : Response {
        $book=$repo->find($ref);
       
        
        return $this->render('book/detail.html.twig', [
            'book' => $book,
        ]);
    }
   #[Route('/deleteAuthor',name:'delete_author')]
   public function deleteAuthor(EntityManagerInterface $em ){
    $authorRepo=$em->getRepository(Author::class);
    $deleteAuthor=$authorRepo->findBy(['nb_books' => 0]);
    foreach ($deleteAuthor as $author) {
        $em->remove($author);
    }

    $em->flush();
    $this->addFlash('success', 'Authors with zero books removed successfully.');
    return $this->redirectToRoute('showPublished');


   }
   #[Route('/bookSearch',name:'searchBook')]
 function searchBook(BookRepository $repo,Request $request){
    $books=$repo->findAll();
    $form=$this->createForm(SearchBookType::class);
    $form->handleRequest($request);
    if($form->isSubmitted()){
$ref=$form->get('ref')->getData();
$books=$repo->findBy(['ref'=>$ref]);
    }
    return $this->render('book/listSearch.html.twig',['list'=>$books,'form'=>$form->createView()]);

 }
 #[Route('/book/before2023',name:'before2023')]
 public function before2023(BookRepository $repo){
    $date = new \DateTime('2023-01-01');
    $list = $repo->bookBefore2023();
    return $this->render('book/listBook.html.twig', ['list' => $list]);

 }
 #[Route('/updateCategory',name:'updateW')]
 public function updateWilliam(BookRepository $repo,AuthorRepository $repoA,EntityManagerInterface $em){
       $author = $repoA->findby(['username' => "abed"]);
       $books = $repo->findBy(['author' => $author]);
          $categ = 'ROMANCE';
          foreach ($books as $book) {
           $book->setCategory($categ);
               } 
         $em->flush();
          return  $this->render('book/listBook.html.twig', ['list' => $books]);
 }
   /*#[Route('/author_books/{id}',name:'author_books')]
   public function showBooksByAuthor($id,BookRepository $repo){
    $list=$repo->showBooksByAuthor($id);
   
  
    return $this->render('book/list.html.twig', [
        'list' => $list, 
    ]);
   }*/
}
