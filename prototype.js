function intitalSort(names, n) {
	sortedByLetter = [];
	letterIndex = 0;
	for (var i = 0; i < names.length; i++) {
		let currentLetter = names[i].substring(n, n + 1), offset = 0;
		var blank = [];
		sortedByLetter.push(blank);
		while (names[i + offset + 1].substring(n, n + 1) === currentLetter) {
			sortedByLetter[letterIndex].push(names[i + offset] + ", " + JSON.parse(firstNames)[i + offset]);
			offset++;
			if (i + offset + 1 >= names.length) {
				break;
			}
		}
		i += offset;
		letterIndex++;
	}
	for (var i = 0; i < sortedByLetter.length; i++) {
		sortedByLetter[i] = sortedByLetter[i].sort();
	}
	return sortedByLetter;
}

$(document).ready(function() {
	names = intitalSort(JSON.parse(lastNames).sort(), 0);
	console.log(names)
});