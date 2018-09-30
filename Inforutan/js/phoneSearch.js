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
			
            $('#numberList').html('<thead><tr><th>Namn</th><th>Nr.</th><th>Roll</th><th>FO</th></thead>');
            $('#numberList').append('<tbody>');
            $.each(jsondata, function (key1, value1) {
                $.each(value1, function (key, value) {
                    if (value.name.search(expression) != -1) {
                        //här väljer vi vilka noder som ska visas
                        $('#numberList').append('<tr class="p-1"><th id="erikstest" class="p-1" scope="row" data-toggle="tooltip" title="' + value.description + '">' + value.name + '</th><td class="p-1">' + value.phonenumber + '</td><td class="p-1">' + value.type + '</td><td class="p-1">' + value.organisation + '</td></tr>');
                    }
                });
            });
            $('#numberList').append('</tbody>');
		}
	});
});