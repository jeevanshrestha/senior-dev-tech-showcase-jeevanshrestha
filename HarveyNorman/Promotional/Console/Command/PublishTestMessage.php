<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Console\Command;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class PublishTestMessage extends Command
{
    private const TOPIC_NAME = 'publisher_test';

    public function __construct(
        private readonly PublisherInterface $publisher,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('harveynorman:promotional:publish-test')
            ->setDescription('Publish test message to Harvey Norman promotional queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $this->createTestMessage();

        try {
            $this->publishMessage($message);
            $output->writeln('<info>Successfully published test message.</info>');
            $this->logger->debug('Test message published', ['message' => $message]);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            $this->logger->error('Failed to publish message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function createTestMessage(): array
    {
        return [
            'action' => 'test',
            'product_id' => 1234,
            'sku' => 'PROD-1234',
            'name' => 'Example Product',
            'promotion' => [
                'id' => 5678,
                'name' => 'Summer Sale',
                'type' => 'percentage_discount',
                'value' => 20.00,
                'start_date' => '2024-06-01T00:00:00Z',
                'end_date' => '2024-08-31T23:59:59Z',
                'status' => 'active'
            ],
            'original_price' => 99.99,
            'discounted_price' => 79.99,
            'stock_qty' => 100,
            'categories' => [10, 15, 20],
            'attributes' => [
                'color' => 'blue',
                'size' => 'medium'
            ],
            'visibility' => ['catalog', 'search'],
            'is_in_stock' => true,
            'updated_at' => date('c')
        ];
    }

    private function publishMessage(array $message): void
    {
        $this->publisher->publish(
            self::TOPIC_NAME,
            $this->serializer->serialize($message)
        );
    }
}