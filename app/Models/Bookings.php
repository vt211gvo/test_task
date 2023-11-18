<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bookings extends Model
{
    public static function getCars($year, $month): \Illuminate\Support\Collection
    {
        return self::fetchBookings($year, $month);
    }

    public static function fetchBookings($year, $month)
    {
        $bookings = DB::table('rc_bookings')
            ->select('rc_bookings.start_date', 'rc_bookings.end_date', 'rc_bookings.car_id', 'rc_cars_translations.title', 'rc_cars_models.attribute_interior_color', 'rc_cars_brands.slug as brand', 'rc_cars.attribute_year', 'rc_cars.registration_number', 'rc_bookings.created_at')
            ->join('rc_cars', 'rc_bookings.car_id', '=', 'rc_cars.car_id')
            ->join('rc_cars_translations', 'rc_bookings.car_id', '=', 'rc_cars_translations.car_id')
            ->join('rc_cars_models', 'rc_cars.car_model_id', '=', 'rc_cars_models.car_model_id')
            ->join('rc_cars_brands', 'rc_cars_models.car_brand_id', '=', 'rc_cars_brands.car_brand_id')
            ->where('rc_bookings.status', 1)
            ->where('rc_cars.company_id', 1)
            ->where('rc_cars.status', 1)
            ->where('rc_cars.is_deleted', '!=', 1)
            ->where(function ($query) use ($year, $month){
                $query->where(function ($innerQuery) use ($year, $month) {
                    $innerQuery->whereYear('rc_bookings.start_date', $year)
                        ->whereMonth('rc_bookings.start_date', '<=', $month)
                        ->whereMonth('rc_bookings.end_date', '>=', $month);
                })->orWhere(function ($innerQuery) use ($year, $month) {
                    $innerQuery->whereYear('rc_bookings.start_date', '<', $year)
                        ->whereYear('rc_bookings.end_date', '>=', $year)
                        ->whereMonth('rc_bookings.end_date', '>=', $month);
                });
            })
            ->distinct()
            ->orderBy('rc_bookings.car_id')
            ->get();


        foreach ($bookings as $booking) {
            $occupiedDays = self::calculateOccupiedDays($booking, $year, $month);
            $booking->occupied = $occupiedDays;
        }

        $grouped = $bookings->groupBy('car_id');

        $summary = $grouped->map(function ($items, $key) {
            $item = $items->first();

            $allOccupiedDays = [];

            foreach ($items as $booking) {
                $occupiedDays = $booking->occupied;
                $allOccupiedDays = array_merge($allOccupiedDays, $occupiedDays);
            }

            $uniqueOccupiedDays = array_unique($allOccupiedDays);
            $item->occupied = count($uniqueOccupiedDays);

            return $item;
        });

        foreach ($summary as $s){
            $freeDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $freeDays -= $s->occupied;
            $s->free = $freeDays;
        }

        return $summary;
    }


    public static function calculateOccupiedDays($booking, $year, $month): array
    {
//        Авто вважається вільним, якщо воно було не зайняте 9 і більше годин підряд з 9 ранку до 9 вечора.

        $startDateTime = new DateTime($booking->start_date);
        $endDateTime = new DateTime($booking->end_date);

        // Check if the booking is entirely within the selected month and year
        $firstDayOfMonth = new DateTime("{$year}-{$month}-01");
        $firstDayOfMonth->setTime(9, 0);
        $lastDayOfMonth = new DateTime("{$year}-{$month}-" . cal_days_in_month(CAL_GREGORIAN, $month, $year));
        $lastDayOfMonth->setTime(21, 0);

        if ($startDateTime < $firstDayOfMonth) {
            $startDateTime = $firstDayOfMonth;
        }
        if($endDateTime > $lastDayOfMonth){
            $endDateTime = $lastDayOfMonth;
        }

        $occupiedDays = array();

        while ($startDateTime <= $endDateTime) {
            $hoursArray = array();
            $currentHour = clone $startDateTime;

            if($currentHour->format('j') < $endDateTime->format('j')){
                while ($currentHour->format('H') <= 21) {
                    $hoursArray[] = $currentHour->format('H');
                    $currentHour->modify('+1 hour');
                }
            }
            else{
                while ($currentHour->format('H') < $endDateTime->format('H')) {
                    $hoursArray[] = $currentHour->format('H');
                    $currentHour->modify('+1 hour');
                }
            }

            $filteredHours = array_filter($hoursArray, function ($hour) {
                return $hour >= 9 && $hour <= 21;
            });

            if(count($filteredHours)>3) $occupiedDays[] = $startDateTime->format('j'); ;

            $startDateTime->modify('+1 day');
            $startDateTime->setTime(0, 0);
        }

        return $occupiedDays;
    }
}
