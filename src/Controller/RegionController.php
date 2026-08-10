<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Siganushka\RegionBundle\Dto\RegionQueryDto;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

class RegionController extends AbstractController
{
    public function __construct(protected readonly RegionRepository $regionRepository)
    {
    }

    public function getCollection(#[MapQueryString] RegionQueryDto $dto): Response
    {
        $qb = $this->regionRepository->createQueryBuilderByDto('r', $dto);
        $result = $qb->getQuery()->getResult();

        return $this->json($result, context: [
            'groups' => ['region.collection'],
        ]);
    }

    public function getItem(string $code): Response
    {
        $entity = $this->regionRepository->find($code)
            ?? throw $this->createNotFoundException();

        return $this->json($entity, context: [
            'groups' => ['region.item'],
        ]);
    }
}
