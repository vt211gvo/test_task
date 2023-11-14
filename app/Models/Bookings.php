<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bookings extends Model
{
    public static function getBookings($year, $month)
    {
        return DB::table('rc_bookings')
            ->select('rc_bookings.car_id', 'rc_cars_translations.title', 'rc_cars.attribute_year', 'rc_cars_models.attribute_interior_color',
                'rc_cars_brands.slug as brand', 'rc_cars.registration_number', 'rc_bookings.start_date', 'rc_bookings.end_date', 'rc_bookings.created_at')
            ->join('rc_cars', 'rc_bookings.car_id', '=', 'rc_cars.car_id')
            ->join('rc_cars_translations', 'rc_bookings.car_id', '=', 'rc_cars_translations.car_id')
            ->join('rc_cars_models', 'rc_cars.car_model_id', '=', 'rc_cars_models.car_model_id')
            ->join('rc_cars_brands', 'rc_cars_models.car_brand_id', '=', 'rc_cars_brands.car_brand_id')
            ->where('rc_bookings.status', 1)
            ->where('rc_cars.company_id', 1)
            ->where('rc_cars.status', 1)
            ->where('rc_cars.is_deleted', '!=', 1)
            ->where(function($query) use ($month, $year){
                $query->whereMonth('rc_bookings.start_date', '<=', $month)
                    ->whereYear('rc_bookings.start_date', $year);
            })
            ->where(function($query) use ($month, $year){
                $query->whereMonth('rc_bookings.end_date', '>=', $month);
            })
            ->orderBy('rc_bookings.car_id')
            ->distinct()
            ->get();
    }
}
