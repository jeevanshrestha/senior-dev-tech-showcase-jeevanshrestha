<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\Queue;

use HarveyNorman\Promotional\Api\Data\ProductInterface;
use HarveyNorman\Promotional\Api\QueueTopicInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ProductDeletePublisher implements QueueTopicInterface
{
    private PublisherInterface $publisher;
    private SerializerInterface $serializer;

    public function __construct(
        PublisherInterface $publisher,
        SerializerInterface $serializer
    ) {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    public function publish(ProductInterface $promoProduct): void
    {
        $messageData = [
            'action' => 'delete',
            'id' => (string) $promoProduct->getId(),
        ];

        $payload = $this->serializer->serialize($messageData);
        $this->publisher->publish(self::TOPIC_NAME, $payload);
    }
}
