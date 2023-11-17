@extends('layouts.master')

@section('content')
    <h1 class="text-center">Car Rentals</h1>

    <div id="filters" class="rounded p-3">
        <div class="row w-100">
            <div class="col-md-6 w-50">
                <label for="yearSelector">Year:</label>
                <select id="yearSelector" class="form-control w-100" size="4">
                    @for ($y = 2023; $y >= 2017; $y--)
                        <option value="{{ $y }}" @if ($y == 2023) selected @endif>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6 w-50">
                <label for="monthSelector">Month:</label>
                <select id="monthSelector" class="form-control w-100" size="4">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @if ($m == 1) selected @endif>{{ $m }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <br>

    <table class="table table-bordered" id="bookingTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Year</th>
            <th>Interior color</th>
            <th>Brand</th>
            <th>Registration number</th>
            <th>Date of creation</th>
            <th>Free days</th>
        </tr>
        </thead>
        <tbody>
        <!-- Filtered data -->
        </tbody>
    </table>
@endsection
