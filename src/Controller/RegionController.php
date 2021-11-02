<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Event\RegionFilterEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionController
{
    private $eventDispatcher;
    private $managerRegistry;
    private $normalizer;

    public function __construct(EventDispatcherInterface $eventDispatcher, ManagerRegistry $managerRegistry, NormalizerInterface $normalizer)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->managerRegistry = $managerRegistry;
        $this->normalizer = $normalizer;
    }

    public function __invoke(Request $request)
    {
        $regions = $this->getRegions($request);

        $event = new RegionFilterEvent($regions);
        $this->eventDispatcher->dispatch($event);

        $data = $this->normalizer->normalize($event->getRegions());

        return new JsonResponse($data);
    }

    private function getRegions(Request $request): iterable
    {
        $repository = $this->managerRegistry->getRepository(Region::class);

        if (!$request->query->has('parent')) {
            return $repository->findBy(['parent' => null], ['parent' => 'ASC', 'id' => 'ASC']);
        }

        $parent = $request->query->get('parent');
        if (!$region = $repository->find($parent)) {
            throw new NotFoundHttpException(sprintf('The parent "%s" could not be found.', $parent));
        }

        return $region->getChildren();
    }
}
