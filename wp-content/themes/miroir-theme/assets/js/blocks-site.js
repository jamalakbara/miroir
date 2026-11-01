/*
 * Editor UI of the Miroir site blocks (header, footer, page, product and
 * listing sections). One generic edit per block, built from the "miroir" key
 * of its block.json (passed in window.miroirSiteBlocks by inc/blocks.php):
 * - controls: sidebar settings (text, toggle, select, number);
 * - container: the block holds other blocks (reorder, remove, add your own);
 *   otherwise the canvas shows the server render, i.e. what the storefront prints.
 * Plain script on the wp.* globals: no build step.
 */
(function (wp, blocks) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var ServerSideRender = wp.serverSideRender;

	/** "site-nav__{slot}" with the block's attribute values filled in. */
	function fill(template, attributes) {
		return String(template || '').replace(/\{(\w+)\}/g, function (match, key) {
			return attributes[key] !== undefined ? attributes[key] : match;
		});
	}

	function control(def, props) {
		var value = props.attributes[def.attr];
		var set = function (v) {
			var next = {};
			next[def.attr] = v;
			props.setAttributes(next);
		};
		switch (def.type) {
			case 'toggle':
				return el(c.ToggleControl, { key: def.attr, label: def.label, help: def.help, checked: !!value, onChange: set, __nextHasNoMarginBottom: true });
			case 'select':
				return el(c.SelectControl, {
					key: def.attr, label: def.label, help: def.help, value: value, onChange: set, __nextHasNoMarginBottom: true,
					options: def.options.map(function (o) { return { value: o[0], label: o[1] }; })
				});
			case 'number':
				return el(c.TextControl, {
					key: def.attr, type: 'number', label: def.label, help: def.help, value: String(value), __nextHasNoMarginBottom: true,
					onChange: function (v) { set(v === '' ? 0 : Number(v)); }
				});
			default:
				return el(c.TextControl, { key: def.attr, label: def.label, help: def.help, value: value || '', onChange: set, __nextHasNoMarginBottom: true });
		}
	}

	function Inspector(props) {
		var controls = props.def.controls || [];
		if (!controls.length) {
			return null;
		}
		return el(be.InspectorControls, null,
			el(c.PanelBody, { title: __('Settings', 'miroir') },
				el('div', { style: { display: 'grid', gap: '16px' } }, controls.map(function (d) { return control(d, props.edit); }))
			)
		);
	}

	function containerEdit(def) {
		return function (props) {
			var box = def.container;
			var blockProps = be.useBlockProps({ className: fill(box.className, props.attributes) });
			var inner = be.useInnerBlocksProps(blockProps, { template: box.template, templateLock: false });
			return el(Fragment, null, el(Inspector, { def: def, edit: props }), el(box.tag || 'div', inner));
		};
	}

	function leafEdit(def) {
		return function (props) {
			var blockProps = be.useBlockProps({ className: 'miroir-block-preview' });
			return el(Fragment, null,
				el(Inspector, { def: def, edit: props }),
				el('div', blockProps, el(ServerSideRender, { block: def.name, attributes: props.attributes, skipBlockSupportAttributes: true }))
			);
		};
	}

	blocks.forEach(function (def) {
		wp.blocks.registerBlockType(def.name, {
			edit: def.container ? containerEdit(def) : leafEdit(def),
			save: def.container ? function () { return el(be.InnerBlocks.Content); } : function () { return null; }
		});
	});
})(window.wp, window.miroirSiteBlocks || []);
