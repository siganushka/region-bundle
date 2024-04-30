<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Controller;

use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegionController extends AbstractController
{
    protected SerializerInterface $serializer;
    protected RegionRepository $regionRepository;

    public function __construct(SerializerInterface $serializer, RegionRepository $regionRepository)
    {
        $this->serializer = $serializer;
        $this->regionRepository = $regionRepository;
    }

    /**
     * @Route("/regions", methods={"GET"})
     */
    public function getCollection(Request $request): Response
    {
        $parent = $request->query->get('parent');

        $criteria = compact('parent');
        $orderBy = ['parent' => 'ASC', 'id' => 'ASC'];

        $regions = $this->regionRepository->findBy($criteria, $orderBy);

        return $this->createResponse($regions);
    }

    /**
     * @Route("/regions/{code}", methods={"GET"})
     */
    public function getItem(string $code): Response
    {
        $entity = $this->regionRepository->find($code);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $code));
        }

        return $this->createResponse($entity);
    }

    /**
     * @param mixed $data
     */
    protected function createResponse($data = null, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $attributes = ['code', 'name', 'root', 'leaf', 'depth'];
        $json = $this->serializer->serialize($data, 'json', compact('attributes'));

        return JsonResponse::fromJsonString($json, $statusCode, $headers);
    }
}
