$(document).ready(function(){
    $.ajaxSetup({ cache: false });
    
	
	//Remember selected tab on refresh and between sessions
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if(activeTab){
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    }

	
	
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
	webScraper(); //Run once before the loop
	setInterval(webScraper, 300000); //Run every 5 minutes
	
	/**
	This function works as a webscraper to get information from a local webpage at Karolinska University Hospital.
	It requires the php file "getWebPage.php" which runs two times to be able to fetch the site content of to different sites.
	The result is injected into the divs with id "akutdriftinfo-body" and "planeraddriftinfo-body" within the file index.html
	*/
	function webScraper(){
		var urlBase = 'http://inuti.karolinska.se'; //added to the relative paths scraped from the webpages
		
    /*
	Akut info
	*/
		// Working on hospital network (Uncomment and remove the test link below this link)
		// $.get('/Infopanel/getWebPage.php', {site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Akut-driftinformation/'}, function(html){
		
		//######################### OBS Testing purpose only (Remove on production) OBS ######################
		$.get('/Infopanel/getWebPage.php', {site: 'http://localhost/Infopanel/AkutDriftinformation.htm'}, function(html){
    	//######################### OBS Testing purpose only (Remove on production) OBS ######################    
		
		//Extract the news tag
		var news_elements = $(html).find('.news');

		//Loop through news
		$(news_elements).find('a').each(function() {
			
			//Extract link to news
			var link = $(this).attr('href');
			
			//If not external link add the url base to relative link
			if (!link.startsWith('http')){
				$(this).attr('href',  urlBase + link);
			}
		$(this).attr('target', '_blank');
    });
        $(news_elements).addClass('list-group').removeClass('news');
        $('#akutdriftinfo-body').html(news_elements);
        $('#akutdriftinfo-body .list-group li').addClass('list-group-item bg-warning');
        $('.list-group-item a').addClass('text-danger');
    });
	
	/*
	Planerad info
	*/
		// Working on hospital network (Uncomment and remove the test link below this link)
	    // $.get('/Infopanel/getWebPage.php', {site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Planerad-driftsinformation/'}, function(html){
		
		//######################### OBS Testing purpose only (Remove on production) OBS ######################
		$.get('/Infopanel/getWebPage.php', {site: 'http://localhost/Infopanel/PlaneradDriftinformation.html'}, function(html){
		//######################### OBS Testing purpose only (Remove on production) OBS ######################
		
		//The limit for how many days ahead should be displayed
		var limitDate = new Date($.now());
		limitDate.setDate(limitDate.getDate() + 2); //Today plus 2 days
		
		//Extract the news tag
        var news_elements = $(html).find('.news');
		
		//Loop through news
		$(news_elements).find('a').each(function() {
			
			//Extract the 10 first characters from the date in the news and convert it to Date-format
			var date = new Date($(this).prev().attr('datetime').substring(0,10));
			
			//Remove news if older than limmit 
			if (date > limitDate){
				$(this).parent().remove();
			}
			//Display the news
			else{
				//Extract link to news
				var link = $(this).attr('href');
				
				//If not external link add the url base to relative link
				if (!link.startsWith('http')){
					$(this).attr('href',  urlBase + link);
				}
				$(this).attr('target', '_blank');
				
				//Extract the news information text
				var textInfo = $(this).text();
				
				//If the information text contains pacs/ris/takecare make it orange background with red text
				if(textInfo.toLowerCase().indexOf('pacs')>-1 || textInfo.toLowerCase().indexOf('ris')>-1 || textInfo.toLowerCase().indexOf('takecare')>-1){
					$(this).parent().addClass('list-group-item bg-warning')
					$(this).addClass('text-danger');
				}
				else{
					$(this).parent().addClass('list-group-item bg-light');
					$(this).addClass('text-info');
				}
			}
    });
        $(news_elements).addClass('list-group').removeClass('news');
        $('#planeraddriftinfo-body').html(news_elements);
    });
	}

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