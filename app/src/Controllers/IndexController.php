<?php

namespace EDM\Controllers;

use EDM\Rest\Processors\ControllerProcessor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Processors\BuildPaths;
use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;

class IndexController extends AbstractController
{
//	private static function generate(): ?OpenApi
//	{
//		$generator = new Generator();
//		$generator->addProcessor(new ControllerProcessor(), BuildPaths::class);
//		$generator->setVersion('3.1.0');
//		return $generator->generate([__DIR__, __DIR__ . '/../Rest/Specs.php']);
//	}
//
//	#[Route(path: '/openapi.yml', methods: ['GET'])]
//	#[Route(path: '/openapi.yaml', methods: ['GET'])]
//	public function getYAML(): Response
//	{
//		$response = new Response(self::generate()->toYaml());
//		$response->headers->set('Content-Type', 'application/x-yaml');
//		return $response;
//	}
//
//	#[Route(path: '/openapi.json', methods: ['GET'])]
//	public function getJSON(): JsonResponse
//	{
//		return new JsonResponse(self::generate());
//	}
}