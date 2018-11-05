<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Infopanelen</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<!--Font awesome-->

	<!-- <link href="fontawesome-free-5.2.0/css/fontawesome.css" rel="stylesheet">
	<link href="fontawesome-free-5.2.0/css/brands.css" rel="stylesheet">
	<link href="fontawesome-free-5.2.0/css/solid.css" rel="stylesheet">
	<script defer src="fontawesome-free-5.2.0/js/brands.js"></script>
	<script defer src="fontawesome-free-5.2.0/js/solid.js"></script>
	<script defer src="fontawesome-free-5.2.0/js/fontawesome.js"></script> -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">



	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
	<style type="text/css">
		html {
			font-size: 0.8rem;
		}

		body {
			background: #F5F4F0;
		}

		.card-header {
			cursor: pointer;
		}

		.driftinfoHeader span:last-child {
			position: absolute;
			right: 20px;
		}

		#myTab {
			background: #F5F4F0;
		}

	</style>
</head>

<body>
	<ul class="nav nav-tabs fixed-top nav-fill" id="myTab">
		<li class="nav-item">
			<a class="nav-link px-1 py-2 " data-toggle="tab" href="#home">
				<i class="fas fa-info-circle"></i>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link px-1 py-2" data-toggle="tab" href="#phone">
				<i class="fas fa-phone"></i>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link px-1 py-2" data-toggle="tab" href="#metod">
				<i class="fas fa-book"></i>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link px-1 py-2" data-toggle="tab" href="#schema">
				<i class="fas fa-calendar"></i>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link text-danger px-1 py-2" data-toggle="tab" href="#akut">
				<b>AKUT</b>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link text-secondary px-1 py-2" id="navSettings" data-toggle="tab" href="#userSettings">
				<i class="fas fa-cog"></i>
			</a>
		</li>

	</ul>
	<div class="tab-content">
		<div id="home" class="tab-pane fade show active container-fluid mt-5">
			<h4>Om Inforutan</h4>
			<p>I den här rutan kan vi lägga all möjlig information som vi behöver, t.ex. telefonnummer, metodböcker m.m.</p>
			<div class="card">
				<div class="card-header driftinfoHeader bg-danger pt-1 pb-0" data-toggle="collapse" data-target="#akutdriftinfo-body">
					<h5 class="text-white font-weight-bold p-0">Akut Driftinfo <span id="adiHeaderNumber"></span></h5>
				</div>
				<div id="akutdriftinfo-body" class="card-body collapse show driftinfo p-1">
				</div>
				<div data-html="true" class="card-header driftinfoHeader bg-secondary pt-1 pb-0" data-toggle="collapse" data-target="#planeraddriftinfo-body" data-toggle="tooltip"
				 title="Visar 2 dagar framåt;<br/> 14 dagar för RIS/PACS/TakeCare relaterat">
					<h5 class="text-white font-weight-bold p-0">Planerad Driftinfo <span id="pdiHeaderNumber"></span></h5>
				</div>
				<div id="planeraddriftinfo-body" class="card-body collapse driftinfo p-1">
				</div>
				<div class="card-header driftinfoHeader bg-info pt-1 pb-0" data-toggle="collapse" data-target="#ongoingdriftinfo-body">
					<h5 class="text-white font-weight-bold p-0">Pågående Driftinfo <span id="odiHeaderNumber"></span></h5>
				</div>
				<div id="ongoingdriftinfo-body" class="card-body collapse driftinfo p-1">
				</div>
			</div>
		</div>
		<div id="phone" class="tab-pane fade container-fluid mt-5">
			<h4>Telefoni</h4>
			<a href="http://informera.sll.se/catalogue.whtml?search_partuid=6eafc255-9f22-11e5-9b53-24e9b38a9f93" target="_blank" class="btn btn-primary btn-sm">
				<span class="glyphicon glyphicon-earphone"></span>
				Vision 80/20
			</a>
			<a href="http://136.155.84.120/personsokning.html" target="_blank" class="btn btn-primary btn-sm">Personsökning</a>
			<div id="displayUserPhoneNumber" class="mt-2"></div>
			<hr>
			<h6>Sök telefonnummer</h6>
			<div class="form-row">
				<div class="form-group">
					<input type="text" class="form-control form-control-sm" name="dummyPhone" id="dummyPhone" placeholder="Sök..." onclick="openPhoneSearch()">
				</div>
			</div>
		</div>
		<div id="metod" class="tab-pane fade container-fluid mt-5">
			<h4>Metodböcker</h4>

			<div class="btn-toolbar">
				<div class="btn-group mr-1" role="group">
					<a href="http://ks.rontgen.interactit.se/Mod/Mbook/User/" target="_blank" class="btn btn-primary btn-sm">Solna</a>
					<a href="http://gantry.episerverhosting.com/login.aspx?username=huddo&password=huddo2013" target="_blank" class="btn btn-primary btn-sm">Gantry</a>
					<a href="file:///R:/Kar/Wwwarb/Lokal-sida-rtg/Start07/Start_Metoder.htm" target="_blank" class="btn btn-primary btn-sm">Huddinge</a>
				</div>
				<div class="btn-group mr-1">
					<a href="https://sites.google.com/a/neuroradkarolinska.se/neurorad/home" target="_blank" class="btn btn-warning btn-sm">Neuro</a>
					<a href="http://inuti.karolinska.se/Inuti/Verksamheter/Funktioner/Funktion-Bild-och-funktion/Bild-och-Funktion-verksamheter/Barnradiologi/For-oss/Metodbocker/" target="_blank" class="btn btn-warning btn-sm">Barn</a>
				</div>
				<div class="btn-group mr-1">
						<a href="http://www.fysiologen.se/" target="_blank" class="btn btn-info btn-sm">Klinfys</a>
				</div>
			</div>
			<hr>
			<h6>Länkar</h6>
			<a href="https://uniview.bft.sll.se/UniView/#/" class="btn btn-primary btn-sm mb-2" target="_blank">UniView</a>
			<button class="btn btn-primary btn-sm mb-2" id="statdxButton" onclick="statdxSubmit()">StatDx</button>
			<a href="https://app.radprimer.com/login" class="btn btn-primary btn-sm mb-2" target="_blank">RadPrimer</a>
			<a href="http://www.imaios.com" class="btn btn-primary btn-sm mb-2" target="_blank">IMAIOS</a>
			<a href="http://inuti.karolinska.se/Inuti/Verksamheter/Funktioner/Funktion-Bild-och-funktion/Projekt-RISPACS/" target="_blank" class="btn btn-primary btn-sm mb-2">RIS/PACS manualer</a>
			<a href="file:///R:/Kar/WWWrtg/Default.htm" target="_blank" class="btn btn-primary btn-sm mb-2">Huddinges Internsida</a>
			<form hidden>
				<div class="form-group">
					<label>Sök i Gantry</label>
					<input type="text" class="form-control" placeholder="Sök...">
				</div>
			</form>
		</div>
		<div id="schema" class="tab-pane fade container-fluid mt-5">
			<h4>Schema</h4>
			<div class="btn-toolbar">
				<div class="btn-group mr-1">
					<a href="https://schema.medinet.se/ksrtgsolna/schema/sateet" target="_blank" class="btn btn-primary btn-sm">Solna</a>
					<a href="https://schema.medinet.se/ksrtghuddinge/schema/dicom" target="_blank" class="btn btn-primary btn-sm">Huddinge</a>
				</div>
				<div class="btn-group mr-1">
					<a href="https://schema.medinet.se/ksneurorad/schema/neuron" target="_blank" class="btn btn-warning btn-sm">Neuro</a>
					<a href="https://schema.medinet.se/albrtg/schema/SAOsE1nY" target="_blank" class="btn btn-warning btn-sm">Barn</a>
				</div>
				<div class="btn-group mr-1">
					<button class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" data-flip=false>
						Klinfys
					</button>
					<div class="dropdown-menu">
						<a href="https://schema.medinet.se/ksfys/schema/tyokoe" target="_blank" class="dropdown-item">S Läkare</a>
						<a href="file:///S:/Kar/OnkTho/Fyskli/Solna/BMA" target="_blank" class="dropdown-item">S BMA</a>
						<a href="https://schema.medinet.se/fyshe/schema/koetyo" target="_blank" class="dropdown-item">H Läkare</a>
						<a href="file:///S:/Kar/OnkTho/Fyskli/Huddinge/BMA/Veckoschema BMA" target="_blank" class="dropdown-item">H BMA</a>
					</div>
				</div>
			</div>
			<hr>
			<h5>Dygnets jourer</h5>
			<div id="jourLista">
				<ul class="nav nav-pills nav-fill" id="jourTab">
					<li class="nav-item">
						<a id="jourLink0" class="nav-link px-1 py-2" data-toggle="tab" href="#jouryesterday">Igår</a>
					</li>
					<li class="nav-item">
						<a id="jourLink1" class="nav-link px-1 py-2 active" data-toggle="tab" href="#jourtoday">Idag</a>
					</li>
					<li class="nav-item">
						<a id="jourLink2" class="nav-link px-1 py-2" data-toggle="tab" href="#jourtomorrow">Imorn</a>
					</li>
				</ul>
				<div class="tab-content">
					<div id="jouryesterday" class="tab-pane fade">
						<table class="table table-borderless table-sm">
							<tbody id="jourListaBody0">
							</tbody>
						</table>
					</div>
					<div id="jourtoday" class="tab-pane fade active show">
						<table class="table table-borderless table-sm">
							<tbody id="jourListaBody1">
							</tbody>
						</table>
					</div>

					<div id="jourtomorrow" class="tab-pane fade">
						<table class="table table-borderless table-sm">
							<tbody id="jourListaBody2">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div id="akut" class="tab-pane fade container-fluid mt-5">
			<div class="accordion" id="akutgrupp">
				<div class="card">
					<div class="card-header bg-danger py-2" data-toggle="collapse" data-target="#hjartstoppinfo">
						<h5 class="m-0 text-white">
							<span class="fa fa-heartbeat"></span> Hjärtstopp
						</h5>
					</div>
					<div id="hjartstoppinfo" class="collapse" data-parent="#akutgrupp">
						<div class="card-body">
							<ol>
								<li>LARMA tel 6000 </li>
								<li>Påbörja HLR</li>
								<div class="card-body">
									<h5 class="mb-0">30:2</h5>
								</div>
								<li>Hämta och koppla defibrilator</li>
							</ol>
							<h5>Planscher Vuxna</h5>
							<a href="A-HLR_plansch.png" class="btn btn-sm btn-success" target="_blank">A-HLR</a>
							<a href="S-HLR_plansch.png" class="btn btn-sm btn-warning" target="_blank">S-HLR</a>
							<h5>Planscher Barn</h5>
							<a href="A-HLR_Barn_plansch.png" class="btn btn-sm btn-info" target="_blank">Barn A-HLR</a>
							<a href="HLR-Barn_generell.png" class="btn btn-sm btn-primary" target="_blank">Barn HLR Allmänt</a>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header bg-danger py-2" data-toggle="collapse" data-target="#anafylaxiinfo">
						<h5 class="m-0 text-white">
								<span class="fas fa-syringe"></span> Anafylaxi
						</h5>
					</div>
					<div id="anafylaxiinfo" class="collapse" data-parent="#akutgrupp">
						<div class="card-body">
							<ol>
								<li>LARMA tel 6000 </li>
								<li>Adrenalin – Emerade adrenalinpenna intramuskulärt utsida lår 0.3 mg Upprepas vid behov en gång.</li>
								<li>Syrgas 10 L/min på mask</li>
								<li>Sänkt huvudända</li>
								<li>Om medvetslöshet och avsaknad av andning starta HLR.</li>
								<li>Ringer infusion iv</li>
								<li>Tablett Aerius 10 mg</li>
								<li>Tablett Betapred 0.5 mg x 10 alt injektion Betapred intravenöst 2 ml (4mg/ml)</li>
							</ol>
							<a href="kontrast-checklista.html" class="btn btn-sm btn-block btn-warning" target="_blank">Checklista - Kontrastreaktion</a>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header bg-warning py-2" data-toggle="collapse" data-target="#lindrig">
						<h5 class="m-0 text-white">
								<span class="fa fa-medkit"></span> Lindrig kontrastreaktion
						</h5>
					</div>
					<div id="lindrig" class="collapse" data-parent="#akutgrupp">
						<div class="card-body">
							<h5>Isolerade symtom (utan påverkan av ABCD)</h5>
							<ul>
								<li>Lugnt omhändertagnade</li>
								<li>Säkra i.v. infart</li>
								<li>Observation i minst 30 minuter</li>
							</ul>
							<h5>Kliande urticaria (utan påverkan av ABCD)</h5>
							<ul>
								<li>Tablett Aerius 10 mg</li>
								<li>Observation under minst 30 minuter</li>
							</ul>
							<a href="kontrast-checklista.html" class="btn btn-sm btn-block btn-warning" target="_blank">Checklista - Kontrastreaktion</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="userSettings" class="tab-pane fade container-fluid mt-5">
			<h5>Användarinställningar</h5>
			<div id="newUserAlert"></div>
			<form action="set-user-settings.php" id="userSettingsForm" method="POST">
				<div class="form-group">
					<label for="userNameLabel">Användarnamn</label>
					<input type="text" class="form-control" id="userNameInput" value="" name="userName" readonly>
				</div>
				<div class="form-group">
					<label for="userPhonenumber">Mitt telefonnummer</label>
					<input type="text" class="form-control" id="userPhonenumber" placeholder="Telefonnummer" name="phoneNumber1">
					<small id="userPhoneHelp" class="form-text text-muted">Används för att automatiskt kunna göra personsökningar och visas i telefoni-fliken</small>
				</div>
				<div class="form-group">
					<label for="settingStartSida">Startflik</label>
					<select class="custom-select" id="settingStartSida" name="startTab">
						<option value="1">Senast använda flik</option>
						<option value="home">Driftinfo</option>
						<option value="phone">Telefoni</option>
						<option value="metod">Metodbok</option>
						<option value="schema">Schema</option>
						<option value="akut">Akut</option>
					</select>
				</div>
				<div class="form-group" id="chooseOnCall">
					<label for="chooseOnCall">Välj jourlinjer att visa</label><br>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="SolnaOncall" name="chooseOnCall[]" value="Solna">
						<label class="custom-control-label" for="SolnaOncall">Solna</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="KFOncall" name="chooseOnCall[]" value="KF">
						<label class="custom-control-label" for="KFOncall">Klinfys</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="NeuroOncall" name="chooseOnCall[]" value="Neuro">
						<label class="custom-control-label" for="NeuroOncall">Neuro</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="HuddingeOncall" name="chooseOnCall[]" value="Huddinge">
						<label class="custom-control-label" for="HuddingeOncall">Huddinge</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="BarnOncall" name="chooseOnCall[]" value="Barn">
						<label class="custom-control-label" for="BarnOncall">Barn</label>
					</div>
				</div>
				<div id="protectedsettings">
					<div class="form-group">
						<label for="userMedinetPassword">Lösenord till medinet</label>
						<input type="password" class="form-control" id="userMedinetPassword" placeholder="Lösenord" name="medinetPassword">
						<div class="btn-group btn-group-toggle mt-2" data-toggle="buttons" id="chooseSite">
							<label class="btn btn-secondary btn-sm">
							  <input type="radio" name="medinetSite" id="medinetSiteSolnartg" value="Solnartg" autocomplete="off"> Solna
							</label>
							<label class="btn btn-secondary btn-sm">
							  <input type="radio" name="medinetSite" id="medinetSiteHuddingertg" value="Huddingertg" autocomplete="off"> Huddinge
							</label>
							<label class="btn btn-secondary btn-sm">
							  <input type="radio" name="medinetSite" id="medinetSiteNeuro" value="Neuro" autocomplete="off"> Neuro
							</label>
						</div>
						<small id="medinetPasswordInfo" class="form-text text-muted">Används för att automatiskt kunna logga in på medinet via länken under schema</small>
					</div>
					<div class="form-group">
						<label for="statDxUserName">Inloggningsuppgifter till StatDx</label>
						<input type="text" class="form-control" name="statdxusername" id="statDxUserName" placeholder="Användarnamn">
						<input type="password" class="form-control" name="statdxpassword" id="statDxPassword" placeholder="Lösenord">
						<small class="form-text text-muted">Används för automatisk inloggning till StatDx</small>
					</div>
				</div>
				<div id="encryptionkey" class="alert alert-info"><h5>För att kunna spara lösenord till statdx och medinet måste du lägga till en krypteringsnyckel till adressen till infopanel genom att lägga till följande: <code>&key=<?php echo bin2hex(openssl_random_pseudo_bytes(16));?></code></h5></div>
				<button id="saveUserSettings" type="submit" class="btn btn-primary">Spara</button>
			</form>
		</div>
	</div>




	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
	 crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
	 crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
	 crossorigin="anonymous"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/main.js"></script>

</body>

</html>
