<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Form\FilterFruitType;
use App\Entity\FavouriteFruit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;


class FruitController extends AbstractController
{
    private $em;
    private $serializer;


    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, ManagerRegistry $doctrine)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->doctrine = $doctrine;

    }

    /**
     * @Route("/api/fruits", name="fruits_api_index", methods={"GET"})
     */
    public function getFruits(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        // Get query parameters
        $name = $request->query->get('name');
        $family = $request->query->get('family');
        $orderBy = $request->query->get('order_by', 'fruit_id');
        $direction = $request->query->get('direction', 'asc');
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 10);
        
        // Get fruits from database
        $repository = $this->em->getRepository(Fruit::class);
        $queryBuilder = $repository->createQueryBuilder('f');
        // Apply filters
        if ($name) {
            $queryBuilder->where('f.name LIKE :name')
                ->setParameter('name', "%{$name}%");
        }
        if ($family) {
            $queryBuilder->where('f.family LIKE :family')
                ->setParameter('family', "%{$family}%");
        }

        // Apply ordering
        $queryBuilder->orderBy("f.{$orderBy}", $direction);

        // Paginate results
        $get_fruits = $paginator->paginate($queryBuilder->getQuery(), $page, $perPage);

        foreach ($get_fruits as $fruit) {
                    // Add the basic fruit data to the flattened array
        
                    $all_nutritions = $fruit->getNutritions();
                    $fruits[] = array(
                        'name' => $fruit->getName(),
                        'id' => $fruit->getFruitId(),
                        'family' => $fruit->getFamily(),
                        'order' => $fruit->getFruitOrder(),
                        'genus' => $fruit->getgenus(),
                        'calories' => $all_nutritions['calories'],
                        'fat' => $all_nutritions['fat'],
                        'sugar' => $all_nutritions['sugar'],
                        'carbohydrates' => $all_nutritions['carbohydrates'],
                        'protein' => $all_nutritions['protein']
                    );
        
        
                }
        // Get the total count of fruits
        $totalFruitsCount = $get_fruits->getTotalItemCount();

        // Serialize fruits data
        $data = $this->serializer->serialize($fruits, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        // dd($data);
        return new JsonResponse(['fruits' => json_decode($data), 'totalCount' => $totalFruitsCount], Response::HTTP_OK);
    }

    /**
     * @Route("/api/favorite-fruits", name="favourite_fruits_api_index", methods={"GET"})
     */
    public function getFavouriteFruits(): JsonResponse
    {
        // Get favourite fruits from database
        $repository = $this->em->getRepository(FavouriteFruit::class);
        $favouriteFruits = $repository->findAll();

        // Retrieve corresponding fruit data from fruits table based on favourite fruit ids
        $fruitRepository = $this->em->getRepository(Fruit::class);
        $fruits = [];

        foreach ($favouriteFruits as $favouriteFruit) {
            $fruit = $fruitRepository->findOneBy(['fruit_id' => $favouriteFruit->getFruitId()]);

            if ($fruit) {
                $all_nutritions = $fruit->getNutritions();
                    $fruits[] = array(
                        'name' => $fruit->getName(),
                        'id' => $fruit->getFruitId(),
                        'family' => $fruit->getFamily(),
                        'order' => $fruit->getFruitOrder(),
                        'genus' => $fruit->getgenus(),
                        'calories' => $all_nutritions['calories'],
                        'fat' => $all_nutritions['fat'],
                        'sugar' => $all_nutritions['sugar'],
                        'carbohydrates' => $all_nutritions['carbohydrates'],
                        'protein' => $all_nutritions['protein']
                    );
            }
        }
         // Serialize fruits data
         $data = $this->serializer->serialize($fruits, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        // dd($data);

        return new JsonResponse(['fruits' => $fruits], Response::HTTP_OK);
    }


        /**
     * @Route("/api/add-favorite-fruit", name="api_add-favorite_fruits", methods={"POST"})
     */
    public function addFavoriteFruit(Request $request): JsonResponse
    {
        // Get fruit ID from request payload
        $fruitId = $request->query->get('fruit_id');
        // Get fruit data from request payload
        // $fruitData = json_decode($request->getContent(), true);
        // dd($fruitId);

        // Get favourite fruits from database
        $repository = $this->em->getRepository(FavouriteFruit::class);
        $count = $repository->createQueryBuilder('ff')
            ->select('COUNT(ff.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $success = false;
        if($count < 10){

            // Create a new favorite fruit object and set data
            $favoriteFruit = new FavouriteFruit();
            $favoriteFruit->setFruitId($fruitId);

            // Save favorite fruit to database
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($favoriteFruit);
            $entityManager->flush();
            $success = true;
        }

        // Return success response
        return new JsonResponse(['success' => $success], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/favorite-fruits/{id}", name="api_delete_favorite_fruit", methods={"DELETE"})
     */
    public function deleteFavoriteFruit($id): JsonResponse
    {
        // Check if the favorite fruit exists
        $favoriteFruitRepository = $this->em->getRepository(FavouriteFruit::class);
        $favoriteFruit = $favoriteFruitRepository->findOneBy(['fruit_id' => $id]);
        if (!$favoriteFruit) {
            return new JsonResponse(['error' => 'Invalid favorite fruit ID'], Response::HTTP_BAD_REQUEST);
        }

        // Delete the favorite fruit object from the database
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($favoriteFruit);
        $entityManager->flush();

        // Return success response
        return new JsonResponse(['success' => true]);
    }

}