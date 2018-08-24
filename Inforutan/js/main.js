$(document).ready(function () {
	$.ajaxSetup({ cache: false });

	/**
	Polyfill for missing function startsWith within Internet Explorer 
	*/
	if (!String.prototype.startsWith) {
		String.prototype.startsWith = function (search, pos) {
			return this.substr(!pos || pos < 0 ? 0 : +pos, search.length) === search;
		};
    }
    
    userName = getUrlParameter('user') 
    if(userName != ''){
        console.log(userName);
    }else{
        console.log('no user')
    }

	/**
	Hide collapseable card if pressed anywhere on the card
	*/
	// 	$('.card').on('click', function(event) {
	// 		var target = $(event.target).children('a').attr('href');
	// 		$(target).collapse('toggle');
	//    });

	/**
	Hide collapseable card if pressed anywhere on the card
	*/
	$('.card-header').on('click', function (event) {
		var target = $(event.target).attr('data-target');
		$(target).collapse('toggle');
	});


	//Remember selected tab on refresh and between sessions
	keepTabOnReload();

	//Scrape website
	webScraper(); //Run once before the loop
	setInterval(webScraper, 300000); //Run every 5 minuteske

	//Show tooltip
	$(document).tooltip({
		container: 'body',
		selector: '[data-toggle]'
	});

	//Search for phonenumbers
	$('#searchNumber').keyup(function () {
		$('#numberList').html('');
		//$('#state').val('');
		var searchField = $('#searchNumber').val();
		if (searchField != '') {
			var expression = new RegExp(searchField, 'i');
			$.getJSON('telefonbok_3.json', function (data) {
				$('#numberList').html('<thead><tr><th>Namn</th><th>Nummer</th><th>Roll</th><th>FO</th></thead>');
				$('#numberList').append('<tbody>');
				$.each(data, function (key1, value1) {
					$.each(value1, function (key, value) {
						if (value.name.search(expression) != -1) {
							//här väljer vi vilka noder som ska visas
							$('#numberList').append('<tr><th id="erikstest" scope="row" data-toggle="tooltip" title="' + value.description + '">' + value.name + '</th><td>' + value.phonenumber + '</td><td>' + value.type + '</td><td>' + value.organisation + '</td></tr>');
						}
					});
				});
				$('#numberList').append('</tbody>');
			});
		};
	});

});

/**
Remember selected tab on refresh and between sessions
Requires nav-tab to have myTab ID and the tabs to have the datatoggle "tab"
*/
function keepTabOnReload() {
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if (activeTab) {
		$('#myTab a[href="' + activeTab + '"]').tab('show');
	}
}

