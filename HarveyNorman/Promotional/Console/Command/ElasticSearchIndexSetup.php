<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Console\Command;

use HarveyNorman\Promotional\Helper\ElasticsearchHelper;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticSearchIndexSetup extends Command
{
    private ElasticsearchHelper $elasticHelper;
    private State $appState;

    public function __construct(
        ElasticsearchHelper $elasticHelper,
        State $appState
    ) {
        parent::__construct();
        $this->elasticHelper = $elasticHelper;
        $this->appState = $appState;
    }

    protected function configure()
    {
        $this->setName('promotional:elasticsearch:setup')
            ->setDescription('Create Elasticsearch index template and index if not exist');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Set area code if not set to avoid errors
            $this->appState->setAreaCode(Area::AREA_GLOBAL);
        } catch (LocalizedException $e) {
            // area code was already set, no problem
        }

        $output->writeln('<info>Starting Elasticsearch setup...</info>');

        try {
            $this->elasticHelper->createIndexTemplate();
            $output->writeln('<info>Index template created or already exists.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Error creating index template: ' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        try {
            $this->elasticHelper->createIndex();
            $output->writeln('<info>Index created or already exists.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Error creating index: ' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Elasticsearch setup finished successfully.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
