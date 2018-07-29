$(document).ready(function(){
    $.ajaxSetup({ cache: false });
    
    /*
    //Test för att parsa komplexa JSON
    $.getJSON('telefonbok_2.json', function(json) {
        $.each(json, function(key, value){
            $('#home').append('<h3>'+key+'</h3>')
            $.each(value, function(key2, value2){
                $('#home').append('<p>Phonenumber: '+value2.name+'</p>')
            });
        });
    });
    */

    $(document).tooltip({
        container: 'body',
        selector: '[data-toggle]'
    });
    /*
    $('#hjartstopp').click(function(){
        //console.log("Hjärtstopp klickad");
        $('#anafylaxiinfo').hide();
        $('#lindrig').hide();
        $('#hjartstoppinfo').show();
    });

    $('#anafylaxi').click(function(){
        //console.log("Anafylaxi klickad");
        $('#hjartstoppinfo').hide();
        $('#lindrig').hide();
        $('#anafylaxiinfo').show();
        
    });

    $('#kontrastreak').click(function(){
        //console.log("Anafylaxi klickad");
        $('#hjartstoppinfo').hide();
        $('#anafylaxiinfo').hide();
        $('#lindrig').show();
    });
    */
   
    $('#searchNumber').keyup(function(){
        $('#numberList').html('');
        //$('#state').val('');
        var searchField = $('#searchNumber').val();
        if(searchField != ''){
            var expression = new RegExp(searchField, 'i');
            $.getJSON('telefonbok_3.json', function(data) {
                $('#numberList').html('<thead><tr><th>Namn</th><th>Nummer</th><th>Roll</th><th>FO</th></thead>');
                $('#numberList').append('<tbody>');
                $.each(data, function(key1, value1){
                    $.each(value1, function(key, value){
                        if (value.name.search(expression) != -1)
                            {
                            //här väljer vi vilka noder som ska visas
                            $('#numberList').append('<tr><th id="erikstest" scope="row" data-toggle="tooltip" title="'+value.description+'">'+value.name+'</th><td>'+value.phonenumber+'</td><td>'+value.type+'</td><td>'+value.organisation+'</td></tr>');
                            }
                    });
                });
                $('#numberList').append('</tbody>');
            });
        };
    });
});