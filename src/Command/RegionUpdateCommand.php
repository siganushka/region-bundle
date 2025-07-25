<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('siganushka:region:update', 'Update administrative divisions data to database.')]
class RegionUpdateCommand extends Command
{
    /**
     * @var Region[]
     */
    private array $cachedRegions;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly RegionRepository $regionRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('level', null, InputOption::VALUE_REQUIRED, 'How many levels of data should be updated?', '3');
    }

    /**
     * @see https://github.com/modood/Administrative-divisions-of-China
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string */
        $level = $input->getOption('level');
        $json = match ($level) {
            '2' => 'pc-code.json',
            '3' => 'pca-code.json',
            '4' => 'pcas-code.json',
            default => throw new \InvalidArgumentException(\sprintf('The option "level" with value "%s" is invalid (2, 3, 4).', $level)),
        };

        $output->writeln('<info>下载数据...</info>');

        $response = $this->httpClient->request('GET', "https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/{$json}");
        $data = $response->toArray();

        $this->import($output, $data);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function import(OutputInterface $output, array $data, ?Region $parent = null): void
    {
        foreach ($data as $value) {
            $region = $this->regionRepository->createNew($value['code'], $value['name']);
            $region->setParent($parent);

            $messages = \sprintf('[%s] %s', $region->getCode(), $region->getName());

            $newParent = $this->findRegionByCode($region->getCode());
            if ($newParent) {
                $output->writeln("<comment>{$messages} 已存在！</comment>");
            } else {
                $output->writeln("<info>{$messages} 导入成功！</info>");
                $this->entityManager->persist($region);
            }

            if (isset($value['children'])) {
                $this->import($output, $value['children'], $newParent ?? $region);
            }
        }
    }

    protected function findRegionByCode(string $code): ?Region
    {
        if (!isset($this->cachedRegions)) {
            $this->cachedRegions = $this->regionRepository->findAll();
        }

        return array_find($this->cachedRegions, fn (Region $item) => $code === $item->getCode());
    }
}
