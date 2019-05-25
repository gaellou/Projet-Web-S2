const HOST_PATH = "https://perso-etudiant.u-pem.fr/~pthiel/imac_toile_2/";

Document.prototype.ready = callback => {
	if(callback && typeof callback === 'function') {
		document.addEventListener("DOMContentLoaded", () =>  {
			if(document.readyState === "interactive" || document.readyState === "complete") {
				return callback();
			}
		});
	}
};

var tabNomsVilles = [];
var tabCodesPostauxVilles = [];
var tabIdVilles = [];

document.ready( () => {
	fetch(HOST_PATH+"api/ville/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let villes = document.getElementById('list-ville');
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					for(var item2 in obj[item]){
						for(var item3 in obj[item][item2]){
							var ville = obj[item][item2][item3];
							if(item3=="nom"){
								var AUnNom = (ville != "" || ville!=null);
								tabNomsVilles.push(ville);
								
								var valeur = ville;
							}
							if(item3=="id"){
								tabIdVilles.push(ville);
							}
							if(item3=='code_postal'){
								tabCodesPostauxVilles.push(ville);
								valeur = valeur.concat(" ("+ville+")");
							}
						}
						if(AUnNom){
								var list = document.createElement("option");
								list.value = valeur;
								var itmText = document.createTextNode(valeur);
								list.appendChild(itmText);
								villes.appendChild(list);
						}
					}				
				}
			}
		})
		.catch(error => { console.log(error) });
});

var tabNomsGenres = [];
var tabIdGenres = [];

let genres = document.getElementById('list-genre');

document.ready( () => {
	fetch(HOST_PATH+"api/genre/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					for(var item2 in obj[item]){
						for(var item3 in obj[item][item2]){
							var genre = obj[item][item2][item3];
							if(item3=="nom"){
								tabNomsGenres.push(genre);
								var checkbox = document.createElement("input");
								checkbox.type = "checkbox";
								checkbox.name = "genre";
								checkbox.value = genre
								checkbox.id = "input-checkbox-" + genre.toLowerCase();
								
								let label = document.createElement("label")
								label.htmlFor = "input-checkbox-" + genre.toLowerCase();
								label.innerHTML = genre;
				
								let li  = document.createElement("li");
								li.appendChild(checkbox);
								li.appendChild(label);
	
								genres.appendChild(li);
							}
							if(item3=="id"){
								tabIdGenres.push(genre);
							}
						}
					}
				}
			}
		})
		.catch(error => { console.log(error) });
});

var tabNomsInstruments = [];
var tabIdInstruments = [];

/*Cette function a pour rôle d'afficher et de masquer la selection de dates "depuis" en fonction
de la valeur checked de la checkbox dans la liste d'instruments.*/
function functDisplay(){
	var ul = document.getElementById("list-instrument");
	var li = ul.getElementsByTagName("li");
	
	for(var i=0; i<li.length; ++i){
		var input = li[i].firstElementChild;
		var nom = li[i].childNodes[1].innerHTML;
		var res = nom.replace(/, depuis /,'');
		li[i].childNodes[1].innerHTML = res;
		if(input.checked){
			li[i].childNodes[1].innerHTML += ", depuis ";
			li[i].lastElementChild.style.display= "inline";
		}
		else{
			li[i].lastElementChild.style.display= "none";
		}
	}
};

document.ready( () => {
	fetch(HOST_PATH+"api/instrument/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let instruments = document.getElementById('list-instrument');
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					for(var item2 in obj[item]){
						for(var item3 in obj[item][item2]){
							var instrument = obj[item][item2][item3];
							if(item3=="nom"){
								tabNomsInstruments.push(instrument);
								var checkbox = document.createElement("input");
								checkbox.type = "checkbox";
								checkbox.name = "instrument";
								checkbox.value = instrument;
								checkbox.id = "input-checkbox-" + instrument.toLowerCase();
								
								
								let label = document.createElement("label")
								label.htmlFor = "input-checkbox-" + instrument.toLowerCase();
								label.innerHTML = instrument;
								
								
								let select = document.createElement("select");
								select.id = "list-annees";
								select.name = "depuis";
								select.style="display:none";
								
								for(var i=2020; i>=1900; i--){
									let option = document.createElement("option");
									var date = i;
									option.value=date.toString();
									var itmText = document.createTextNode(date.toString());
									option.appendChild(itmText);
									select.appendChild(option);
								}
								
				
								
								checkbox.addEventListener("click",functDisplay,true);
								
								let li  = document.createElement("li");
								li.appendChild(checkbox);
								li.appendChild(label);
								li.appendChild(select);									
								
								instruments.appendChild(li);
								
							}
							if(item3=="id"){
								tabIdInstruments.push(instrument);
							}
						}
					}				
				}
			}
		})
		.catch(error => { console.log(error) });
});

