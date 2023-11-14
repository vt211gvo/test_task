<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use DB;

class CarRentController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function getBookings(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $bookings = Bookings::getBookings($year, $month);

        return response()->json(['bookings' => $bookings]);
    }
}

/*

SELECT DISTINCT rc_bookings.start_date, rc_bookings.end_date, rc_bookings.car_id, rc_cars_translations.title, rc_cars_models.attribute_interior_color,
 rc_cars_brands.slug as brand, rc_cars.attribute_year,rc_cars.registration_number, rc_bookings.created_at
FROM rc_bookings
JOIN rc_cars ON rc_bookings.car_id=rc_cars.car_id
JOIN rc_cars_translations ON rc_bookings.car_id=rc_cars_translations.car_id
JOIN rc_cars_models on rc_cars.car_model_id=rc_cars_models.car_model_id
JOIN rc_cars_brands on rc_cars_models.car_brand_id=rc_cars_brands.car_brand_id
WHERE rc_bookings.status=1 AND rc_cars.company_id=1 AND rc_cars.status=1 AND rc_cars.is_deleted!=1

AND (MONTH(rc_bookings.start_date) <= 10 AND YEAR(rc_bookings.start_date)=2023)
    AND MONTH(rc_bookings.end_date) >= 10

ORDER BY rc_bookings.car_id

*/
