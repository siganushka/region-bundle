<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends AbstractController
{
    public function __construct(protected readonly RegionRepository $regionRepository)
    {
    }

    public function getCollection(Request $request): Response
    {
        $parent = $request->query->get('parent');
        $result = $this->regionRepository->findByParent($parent, ['parent' => 'ASC', 'code' => 'ASC']);

        return $this->json($result, context: [
            'groups' => ['region:collection'],
        ]);
    }

    public function getItem(string $code): Response
    {
        $entity = $this->regionRepository->find($code)
            ?? throw $this->createNotFoundException();

        return $this->json($entity, context: [
            'groups' => ['region:item'],
        ]);
    }
}
