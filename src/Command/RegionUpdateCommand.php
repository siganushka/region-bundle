<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Entity\RegionInterface;
use Siganushka\RegionBundle\SiganushkaRegionBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @see https://github.com/modood/Administrative-divisions-of-China
 */
class RegionUpdateCommand extends Command
{
    protected static $defaultName = 'siganushka:region:update';

    private ObjectManager $objectManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $objectManager = $managerRegistry->getManagerForClass(Region::class);
        if (null === $objectManager) {
            throw new \InvalidArgumentException(sprintf('Invalid entity class "%s" for object manager.', Region::class));
        }

        $this->objectManager = $objectManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('更新行政区划数据（来原 Github）');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reflection = new \ReflectionClass(SiganushkaRegionBundle::class);
        $fileName = $reflection->getFileName();
        if (false === $fileName) {
            throw new \RuntimeException('Unable to access file.');
        }

        $jsonFile = \dirname($fileName).'/Resources/data/pca-code.json';
        if (!file_exists($jsonFile)) {
            throw new \RuntimeException('Unable to access file.');
        }

        $json = file_get_contents($jsonFile);
        if (false === $json) {
            throw new \RuntimeException('Unable to access file.');
        }

        /** @var array */
        $data = json_decode($json, true);
        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \UnexpectedValueException(json_last_error_msg());
        }

        // Manually assign id
        $metadata = $this->objectManager->getClassMetadata(Region::class);
        if ($metadata instanceof ClassMetadataInfo) {
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        }

        $this->import($output, $data);
        $this->objectManager->flush();

        return Command::SUCCESS;
    }

    protected function import(OutputInterface $output, array $data, RegionInterface $parent = null): void
    {
        foreach ($data as $value) {
            $region = new Region();
            $region->setParent($parent);
            $region->setCode($value['code']);
            $region->setName($value['name']);
            $region->setCreatedAt(new \DateTimeImmutable());

            $messages = sprintf('[%d] %s', $region->getCode(), $region->getName());

            $newParent = $this->objectManager->find(Region::class, $region->getCode());
            if ($newParent) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
            } else {
                $output->writeln("<info>{$messages} 添加成功！</info>");
                $this->objectManager->persist($region);
            }

            if (isset($value['children'])) {
                $this->import($output, $value['children'], $newParent ?: $region);
            }
        }
    }
}
