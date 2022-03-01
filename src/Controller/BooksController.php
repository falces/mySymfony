<?php

namespace App\Controller;

use App\Entity\Books;
use App\Service\ParamCheck;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends BaseController
{
    public const KEY_TITLE = "title";
    public const KEY_ID    = "id";

    private const MESSAGE_BOOK_NOT_FOUND = "Book not found";

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'books_index', methods: "GET")]
    public function index(): Response
    {
        $number = random_int(0, 100);
        return new Response('<html lang="es"><body>Número aleatorio: '.$number.'</body></html>');
    }


    #[Route('/books', name: 'books_list', methods: "GET")]
    public function getBooks(): Response
    {
        $booksRepository = $this->em->getRepository(Books::class);
        $books = $booksRepository->findAll();
        $data = [];
        foreach($books as $book){
            $data[] = [
                self::KEY_ID    => $book->getid(),
                self::KEY_TITLE => $book->getTitle(),
            ];
        }

        return $this->json($this->getResultData(true, '', $data));
    }

    #[Route('/books/{id}', name: 'book_detail', methods: "GET")]
    public function getBook($id): Response
    {
        if(!is_int($id)){
            return new JsonResponse(
                $this->getResultData(false, self::MESSAGE_BOOK_NOT_FOUND, []),
                Response::HTTP_BAD_REQUEST,
                [],
                false);
        }

        $booksRepository = $this->em->getRepository(Books::class);
        $book = $booksRepository->findOneBy([Books::FIELD_ID => $id]);

        if(is_null($book)){
            return new JsonResponse(
                $this->getResultData(false, self::MESSAGE_BOOK_NOT_FOUND, []),
                Response::HTTP_BAD_REQUEST,
                [],
                false);
        }

        $data = [
            self::KEY_ID    => $book->getid(),
            self::KEY_TITLE => $book->getTitle(),
        ];

        return $this->json($this->getResultData(true, '', $data));
    }

    #[Route('/books', name: 'books_add', methods: "POST")]
    public function postBooks(
        Request $request,
        EntityManagerInterface $entityManager,
        ParamCheck $paramCheck,
    ): Response {
        $parameters = json_decode($request->getContent(), true);

        if (!$paramCheck->checkParams([self::KEY_TITLE], $parameters)) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST, [], true);
        }

        $book = new Books();
        $book->setTitle($parameters[self::KEY_TITLE]);
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->getBook($book->getId());
    }
}
