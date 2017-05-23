<?php

class Reservation
{
    private $capacity;
    private $monthlyPrice;
    private $startDate;
    private $endDate;

    private $startYearAndMonth;
    private $endYearAndMonth;

    public function __construct($capacity, $monthlyPrice, $startDate, $endDate)
    {
        $this->capacity = $capacity;
        $this->monthlyPrice = $monthlyPrice;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->startYearAndMonth = substr($this->startDate, 0, -3);
        $this->endYearAndMonth = $this->endDate ? substr($this->endDate, 0, -3) : $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @return mixed
     */
    public function getMonthlyPrice()
    {
        return $this->monthlyPrice;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return bool|string
     */
    public function getStartYearAndMonth()
    {
        return $this->startYearAndMonth;
    }

    /**
     * @return bool|string
     */
    public function getEndYearAndMonth()
    {
        return $this->endYearAndMonth;
    }

    /**
     * Get all reservations
     *
     * @param $url
     * @return array
     */
    public static function all($url)
    {
        $data = file_get_contents($url);
        $rows = explode("\n",$data);
        $reservations = [];
        foreach($rows as $key => $row) {
            // Skip title
            if ($key === 0) {
                continue;
            }

            list($capacity, $monthlyPrice, $startDate, $endDate) = str_getcsv($row);
            $reservation = new Reservation($capacity, $monthlyPrice, $startDate, $endDate);
            $reservations[] = $reservation;
        }

        return $reservations;
    }

    /**
     * Check if office is reserved in the passed month
     *
     * @param $yearAndMonth
     * @return bool
     */
    public function isReserved($yearAndMonth)
    {
        if (
            strtotime($this->getStartYearAndMonth()) <= strtotime($yearAndMonth) &&
            (
                strtotime($yearAndMonth) <= strtotime($this->getEndYearAndMonth()) ||
                $this->getEndYearAndMonth() === ''
            )
        )
        {
            return true;
        }

        return false;
    }
}