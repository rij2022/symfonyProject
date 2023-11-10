<?php

namespace App\Repository;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function bookBefore2023(){
        $date = new \DateTime('2023-01-01');
        return  $this->createQueryBuilder('b')
        ->join('b.author', 'a')
    ->where('b.publishedDate < :date')
    ->andWhere('a.nb_books > 35')
    ->setParameter('date', $date)
    ->getQuery()
    ->getResult();
    }
    public function updateCategory(){
      return $this->createQueryBuilder('b')
       ->join('b.author', 'a') 
        ->where('a.username = :found')
        ->update(Book::class,'b')
        ->set('b.category ', ':newdata')
       
     
       ->setParameter('newdata','Romance')
       ->setParameter('found', 'abed')
       ->getQuery()
       ->execute();
    }
   /* public function searchBook($ref){
        return  $this->createQueryBuilder('b')
        ->where("b.ref = :ref")
        ->setParameter(':ref', $ref)
        ->getQuery()->getResult();
    }*/
   /* public function showBooksByAuthor($id) {
        return $this->createQueryBuilder('b')
        ->join('b.title','a')
        ->addSelect('a')
        ->where('a.id = :id')
        ->setParameter(':id', $id)
        ->getQuery()->getResult();

    }*/

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
