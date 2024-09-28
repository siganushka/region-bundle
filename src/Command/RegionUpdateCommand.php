<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://github.com/modood/Administrative-divisions-of-China
 */
class RegionUpdateCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly RegionRepository $regionRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('siganushka:region:update')
            ->setDescription('更新行政区划数据，来源 https://github.com/modood/Administrative-divisions-of-China')
            ->addOption('with-street', null, InputOption::VALUE_NONE, '是否包含乡/街道数据？')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '1024M');

        $json = $input->getOption('with-street')
            ? 'pcas-code.json'
            : 'pca-code.json';

        $output->writeln('<info>下载数据...</info>');

        $response = $this->httpClient->request('GET', "https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/{$json}");
        $data = $response->toArray();

        $this->import($output, $data);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function import(OutputInterface $output, array $data, Region $parent = null): void
    {
        foreach ($data as $value) {
            $region = $this->regionRepository->createNew($value['code'], $value['name']);
            $region->setParent($parent);

            $messages = \sprintf('[%s] %s', $region->getCode(), $region->getName());

            $newParent = $this->entityManager->find(Region::class, $region->getCode());
            if ($newParent) {
                $output->writeln("<comment>{$messages} 已存在！</comment>");
            } else {
                $output->writeln("<info>{$messages} 导入成功！</info>");
                $this->entityManager->persist($region);
            }

            if (isset($value['children'])) {
                $this->import($output, $value['children'], $newParent ?: $region);
            }
        }
    }
}
