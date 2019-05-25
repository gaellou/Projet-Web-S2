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
								tabNomsVilles.push(ville);
								var list = document.createElement("option");
								list.value=ville;
								var itmText = document.createTextNode(ville);
								list.appendChild(itmText);
								villes.appendChild(list);
							}
							if(item3=="id"){
								tabIdVilles.push(ville);
							}
							if(item3=='code_postal'){
								tabCodesPostauxVilles.push(ville);
							}
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
			//let genres = document.getElementById('list-genre');
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					//console.log(item);
					for(var item2 in obj[item]){
						//console.log(obj[item][item2]);
						for(var item3 in obj[item][item2]){
							//console.log(item3);
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
	//console.log(li);
	
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
					//console.log(item);
					for(var item2 in obj[item]){
						//console.log(obj[item][item2]);
						for(var item3 in obj[item][item2]){
							//console.log(item3);
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
								
								//console.log(checkbox);
								
								let select = document.createElement("select");
								select.id = "list-annees";
								select.name = "depuis";
								//select.innerHTML = ",&nbsp depuis :";
								select.style="display:none";
								
								for(var i=2020; i>=1900; i--){
									let option = document.createElement("option");
									var date = i;
									option.value=date.toString();
									var itmText = document.createTextNode(date.toString());
									option.appendChild(itmText);
									select.appendChild(option);
								}
								
								/*functDisplay = function(){
									if(checkbox.checked==true)
										select.style.display= "block";
									else
										select.style.display = "none";
								};*/
								
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
		label1.innerHTML = " Genre "+compteur + " : ";
		proposerGenre.appendChild(label1);
		
		var text = document.createElement("input");
		text.type = "text";
		text.name = "proposeGenre";
		text.id = "ajouterGenre"+compteur;
		
		proposerGenre.appendChild(text);
		//console.log(button);
		document.getElementById("genres").removeChild(button);//au clic, le bouton disparait !
		
		button = document.createElement("button");
		button.id ="button-proposerNouveau-genre";
		button.innerHTML = "Proposer un autre genre !";
		document.getElementById("genres").appendChild(button);
		//console.log(compteur);
		
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
		label1.innerHTML = " Nom de ta ville: ";
		proposerVille.appendChild(label1);
		
		var nom = document.createElement("input");
		nom.type = "text";
		nom.name = "proposeVilleNom";
		nom.id = "ajouterVilleNom";
		proposerVille.appendChild(nom);
		
		let label2 = document.createElement("label");
		label2.innerHTML = " et son Code Postal : ";
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
		label1.innerHTML = " Instrument "+compteur + " : ";
		proposerInstrument.appendChild(label1);
		
		var text = document.createElement("input");
		text.type = "text";
		text.name = "proposeInstrument";
		text.id = "ajouterInstrument"+compteur;
		
		proposerInstrument.appendChild(text);
		//console.log(button);
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
		
		//console.log(compteur);
		
		document.getElementById("instruments").appendChild(button);
		compteur++;
		
		ProposerInstrument(button,compteur);
	};
};

var compteurInstruments = 1;
const buttonProposerInstrument = document.getElementById('button-proposer-instrument');
ProposerInstrument(buttonProposerInstrument,compteurInstruments);

/*buttonProposerInstrument.onclick = event => {
	event.preventDefault();
	
	let proposerInstrument = document.getElementById('TxtProposeInstrument');
	
	var text = document.createElement("input");
	text.type = "text";
	text.name = "proposeInstrument";
	text.id = "ajouterInstrument";

	proposerInstrument.appendChild(text);
	document.getElementById("instruments").removeChild(buttonProposerInstrument);//au clic, le bouton disparait !
};*/



/*
checkbox = document.createElement("input");
checkbox.type = "checkbox";
checkbox.name = "genre";
checkbox.value = "propose";
checkbox.id = "input-checkbox-propose";

let label = document.createElement("label")
label.htmlFor = "input-checkbox-propose";
label.innerHTML = "Proposer un Genre";
let li  = document.createElement("li");
li.appendChild(checkbox);
li.appendChild(label);
genres.appendChild(li);
*/

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
				//console.log(document.getElementById('TxtProposeGenre').childNodes[0]);
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
	
	console.log(tabNomsGenres);
	console.log(tabIdGenres);

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
	
	//console.log(stringIdGenres);
	
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
		
		console.log("nom : " + nomVille + ".");
		console.log("code : " + codePostalVille + ".");
		
		var contains=false;
		
		for(var j=0; j<tabNomsVilles.length-1; j++){
			if(nomVille == tabNomsVilles[j] && codePostalVille == tabCodesPostauxVilles[j]){
				stringIdVille = stringIdVille.concat(tabIdVilles[j]);
				contains=true;
			}
		}
		
		if(!contains && nomVille!=""){
							
			var parametrePost = "nom=" + nomVille;
			
			if(codePostalVille!="")
				parametrePost = parametrePost.concat("&code_postal="+codePostalVille);
			
			console.log(parametrePost);
			
			var request = new XMLHttpRequest();
			var url = HOST_PATH+'api/ville/create.php';
				
			request.onreadystatechange = function(){
				console.log(request.readyState, request.status);
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
				if(tabNomsVilles[i]==form.ville.value){
					stringIdVille = tabIdVilles[i];
					break;
				}
			}
		}
	}
	
	if(stringIdVille == "")//peut-être inutile.
		stringIdVille = null;
	
	console.log(stringIdVille);
	
	let params = {};
	
	/*if(form.prenom.value) params['prenom'] = form.prenom.value;
	if(form.nom.value) params['nom'] = form.nom.value;
	if(form.dateNaissance.value) params['date-naissance'] = form.date-naissance.value;
	if(form.genre.value) params['genre'] = form.genre.value;
	if(form.ville.value) params['ville'] = form.ville.value;
	if(form.intrument.value) params['ville'] = form.instrument.value;
	if(form.list-annee.value) params['annee_debut'] = form.list-annee.value;
	
	console.log(params);
	*/
	var stringToSend = "";
	
	/*for(int i=0; i<params.length; i++){
		
	}*/
	
};