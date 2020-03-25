<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\ApiService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CharactersController extends AbstractController
{

    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function listCharactersAction(Request $request)
    {
        $page = array();
        $next = "";
        $previous = "";
        $charList = array();

        if($request->get('page'))
            $page['page'] = $request->get('page');

        try {
                $response = $this->apiService->call(
                    'GET',
                    'people/',
                    $page
                );

        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e->getCode());
        }

        if( ($response->code == 200) && ($response->data['count'] > 0) ){
            if ($response->data['next']){
                parse_str( parse_url( $response->data['next'], PHP_URL_QUERY), $next );
                $next = $next['page'];
            }
            if ($response->data['previous']){
                parse_str( parse_url( $response->data['previous'], PHP_URL_QUERY), $previous );
                $previous = $previous['page'];
            }

            foreach ($response->data['results'] as $charInfo){
                $charId = trim(parse_url($charInfo['url'], PHP_URL_PATH), '/');
                $charId = explode('/',$charId);
                $charId = array_pop($charId);
                $charList[$charId] = $charInfo['name'];
            }
        }

        if($request->isXmlHttpRequest()) {
            return new JsonResponse([
                    'charList' => $charList,
                    'next' => $next,
                    'previous' => $previous,
                ]);
        } else {
            return $this->render('index.html.twig', [
                'charList' => $charList,
                'next' => $next,
                'previous' => $previous,
            ]);
        }


    }

    /**
     * @Route("/characters/", name="characters", methods={"POST"})
     */
    public function characterAction(Request $request)
    {
        $characterId = $request->get('id');
        try {
                $response = $this->apiService->call(
                    'GET',
                    'people/' . $characterId
                );
            if ($response->code == 200) {
                $data = array('status' => 'OK', 'data' => $response->data);
                return new JsonResponse($data);
            } else {
                $data = array('status' => 'ERROR');
                return new JsonResponse($data);
            }
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e->getCode());
        }
    }
}
