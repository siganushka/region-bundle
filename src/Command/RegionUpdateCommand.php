<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\RegionBundle\Entity\Region;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://github.com/modood/Administrative-divisions-of-China
 */
class RegionUpdateCommand extends Command
{
    protected static $defaultName = 'siganushka:region:update';
    protected static $defaultDescription = '更新行政区划数据，来源 https://github.com/modood/Administrative-divisions-of-China';

    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;
    private array $sourceMapping = ['pc' => '省市', 'pca' => '省市区', 'pcas' => '省市区乡'];

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $mapping = array_map(fn (string $key) => sprintf('%s: %s', $key, $this->sourceMapping[$key]), array_keys($this->sourceMapping));

        $this->addArgument('source', InputArgument::OPTIONAL, sprintf('行政区划数据级别 (%s)', implode('/', $mapping)), 'pc');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '1024M');

        $source = $input->getArgument('source');
        if (!\array_key_exists($source, $this->sourceMapping)) {
            throw new \InvalidArgumentException(sprintf('参数 source 值无效 (仅能为：%s).', implode('/', array_keys($this->sourceMapping))));
        }

        $output->writeln('<info>下载数据...</info>');

        $response = $this->httpClient->request('GET', "https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/{$source}-code.json");
        $data = $response->toArray();

        $output->writeln('<info>下载完成，开始导入数据库...</info>');

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
