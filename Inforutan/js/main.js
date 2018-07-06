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
                        $('#numberList').append('<li class="list-group-item">'+value.name+' | <span class="text-muted">'+value.phonenumber+'</span></li>');
                        }
                });   
            });
        };
    });
});