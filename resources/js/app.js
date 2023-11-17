import './bootstrap.js';
import $ from "jquery";

let yearSelector = $('#yearSelector');
let monthSelector = $('#monthSelector');
let bookingTable = $('#bookingTable tbody');

yearSelector.change(function () {
    let year = yearSelector.val();
    let month = monthSelector.val();
    filterData(year, month);
});

monthSelector.change(function () {
    let year = yearSelector.val();
    let month = monthSelector.val();
    filterData(year, month);
});

function filterData(year, month) {
    bookingTable.html('');
    $.ajax({
        type: 'GET',
        url: '/get-bookings?year='+year+'&month='+month,
        success: function(data){
            console.log(data);

            if (data.length === 0) {
                bookingTable.append('<tr><td colspan="11">No cars were booked in the period you selected.</td></tr>');
            } else {
                $.each(data, function (index, booking) {
                    let newRow = $('<tr>');
                    newRow.append('<td>' + booking.car_id + '</td>');
                    newRow.append('<td>' + booking.title + '</td>');
                    newRow.append('<td>' + booking.attribute_year + '</td>');
                    newRow.append('<td>' + booking.attribute_interior_color + '</td>');
                    newRow.append('<td>' + booking.brand + '</td>');
                    newRow.append('<td>' + booking.registration_number + '</td>');
                    newRow.append('<td>' + booking.created_at + '</td>');
                    newRow.append('<td>' + booking.free + '</td>');

                    bookingTable.append(newRow);
                });
            }
        },
        error: function (error){
            console.log(error);
        }
    });
}

filterData(yearSelector.val(), monthSelector.val());
