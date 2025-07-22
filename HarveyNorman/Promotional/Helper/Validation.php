<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Validation
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Validates the start_date and end_date.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @throws LocalizedException
     */
    public function validateDates(?string $startDate, ?string $endDate): ?string
    {
        if (empty($startDate) || empty($endDate)) {
            return (string) __('Start Date and End Date cannot be empty.');
        }

        $start = $this->dateTime->timestamp($startDate);
        $end = $this->dateTime->timestamp($endDate);

        if ($end < $start) {
            return (string) __('End Date must be greater than or equal to Start Date.');
        }

        return null;
    }
}
