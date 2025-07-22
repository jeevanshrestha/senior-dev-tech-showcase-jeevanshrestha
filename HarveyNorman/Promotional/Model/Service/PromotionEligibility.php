<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\Service;

use HarveyNorman\Promotional\Api\Data\ProductInterface;
use DateTime;
use DateTimeZone;
use Exception;

class PromotionEligibility
{
    private DateTimeZone $utc;
    private ?DateTime $currentDateTime;

    /**
     * @param DateTimeZone|null $utc 
     */
    public function __construct(
        ?DateTimeZone $utc = null,
        ?DateTime $currentDateTime = null
    ) {
        $this->utc = $utc ?? new DateTimeZone('UTC');
        $this->currentDateTime = $currentDateTime;
    }

    /**
     * Check if a product is eligible for promotion.
     *
     * @param ProductInterface $product
     * @return bool
     */
    public function isEligible(ProductInterface $product): bool
    {
        $status = $product->getPromotionStatus();
        $startDate = $product->getStartDate();
        $endDate = $product->getEndDate();

        if ((int)$status !== 1) {
            return false;
        }

        if (!$startDate || !$endDate) {
            return false;
        }

        try {
            $now = $this->currentDateTime ?? new DateTime('now', $this->utc);
            $start = new DateTime($startDate, $this->utc);
            $end = new DateTime($endDate, $this->utc);

            return $now >= $start && $now <= $end;
        } catch (Exception) {
            return false;
        }
    }
}
