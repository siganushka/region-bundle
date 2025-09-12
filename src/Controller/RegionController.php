<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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

        return $this->json($result, context: [
            AbstractNormalizer::GROUPS => ['region:collection'],
        ]);
    }

    #[Route('/regions/{code}', methods: 'GET')]
    public function getItem(string $code): Response
    {
        $entity = $this->regionRepository->find($code)
            ?? throw $this->createNotFoundException();

        return $this->json($entity, context: [
            AbstractNormalizer::GROUPS => ['region:item'],
        ]);
    }
}
