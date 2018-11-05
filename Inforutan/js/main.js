var userData;
var searchWindow;

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

	//Checks if a username is supplied in the query, if it is it gets the user settings and populates them on the page

	checkUser(false);

	//Toggle cards if pressed anywhere on the card header

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
});

/**
Remember selected tab on refresh and between sessions
Requires nav-tab to have myTab ID and the tabs to have the datatoggle "tab"
*/
function keepTabOnReload() {
	$('#myTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
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
	$.get('getWebPage.php', { site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Akut-driftinformation/', cachetime: 5, newscut: "true" }, function (html) {

	//######################### OBS Testing purpose only (Remove on production) OBS ######################
	//$.get('getWebPage.php', { site: 'http://localhost/AkutDriftinformation.htm', cachetime: 5, newscut: "true" }, function (html) {
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
	$.get('getWebPage.php', { site: 'http://inuti.karolinska.se/Driftinformation/Driftinformation/Planerad-driftsinformation/', cachetime:5, newscut: "true" }, function (html) {

	//######################### OBS Testing purpose only (Remove on production) OBS ######################
	// $.get('/Infopanel/getWebPage.php', { site: 'http://localhost/Infopanel/PlaneradDriftinformation.html' }, function (html) {
		//######################### OBS Testing purpose only (Remove on production) OBS ######################

		//The limit for how many days ahead should be displayed
		var todaysDate = new Date();
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

//Function to parse strings from the query string
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

//Check if a username is specified and if so set the user settings.
function checkUser(repopulate) {
	userName = getUrlParameter('user');
	userKey = getUrlParameter('key');
	if(userKey == ""){
		document.getElementById("protectedsettings").innerHTML = '';
		$("#encryptionkey").show();
	}else{
		$("#encryptionkey").hide();
	}
    if(userName != ''){
		userData = $.ajax({
			url: "user-settings.php",
			data: {user: userName},
			dataType: "json",
		}).done(function(data){
			if(data.status == "success"){
				console.log('User "' + userName +  '" already excists');
				populateUserSettings(repopulate);
			}else{
				console.log(data.statusText);
				console.log('"'+ userName + '" is a new user');
				newUser(userName);
			}
		}).fail(function(jqXHR, textStatus, errorThrown){
			console.log('error retreiving user status');
			console.log("Status= " + textStatus);
			document.getElementById("userSettingsForm").innerHTML = "";
			document.getElementById("newUserAlert").className = "alert alert-danger";
			document.getElementById("newUserAlert").innerHTML = '<h4>Varning!</h4><p>Lyckades ej kontrollera användarens status</p>';
		});
    }else{
		console.log('no user');
		document.getElementById("userSettingsForm").innerHTML = "";
		document.getElementById("newUserAlert").className = "alert alert-primary";
		document.getElementById("newUserAlert").innerHTML = '<h4>Skapa konto</h4><p>För att kunna göra användarinställingar måste du skapa ett konto. Detta gör du genom att gå in i <kbd>inställningar/avancerade inställningar/</kbd> och söka efter infopanel. I slutet av den angivna adressen lägger du till <kbd>?user=*ditt HSAID*</kbd> (4 tecken). När du sen stänger ner inställningsfönstret så kommer det i den här rutan dyka upp flera inställningar.</p>';
		noUser();
	}
}

//This function is run when a known user logs on and applies that users settings.
function populateUserSettings(repopulate){
	console.log(repopulate);

	$.each(userData.responseJSON.userData, function(key, value){
		if(key != "medinetSite" && key != "chooseOnCall"){
			$("[name=" + key + "]").val(value);
		}else if(key == "_id"){
			//do nothing
		}

	});
	if(userData.responseJSON.userData.phoneNumber1 != ""){
		document.getElementById("displayUserPhoneNumber").innerHTML = '<div class="alert alert-secondary py-2">Ditt telefonnummer är ' + userData.responseJSON.userData.phoneNumber1 + '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>';
	}

	if(userData.responseJSON.userData.chooseOnCall != undefined){
		$.each(userData.responseJSON.userData.chooseOnCall, function(index, site){
			onCallSite = "#" + site + "Oncall";
			$(onCallSite).prop("checked", true);
		});
		getOnCallDr(userData.responseJSON.userData.chooseOnCall);
	}else{
		onCallSites = ["Solna","Neuro","KF","Huddinge","Barn"]
		$.each(onCallSites, function(index, site){
			onCallSite = "#" + site + "Oncall";
			$(onCallSite).prop("checked", true);
		});
		getOnCallDr(onCallSites);
	}

	if(userData.responseJSON.userData.medinetSite != null){
		selectSite = "medinetSite" + userData.responseJSON.userData.medinetSite;
		document.getElementById(selectSite).checked = true;
		document.getElementById(selectSite).parentNode.classList.add("active");
	}

	if(repopulate === false){
		//if the user has set a specific start tab, start there. Otherwise start with the last tab.
		if(userData.responseJSON.userData.startTab == "1"){
			//Remember selected tab on refresh and between sessions
			keepTabOnReload();
		}else{
			$('#myTab a[href="#' + userData.responseJSON.userData.startTab + '"]').tab('show');
		}
	}


}


function newUser(userName){
	document.getElementById("userNameInput").value = userName;
	document.getElementById("newUserAlert").className = "alert alert-primary";
	document.getElementById("newUserAlert").innerHTML = "<h4>Välkommen som ny användare</h4><p>Ställ in dina inställningar och tryck sen på spara. Genom att spara godkänner du att den information om dig som du angett sparas på denna server. Du kan när som helst återkomma hit och ta bort dina användarinställningar.</p>";
	document.getElementById("saveUserSettings").innerHTML = "Spara & Godkänn";
	$('#myTab a[href="#userSettings"]').tab('show');
	onCallSites = ["Solna","Neuro","KF","Huddinge","Barn"];
	$.each(onCallSites, function(index, site){
		onCallSite = "#" + site + "Oncall";
		$(onCallSite).prop("checked", true);
	});
	getOnCallDr(onCallSites);
}

function noUser(){
	//Remember selected tab on refresh and between sessions
	keepTabOnReload();
	onCallSites = ["Solna","Neuro","KF","Huddinge","Barn"];

	getOnCallDr(onCallSites);
}

function submitUserForm(){
	var formElement = document.getElementById('userSettingsForm');
	var formData = new FormData(formElement);
	var userKeyval = getUrlParameter('key');
	if(userKeyval != ""){
		formData.append("userKey", userKeyval);
	}
	$.ajax({
		type: 'POST',
		url: 'user-settings.php',
		data: formData,
		processData: false,
		contentType: false,
	}).done(function(data){
		if(data.status == "success"){
			$('#userSettingsForm').append('<span class="alert alert-success p-2 alert-trim alert-dismissible fade show" role="alert" id="postAlert">Inställningar sparade</span>');
			setTimeout(function(){
				$("#postAlert").alert('close');
			}, 5000);
			console.log(data);
			checkUser(true);
			document.getElementById("saveUserSettings").innerHTML = "Spara";
		}else{
			failAlert(data.statusText);
			console.log(data);
		}

	}).fail(function(){
		failAlert("Okänt fel");
	});
}

function failAlert(failText){
	$('#userSettingsForm').append('<span class="alert alert-danger alert-trim alert-dismissible fade show" role="alert" id="postAlert">' + failText + '</span>');
	setTimeout(function(){
		$("#postAlert").alert('close');
	}, 5000);
}


function getOnCallDr(getSites){

	var jourRequest = 'getJourer_DB.php?';
	$.each(getSites, function(index, site){
		jourRequest += "site[]=" + site + "&";
	});
	var d = new Date();
	console.log(createDateString(d));
	d.setHours(d.getHours()-7);
	jourRequest += "centerdate=" + createDateString(d);
	var intDate = d.getDate()-1;
	for(i=0; i<3; i++){
		d.setDate(intDate+i);
		var datestring = createDateString(d);
		var jourbody = document.getElementById("jourListaBody"+i);
		jourbody.setAttribute("data-date", datestring);
		jourbody.innerHTML = "";
		var jourlink = document.getElementById("jourLink"+i);
		jourlink.setAttribute("data-date", datestring);
		jourlink.innerHTML = d.getDate() + "/" + (d.getMonth()+1);
	}

	d = new Date();

	document.querySelector('a[data-date="' + createDateString(d)+'"]').insertAdjacentHTML('beforeend', ' <span class="badge badge-light">Idag</span>');
	$.ajax({
		type: 'GET',
		url: jourRequest,
		dataType: "json"
	}).done(function(data){
		$.each(data, function(index, jour){
			if(jour.jourtyp == "Bakjour" || jour.jourtyp == "Mellanjour"){
					var jourtypfiltered = jour.jourtyp + " ";
			}else{
				var jourtypfiltered = jour.jourtod + "jour ";
			}
			var query = 'tbody[data-date="' + jour.startdate +'"]'
			document.querySelector(query).insertAdjacentHTML('beforeend','<tr class="' + jour.site + '"><td>' + jour.site + " " + jourtypfiltered + jour.starttime + "-" + jour.stopptime +  '</td><td>' + jour.journamn + '</td></tr>');

		});
	});
}

function statdxSubmit(){
	if(userData === undefined){
		window.open("https://app.statdx.com/login");
	}else{
		userdatauser = getUrlParameter('user');
		userdatakey = getUrlParameter('key');
		statdxusername = userData.responseJSON.userData.statdxusername;
		statdxpassword = userData.responseJSON.userData.statdxpassword;
		if(statdxusername != "" && statdxpassword == "PlaceHolder" && userdatauser != "" && userdatakey != ""){
			$.ajax({
				url: "getPassword.php",
				method: "GET",
				data: {
					user: userdatauser,
					userKey: userdatakey,
					pwtype: "statdxpassword"
				},
				dataType: "json",
			}).done(function(data){
				if(data.status == "success"){
					form = '<form id="statdxform" action="https://app.statdx.com/login" method="POST" target="_blank" hidden><input type="text" name="username" value="' + statdxusername + '"><input type="password" name="password" value="' + data.statdxpassword +'"></form>';
					$(form).appendTo("#metod").submit().remove();
				}else{
					window.open("https://app.statdx.com/login");
				}

			}).fail(function(){
				window.open("https://app.statdx.com/login");
			});
		}else{
			window.open("https://app.statdx.com/login");
		}
	}
}

function openPhoneSearch(){
	if(!searchWindow || searchWindow.closed){
		searchWindow = window.open('phone.html',"_blank", "width=400, height=600, resizable=yes, scrollbars=yes");
	}else{
		searchWindow.focus();
		searchWindow.document.getElementById("searchNumber").focus();
	}

}

function createDateString(dateObject){
	year = dateObject.getFullYear();
	month = dateObject.getMonth()+1;
	if(month < 10){
		month= "0" + month;
	}
	day = dateObject.getDate();
	if(day < 10){
		day= "0" + day;
	}

	return year + "-" + month + "-" + day;
}