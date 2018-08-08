$(document).ready(function(){
    $.ajaxSetup({ cache: false });

    $(document).tooltip({
        container: 'body',
        selector: '[data-toggle]'
    });

    $.ajax({
        url: 'https://eriklofblad.github.io/Inforutan/driftinfo_3.htm',
        dataType: "html",
        context: document.body,
        success: function(data){
            //console.log(data);
            //console.log(initial);
            var split1 = data.split('<section class="news-list">');
            //onsole.log(split1[1]);
            var split2 = split1[1].split('</section>');
            var news_elements = $(split2[0]);
            $(news_elements).addClass('list-group').removeClass('news');
            //console.log(news_elements);
            $('#driftinfo-body').append(news_elements);
            $(".list-group li").addClass('list-group-item bg-warning');
            $(".list-group-item a").addClass('text-danger');
        }
    });

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