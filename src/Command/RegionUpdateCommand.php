<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\RegionBundle\Entity\Region;
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

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('更新行政区划数据（来原 Github）');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '1024M');

        $reflection = new \ReflectionClass(SiganushkaRegionBundle::class);
        $fileName = $reflection->getFileName();
        if (false === $fileName) {
            throw new \RuntimeException('Unable to access file.');
        }

        $jsonFile = \dirname($fileName).'/Resources/data/pca-code.json';
        if (!file_exists($jsonFile)) {
            throw new \RuntimeException(sprintf('Unable to access file(%s).', $jsonFile));
        }

        $json = file_get_contents($jsonFile);
        if (false === $json) {
            throw new \RuntimeException(sprintf('Unable to access file(%s).', $jsonFile));
        }

        /** @var array */
        $data = json_decode($json, true);
        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \UnexpectedValueException(json_last_error_msg());
        }

        $this->import($output, $data);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function import(OutputInterface $output, array $data, Region $parent = null): void
    {
        foreach ($data as $value) {
            $region = new Region();
            $region->setParent($parent);
            $region->setCode($value['code']);
            $region->setName($value['name']);

            $messages = sprintf('[%d] %s', $region->getCode(), $region->getName());

            $newParent = $this->entityManager->find(Region::class, $region->getCode());
            if ($newParent) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
            } else {
                $output->writeln("<info>{$messages} 添加成功！</info>");
                $this->entityManager->persist($region);
            }

            if (isset($value['children'])) {
                $this->import($output, $value['children'], $newParent ?: $region);
            }
        }
    }
}
