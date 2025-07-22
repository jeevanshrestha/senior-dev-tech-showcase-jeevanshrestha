<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\Queue;

use HarveyNorman\Promotional\Helper\ElasticsearchHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class Consumer
{
    public function __construct(
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
        private ElasticsearchHelper $elasticHelper
    ) {}

    public function process(string $message): void
    {
        try {
            // Unserialize JSON message
            $data = $this->serializer->unserialize($message);

            if (!is_array($data) || !isset($data['action'])) {
                throw new \InvalidArgumentException('Invalid message format');
            }

            $this->logger->debug('Processing promotional message', ['data' => $data]);

            match ($data['action']) {
                'upsert' => $this->handleUpsert($data),
                'delete' => $this->handleDelete($data),
                'test'   => $this->handleTest($data),
                default => $this->logger->warning("Unknown action: {$data['action']}")
            };
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Invalid message: ' . $e->getMessage(), ['message' => $message]);
        } catch (\Exception $e) {
            $this->logger->error('Error processing message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function handleUpsert(array $data): void
    {
        try {
            if (!isset($data['id']) || !is_array($data)) {
                throw new \InvalidArgumentException('Missing required upsert fields.');
            }

            $this->elasticHelper->upsertDocument((string)$data['id'], $data);
            $this->logger->info('Successfully upserted product to Elasticsearch', ['id' => $data['id']]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to upsert document: ' . $e->getMessage(), ['data' => $data]);
        }
    }

    private function handleDelete(array $data): void
    {
        $this->logger->info('Processing product deletion', ['sku' => $data['sku'] ?? '']);
    }

    private function handleTest(array $data): void
    {
        $this->logger->info('Received test message', ['data' => $data]);
    }
}
