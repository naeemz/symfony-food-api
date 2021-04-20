<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FoodRepository;
use App\Repository\CategoryRepository;
use App\Traits\HelperTrait;
use App\Entity\Category;
use App\Request\CategoryRequest;

class CategoryController extends AbstractController
{
    use HelperTrait;

    private $foodRepository;
    private $categoryRepository;
    private $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
                                    FoodRepository $foodRepository,
                                    CategoryRepository $categoryRepository,
                                    EntityManagerInterface $entityManager,
                                    ValidatorInterface $validator
                                ) 
    {
        $this->foodRepository       = $foodRepository;
        $this->categoryRepository   = $categoryRepository;
        $this->entityManager        = $entityManager;
        $this->validator            = $validator;
    }

    /**
     * @Route("/category", name="category", methods={"GET"})
     */
    public function index()
    {
        $data = $this->categoryRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws Exception
     * @Route("/category", name="category_store", methods={"POST"})
     */
    public function store(
                            Request $request, 
                            ValidatorInterface $validator
                         ) : JsonResponse
    {
        // prepare
        $input = $request->request->all();
        if( $request->files->get('image') ) $input = $input + ['image'=>$request->files->get('image')];
        $constraint = CategoryRequest::rules();
        //
        $errors = $this->validator->validate($input, $constraint);
        //
        if ($errors->count() > 0) {
            foreach($errors as $error) {
                $msgs[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->response(
                $msgs,
                Response::HTTP_BAD_REQUEST
            );
        }
        //        
        try {
            //
            $request = $this->transformJsonBody($request);
            //
            $category = new Category();
            $category->setName( $request->get('name') );
            //
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            // else data is valid
            return $this->response($category, Response::HTTP_CREATED);            
        } catch (\Exception $e){
            $data = [
                'errors' => $e->getMessage(),
            ];
            //
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
    }

        /**
     * @Route("/category/{id}", name="category_show")
     */
    public function getCategory(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);
        //
        if (!$category) {
            $data = ['errors' => "Category not found for ID = $id"];
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
        // prepare response data
        return $this->response($category, Response::HTTP_OK);
    }

}
