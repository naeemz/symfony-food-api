<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

trait HelperTrait
{
    
    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = Response::HTTP_OK, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Transform Json Body
     * 
     * @param Request $request
     * @return $request 
     */
    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}