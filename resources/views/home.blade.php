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

    <table id="bookingTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Car ID</th>
                <th>Title</th>
                <th>Year</th>
                <th>Interior Color</th>
                <th>Brand</th>
                <th>Registration Number</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Created At</th>
                <th>Free Days</th>
            </tr>
        </thead>
        <tbody>
            <!-- Відфільтровані дані будуть виведені тут -->
        </tbody>
    </table>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var selectedYear, selectedMonth;
            var yearSelector = $('#yearSelector');
            var monthSelector = $('#monthSelector');
            var bookingTable = $('#bookingTable tbody');

            yearSelector.change(function() {
                var year = yearSelector.val();
                var month = monthSelector.val();
                filterData(year, month);
            });

            monthSelector.change(function() {
                var year = yearSelector.val();
                var month = monthSelector.val();
                filterData(year, month);
            });

            function filterData(year, month) {
                selectedYear = parseInt(year);
                selectedMonth = parseInt(month);

                $.get('/get-bookings', { year: selectedYear, month: selectedMonth }, function(data) {
                    bookingTable.html('');

                    if (data.bookings.length === 0) {
                        bookingTable.append('<tr><td colspan="10">Немає відповідних букінгів.</td></tr>');
                    } else {
                        $.each(data.bookings, function(index, booking) {
                            var newRow = $('<tr>');
                            newRow.append('<td>' + booking.car_id + '</td>');
                            newRow.append('<td>' + booking.title + '</td>');
                            newRow.append('<td>' + booking.attribute_year + '</td>');
                            newRow.append('<td>' + booking.attribute_interior_color + '</td>');
                            newRow.append('<td>' + booking.brand + '</td>');
                            newRow.append('<td>' + booking.registration_number + '</td>');
                            newRow.append('<td>' + booking.start_date + '</td>');
                            newRow.append('<td>' + booking.end_date + '</td>');
                            newRow.append('<td>' + booking.created_at + '</td>');

                            var freeDays = calculateFreeDays(booking.start_date, booking.end_date);
                            newRow.append('<td>' + freeDays + '</td>');

                            bookingTable.append(newRow);
                        });
                    }
                });
            }

            filterData(yearSelector.val(), monthSelector.val());

            function calculateFreeDays(startDate, endDate) {
                var startDateTime = new Date(startDate);
                var endDateTime = new Date(endDate);
                var freeDays = parseInt(new Date(yearSelector.val(), monthSelector.val(), 0).getDate());
                var busydays = 0;

                if(startDateTime.getHours() > 9 && startDateTime.getDate() !== endDateTime.getDate())
                {
                    freeDays++;
                }

                if(endDateTime.getHours() < 21 && startDateTime.getDate() !== endDateTime.getDate())
                {
                    freeDays++;
                }

                while (startDateTime <= endDateTime) {
                    if (
                        startDateTime.getFullYear() == selectedYear &&
                        startDateTime.getMonth() + 1 === selectedMonth
                    ) {
                        if (
                            startDateTime.getHours() >= 9 &&
                            startDateTime.getHours() <= 21
                        ) {
                            busydays++;
                        }
                    }

                    startDateTime.setDate(startDateTime.getDate() + 1);
                }

                return freeDays-busydays;
            }
        });
    </script>
@endsection