const form = document.getElementById('form-inscription');

function ProposerGenre(button,compteur){
	button.onclick = event => {
		event.preventDefault();
		
		let proposerGenre = document.getElementById('TxtProposeGenre');
		
		let label1 = document.createElement("label");
		label1.innerHTML = "<br> Genre "+compteur + " : ";
		proposerGenre.appendChild(label1);
		
		var text = document.createElement("input");
		text.type = "text";
		text.name = "proposeGenre";
		text.id = "ajouterGenre"+compteur;
		
		proposerGenre.appendChild(text);
		document.getElementById("genres").removeChild(button);//au clic, le bouton disparait !
		
		button = document.createElement("button");
		button.id ="button-proposerNouveau-genre";
		button.innerHTML = "Proposer un autre genre !";
		document.getElementById("genres").appendChild(button);
		
		compteur++;
		
		ProposerGenre(button,compteur);
	};
};

var compteurGenres = 1;
const buttonProposerGenre = document.getElementById('button-proposer-genre');
ProposerGenre(buttonProposerGenre,compteurGenres)

function ProposerVille(button){
	button.onclick = event => {
		event.preventDefault();
		
		let proposerVille = document.getElementById('TxtProposeVille');
		
		let label1 = document.createElement("label");
		label1.innerHTML = "<br> Nom de ta ville: ";
		proposerVille.appendChild(label1);
		
		var nom = document.createElement("input");
		nom.type = "text";
		nom.name = "proposeVilleNom";
		nom.id = "ajouterVilleNom";
		proposerVille.appendChild(nom);
		
		let label2 = document.createElement("label");
		label2.innerHTML = "<br> et son Code Postal : ";
		proposerVille.appendChild(label2);
		
		var codePostal = document.createElement("input");
		codePostal.type = "text";
		codePostal.name = "proposeVilleCode";
		codePostal.id = "ajouterVilleCode";
		codePostal.minLength="5";
		codePostal.maxLength="5";
		proposerVille.appendChild(codePostal);
		
		document.getElementById("villes").removeChild(button);/*au clic, le bouton disparait ! Ainsi que la liste*/
		document.getElementById("villes").removeChild(document.getElementById("list-ville"));		
	};
};

const buttonProposerVille = document.getElementById('button-proposer-ville');
ProposerVille(buttonProposerVille);

function ProposerInstrument(button,compteur){
	button.onclick = event => {
		event.preventDefault();
		
		let proposerInstrument = document.getElementById('TxtProposeInstrument');
		
		let label1 = document.createElement("label");
		label1.innerHTML = "<br> Instrument "+compteur + " : ";
		proposerInstrument.appendChild(label1);
		
		var text = document.createElement("input");
		text.type = "text";
		text.name = "proposeInstrument";
		text.id = "ajouterInstrument"+compteur;
		
		proposerInstrument.appendChild(text);
		document.getElementById("instruments").removeChild(button);//au clic, le bouton disparait !
		
		let label=document.createElement("label");
		label.innerHTML="-- depuis ";
		proposerInstrument.appendChild(label);
		
		let select = document.createElement("select");
		select.id = "list-annees";
		select.name = "depuis";
		proposerInstrument.appendChild(select);
		for(var i=2020; i>=1900; i--){
			let option = document.createElement("option");
			var date = i;
			option.value=date.toString();
			var itmText = document.createTextNode(date.toString());
			option.appendChild(itmText);
			select.appendChild(option);
		}
		
		button = document.createElement("button");
		button.id ="button-proposerNouvel-instrument";
		button.innerHTML = "Proposer un autre instrument !";
				
		document.getElementById("instruments").appendChild(button);
		compteur++;
		
		ProposerInstrument(button,compteur);
	};
};

