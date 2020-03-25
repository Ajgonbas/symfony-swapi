<?php
    namespace App\Service;
     
     use GuzzleHttp\Client;
     use GuzzleHttp\Exception\BadResponseException;
     use GuzzleHttp\Psr7\Response;
      
     class ApiService
     {
         const API_ENDPOINT = 'https://swapi.co/api/';        
      
         /**
          * @param string $method
          * @param string $uri
          * @param array $getParams
          * @param string|null $postData
          *
          * @return Response
          */
         public function call($method, $uri, array $getParams = [], $postData = null)
         {
             return $this->consume(
                 $method,
                 $this->createUri($uri, $getParams),
                 $postData
             );
         }
      
         /**
          * @param string $uri
          * @param array $getParams
          *
          * @return string
          */
         private function createUri($uri, array $getParams = [])
         {
            return sprintf(
                 '%s%s?%s',
                 self::API_ENDPOINT,
                 $uri,
                 http_build_query($getParams)
             );
         }
      
         /**
          * @param string $method
          * @param string $uri
          * @param string $auth
          * @param string|null $postData
          *
          * @return Response
          */
         private function consume($method, $uri, $postData = null)
         {
             $response = new Response();
      
             try {
                 $client = new Client();
                 $result = $client->request($method, $uri, [
                     'body' => $postData
                 ]);      
                 $response->code = $result->getStatusCode();
                 $response->data = json_decode($result->getBody(), true);
             } catch (BadResponseException $e) {
                 $response->code = $e->getCode();
                 $response->data = $e->getMessage();
             }
      
             return $response;
         }
     }