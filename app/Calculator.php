<?php

require_once 'Reservation.php';

class Calculator
{
    private $url;
    private $reservations;

    public function __construct($url)
    {
        $this->url = $url;
        $this->reservations = Reservation::all($this->url);
    }

    /**
     * Get revenue
     *
     * @param $yearAndMonth
     * @return int
     */
    public function getRevenue($yearAndMonth)
    {
        $revenue = 0;
        foreach ($this->reservations as $reservation) {
            // If reserved in the month and year
            if ($reservation->isReserved($yearAndMonth)) {
                // Get start date for calculating revenue
                $revenueStartDate = $this->getRevenueStartDate($reservation, $yearAndMonth);
                if ($revenueStartDate === false) {
                    continue;
                }
                // Get end date for calculating revenue
                $revenueEndDate = $this->getRevenueEndDate($reservation, $yearAndMonth);
                if ($revenueEndDate === false) {
                    continue;
                }

                $revenue += $this->getReservationRevenue($reservation->getMonthlyPrice(), $revenueStartDate, $revenueEndDate);
            }
        }

        return $revenue;
    }

    /**
     * Get first date for revenue calculation
     *
     * @param $reservation
     * @param $yearAndMonth
     * @return bool|string
     */
    private function getRevenueStartDate($reservation, $yearAndMonth)
    {
        // If reservation starts at the same month we check
        if (strtotime($reservation->getStartYearAndMonth()) === strtotime($yearAndMonth)) {
            // Return reservation's start date
            $revenueStartDate = $reservation->getStartDate();
        }
        // If reservation starts before the month we check
        elseif (strtotime($reservation->getStartYearAndMonth()) < strtotime($yearAndMonth)) {
            // Return the checked date
            $revenueStartDate = $yearAndMonth . '-01';
        }
        // If reservation starts after the month we check
        else {
            $revenueStartDate = false;
        }

        return $revenueStartDate;
    }

    /**
     * Get end date for revenue calculation
     *
     * @param $reservation
     * @param $yearAndMonth
     * @return bool|false|string
     */
    private function getRevenueEndDate($reservation, $yearAndMonth)
    {
        $revenueEndDate = false;
        $yearAndMonthAndMaxDay = date('Y-m-t', strtotime($yearAndMonth));
        if ($reservation->getEndDate() === '') {
            $revenueEndDate = $yearAndMonthAndMaxDay;
        } else {
            // If reservation ends before the month we check
            if ($reservation->getEndYearAndMonth() < $yearAndMonth) {
                return false;
            }
            // If reservation ends at the same month we check
            elseif($reservation->getEndYearAndMonth() === $yearAndMonth) {
                // Return reservation's end date
                $revenueEndDate = $reservation->getEndDate();
            }
            // If reservation ends after the month we check
            elseif($reservation->getEndYearAndMonth() > $yearAndMonth) {
                $revenueEndDate = $yearAndMonthAndMaxDay;
            }
        }

        return $revenueEndDate;
    }

    /**
     * Get revenue for an reservation
     *
     * @param $monthlyPrice
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public function getReservationRevenue($monthlyPrice, $startDate, $endDate)
    {
        list($startYear, $startMonth, $startDay) = explode('-', $startDate);
        list($endYear, $endMonth, $endDay) = explode('-', $endDate);
        // If reservation starts at the first day of the month
        // and ends at the last day of the month
        // it means the whole month is reserved
        if (intval($startDay) === 1 && $this->getNumberOfDaysInMonth($startDate) === intval($endDay)) {
            $revenue = $monthlyPrice;
        }
        // Calculate prorated revenue
        else {
            $d1 = new DateTime($startDate);
            $d2 = new DateTime($endDate);

            $diff = $d2->diff($d1);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $startMonth, $startYear);
            $revenuePerDay = floor($monthlyPrice / $daysInMonth);
            // Number of days * revenue per day
            $revenue = $diff->d * $revenuePerDay;
        }

        return $revenue;
    }

    /**
     * Get number of days in the month
     *
     * @param $date
     * @return int
     */
    private function getNumberOfDaysInMonth($date)
    {
        $dateTime = new DateTime($date);
        return intval($dateTime->format('t'));
    }

    /**
     * Get total capacity of unreserved offices
     *
     * @param $yearAndMonth
     * @return int
     */
    public function getTotalCapacityOfUnreservedOffices($yearAndMonth)
    {
        $totalCapacity = 0;
        foreach ($this->reservations as $reservation) {
            // If not reserved
            if (!$reservation->isReserved($yearAndMonth)) {
                $totalCapacity += $reservation->getCapacity();
            }
        }

        return $totalCapacity;
    }
}