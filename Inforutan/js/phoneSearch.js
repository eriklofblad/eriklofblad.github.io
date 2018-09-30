var jsondata;

$(document).ready(function(){
    document.getElementById("searchNumber").focus();

    //Search for phonenumbers
    $.getJSON('telefonbok_3.json', function (data) {
        jsondata = data;
    });
	$('#searchNumber').keyup(function () {
		$('#numberList').html('');
		//$('#state').val('');
		var searchField = $('#searchNumber').val();
		if (searchField != '') {
			var expression = new RegExp(searchField, 'i');
			
            $('#numberList').html('<thead><tr><th>Namn</th><th>Nummer</th><th>Roll</th><th>FO</th></thead>');
            $('#numberList').append('<tbody>');
            $.each(jsondata, function (key1, value1) {
                $.each(value1, function (key, value) {
                    if (value.name.search(expression) != -1) {
                        //här väljer vi vilka noder som ska visas
                        $('#numberList').append('<tr><th id="erikstest" scope="row" data-toggle="tooltip" title="' + value.description + '">' + value.name + '</th><td>' + value.phonenumber + '</td><td>' + value.type + '</td><td>' + value.organisation + '</td></tr>');
                    }
                });
            });
            $('#numberList').append('</tbody>');
		}
	});
});