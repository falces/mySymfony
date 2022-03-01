<?php

namespace App\Controller;

use App\Entity\Books;
use App\Service\ParamCheck;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'books_index', methods: "GET")]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BooksController.php',
        ]);
    }


    #[Route('/books', name: 'books_list', methods: "GET")]
    public function getBooks(): Response
    {
        $booksRepository = $this->em->getRepository(Books::class);
        $books = $booksRepository->findAll();
        $data = [];
        foreach($books as $book){
            $data[] = [
                'id' => $book->getid(),
                'title' => $book->getTitle(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/books/{id}', name: 'book_detail', methods: "GET")]
    public function getBook($id): Response
    {
        if(!is_int($id)){
            return new JsonResponse(["message" => "Not found"],Response::HTTP_BAD_REQUEST, [], false);
        }

        $booksRepository = $this->em->getRepository(Books::class);
        $book = $booksRepository->findOneBy(["id" => $id]);

        if(is_null($book)){
            return new JsonResponse(["message" => "Not found"],Response::HTTP_BAD_REQUEST, [], false);
        }

        $data = [
            'id' => $book->getid(),
            'title' => $book->getTitle(),
        ];
        return $this->json($data);
    }

    #[Route('/books', name: 'books_add', methods: "POST")]
    public function postBooks(
        Request $request,
        EntityManagerInterface $entityManager,
        ParamCheck $paramCheck,
    ): Response {
        $parameters = json_decode($request->getContent(), true);

        if($paramCheck->checkParams(['title'], $parameters)){
            return new JsonResponse([], Response::HTTP_BAD_REQUEST, [], true);
        }

        $book = new Books();
        $book->setTitle($parameters["title"]);
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->getBook($book->getId());
    }
}