var compteurInstruments = 1;
const buttonProposerInstrument = document.getElementById('button-proposer-instrument');
ProposerInstrument(buttonProposerInstrument,compteurInstruments);

function DeleteSpacesBeforeAndAfter(chaine){
	while(chaine.substr(chaine.length-1)==" ")
			chaine = chaine.slice(0, chaine.length-1);
	while(chaine.substr(0,1)==" ")
			chaine = chaine.slice(1, chaine.length);
	return chaine;
}

document.getElementById('button-signIn').onclick = event => {
	event.preventDefault();
	
	const form = document.getElementById("form-inscription");
		
	var nbGenresSelected=0;
	var stringIdGenresText="";
	var firstElementSringIdGenresText="";
	/*
	On ajoute tous les genres à la base de Données (ou non s'ils existent déjà ou sont vides)
	*/
	for(var i=2; i<document.getElementById('TxtProposeGenre').childNodes.length; i=i+2){
		
		var nomGenre = document.getElementById('TxtProposeGenre').childNodes[i].value;
		
		/*Si le genre tapé contient des espaces à la fin ou au début, on lui enlève*/
		nomGenre = DeleteSpacesBeforeAndAfter(nomGenre);	
			
		var contains=false;
		for(var j=0; j<tabNomsGenres.length-1; j++){
			if(nomGenre == tabNomsGenres[j]){
				if(stringIdGenresText==""){
					firstElementSringIdGenresText=firstElementSringIdGenresText.concat(tabIdGenres[j]);
				}	
				console.log(firstElementSringIdGenresText);
				stringIdGenresText=stringIdGenresText.concat(tabIdGenres[j] + ",");
				nbGenresSelected++;
				contains=true;
			}	
		}	
		if(!contains && nomGenre!=""){	
				var parametrePost = "nom=" + nomGenre;
				
				console.log(parametrePost);
				
				var request = new XMLHttpRequest();
				var url = HOST_PATH+'api/genre/create.php';
				
				request.onreadystatechange = function(){
					if(request.readyState == 4 && request.status == 200){
						var response = JSON.parse(request.responseText);
						tabNomsGenres.push(response.genre.nom);
						tabIdGenres.push(response.genre.id.toString());
						nbGenresSelected++;
						if(stringIdGenresText=="")
							firstElementSringIdGenresText.concat(tabIdGenres[j]);
						stringIdGenresText=stringIdGenresText.concat(response.genre.id.toString() + ",");
					}
				}
				request.open('POST', url, false);//On met false (synchrone) pour que les requêtes se fassent dans l'ordre.
				request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				request.send(parametrePost);			
		}
		else if(nomGenre=="")
			console.log("Le genre "+(i/2)+" est vide.");
		else
			console.log("Le genre "+(i/2)+" est déjà dans la liste.");
			
	}
	

	var stringIdGenres="";
	var ul = document.getElementById("list-genre");
	var liGenres = ul.getElementsByTagName("li");
	
	/*La prochaine boucle for va permettre de créer la chaine contenant les id des genres cochés.
	  Ce sera stringIdGenres.
	*/
	for(var i=0; i<liGenres.length; ++i){
		if(liGenres[i].firstElementChild.checked){
			nbGenresSelected++;
			for(var j=0; j<tabNomsGenres.length; j++){
				if(tabNomsGenres[j]==liGenres[i].firstElementChild.value && !(stringIdGenresText.includes(','+tabIdGenres[j]+',')) && firstElementSringIdGenresText!=tabIdGenres[j])
					stringIdGenres=stringIdGenres.concat(tabIdGenres[j] + ",");
			}
		}
	}
	
	stringIdGenres=stringIdGenres.concat(stringIdGenresText);
	
	if(nbGenresSelected>0)
		stringIdGenres=stringIdGenres.slice(0,stringIdGenres.length-1);
	
	/*À partir de maintenant on a notre belle chaine de caractères (sans doublon) des 
	  identifiants des genres à envoyer. */
	
	
	/*
	On ajoute la nouvelle ville à la base de Données (ou non si elle existe déjà ou est vide)
	*/
	
	var stringIdVille = "";
	
	if(document.getElementById('TxtProposeVille').childNodes.length !=0){
		var nomVille = document.getElementById('TxtProposeVille').childNodes[1].value;
		var codePostalVille = document.getElementById('TxtProposeVille').childNodes[3].value;
	
		/*On retire les espaces du début et de la fin de nomVille 
		  et codePostalVille (bien que codePostalVille ne puisse pas
		  dépasser cinq caractères)*/
		  
		nomVille = DeleteSpacesBeforeAndAfter(nomVille);
		codePostalVille = DeleteSpacesBeforeAndAfter(codePostalVille);
		
		//console.log("nom : " + nomVille + ".");
		//console.log("code : " + codePostalVille + ".");
		
		var contains=false;
		
		for(var j=0; j<tabNomsVilles.length-1; j++){
			if(nomVille == tabNomsVilles[j] && (codePostalVille == tabCodesPostauxVilles[j] || codePostalVille=="")){
				stringIdVille = stringIdVille.concat(tabIdVilles[j]);
				contains=true;
			}
		}
		
		if(!contains && nomVille!=""){
							
			var parametrePost = "nom=" + nomVille + "&code_postal="+codePostalVille;
			
			/*if(codePostalVille!="")
				parametrePost = parametrePost.concat("&code_postal="+codePostalVille);
			*/
			//console.log(parametrePost);
			
			var request = new XMLHttpRequest();
			var url = HOST_PATH+'api/ville/create.php';
				
			request.onreadystatechange = function(){
				//console.log(request.readyState, request.status);
				if(request.readyState == 4 && request.status == 200){
					var response = JSON.parse(request.responseText);
					tabNomsVilles.push(response.ville.nom);
					tabCodesPostauxVilles.push(response.ville.code_postal);
					tabIdVilles.push(response.ville.id.toString());
					stringIdVille = stringIdVille.concat(response.ville.id.toString());
				}
			}
			request.open('POST', url, false);//On met false (synchrone) pour que les requêtes se fassent dans l'ordre.
			request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			request.send(parametrePost);
		}
		else if(nomVille=="")
			console.log("La ville est vide.");
		else
			console.log("La ville est déjà dans la liste.");
	}
	
	else{
		if(form.ville.value!="vide"){
			for(var i=0; i<tabNomsVilles.length; i++){
				if(tabNomsVilles[i] +" ("+tabCodesPostauxVilles[i]+")" ==form.ville.value){
					stringIdVille = tabIdVilles[i];
					break;
				}
			}
		}
	}
	
	/*if(stringIdVille == "")//peut-être inutile.
		stringIdVille = null;
	*/
	
	/*À ce moment on a tout ce qui concerne les genres et les villes. Les chaines avec
	 les id sont prêtes à être envoyées*/
	
	

	
	
	
	var nbInstrumentsSelected=0;
	var stringIdInstrumentsText = "";
	var stringAnneesDebutText = "";
	var firstElementSringIdInstrumentsText="";
	/*
	On ajoute tous les instruments à la base de Données (ou non s'ils existent déjà ou sont vides)
	*/
	var numInstrument = 0;
	for(var i=2; i<document.getElementById('TxtProposeInstrument').childNodes.length; i=i+4){
		numInstrument ++;
		var nomInstrument = document.getElementById('TxtProposeInstrument').childNodes[i].value;
		var anneeDebut = document.getElementById('TxtProposeInstrument').childNodes[i+2].value;
		
		/*Si le genre tapé contient des espaces à la fin ou au début, on lui enlève*/
		nomInstrument = DeleteSpacesBeforeAndAfter(nomInstrument);	
		
		
		var contains=false;
		for(var j=0; j<tabNomsInstruments.length-1; j++){
			if(nomInstrument == tabNomsInstruments[j]){
				if(stringIdInstrumentsText==""){
					firstElementSringIdInstrumentsText=firstElementSringIdInstrumentsText.concat(tabIdInstruments[j]);
				}	
				console.log(firstElementSringIdInstrumentsText);
				stringIdInstrumentsText=stringIdInstrumentsText.concat(tabIdInstruments[j] + ",");
				stringAnneesDebutText = stringAnneesDebutText.concat(anneeDebut.toString()+ ",");
				nbInstrumentsSelected++;
				contains=true;
			}	
		}	
		if(!contains && nomInstrument!=""){	
				var parametrePost = "nom=" + nomInstrument;
				
				console.log(parametrePost);
				
				var request = new XMLHttpRequest();
				var url = HOST_PATH+'api/instrument/create.php';
				
				request.onreadystatechange = function(){
					if(request.readyState == 4 && request.status == 200){
						var response = JSON.parse(request.responseText);
						tabNomsInstruments.push(response.instrument.nom);
						tabIdInstruments.push(response.instrument.id.toString());
						nbInstrumentsSelected++;
						if(stringIdInstrumentsText=="")
							firstElementSringIdInstrumentsText.concat(tabIdInstruments[j]);
						stringIdInstrumentsText=stringIdInstrumentsText.concat(response.instrument.id.toString() + ",");
						stringAnneesDebutText = stringAnneesDebutText.concat(anneeDebut.toString()+ ",");
					}
				}
				request.open('POST', url, false);//On met false (synchrone) pour que les requêtes se fassent dans l'ordre.
				request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				request.send(parametrePost);			
		}
		else if(nomInstrument=="")
			console.log("L'instrument "+(numInstrument)+" est vide.");
		else
			console.log("L'instrument "+(numInstrument)+" est déjà dans la liste.");

	}

	var stringIdInstruments="";
	var stringAnneesDebut="";
	var ul = document.getElementById("list-instrument");
	var liInstruments = ul.getElementsByTagName("li");
	/*La prochaine boucle for va permettre de créer la chaine contenant les id des instruments cochés.
	  Ce sera stringIdInstruments.
	*/
	for(var i=0; i<liInstruments.length; ++i){
		if(liInstruments[i].firstElementChild.checked){
			nbInstrumentsSelected++;
			for(var j=0; j<tabNomsInstruments.length; j++){
				if(tabNomsInstruments[j]==liInstruments[i].firstElementChild.value && !(stringIdInstrumentsText.includes(','+tabIdInstruments[j]+',')) && firstElementSringIdInstrumentsText!=tabIdInstruments[j]){
					stringIdInstruments=stringIdInstruments.concat(tabIdInstruments[j] + ",");
					stringAnneesDebut=stringAnneesDebut.concat(liInstruments[i].childNodes[2].value + ",");//On met dans la chaine de caractère, les années de début de pratique.
				}
			}
		}
	}
	
	stringIdInstruments=stringIdInstruments.concat(stringIdInstrumentsText);
	stringAnneesDebut=stringAnneesDebut.concat(stringAnneesDebutText);
	
	if(nbInstrumentsSelected>0){//On retire la dernière virgule des chaines de caractères.
		stringIdInstruments=stringIdInstruments.slice(0,stringIdInstruments.length-1);
		stringAnneesDebut=stringAnneesDebut.slice(0,stringAnneesDebut.length-1);
	}
	
	/*Maintenant on a les strings à envoyer pour créer noter musicien. On n'a plus qu'à faire la requête*/
	
	let params = "";
	
	if(form.prenom.value) params=params.concat("prenom="+form.prenom.value+"&");
	if(form.nom.value) params=params.concat("nom="+form.nom.value+"&");
	if(form.dateNaissance.value) params=params.concat("date_naissance="+form.dateNaissance.value+"&");
	if(stringIdGenres!="") params=params.concat("genres="+stringIdGenres+"&");
	if(stringIdVille!="") params=params.concat("ville="+stringIdVille+"&");
	if(stringIdInstruments!="") params=params.concat("instruments="+stringIdInstruments+"&");
	if(stringAnneesDebut) params=params.concat("annee_debut="+stringAnneesDebut+"&");
	
	
	if(params.length!=0)
		params=params.slice(0,params.length-1);
		
	console.log(params);
	
	let urlCreateMusicien = new URL("api/musicien/create.php", HOST_PATH);
	
	var requestCreateMusicien = new XMLHttpRequest();
	
	requestCreateMusicien.onreadystatechange = function(){
		if(requestCreateMusicien.readyState == 4 && requestCreateMusicien.status == 200){
			var response = JSON.parse(requestCreateMusicien.responseText);
			console.log(response);
		}
	}
	requestCreateMusicien.open('POST', urlCreateMusicien, true);
	requestCreateMusicien.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	requestCreateMusicien.send(params);
	
};