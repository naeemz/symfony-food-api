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
        $allQueries = $request->query->all();
        //
        if($allQueries) { // if request query 
            // loop through queries to find out which query is for sorting
            foreach($allQueries as $key => $val ) {
                // convert to uppercase for comparison 
                $valUpper = strtoupper($val);
                // if query have asc or desc value then send it's 
                // key and value to sortedBy function 
                if( $valUpper == 'ASC' || $valUpper == 'DESC' ) {
                    $data = $this->categoryRepository->sortedBy($key, $valUpper);
                    // exit loop
                    break;
                }
            }
        } else {
            //
            $data = $this->categoryRepository->findAll();
        }
        // response
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
            //
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

        /**
     * @Route("/category/{id}/update", name="category_update", methods={"PUT", "PATCH", "POST"})
     */
    public function update(
                                int $id, 
                                Request $request, 
                                ValidatorInterface $validator
                            ) : JsonResponse 
    {
        $category = $this->categoryRepository->find($id);
        //
        if(!$category) {
            $data = ['errors' => "Category not found for ID = $id"];
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
        // prepare
        $input = $request->request->all();
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
            $request = $this->transformJsonBody($request);
            //
            $category->setName( $request->get('name') );
            $this->entityManager->persist($category);
            //
            $this->entityManager->flush();
            //
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
     * @Route("/category/{id}", name="category_destroy", methods={"DELETE"})
     */
    public function destroy(int $id): JsonResponse
    {
        // check record
        $category = $this->categoryRepository->find($id);
        //
        if(!$category) {
            $data = ['errors' => "Category not found for ID = $id"];
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
        
        try{
            // destroy
            $category->setDeletedAt(new \DateTime());
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            //
            return $this->response(['message'=>'Record deleted successfully.'], Response::HTTP_CREATED); 
        } catch (\Exception $e){
            $data = [
                'errors' => $e->getMessage(),
            ];
            //
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
    }
}
