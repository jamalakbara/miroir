/* Customizer live preview: colour, size and layout settings set their CSS token; short texts replace their element's text. */
(function (api, live) {
	Object.keys(live).forEach(function (id) {
		var s = live[id];
		api(id, function (setting) {
			setting.bind(function (value) {
				if (s.var) {
					document.documentElement.style.setProperty(s.var, value === '' ? '' : value + (s.unit || ''));
				} else if (s.selector) {
					document.querySelectorAll(s.selector).forEach(function (el) {
						el.textContent = value;
					});
				}
			});
		});
	});
})(wp.customize, window.miroirPreview || {});
