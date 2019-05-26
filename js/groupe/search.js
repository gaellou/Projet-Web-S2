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

var tabNomsGenres = [];
var tabIdGenres = [];

document.ready( () => {
	fetch(HOST_PATH+"api/genre/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let genres = document.getElementById('list-genre');
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					for(var item2 in obj[item]){
						for(var item3 in obj[item][item2]){
							var genre = obj[item][item2][item3];
							if(item3=="nom"){
								tabNomsGenres.push(genre);
								var list = document.createElement("option");
								list.value=genre;
								var itmText = document.createTextNode(genre);
								list.appendChild(itmText);
								genres.appendChild(list);
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

//console.log(tabNomsGenres);
//console.log(tabIdGenres);

var tabNomsMusiciens=[];
var tabPrenomsMusiciens = [];
var tabIdMusiciens=[];

document.ready( () => {
	fetch(HOST_PATH+"api/musicien/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let musiciens = document.getElementById('list-musicien');
			obj = data;
			for(var item in obj){
				for(var item2 in obj[item]){
					if(item2=="musicien"){
						var valeur="";
						for(var item3 in obj[item][item2]){
							var musicien = obj[item][item2][item3];
							if(item3=="nom"){
								tabNomsMusiciens.push(musicien);
								valeur = valeur.concat(musicien);
							}
							if(item3=="prenom"){
								tabPrenomsMusiciens.push(musicien);
								valeur = musicien.concat(" "+valeur);
							}
							if(item3=="id"){
								tabIdMusiciens.push(musicien);
							}
						}
						var checkbox = document.createElement("input");
						checkbox.type = "checkbox";
						checkbox.name = "musicien";
						checkbox.value = valeur;
						checkbox.id = "input-checkbox-" + valeur.toLowerCase();
						let label = document.createElement("label")
						label.htmlFor = "input-checkbox-" + valeur.toLowerCase();
						label.innerHTML = valeur;
						let li  = document.createElement("li");
						li.appendChild(checkbox);
						li.appendChild(label);
						musiciens.appendChild(li);
					}				
				}
			}
		})
		.catch(error => { console.log(error) });
});

//console.log(tabNomsMusiciens);
//console.log(tabIdMusiciens);

var tabNomsInstruments = [];
var tabIdInstruments = [];

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

//console.log(tabIdInstruments);
//console.log(tabNomsInstruments);

document.getElementById('button-search').onclick = event => {
	event.preventDefault();
	
	const form = document.getElementById('form-groupe');
	
	
	var stringIdGenre="";
	console.log(form.genre.value);
	if(form.genre.value!="vide"){
		for(var i=0; i<tabNomsGenres.length; i++){
			if(tabNomsGenres[i]==form.genre.value){
				stringIdGenre = tabIdGenres[i];
				break;
			}
		}
	}
	
	
	var nbMusiciensChecked=0;
	var stringIdMusiciens="";
	var ul = document.getElementById("list-musicien");
	var liMusiciens = ul.getElementsByTagName("li");
	
	/*La prochaine boucle for va permettre de créer la chaine contenant les id des musiciens cochés.
	  Ce sera stringIdMusiciens.
	*/
	for(var i=0; i<liMusiciens.length; ++i){
		if(liMusiciens[i].firstElementChild.checked){
			nbMusiciensChecked++;
			for(var j=0; j<tabNomsMusiciens.length; j++){
				if(tabPrenomsMusiciens[j] + " " + tabNomsMusiciens[j]==liMusiciens[i].firstElementChild.value){
					stringIdMusiciens=stringIdMusiciens.concat(tabIdMusiciens[j] + ",");
				}
			}
		}
	}
	
	if(nbMusiciensChecked>0)
		stringIdMusiciens=stringIdMusiciens.slice(0,stringIdMusiciens.length-1);
	
	console.log(stringIdMusiciens);
	
	var nbInstrumentsChecked=0;
	var stringIdInstruments="";
	var ul2 = document.getElementById("list-instrument");
	var liInstruments = ul2.getElementsByTagName("li");
	
	/*La prochaine boucle for va permettre de créer la chaine contenant les id des instruments cochés.
	  Ce sera stringIdInstruments.
	*/
	for(var i=0; i<liInstruments.length; ++i){		
		if(liInstruments[i].firstElementChild.checked){
			nbInstrumentsChecked++;
			for(var j=0; j<tabNomsInstruments.length; j++){
				if(tabNomsInstruments[j]==liInstruments[i].firstElementChild.value)
					stringIdInstruments=stringIdInstruments.concat(tabIdInstruments[j] + ",");
			}
		}
	}
	if(nbInstrumentsChecked>0)
		stringIdInstruments=stringIdInstruments.slice(0,stringIdInstruments.length-1);
	
	console.log(stringIdInstruments);
	
	let params ={};

	if(form.nom.value) params['nom'] = form.nom.value;
	if(stringIdGenre !="") params['genre'] = stringIdGenre;
	if(stringIdMusiciens !="") params['musiciens'] = stringIdMusiciens;
	if(stringIdInstruments !="") params['instruments'] = stringIdInstruments;
	
	let url = new URL("api/groupe/search.php", HOST_PATH);
	url.search = new URLSearchParams(params);
	console.log(url);
	
	var resultat = document.getElementById("resultat");
	
	fetch(url)
		.then( response => response.json() )
		.then( data => {
			console.log(data);
			
			if(data.nombre == 0)
				resultat.innerHTML = "Il n'y a aucun groupe qui correspond à votre recherche.";
			
			/*else if(data.nombre == 1){
				if(data[0]){
					resultat.innerHTML = "L'unique groupe correspondant à votre recherche est : <br><br> - "+data[0].groupe.nom+", composé ";
					if(data[0].membres.length==1) resultat.innerHTML += "du sublime <br>";
					else if(data[0].membres.length > 1) resultat.innerHTML += "des sublimes <br><br>";
					for(var i=0; i<data[0].membres.length; i++){	
						resultat.innerHTML += "&nbsp &nbsp" + data[0].membres[i].musicien.prenom+ " " + data[0].membres[i].musicien.nom + "  ( ";
						for(var j=0; j<data[0].membres[i].instruments.length; j++){
							resultat.innerHTML += data[0].membres[i].instruments[j].nom;
							if(j!=data[0].membres[i].instruments.length-1)resultat.innerHTML+=", ";
						}
						resultat.innerHTML += " )";
					}
				}
			}	*/
				
			else{
				if(data.nombre == 1)
					resultat.innerHTML = "L'unique groupe correspondant à votre recherche est : <br><br> - ";
				else 
					resultat.innerHTML = "Les groupes correspondants à votre recherche sont : <br><br> - ";
				for(var h=0; h<data.nombre; h++){
					if(data[h]){
						resultat.innerHTML += '"'+data[h].groupe.nom+'"'+"  ( "+ data[h].genre.nom +" )"+", composé ";
						console.log("data[h]",data[h]);
						if(data[h].membres.length==1) resultat.innerHTML += "du sublime <br>";
						else if(data[h].membres.length > 1) resultat.innerHTML += "des sublimes <br><br>";
						for(var i=0; i<data[h].membres.length; i++){	
							resultat.innerHTML += "&nbsp - " + data[h].membres[i].musicien.prenom+ " " + data[h].membres[i].musicien.nom + "  ( ";
							for(var j=0; j<data[h].membres[i].instruments.length; j++){
								resultat.innerHTML += data[h].membres[i].instruments[j].nom;
								if(j!=data[h].membres[i].instruments.length-1)resultat.innerHTML+=", ";
							}
							resultat.innerHTML += " ) <br>";
						}
						resultat.innerHTML +="<br><br><br>";
					}
				}
			}
		console.log(resultat.innerHTML);
		})
		.catch( error => { console.log(error)} );
};