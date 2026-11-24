/**
 * NYDLA CMSP — podcast filter chips (progressive enhancement).
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-nydla-podcast-filters]');
	if (!root) {
		return;
	}

	var chips = root.querySelectorAll('[data-nydla-cat]');
	var tiles = root.querySelectorAll('.nydla-cmsp__podcast');
	if (!chips.length || !tiles.length) {
		return;
	}

	function apply(cat) {
		chips.forEach(function (chip) {
			chip.classList.toggle('is-on', chip.getAttribute('data-nydla-cat') === cat);
		});
		tiles.forEach(function (tile) {
			var cats = (tile.getAttribute('data-cats') || '').split(/\s+/).filter(Boolean);
			var show = !cat || cats.indexOf(cat) !== -1;
			tile.hidden = !show;
		});
	}

	chips.forEach(function (chip) {
		chip.addEventListener('click', function (e) {
			// Keep URL updates from the href; also filter instantly.
			var cat = chip.getAttribute('data-nydla-cat') || '';
			apply(cat);
		});
	});
})();
