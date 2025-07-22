<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Console\Command;

use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory as PromoProductCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticSearchReindex extends Command
{
    private const TOPIC_NAME = 'promotional_product_upsert';

    public function __construct(
        private State $appState,
        private PromoProductCollectionFactory $promoCollectionFactory,
        private PublisherInterface $publisher,
        private Json $serializer
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

        foreach ($collection as $promoProduct) {
            $messageData = [
                'action' => 'upsert',
                'id' => (string)$promoProduct->getId(),
                'sku' => $promoProduct->getSku(),
                'name' => $promoProduct->getName(),
                'price' => (float)$promoProduct->getPrice(),
                'discount_percentage' => (float)$promoProduct->getDiscountPercentage(),
                'start_date' => $promoProduct->getStartDate(),
                'end_date' => $promoProduct->getEndDate(),
                'promotion_status' => (bool)$promoProduct->getPromotionStatus(),
                'isPromotionEligible' => (bool)$promoProduct->getIsPromotionEligible(),
                'discounted_price' => (float)$promoProduct->getDiscountedPrice(),
            ];

            try {
                $serialized = $this->serializer->serialize($messageData);
                $this->publisher->publish(self::TOPIC_NAME, $serialized);
                $output->writeln("<info>Queued product ID {$promoProduct->getId()} ({$promoProduct->getSku()})</info>");
            } catch (\Exception $e) {
                $output->writeln("<error>Failed to queue product ID {$promoProduct->getId()}: {$e->getMessage()}</error>");
            }
        }

        $output->writeln('<info>All promotional products queued for Elasticsearch upsert.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
