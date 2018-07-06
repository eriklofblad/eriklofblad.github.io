$(document).ready(function(){
    $('#searchNumber').on('keyup', function(e){
        let phonename = e.target.value;

        $("#numberList").load("test.txt");
    });
});