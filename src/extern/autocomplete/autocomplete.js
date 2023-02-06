// Inspired by https://www.codehim.com/vanilla-javascript/javascript-autocomplete-dropdown/

//const input = document.querySelector('#fruit');
//const suggestions = document.querySelector('.suggestions ul');

//const fruit = [ 'Apple', 'Apricot', 'Avocado 🥑', 'Banana', 'Bilberry', 'Blackberry', 'Blackcurrant', 'Blueberry', 'Boysenberry', 'Currant', 'Cherry', 'Coconut', 'Cranberry', 'Cucumber', 'Custard apple', 'Damson', 'Date', 'Dragonfruit', 'Durian', 'Elderberry', 'Feijoa', 'Fig', 'Gooseberry', 'Grape', 'Raisin', 'Grapefruit', 'Guava', 'Honeyberry', 'Huckleberry', 'Jabuticaba', 'Jackfruit', 'Jambul', 'Juniper berry', 'Kiwifruit', 'Kumquat', 'Lemon', 'Lime', 'Loquat', 'Longan', 'Lychee', 'Mango', 'Mangosteen', 'Marionberry', 'Melon', 'Cantaloupe', 'Honeydew', 'Watermelon', 'Miracle fruit', 'Mulberry', 'Nectarine', 'Nance', 'Olive', 'Orange', 'Clementine', 'Mandarine', 'Tangerine', 'Papaya', 'Passionfruit', 'Peach', 'Pear', 'Persimmon', 'Plantain', 'Plum', 'Pineapple', 'Pomegranate', 'Pomelo', 'Quince', 'Raspberry', 'Salmonberry', 'Rambutan', 'Redcurrant', 'Salak', 'Satsuma', 'Soursop', 'Star fruit', 'Strawberry', 'Tamarillo', 'Tamarind', 'Yuzu'];

function AUTOCOMPLETE_search(el,str) {
	let results = [];
	const val = str.toLowerCase();

	var data=el.autocompleteData;
	for (i = 0; i < data.length; i++) {
		if (data[i].toLowerCase().indexOf(val) > -1) {
			results.push(data[i]);
		}
	}

	return results;
}

function AUTOCOMPLETE_searchHandler(e) {
	const inputVal = e.currentTarget.value;
	let results = [];
	if (inputVal.length > 0) {
		results = AUTOCOMPLETE_search(e.currentTarget,inputVal);
	}
	AUTOCOMPLETE_showSuggestions(e.currentTarget,results, inputVal);
}

function AUTOCOMPLETE_showSuggestions(el,results, inputVal) {
    var suggestions = el.parentElement.querySelector('.autocomplete-suggestions ul');
    suggestions.innerHTML = '';

	if (results.length > 0) {
		for (i = 0; i < results.length; i++) {
			let item = results[i];
			// Highlights only the first match
			// TODO: highlight all matches
			const match = item.match(new RegExp(inputVal, 'i'));
			item = item.replace(match[0], `<strong>${match[0]}</strong>`);
			suggestions.innerHTML += `<li>${item}</li>`;
		}
		suggestions.classList.add('has-suggestions');
	} else {
		results = [];
		suggestions.innerHTML = '';
		suggestions.classList.remove('has-suggestions');
	}
}

function AUTOCOMPLETE_useSuggestion(el,e) {
	el.value = e.target.innerText;
	el.focus();
	var suggestions=el.parentElement.querySelector('.autocomplete-suggestions ul');
	suggestions.innerHTML = '';
	suggestions.classList.remove('has-suggestions');
}

//input.addEventListener('keyup', searchHandler);
//suggestions.addEventListener('click', useSuggestion);

function enableAutocomplete(el){
	el.addEventListener('keyup',AUTOCOMPLETE_searchHandler);
	el.parentElement.querySelector('.autocomplete-suggestions ul').addEventListener('click', function(ev){AUTOCOMPLETE_useSuggestion(el,ev);});
}
