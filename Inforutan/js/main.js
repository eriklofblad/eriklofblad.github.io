$(document).ready(function(){
    $.ajaxSetup({ cache: false });
    $('#searchNumber').keyup(function(){
        $('#numberList').html('');
        $('#state').val('');
        var searchField = $('#searchNumber').val();
        if(searchField != ''){
            var expression = new RegExp(searchField, 'i');
            $.getJSON('data.json', function(data) {
                $('#numberList').append('<table class="table table-striped">');
                $('#numberList').append('<thead><tr><th>Namn</th><th>Nummer</th></thead>');
                $('#numberList').append('<tbody>');
                $.each(data, function(key, value){
                    if (value.name.search(expression) != -1)
                        {
                        //här väljer vi vilka noder som ska visas
                        $('#numberList').append('<tr><th scope="row">'+value.name+'</th><td>'+value.phonenumber+'</td></tr>');
                        }
                });
                $('numberList').append('</tbody>');
            });
        };
    });
});