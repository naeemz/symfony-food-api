<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\FoodRepository;
use App\Repository\CategoryRepository;
use App\Entity\Food;
use App\Traits\HelperTrait;
use App\Request\FoodRequest;
use App\Services\FileUploader;

class FoodController extends AbstractController
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
     * @Route("/food", name="food_list", methods={"GET"})
     */
    public function index(Request $request)
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
                    $data = $this->foodRepository->sortedBy($key, $valUpper);
                    // exit loop
                    break;
                }
            }
        } else {
            //
            $data = $this->foodRepository->findAll();
        }
        // response
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws Exception
     * @Route("/food", name="food_store", methods={"POST"})
     */
    public function store(
                            Request $request, 
                            ValidatorInterface $validator,
                            FileUploader $fileUploader
                         ) : JsonResponse
    {
        // prepare
        $input = $request->request->all();
        if( $request->files->get('image') ) $input = $input + ['image'=>$request->files->get('image')];
        $constraint = FoodRequest::rules();
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
            // Check image
            $imageFileName = null;
            if( $request->files->get('image') ) {
                $image = $request->files->get('image');
                //
                if ($image) {
                    $imageFileName = $fileUploader->getTargetUrl().$fileUploader->upload($image);
                }
            }
            $request = $this->transformJsonBody($request);
            //
            $food = new Food();
            $food->setName( $request->get('name') );
            $food->setDescription( $request->get('description') );
            $food->setPrice( $request->get('price') );
            $food->setServingPerPerson( $request->get('serving_per_person') );
            if($imageFileName!=null)
                $food->setImageUrl( $imageFileName );
            //
            $this->entityManager->persist($food);
            //
            if( $request->request->has('categories') ) {
                //
                $categoryIds = $request->get('categories');
                $categories = $this->categoryRepository->findBy( array('id' => $categoryIds) );
                
                foreach($categories as $category) {
                    $food->addToCategory($category);
                    $this->entityManager->persist($food);
                }
            }
            //
            $this->entityManager->flush();
            //
            return $this->response($food, Response::HTTP_CREATED);            
        } catch (\Exception $e){
            $data = [
                'errors' => $e->getMessage(),
            ];
            //
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/food/{id}", name="food_show", methods={"GET"})
     */
    public function getFood(int $id): JsonResponse
    {
        $food = $this->foodRepository->find($id);
        //
        if (!$food) {
            $data = ['errors' => "Food not found for ID = $id"];
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
        // prepare response data
        return $this->response($food, Response::HTTP_OK);
    }

    /**
     * @Route("/food/{id}/update", name="food_update", methods={"PUT", "PATCH", "POST"})
     */
    public function update(
                                int $id, 
                                Request $request, 
                                ValidatorInterface $validator,
                                FileUploader $fileUploader
                            ) : JsonResponse 
    {
        $food = $this->foodRepository->find($id);
        //
        if(!$food) {
            $data = ['errors' => "Food not found for ID = $id"];
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
        // prepare
        $input = $request->request->all();
        if( $request->files->get('image') ) $input = $input + ['image'=>$request->files->get('image')];
        $constraint = FoodRequest::rules();
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
            // Check image
            $imageFileName = null;
            if( $request->files->get('image') ) {
                $image = $request->files->get('image');
                //
                if ($image) {
                    $imageFileName = $fileUploader->getTargetUrl().$fileUploader->upload($image);
                }
            }
            $request = $this->transformJsonBody($request);
            //
            $food->setName( $request->get('name') );
            $food->setDescription( $request->get('description') );
            $food->setPrice( $request->get('price') );
            $food->setServingPerPerson( $request->get('serving_per_person') );
            if($imageFileName!=null)
                $food->setImageUrl( $imageFileName );
            //
            $this->entityManager->persist($food);
            //
            if( $request->request->has('categories') ) {
                //
                $categoryIds = $request->get('categories');
                $categories = $this->categoryRepository->findBy( array('id' => $categoryIds) );
                // if categories found
                if( $categories ) {
                    // remove all previous categories
                    $food->detachAllCategories();
                    //
                    foreach($categories as $category) {
                        $food->addToCategory($category);
                        $this->entityManager->persist($food);
                    }
                }
            }
            //
            $this->entityManager->flush();
            //
            return $this->response($food, Response::HTTP_CREATED);            
        } catch (\Exception $e){
            $data = [
                'errors' => $e->getMessage(),
            ];
            //
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/food/{id}", name="food_destroy", methods={"DELETE"})
     */
    public function destroy(int $id): JsonResponse
    {
        // check record
        $food = $this->foodRepository->find($id);
        //
        if(!$food) {
            $data = ['errors' => "Food not found for ID = $id"];
            return $this->response($data, Response::HTTP_BAD_REQUEST);
        }
        
        try{
            // destroy
            $food->setDeletedAt(new \DateTime());
            $this->entityManager->persist($food);
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
