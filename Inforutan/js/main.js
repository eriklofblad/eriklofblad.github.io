$(document).ready(function(){
    $.ajaxSetup({ cache: false });
    $('#searchNumber').keyup(function(){
        $('#numberList').html('');
        $('#state').val('');
        var searchField = $('#searchNumber').val();
        if(searchField != ''){
            var expression = new RegExp(searchField, 'i');
            $.getJSON('data.json', function(data) {
                $.each(data, function(key, value){
                    if (value.name.search(expression) != -1)
                        {
                        //här väljer vi vilka noder som ska visas
                        $('#numberList').append('<li class="list-group-item">'+value.name+' | <span class="text-muted">'+value.phonenumber+value.type+value.organisation+'</span></li>');
                        }
                });   
            });
        };
    });
});