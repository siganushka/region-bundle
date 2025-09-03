<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RegionController extends AbstractController
{
    public function __construct(protected readonly RegionRepository $regionRepository)
    {
    }

    #[Route('/regions', methods: 'GET')]
    public function getCollection(Request $request): Response
    {
        $parent = $request->query->get('parent');
        $result = $this->regionRepository->findByParent($parent, ['parent' => 'ASC', 'code' => 'ASC']);

        return $this->createResponse($result);
    }

    #[Route('/regions/{code}', methods: 'GET')]
    public function getItem(string $code): Response
    {
        $entity = $this->regionRepository->find($code)
            ?? throw $this->createNotFoundException();

        return $this->createResponse($entity);
    }

    protected function createResponse(array|Region $data, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        return $this->json($data, $statusCode, $headers, [
            ObjectNormalizer::IGNORED_ATTRIBUTES => ['parent', 'children', 'siblings', 'descendants', 'ancestors'],
        ]);
    }
}
