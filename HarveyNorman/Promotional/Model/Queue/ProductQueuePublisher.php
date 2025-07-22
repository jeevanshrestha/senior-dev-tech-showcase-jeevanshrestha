<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\Queue;

use HarveyNorman\Promotional\Api\Data\ProductInterface;
use HarveyNorman\Promotional\Api\QueueTopicInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;
use HarveyNorman\Promotional\Model\Product;

class ProductQueuePublisher implements QueueTopicInterface
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
            'action' => 'upsert',
            'id' => (string) $promoProduct->getId(),
            'sku' => $promoProduct->getSku(),
            'name' => $promoProduct->getName(),
            'price' => (float) $promoProduct->getPrice(),
            'discount_percentage' => (float) $promoProduct->getDiscountPercentage(),
            'start_date' => $promoProduct->getStartDate(),
            'end_date' => $promoProduct->getEndDate(),
            'promotion_status' => (bool) $promoProduct->getPromotionStatus(),
            'isPromotionEligible' => (bool) $promoProduct->isEligibleForPromotion(),
            'discounted_price' => (float) $promoProduct->getDiscountedPrice(),
        ];

        $payload = $this->serializer->serialize($messageData);
        $this->publisher->publish(self::TOPIC_NAME, $payload);
    }
}
