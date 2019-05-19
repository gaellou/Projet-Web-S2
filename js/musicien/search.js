/*const searchFirstname = document.getElementById('search_firstname');
const searchLastname = document.getElementById('search_lastname');
const searchTown = document.getElementById('search_town');

const searchGenres = document.getElementById('search_genres[]');
const searchInstruments = document.getElementById('search_instruments[]');*/
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
var tabIdVilles = [];

document.ready( () => {
	fetch("http://localhost:8888/Projet/Random_Platypus__Projet-Web-S2--master/api/ville/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let villes = document.getElementById('list-ville');
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					//console.log(item);
					for(var item2 in obj[item]){
						//console.log(obj[item][item2]);
						for(var item3 in obj[item][item2]){
							//console.log(item3);
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
						}
					}				
				}
			}
		})
		.catch(error => { console.log(error) });
});
console.log(tabNomsVilles);
console.log(tabIdVilles);

var tabNomsGenres = [];
var tabIdGenres = [];

document.ready( () => {
	fetch("http://localhost:8888/Projet/Random_Platypus__Projet-Web-S2--master/api/genre/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let genres = document.getElementById('list-genre');
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

console.log(tabIdGenres);
console.log(tabNomsGenres);

var tabNomsInstruments = [];
var tabIdInstruments = [];

document.ready( () => {
	fetch("http://localhost:8888/Projet/Random_Platypus__Projet-Web-S2--master/api/instrument/read.php") // à corriger si cela ne fonctionne pas
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
								checkbox.value = instrument
								checkbox.id = "input-checkbox-" + instrument.toLowerCase();
								
								let label = document.createElement("label")
								label.htmlFor = "input-checkbox-" + instrument.toLowerCase();
								label.innerHTML = instrument;
				
								let li  = document.createElement("li");
								li.appendChild(checkbox);
								li.appendChild(label);
	
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

console.log(tabIdInstruments);
console.log(tabNomsInstruments);

document.getElementById('button-search').onclick = event => {
	event.preventDefault();
	
	const form = document.getElementById('form-musicien');
	
	let params = {};
	
	var idVille="";
	console.log(form.ville.value);
	if(form.ville.value!="nul"){
		for(var i=0; i<tabNomsVilles.length; i++){
			if(tabNomsVilles[i]==form.ville.value){
				idVille = tabIdVilles[i];
				break;
			}
		}
	}
	
	
	var nbGenresChecked=0;
	var stringIdGenres="";
	var ul = document.getElementById("list-genre");
	var items = ul.getElementsByTagName("li");
	
	/*La prochaine boucle for va permettre de créer la chaine contenant les id des genres cochés.
	  Ce sera stringIdGenres.
	*/
	for(var i=0; i<items.length; ++i){
		//console.log(items[i]);
		console.log(items[i].firstElementChild.value + " : " + items[i].firstElementChild.checked);
		
		if(items[i].firstElementChild.value == "Tous les genres" && items[i].firstElementChild.checked){
			for(var j=0; j<items.length; ++j)
				items[j].firstElementChild.checked=true;//si tous les genres est coché, on coche toutes les autres cases.
		}
		else if(items[i].firstElementChild.value != "Tous les genres" && items[i].firstElementChild.checked){
			nbGenresChecked++;
			for(var j=0; j<tabNomsGenres.length; j++){
				if(tabNomsGenres[j]==items[i].firstElementChild.value)
					stringIdGenres=stringIdGenres.concat(tabIdGenres[j] + ",");
			}
		}
		//console.log(items[i].firstElementChild.value + " : " + items[i].firstElementChild.checked);
	}
	console.log(nbGenresChecked);
	if(nbGenresChecked>0)
		stringIdGenres=stringIdGenres.slice(0,stringIdGenres.length-1);
	
	console.log(stringIdGenres);
	/*for(var i = 0, li; li=ul.childNodes[i];i++){
		if(li.tagName=='LI' && li.genre.value)
			console.log(li.genre.value);
	}*/
	
	var nbInstrumentsChecked=0;
	var stringIdInstruments="";
	var ul2 = document.getElementById("list-instrument");
	items = ul2.getElementsByTagName("li");
	
	/*La prochaine boucle for va permettre de créer la chaine contenant les id des instruments cochés.
	  Ce sera stringIdInstruments.
	*/
	for(var i=0; i<items.length; ++i){
		//console.log(items[i]);
		console.log(items[i].firstElementChild.value + " : " + items[i].firstElementChild.checked);
		
		if(items[i].firstElementChild.value == "Tous les instruments" && items[i].firstElementChild.checked){
			for(var j=0; j<items.length; ++j)
				items[j].firstElementChild.checked=true;//si tous les genres est coché, on coche toutes les autres cases.
		}
		else if(items[i].firstElementChild.value != "Tous les instruments" && items[i].firstElementChild.checked){
			nbInstrumentsChecked++;
			for(var j=0; j<tabNomsInstruments.length; j++){
				if(tabNomsInstruments[j]==items[i].firstElementChild.value)
					stringIdInstruments=stringIdInstruments.concat(tabIdInstruments[j] + ",");
			}
		}
		//console.log(items[i].firstElementChild.value + " : " + items[i].firstElementChild.checked);
	}
	console.log(nbInstrumentsChecked);
	if(nbInstrumentsChecked>0)
		stringIdInstruments=stringIdInstruments.slice(0,stringIdInstruments.length-1);
	
	console.log(stringIdGenres);
	console.log(stringIdInstruments);
	console.log(form.prenom.value);
	

	if(form.prenom.value) params['prenom'] = form.prenom.value;
	if(form.nom.value) params['nom'] = form.nom.value;
	if(idVille!="") params['ville'] = idVille;
	if(form.dateApres.value) params['date_apres'] = form.dateApres.value;
	if(form.dateAvant.value) params['date_avant'] = form.dateAvant.value;
	if(stringIdGenres !="") params['genres'] = stringIdGenres;
	if(stringIdInstruments !="") params['instruments'] = stringIdInstruments;
	
	let url = new URL("/Projet/Random_Platypus__Projet-Web-S2--master/api/musicien/search.php", "http://localhost:8888");
	url.search = new URLSearchParams(params);
	console.log(url);
	
	var resultat = document.getElementById("resultat");
	
	fetch(url)
		.then( response => response.json() )
		.then( data => {
			console.log(data);
			
			if(data.nombre == 0)
				resultat.innerHTML = "Il n'y a aucun musicien qui correspond à votre recherche.";
			
			else if(data.nombre == 1)
				resultat.innerHTML = "L'unique musicien correspondant à votre recherche est : <br><br> - "+data[0].musicien.prenom+" "+data[0].musicien.nom+" ("+data[0].musicien.id+")" ;
				
			
			else{
				resultat.innerHTML = "Les musiciens correspondants à votre recherche sont : <br><br>";
			
				for(var i=0; i<data.nombre; i++){
					console.log(data[i].musicien);
				 	resultat.innerHTML+="<br> - "+data[i].musicien.prenom+" "+ data[i].musicien.nom+ " ("+data[i].musicien.id+")<br>";
				}
			}
			
			/* -- TODO - à compléter */ } )
		.catch( error => { console.log(error)} );
};