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

var tabNomsSalles = [];
var tabIdSalles = [];

document.ready( () => {
	fetch(HOST_PATH+"api/salle/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let salles = document.getElementById('list-salle');
			obj = data;
			for(var item in obj){
				if(item !="nombre"){
					for(var item2 in obj[item]){
						
						if(item2=="salle"){
							for(var item3 in obj[item][item2]){
								var salle = obj[item][item2][item3];
								if(item3=="nom"){
									tabNomsSalles.push(salle);
									var list = document.createElement("option");
									list.value=salle;
									var itmText = document.createTextNode(salle);
									list.appendChild(itmText);
									salles.appendChild(list);
								}
								if(item3=="id"){
									tabIdSalles.push(salle);
								}	
							}
						}
					}				
				}
			}
		})
		.catch(error => { console.log(error) });
});


var tabNomsGroupes = [];
var tabIdGroupes = [];

document.ready( () => {
	fetch(HOST_PATH+"api/groupe/read.php") // à corriger si cela ne fonctionne pas
		.then( response => response.json())
		.then( data => {
			let groupes = document.getElementById('list-groupe');
			obj = data;
			for(var item in obj){
				if(item > 0){
					for(var item2 in obj[item]){
						if(item2 == "groupe"){
							for(var item3 in obj[item][item2]){
								var groupe = obj[item][item2][item3];
								if(item3=="nom"){
									tabNomsGroupes.push(groupe);
									var list = document.createElement("option");
									list.value=groupe;
									var itmText = document.createTextNode(groupe);
									list.appendChild(itmText);
									groupes.appendChild(list);
								}
								if(item3=="id"){
									tabIdGroupes.push(groupe);
								}	
							}
						}
					}				
				}
			}
		})
		.catch(error => { console.log(error) });
});

function transformeDateEnFrancais(date){
	if(date.length!=10 || date[4]!='-' || date[7]!='-'){
		console.log("erreur lors de la conversion de la date en français - date non valide");
		return date;
	}
	for(var i=0; i<10; i++){
		if( i!=4 && i!=7 && isNaN(date[i])){
			console.log("erreur lors de la conversion de la date en français - date non valide");
			return date;
		}
	}
	var annee = date.slice(0,4);
	var mois = date.slice(5,7);
	var jour = date.slice(8,10);
	
	if((mois < 1 && mois > 12) || (jour <1 && jour > 31)){
		console.log("erreur lors de la conversion de la date en français - date non valide");
		return date;
	}
	
	var phrase = "";
	if(jour==1) phrase+="1er ";
	else(phrase+=jour+" ");
	if(mois==1) phrase+="janvier ";
	else if(mois==2) phrase+="février ";
	else if(mois==3) phrase+="mars ";
	else if(mois==4) phrase+="avril ";
	else if(mois==5) phrase+="mai ";
	else if(mois==6) phrase+="juin ";
	else if(mois==7) phrase+="juillet ";
	else if(mois==8) phrase+="aout ";
	else if(mois==9) phrase+="septembre ";
	else if(mois==10) phrase+="octobre ";
	else if(mois==11) phrase+="novembre ";
	else if(mois==12) phrase+="décembre ";
	phrase+=annee;
	
	return phrase;
	
}

document.getElementById('button-search').onclick = event => {
	event.preventDefault();
	
	const form = document.getElementById('form-concert');
	
	
	var stringIdSalle="";
	console.log(form.salle.value);
	if(form.salle.value!="vide"){
		for(var i=0; i<tabNomsSalles.length; i++){
			if(tabNomsSalles[i]==form.salle.value){
				stringIdSalle = tabIdSalles[i];
				break;
			}
		}
	}
	
	var stringIdGroupe="";
	console.log(form.groupe.value);
	if(form.groupe.value!="vide"){
		for(var i=0; i<tabNomsGroupes.length; i++){
			if(tabNomsGroupes[i]==form.groupe.value){
				stringIdGroupe = tabIdGroupes[i];
				break;
			}
		}
	}
	
	
	let params ={};

	if(form.dateApres.value) params['date_apres'] = form.dateApres.value;
	if(form.dateAvant.value) params['date_avant'] = form.dateAvant.value;
	if(stringIdSalle !="") params['salle'] = stringIdSalle;
	if(stringIdGroupe !="") params['groupe'] = stringIdGroupe;
	
	let url = new URL("api/concert/search.php", HOST_PATH);
	url.search = new URLSearchParams(params);
	console.log(url);
	
	var resultat = document.getElementById("resultat");
	
	fetch(url)
		.then( response => response.json() )
		.then( data => {
			console.log(data);
			
			if(data.nombre == 0)
				resultat.innerHTML = "Il n'y a aucun concert qui correspond à votre recherche.";
		
			else{
				if(data.nombre == 1)
					resultat.innerHTML = "Un concert trouvé : <br><br>";
				else 
					resultat.innerHTML = "Concerts trouvés : <br><br>";
				for(var h=0; h<data.nombre; h++){
					if(data[h]){
						resultat.innerHTML += "- Le "+ transformeDateEnFrancais(data[h].concert.date_concert)+ ', "' + data[h].groupe.nom + '" jouera dans la salle "'+data[h].salle.nom+'".<br><br>';
						
						
						/*
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
						resultat.innerHTML +="<br><br><br>";*/
					}
				}
			}
		console.log(resultat.innerHTML);
		})
		.catch( error => { console.log(error)} );
};