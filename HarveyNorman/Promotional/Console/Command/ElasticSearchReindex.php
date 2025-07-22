<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Console\Command;
 
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory as PromoProductCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use HarveyNorman\Promotional\Model\Queue\ProductQueuePublisher;
use HarveyNorman\Promotional\Helper\ElasticSearchHelper;

class ElasticSearchReindex extends Command 
{


    public function __construct(
        private State $appState,
        private PromoProductCollectionFactory $promoCollectionFactory,
        private ProductQueuePublisher $publisher,
        private Json $serializer,
        private ElasticSearchHelper $elasticSearchHelper
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('promotional:products:reindex')
            ->setDescription('Queue promotional products to be upserted into Elasticsearch via RabbitMQ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode('global');
        } catch (LocalizedException $e) {
            // Area code might already be set; ignore.
        }

        $collection = $this->promoCollectionFactory->create();
        $count = $collection->getSize();

        $output->writeln("<info>Found {$count} promotional products to queue for upsert...</info>");
        if ($count === 0) {
            $output->writeln('<info>No promotional products found to queue.</info>');
            return Cli::RETURN_SUCCESS;
        }
        // Clear the Elasticsearch index before queuing new products
        // $output->writeln('<info>Clearing Elasticsearch index...</info>');
        // $this->elasticSearchHelper->deleteAll();

        $output->writeln('<info>Starting to queue promotional products for Elasticsearch upsert...</info>');
    
        foreach ($collection as $promoProduct) {
            try {
                 $this->publisher->publish($promoProduct);
                $output->writeln("<info>Queued product ID {$promoProduct->getId()} ({$promoProduct->getSku()})</info>");
            } catch (\Exception $e) {
                $output->writeln("<error>Failed to queue product ID {$promoProduct->getId()}: {$e->getMessage()}</error>");
            }
        }

        $output->writeln('<info>All promotional products queued for Elasticsearch upsert.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
