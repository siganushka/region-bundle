<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;
use Siganushka\RegionBundle\Event\RegionFilterEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionController
{
    private EventDispatcherInterface $eventDispatcher;
    private ManagerRegistry $managerRegistry;
    private SerializerInterface $serializer;

    public function __construct(EventDispatcherInterface $eventDispatcher, ManagerRegistry $managerRegistry, SerializerInterface $serializer)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->managerRegistry = $managerRegistry;
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $regions = $this->getRegions($request);

        $event = new RegionFilterEvent($regions);
        $this->eventDispatcher->dispatch($event);

        $attributes = (string) $request->query->get('attributes');
        $attributes = empty($attributes) ? [] : explode(',', $attributes);
        $attributes = array_map('trim', $attributes);

        $json = $this->serializer->serialize($event->getRegions(), 'json', [
            AbstractNormalizer::ATTRIBUTES => ['code', 'name', ...$attributes],
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @return iterable<int, RegionInterface>
     */
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
