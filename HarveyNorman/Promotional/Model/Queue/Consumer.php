<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\Queue;

use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Consumer
{
    public function __construct(
        private LoggerInterface $logger,
        private SerializerInterface $serializer
    ) {}

    public function process(string $message): void
    {
        try {
            // Unserialize json data
            $data = $this->serializer->unserialize($message);

            if (!is_array($data) || !isset($data['action'])) {
                throw new \InvalidArgumentException('Invalid message format');
            }

            $this->logger->debug('Processing promotional message', ['data' => $data]);

            match ($data['action']) {
                'upsert' => $this->handleUpsert($data),
                'delete' => $this->handleDelete($data),
                'test' => $this->handleTest($data),
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

        $this->logger->info('Processing product upsert', ['sku' => $data['sku'] ?? '']);
    }

    private function handleDelete(array $data): void
    {
        // Implement your delete logic here
        $this->logger->info('Processing product deletion', ['sku' => $data['sku'] ?? '']);
    }

    private function handleTest(array $data): void
    {
        // Test handler
        $this->logger->info('Received test message', ['data' => $data]);
    }
}