/**
This function works as a webscraper to get information from a local webpage at Karolinska University Hospital.
It requires the php file "getWebPage.php" which runs two times to be able to fetch the site content of to different sites.
The result is injected into the divs with id "akutdriftinfo-body" and "planeraddriftinfo-body" within the file index.html
*/
function webScraper() {
	var urlBase = 'http://inuti.karolinska.se'; //added to the relative paths scraped from the webpages

	//Counters for listitems
	var numAcute = 0;
	var numPlaned = 0;
	var numOngoing = 0;

	/*
	Akut info
	*/
	// Working on hospital network (Uncomment and remove the test link below this link)
	$.get('/Infopanel/getWebPage.php', { site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Akut-driftinformation/' }, function (html) {

	//######################### OBS Testing purpose only (Remove on production) OBS ######################
	// $.get('/Infopanel/getWebPage.php', { site: 'http://localhost/Infopanel/AkutDriftinformation.html' }, function (html) {
		//######################### OBS Testing purpose only (Remove on production) OBS ######################    

		//Extract the news tag
		var news_elements = $(html).find('.news');

		//Loop through news if there are any news
		if (news_elements.length > 0) {
			$(news_elements).find('a').each(function () {

				//Count number of list items
				numAcute++;

				//Extract link to news
				var link = $(this).attr('href');

				//If not external link add the url base to relative link
				if (!link.startsWith('http')) {
					$(this).attr('href', urlBase + link);
				}
				$(this).attr('target', '_blank');
			});
			$(news_elements).addClass('list-group').removeClass('news');
			$('#akutdriftinfo-body').html(news_elements);
			$('#akutdriftinfo-body .list-group li').addClass('list-group-item bg-warning py-0');
			$('.list-group-item a').addClass('text-danger');
		}
		//Display number of list items
		$('#adiHeaderNumber').text(numAcute);
	});

	/*
	Planerad/Ongoing info
	*/
	// Working on hospital network (Uncomment and remove the test link below this link)
	$.get('/Infopanel/getWebPage.php', { site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Planerad-driftsinformation/' }, function (html) {

	//######################### OBS Testing purpose only (Remove on production) OBS ######################
	// $.get('/Infopanel/getWebPage.php', { site: 'http://localhost/Infopanel/PlaneradDriftinformation.html' }, function (html) {
		//######################### OBS Testing purpose only (Remove on production) OBS ######################

		//The limit for how many days ahead should be displayed
		var todaysDate = new Date($.now());
		var firstDateLimit = new Date(); //Limits how long ahead to search for RIS/PACS/takecare
		var secondDateLimit = new Date(); //Limits how long ahead to display other news

		firstDateLimit.setDate(todaysDate.getDate() + 14); //Today plus 14 days (Rember to change tooltip if changed)
		secondDateLimit.setDate(todaysDate.getDate() + 2); //Today plus 2 days (Rember to change tooltip if changed)

		//Extract the news tag
		var news_elements = $(html).find('.news');

		//Ongoing news container
		var news_elements_Ongoing = $.parseHTML('<ul></ul>');

		//Loop through news
		$(news_elements).find('a').each(function () {

			//Anchor-tag
			a = $(this);

			//Extract the 10 first characters from the date in the news and convert it to Date-format
			var date = new Date(a.prev().attr('datetime').substring(0, 10));

			//Remove news if older than the first limit 
			if (date > firstDateLimit) {
				a.parent().remove();
			}

			//Check for RIS/PACS/takecare
			else {

				//Extract the news information text
				var textInfo = a.text();

				//If the information text contains PACS/RIS/takecare make it orange background with red text
				if (textInfo.indexOf('PACS') > -1 || textInfo.indexOf('RIS') > -1 || textInfo.toLowerCase().indexOf('takecare') > -1) {
					a.parent().addClass('list-group-item bg-warning py-0')
					a.addClass('text-danger');
				}

				//Remove if older than secondDateLimit and do not contain RIS/PACS/takecare
				else if (date > secondDateLimit) {
					a.parent().remove();
					return true; //jQuery's equivalent to continue in a regular loop (=skip to next iteration)
				}
				else {
					a.parent().addClass('list-group-item bg-light py-0');
					a.addClass('text-info');
				}

				//Extract link to news
				var link = a.attr('href');

				//If not an external link add the url base to relative link
				if (!link.startsWith('http')) {
					a.attr('href', urlBase + link);
				}
				a.attr('target', '_blank');

				//Check if the information is ongoing
				if (date < todaysDate) {
					$(news_elements_Ongoing).append(a.parent().clone()).html();
					//Count number of list items
					numOngoing++;
					//Remove from planned list
					a.parent().remove();
				}
				else {
					//Count number of list items
					numPlaned++;
				}
			}
		});
		$(news_elements).addClass('list-group').removeClass('news');
		$('#planeraddriftinfo-body').html(news_elements);
		$(news_elements_Ongoing).addClass('list-group')
		$('#ongoingdriftinfo-body').html(news_elements_Ongoing);

		//Display number of list items
		$('#pdiHeaderNumber').text(numPlaned);
		$('#odiHeaderNumber').text(numOngoing);
	});
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};
