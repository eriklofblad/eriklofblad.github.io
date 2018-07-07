$(document).ready(function(){
    
    
    /*
    $('[data-toggle="tooltip"]').tooltip(
        {
            //verkar som att man behöver få option "selector:" att fungera för att få till snygg formatering av tooltips som kommer från dynamiskt insatt DOM
        });
    */
    $.ajaxSetup({ cache: false });
    $('#searchNumber').keyup(function(){
        $('#numberList').html('');
        $('#state').val('');
        var searchField = $('#searchNumber').val();
        if(searchField != ''){
            var expression = new RegExp(searchField, 'i');
            $.getJSON('data.json', function(data) {
                $('#numberList').html('<thead><tr><th>Namn</th><th>Nummer</th><th>Roll</th><th>FO</th></thead>');
                $('#numberList').append('<tbody>');
                $.each(data, function(key, value){
                    if (value.name.search(expression) != -1)
                        {
                        //här väljer vi vilka noder som ska visas
                        $('#numberList').append('<tr><th id="erikstest" scope="row" data-toggle="tooltip" title="'+value.description+'">'+value.name+'</th><td>'+value.phonenumber+'</td><td>'+value.type+'</td><td>'+value.organisation+'</td></tr>');
                        }
                });
                $('#numberList').append('</tbody>');
            });
        };
    });
});