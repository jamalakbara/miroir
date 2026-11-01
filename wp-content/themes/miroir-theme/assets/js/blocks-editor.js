/*
 * Editor UI of the Miroir blocks (blocks/*). Settings live in the sidebar; the
 * canvas shows the server render, i.e. exactly what the storefront prints.
 * Plain script on the wp.* globals: no build step.
 */
(function (wp) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var useSelect = wp.data.useSelect;
	var ServerSideRender = wp.serverSideRender;

	/** Media library picker for one image attribute: thumbnail, choose/replace, remove. */
	function ImagePicker(props) {
		var media = useSelect(function (select) {
			return props.id ? select('core').getMedia(props.id) : null;
		}, [props.id]);
		var thumb = media && (media.media_details && media.media_details.sizes && media.media_details.sizes.medium ? media.media_details.sizes.medium.source_url : media.source_url);
		return el('div', { className: 'miroir-image-picker', style: { marginBottom: '16px' } },
			el('p', { style: { margin: '0 0 8px', fontWeight: 500 } }, props.label),
			props.help ? el('p', { style: { margin: '0 0 8px', color: '#757575', fontSize: '12px' } }, props.help) : null,
			thumb ? el('img', { src: thumb, alt: '', style: { display: 'block', maxWidth: '100%', marginBottom: '8px' } }) : null,
			el(be.MediaUploadCheck, null,
				el(be.MediaUpload, {
					allowedTypes: ['image'],
					value: props.id,
					onSelect: function (m) { props.onChange(m.id); },
					render: function (o) {
						return el(Fragment, null,
							el(c.Button, { variant: 'secondary', onClick: o.open }, props.id ? __('Replace image', 'miroir') : __('Choose image', 'miroir')),
							props.id ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.onChange(0); }, style: { marginLeft: '12px' } }, __('Remove', 'miroir')) : null
						);
					}
				})
			)
		);
	}

	/** Link field with page/post suggestions. */
	function LinkField(props) {
		return el('div', { style: { marginBottom: '16px' } },
			el('p', { style: { margin: '0 0 8px', fontWeight: 500 } }, props.label),
			el(be.URLInput, { value: props.value, onChange: function (url) { props.onChange(url); }, __nextHasNoMarginBottom: true }),
			props.help ? el('p', { style: { margin: '8px 0 0', color: '#757575', fontSize: '12px' } }, props.help) : null
		);
	}

	/** Canvas: the server render, not clickable. */
	function Preview(props) {
		var blockProps = be.useBlockProps({ className: 'miroir-block-preview' });
		return el('div', blockProps,
			el(ServerSideRender, {
				block: props.name,
				attributes: props.attributes,
				EmptyResponsePlaceholder: function () {
					return el('div', { className: 'miroir-block-empty' }, props.empty || __('Nothing to show yet: choose the content in the block settings.', 'miroir'));
				}
			})
		);
	}

	function decode(html) {
		var t = document.createElement('textarea');
		t.innerHTML = html;
		return t.value;
	}

	/** Hand-picked products as searchable tokens. */
	function ProductPicker(props) {
		var state = useState('');
		var search = state[0], setSearch = state[1];
		var data = useSelect(function (select) {
			var core = select('core');
			return {
				chosen: props.ids.length ? core.getEntityRecords('postType', 'product', { include: props.ids, per_page: props.ids.length }) || [] : [],
				found: search.length > 1 ? core.getEntityRecords('postType', 'product', { search: search, per_page: 20 }) || [] : []
			};
		}, [props.ids.join(','), search]);
		var byTitle = {}, byId = {};
		data.chosen.concat(data.found).forEach(function (p) {
			var title = decode(p.title.rendered) + ' #' + p.id;
			byTitle[title] = p.id;
			byId[p.id] = title;
		});
		return el(c.FormTokenField, {
			label: __('Products', 'miroir'),
			value: props.ids.map(function (id) { return byId[id] || '#' + id; }),
			suggestions: data.found.map(function (p) { return byId[p.id]; }),
			onInputChange: setSearch,
			onChange: function (tokens) {
				props.onChange(tokens.map(function (t) {
					return byTitle[t] || parseInt(String(t).replace(/^.*#/, ''), 10);
				}).filter(function (id) { return id > 0; }));
			},
			__experimentalExpandOnFocus: true,
			__next40pxDefaultSize: true,
			__nextHasNoMarginBottom: true
		});
	}

	var text = function (label, value, onChange, help) {
		return el(c.TextControl, { label: label, value: value, onChange: onChange, help: help, __next40pxDefaultSize: true, __nextHasNoMarginBottom: true });
	};

	wp.blocks.registerBlockType('miroir/editorial', {
		edit: function (props) {
			var a = props.attributes, set = props.setAttributes;
			return el(Fragment, null,
				el(be.InspectorControls, null,
					el(c.PanelBody, { title: __('Layout', 'miroir') },
						el(c.SelectControl, {
							label: __('Image order', 'miroir'),
							value: a.layout,
							options: [
								{ label: __('Small image first', 'miroir'), value: 'small-first' },
								{ label: __('Large image first', 'miroir'), value: 'large-first' }
							],
							onChange: function (v) { set({ layout: v }); },
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true
						})
					),
					el(c.PanelBody, { title: __('Images', 'miroir') },
						el(ImagePicker, { label: __('Small image', 'miroir'), help: __('Portrait 3:4.', 'miroir'), id: a.smallImage, onChange: function (id) { set({ smallImage: id }); } }),
						el(ImagePicker, { label: __('Large image', 'miroir'), help: __('Portrait 3:4.', 'miroir'), id: a.largeImage, onChange: function (id) { set({ largeImage: id }); } })
					),
					el(c.PanelBody, { title: __('Caption & link', 'miroir') },
						text(__('Caption', 'miroir'), a.caption, function (v) { set({ caption: v }); }),
						el(LinkField, { label: __('Link', 'miroir'), value: a.url, onChange: function (v) { set({ url: v }); }, help: __('Empty: the shop.', 'miroir') })
					)
				),
				el(Preview, { name: 'miroir/editorial', attributes: a })
			);
		},
		save: function () { return null; }
	});

	wp.blocks.registerBlockType('miroir/banner', {
		edit: function (props) {
			var a = props.attributes, set = props.setAttributes;
			return el(Fragment, null,
				el(be.InspectorControls, null,
					el(c.PanelBody, { title: __('Images', 'miroir') },
						el(ImagePicker, { label: __('Desktop image', 'miroir'), help: __('Wide, about 1328 × 520.', 'miroir'), id: a.image, onChange: function (id) { set({ image: id }); } }),
						el(ImagePicker, { label: __('Tablet image', 'miroir'), help: __('About 704 × 420. Empty: the desktop image.', 'miroir'), id: a.imageTablet, onChange: function (id) { set({ imageTablet: id }); } }),
						el(ImagePicker, { label: __('Mobile image', 'miroir'), help: __('Square. Empty: the tablet image.', 'miroir'), id: a.imageMobile, onChange: function (id) { set({ imageMobile: id }); } })
					),
					el(c.PanelBody, { title: __('Caption & link', 'miroir') },
						text(__('Caption', 'miroir'), a.caption, function (v) { set({ caption: v }); }),
						el(LinkField, { label: __('Link', 'miroir'), value: a.url, onChange: function (v) { set({ url: v }); }, help: __('Empty: the sale collection.', 'miroir') })
					)
				),
				el(Preview, { name: 'miroir/banner', attributes: a })
			);
		},
		save: function () { return null; }
	});

	wp.blocks.registerBlockType('miroir/product-strip', {
		edit: function (props) {
			var a = props.attributes, set = props.setAttributes;
			var cats = useSelect(function (select) {
				return select('core').getEntityRecords('taxonomy', 'product_cat', { per_page: 100, orderby: 'name' }) || [];
			}, []);
			var limits = [{ label: __('All', 'miroir'), value: '-1' }];
			[4, 5, 6, 8, 10, 12, 16, 20, 24].forEach(function (n) { limits.push({ label: String(n), value: String(n) }); });
			return el(Fragment, null,
				el(be.InspectorControls, null,
					el(c.PanelBody, { title: __('Products', 'miroir') },
						el(c.SelectControl, {
							label: __('Show', 'miroir'),
							value: a.source,
							options: [
								{ label: __('A collection', 'miroir'), value: 'category' },
								{ label: __('Products on sale', 'miroir'), value: 'sale' },
								{ label: __('Featured products', 'miroir'), value: 'featured' },
								{ label: __('Newest products', 'miroir'), value: 'newest' },
								{ label: __('Hand-picked products', 'miroir'), value: 'ids' }
							],
							onChange: function (v) { set({ source: v }); },
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true
						}),
						a.source === 'category' ? el(c.SelectControl, {
							label: __('Collection', 'miroir'),
							value: a.category,
							options: [{ label: __('Choose…', 'miroir'), value: '' }].concat(cats.map(function (t) { return { label: decode(t.name) + ' (' + t.count + ')', value: t.slug }; })),
							onChange: function (v) { set({ category: v }); },
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true
						}) : null,
						a.source === 'ids' ? el(ProductPicker, { ids: a.ids, onChange: function (ids) { set({ ids: ids }); } }) : null,
						a.source !== 'ids' ? el(c.SelectControl, {
							label: __('How many', 'miroir'),
							value: String(a.limit),
							options: limits,
							onChange: function (v) { set({ limit: parseInt(v, 10) }); },
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true
						}) : null,
						el(c.SelectControl, {
							label: __('Cards per row (desktop)', 'miroir'),
							value: String(a.columns),
							options: [{ label: '5', value: '5' }, { label: '6', value: '6' }],
							onChange: function (v) { set({ columns: parseInt(v, 10) }); },
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true
						})
					),
					el(c.PanelBody, { title: __('Title & link', 'miroir') },
						text(__('Title', 'miroir'), a.label, function (v) { set({ label: v }); }),
						el(LinkField, { label: __('"View all" link', 'miroir'), value: a.url, onChange: function (v) { set({ url: v }); }, help: __('Empty: the collection page.', 'miroir') })
					)
				),
				el(Preview, { name: 'miroir/product-strip', attributes: a, empty: __('No products to show: choose a collection or products in the block settings.', 'miroir') })
			);
		},
		save: function () { return null; }
	});
})(window.wp);
