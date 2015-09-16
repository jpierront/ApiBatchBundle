<?php

namespace JPierront\ApiBatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ApiBatchController
 */
class ApiBatchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function apiBatchAction(Request $request)
    {
        $subRequestConfigs = json_decode($request->getContent(), true) ?: [];
        $responseData      = [];

        foreach ($subRequestConfigs as $subRequestConfig) {
            $response       = $this->processSubRequest(new ParameterBag($subRequestConfig));
            $responseData[] = [
                'code'    => $response->getStatusCode(),
                'headers' => $response->headers->all(),
                'body'    => $response->getContent(),
            ];
        }

        return new JsonResponse($responseData);
    }

    /**
     * @param ParameterBag $parameters
     *
     * @return Response
     */
    private function processSubRequest(ParameterBag $parameters)
    {
        $subRequest  = $this->buildRequestFromParameters($parameters);
        $subResponse = $this->getHttpKernel()->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        return $subResponse;
    }

    /**
     * @param ParameterBag $parameters
     *
     * @return Request
     */
    private function buildRequestFromParameters(ParameterBag $parameters)
    {
        $uri        = $parameters->get('url');
        $method     = $parameters->get('method', 'GET');
        $parameters = $this->transformParametersStringToArray($parameters->get('parameters', ''));
        $cookies    = [];
        $files      = [];
        $server     = [];
        $content    = null;
        $request    = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        return $request;
    }

    /**
     * @param $string
     *
     * @return array<string, mixed>
     */
    private function transformParametersStringToArray($string)
    {
        $array = null;
        parse_str($string, $array);

        return $array;
    }

    /**
     * @return HttpKernelInterface
     */
    protected function getHttpKernel()
    {
        return $this->container->get('http_kernel');
    }
}
