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
	
	var userData

	checkUser();
	
	getOnCallDr2();
	
	/*
	getOnCallDr('https://schema.medinet.se/ksneurorad/schema/neuron', {'#day-79':"#neuroNattJour", '#day-80': "#neuroBakjour"}, ["Primärjour dag", "Bakjour (ö)"], true);
	getOnCallDr('https://schema.medinet.se/ksrtgsolna/schema/sateet', {'#pm-190':"#solnaKvallsjour", '#pm-189':"#solnaKvallsjour", '#pm-8':"#solnaNattjour", '#pm-7':"#solnaNattjour", '#pm-9':"#solnaMellanjour", '#pm-4':"#solnaNattBakjour", '#pm-5':"#solnaDagBakjour", '#pm-6':"#solnaHelgDagjour"}, ["bakjour" ,"mellanjour"], true);
	getOnCallDr('https://schema.medinet.se/ksfys/schema/tyokoe', {'#pm-11':"#kfSkvall", '#pm-12':"#kfShelg"}, ["Helg", "Kranskärlsrond"], true);
	getOnCallDr('https://schema.medinet.se/ksrtghuddinge/schema/dicom', {'#pm-1':"#Hnattjour", '#pm-2':"#Hnattjour", '#pm-3':"#Hnattjour", "#pm-4":"#Hbakjour"}, ["Primärjour typ A", "Traumasökare"], false);
	*/

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


	//Scrape website
	webScraper(); //Run once before the loop
	setInterval(webScraper, 300000); //Run every 5 minutes

	//Show tooltip
	$(document).tooltip({
		container: 'body',
		selector: '[data-toggle]'
	});

	//catch the submitted form and handle with ajax instead.
	$('#userSettingsForm').submit(event, function(){
		submitUserForm();
		event.preventDefault();
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
function keepTabOnReload(inclSetTab) {
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if (activeTab) {
		if(inclSetTab || activeTab != "#userSettings"){
			$('#myTab a[href="' + activeTab + '"]').tab('show');
		}
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
	$.get('getWebPage.php', { site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Akut-driftinformation/', cachetime: 5 }, function (html) {

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
	$.get('getWebPage.php', { site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Planerad-driftsinformation/', cachetime:5 }, function (html) {

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
					a.parent().addClass('list-group-item bg-warning py-0');
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

//Function to parse the username from the query string
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

//Check if a username is specified and if so set the user settings.
function checkUser() {
	userName = getUrlParameter('user') 
    if(userName != ''){
		$("#navSettings").show();
		userFile = "userData/" + userName + ".json"
		userData = $.ajax({
			url: userFile,
			dataType: "json",
			error: function(xhr, status){
				console.log(status);
				console.log('"'+ userName + '" is a new user');
				document.getElementById("userNameInput").value = userName;
				document.getElementById("newUserAlert").className = "alert alert-primary";
				document.getElementById("newUserAlert").innerHTML = "<h4>Välkommen som ny användare</h4><p>Ställ in dina inställningar och tryck sen på spara. Genom att spara godkänner du att den information om dig som du angett sparas på denna server. Du kan när som helst återkomma hit och ta bort dina användarinställningar.</p>"
				document.getElementById("saveUserSettings").innerHTML = "Spara & Godkänn"
				$('#myTab a[href="#userSettings"]').tab('show');
			},
			success: function(){
				console.log('User "' + userName +  '" already excists');
				populateUserSettings(false);
			}
		});
    }else{
		console.log('no user');
		//Remember selected tab on refresh and between sessions
		keepTabOnReload(false);
    }
}

//This function is run when a known user logs on and applies that users settings.
function populateUserSettings(repopulate){
	console.log(repopulate);
	
	$.each(userData.responseJSON, function(key, value){
		if(key != "medinetSite"){
			$("[name=" + key + "]").val(value);
		}
		
	});
	if(userData.responseJSON.phoneNumber1 != ""){
		document.getElementById("displayUserPhoneNumber").innerHTML = '<div class="alert alert-secondary">Ditt telefonnummer är ' + userData.responseJSON.phoneNumber1 + '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>';
	}
	
	if(userData.responseJSON.medinetSite != null){
		selectSite = "medinetSite" + userData.responseJSON.medinetSite;
		document.getElementById(selectSite).checked = true;
		document.getElementById(selectSite).parentNode.classList.add("active");
	}
	
	document.getElementById("SDusername").value = userData.responseJSON.statdxusername;
	document.getElementById("SDpassword").value = userData.responseJSON.statdxpassword;

	if(repopulate === false){
		//if the user has set a specific start tab, start there. Otherwise start with the last tab.
		if(userData.responseJSON.startTab == "1"){
			//Remember selected tab on refresh and between sessions
			keepTabOnReload(true);
		}else{
			$('#myTab a[href="#' + userData.responseJSON.startTab + '"]').tab('show');
		}
	}
	
	
}

function repopulateUserSettings(){
	userName = getUrlParameter('user')
	userFile = "userData/" + userName + ".json"
	userData = $.ajax({
		url: userFile,
		dataType: "json",
		error: function(xhr, status){
			console.log(status);
		},
		success: function(){
			console.log('Reloading user settings');
			populateUserSettings(true);
		}
	});
}

function submitUserForm(){
	var formElement = document.getElementById('userSettingsForm');
	var formData = new FormData(formElement);
	$.ajax({
		type: 'POST',
		url: 'set-user-settings.php',
		data: formData,
		processData: false,
		contentType: false,
	}).done(function(data){
		if(data.startsWith("Success")){
			$('#userSettingsForm').append('<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert" id="postAlert">Inställningar sparade</div>');
			setTimeout(function(){
				$("#postAlert").alert('close');
			}, 5000);
			console.log(data);
			repopulateUserSettings();
		}else{
			failAlert();
			console.log(data);
		}
		
	}).fail(function(){
		failAlert();
	});
}

function failAlert(){
	$('#userSettingsForm').append('<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert" id="postAlert">Misslyckades med att spara dina instälningar</div>');
	setTimeout(function(){
		$("#postAlert").alert('close');
	}, 5000);
}



function getOnCallDr(medinetSite, positionAndElement,medinetcuts, getUserList){
	if(medinetcuts != undefined){
		var getWebPage = encodeURI("getWebPage.php?site="+medinetSite+"&medinetcut1="+medinetcuts[0]+"&medinetcut2="+medinetcuts[1]+"&cachetime=180");
	}else{
		var getWebPage = encodeURI("getWebPage.php?site="+medinetSite+"&cachetime=180");
	}
	$.get(getWebPage, function (htmlData) {
		var medinetUserSite = medinetSite + "/menu/users"
		var d = new Date(Date.now());
		var dateString = d.toISOString().split("T");
		var htmlData2 = $(htmlData);
		//console.log(htmlData2);
		if(getUserList){
			$.get('getWebPage.php', { site: medinetUserSite, cachetime: 2880 }, function(html){
				var html2 = $(html);
				$.each(positionAndElement, function(position, elementId){			
					var selectElement = position + "-" + dateString[0] + " td";
					var onCallDrAbr = $(htmlData2).find(selectElement).html()
					if(onCallDrAbr != undefined && onCallDrAbr != "&nbsp;"){
						var KFmedinet = medinetSite.indexOf("tyokoe");
						if( KFmedinet != -1){
							var onCallDrAbr = onCallDrAbr.charAt(0) + onCallDrAbr.charAt(2);
						}
						console.log(onCallDrAbr);
						var o = $(html2).find("td:contains(" + onCallDrAbr + ")");
						var insertparent = o.prev().html();
						var insertchild = o.prev().children().html();
						if(insertchild != undefined){
							insert = insertchild;
						}else if(insertparent != undefined){
							insert = insertparent;
						}else{
							console.log("Ingen jour funnen");
							return;
						}
						console.log(insert);

						insert2 = insert.split(",");
						$(elementId).append("<td>" + insert2[1] + " " + insert2[0] + "</td>");

						$(elementId).show();			
					}
				});
			});
		}else{
			$.each(positionAndElement, function(position, elementId){			
				var selectElement = position + "-" + dateString[0] + " td";
				console.log(selectElement);
				var onCallDr = $(htmlData2).find(selectElement).html();
				console.log(onCallDr);
				if(onCallDr != undefined){
					$(elementId).append("<td>" + onCallDr + "</td>").show();
				}
				
			});
		}
	});
}

function getOnCallDr2(){
	$.getJSON('getJourer.php', function(data){
		$.each(data, function(site, jourarray){
			$.each(jourarray, function(index, jour){
				$("#jourListaBody").append(
					'<tr class="' + site + '"><td>' + site + " " + jour.jourtyp + " " + jour.jourtod + " " + jour.starttime + "-" + jour.stopptime +  '</td><td>' + jour.journamn + '</td></tr>'
				);
			});
		});
	});
}