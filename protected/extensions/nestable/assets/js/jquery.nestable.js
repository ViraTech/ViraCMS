/*!
 * Nestable jQuery Plugin - Copyright (c) 2012 David Bushell - http://dbushell.com/
 * Dual-licensed under the BSD or MIT licenses
 */
;(function($, window, document, undefined)
{
	var hasTouch = 'ontouchstart' in window;

	/**
	 * Detect CSS pointer-events property
	 * events are normally disabled on the dragging element to avoid conflicts
	 * https://github.com/ausi/Feature-detection-technique-for-pointer-events/blob/master/modernizr-pointerevents.js
	 */
	var hasPointerEvents = (function()
	{
		var el    = document.createElement('div'),
			docEl = document.documentElement;
		if (!('pointerEvents' in el.style)) {
			return false;
		}
		el.style.pointerEvents = 'auto';
		el.style.pointerEvents = 'x';
		docEl.appendChild(el);
		var supports = window.getComputedStyle && window.getComputedStyle(el, '').pointerEvents === 'auto';
		docEl.removeChild(el);
		return !!supports;
	})();

	var eStart  = hasTouch ? 'touchstart'  : 'mousedown',
		eMove   = hasTouch ? 'touchmove'   : 'mousemove',
		eEnd    = hasTouch ? 'touchend'    : 'mouseup';
		eCancel = hasTouch ? 'touchcancel' : 'mouseup';

	var defaults = {
			listNodeName    : 'ul',
			itemNodeName    : 'li',
			labelNodeName	: 'a',
			rootClass       : 'sitemap',
			dragClass       : 'sitemap-dragging',
			placeClass      : 'sitemap-placeholder',
			dataDisabledItem: 'data-disabled-item',
			dataFirstInClass: 'data-homepage',
			dataNoChild     : 'data-no-child',
			emptyClass      : 'sitemap-empty-item',
			maxDepth        : 30,
			threshold       : 5,
			onDragStart     : function() {},
			onDragStop      : function() {}
		};

	function Plugin(element, options)
	{
		this.w  = $(window);
		this.el = $(element);
		this.options = $.extend({}, defaults, options);
		this.init();
	}

	Plugin.prototype = {

		init: function()
		{
			var list = this;

			list.reset();

			list.placeEl = $('<div class="' + list.options.placeClass + '"/>');

			list.el.on('click', 'button', function(e) {
				if (list.dragEl || (!hasTouch && e.button !== 0)) {
					return;
				}
				var target = $(e.currentTarget),
					action = target.data('action'),
					item   = target.parent(list.options.itemNodeName);
			});

			var onStartEvent = function(e)
			{
				var handle = $(e.target);
				if (handle.prop('tagName').toUpperCase() != list.options.labelNodeName.toUpperCase()) {
					handle = handle.closest(list.options.labelNodeName);
				}
				if (handle.hasClass('btn-control')) {
					return;
				}
				if (handle.closest(list.options.itemNodeName + '[' + list.options.dataDisabledItem + ']').length) {
					return;
				}
				if (!handle.length || list.dragEl || (!hasTouch && e.button !== 0) || (hasTouch && e.touches.length !== 1)) {
					return;
				}
				e.preventDefault();
				list.dragStart(hasTouch ? e.touches[0] : e);
			};

			var onMoveEvent = function(e)
			{
				if (list.dragEl) {
					e.preventDefault();
					list.dragMove(hasTouch ? e.touches[0] : e);
				}
			};

			var onEndEvent = function(e)
			{
				if (list.dragEl) {
					e.preventDefault();
					list.dragStop(hasTouch ? e.touches[0] : e);
				}
			};

			if (hasTouch) {
				list.el[0].addEventListener(eStart, onStartEvent, false);
				window.addEventListener(eMove, onMoveEvent, false);
				window.addEventListener(eEnd, onEndEvent, false);
				window.addEventListener(eCancel, onEndEvent, false);
			} else {
				list.el.on(eStart, onStartEvent);
				list.w.on(eMove, onMoveEvent);
				list.w.on(eEnd, onEndEvent);
			}

		},

		serialize: function()
		{
			var data,
				depth = 0,
				list  = this;
				step  = function(level, depth)
				{
					var array = [ ],
						items = level.children(list.options.itemNodeName);
					items.each(function()
					{
						var li   = $(this),
							item = $.extend({}, li.data()),
							sub  = li.children(list.options.listNodeName);
						if (sub.length) {
							item.children = step(sub, depth + 1);
						}
						array.push(item);
					});
					return array;
				};
			data = step(list.el.find(list.options.listNodeName).first(), depth);
			return data;
		},

		reset: function()
		{
			this.mouse = {
				offsetX   : 0,
				offsetY   : 0,
				startX    : 0,
				startY    : 0,
				lastX     : 0,
				lastY     : 0,
				nowX      : 0,
				nowY      : 0,
				distX     : 0,
				distY     : 0,
				dirAx     : 0,
				dirX      : 0,
				dirY      : 0,
				lastDirX  : 0,
				lastDirY  : 0,
				distAxX   : 0,
				distAxY   : 0
			};
			this.moving     = false;
			this.dragEl     = null;
			this.dragRootEl = null;
			this.dragDepth  = 0;
			this.pointEl    = null;
		},

		dragStart: function(e)
		{
			var mouse    = this.mouse,
				target   = $(e.target),
				dragItem = target.closest(this.options.itemNodeName);

			if (typeof this.options.onDragStart == 'function') {
				this.options.onDragStart(e,dragItem);
			}

			this.placeEl.css('height', dragItem.height());

			mouse.offsetX = e.offsetX !== undefined ? e.offsetX : e.pageX - target.offset().left;
			mouse.offsetY = e.offsetY !== undefined ? e.offsetY : e.pageY - target.offset().top;
			mouse.startX = mouse.lastX = e.pageX;
			mouse.startY = mouse.lastY = e.pageY;

			this.dragRootEl = this.el;

			this.dragEl = $(document.createElement(this.options.listNodeName)).addClass(this.options.dragClass);
			this.dragEl.css('width', dragItem.width());

			dragItem.after(this.placeEl);
			dragItem[0].parentNode.removeChild(dragItem[0]);
			dragItem.appendTo(this.dragEl);
			$(document.body).append(this.dragEl);
			this.dragEl.css({
				'left' : e.pageX - mouse.offsetX,
				'top'  : e.pageY - mouse.offsetY
			});
			// total depth of dragging item
			var i, depth,
				items = this.dragEl.find(this.options.itemNodeName);
			for (i = 0; i < items.length; i++) {
				depth = $(items[i]).parents(this.options.listNodeName).length;
				if (depth > this.dragDepth) {
					this.dragDepth = depth;
				}
			}
		},

		dragStop: function(e)
		{
			var el = this.dragEl.children(this.options.itemNodeName).first();
			el[0].parentNode.removeChild(el[0]);
			this.placeEl.replaceWith(el);

			this.dragEl.remove();
			this.el.trigger('change');

			if (typeof this.options.onDragStop == 'function') {
				this.options.onDragStop(e,el);
			}

			this.reset();
		},

		dragMove: function(e)
		{
			var list, parent, prev, next, depth,
				opt   = this.options,
				mouse = this.mouse;

			this.dragEl.css({
				'left' : e.pageX - mouse.offsetX,
				'top'  : e.pageY - mouse.offsetY
			});

			// mouse position last events
			mouse.lastX = mouse.nowX;
			mouse.lastY = mouse.nowY;
			// mouse position this events
			mouse.nowX  = e.pageX;
			mouse.nowY  = e.pageY;
			// distance mouse moved between events
			mouse.distX = mouse.nowX - mouse.lastX;
			mouse.distY = mouse.nowY - mouse.lastY;
			// direction mouse was moving
			mouse.lastDirX = mouse.dirX;
			mouse.lastDirY = mouse.dirY;
			// direction mouse is now moving (on both axis)
			mouse.dirX = mouse.distX === 0 ? 0 : mouse.distX > 0 ? 1 : -1;
			mouse.dirY = mouse.distY === 0 ? 0 : mouse.distY > 0 ? 1 : -1;
			// axis mouse is now moving on
			var newAx   = Math.abs(mouse.distX) > Math.abs(mouse.distY) ? 1 : 0;

			// do nothing on first move
			if (!mouse.moving) {
				mouse.dirAx  = newAx;
				mouse.moving = true;
				return;
			}

			// calc distance moved on this axis (and direction)
			if (mouse.dirAx !== newAx) {
				mouse.distAxX = 0;
				mouse.distAxY = 0;
			} else {
				mouse.distAxX += Math.abs(mouse.distX);
				if (mouse.dirX !== 0 && mouse.dirX !== mouse.lastDirX) {
					mouse.distAxX = 0;
				}
				mouse.distAxY += Math.abs(mouse.distY);
				if (mouse.dirY !== 0 && mouse.dirY !== mouse.lastDirY) {
					mouse.distAxY = 0;
				}
			}
			mouse.dirAx = newAx;

			/**
			 * move horizontal
			 */
			if (mouse.dirAx && mouse.distAxX >= opt.threshold) {
				// reset move distance on x-axis for new phase
				mouse.distAxX = 0;
				prev = this.placeEl.prev(opt.itemNodeName);
				// increase horizontal level if previous sibling exists and is not collapsed
				if (mouse.distX > 0 && prev.length && !prev.attr(opt.dataNoChild)) {
					// cannot increase level when item above is collapsed
					list = prev.find(opt.listNodeName).last();
					// check if depth limit has reached
					depth = this.placeEl.parents(opt.listNodeName).length;
					if (depth + this.dragDepth <= opt.maxDepth) {
						// create new sub-level if one doesn't exist
						if (!list.length) {
							list = $('<' + opt.listNodeName + '/>');
							list.append(this.placeEl);
							prev.append(list);
						} else {
							// else append to next level up
							list = prev.children(opt.listNodeName).last();
							list.append(this.placeEl);
						}
					}
				}
				// decrease horizontal level
				if (mouse.distX < 0) {
					// we can't decrease a level if an item preceeds the current one
					next = this.placeEl.next(opt.itemNodeName);
					if (!next.length) {
						parent = this.placeEl.parent();
						this.placeEl.closest(opt.itemNodeName).after(this.placeEl);
					}
				}
			}

			var isEmpty = false;

			// find list item under cursor
			if (!hasPointerEvents) {
				this.dragEl[0].style.visibility = 'hidden';
			}
			this.pointEl = $(document.elementFromPoint(e.pageX - document.body.scrollLeft, e.pageY - (window.pageYOffset || document.documentElement.scrollTop)));
			if (!hasPointerEvents) {
				this.dragEl[0].style.visibility = 'visible';
			}
			if (this.pointEl.prop('tagName').toUpperCase() == opt.labelNodeName.toUpperCase()) {
				this.pointEl = this.pointEl.closest(opt.itemNodeName);
			}
			if (this.pointEl.hasClass(opt.emptyClass)) {
				isEmpty = true;
			}
			else if (!this.pointEl.length || this.pointEl.prop('tagName').toUpperCase() != opt.itemNodeName.toUpperCase()) {
				return;
			}

			// find parent list of item under cursor
			var pointElRoot = this.pointEl.closest('.' + opt.rootClass);

			/**
			 * move vertical
			 */
			if (!mouse.dirAx || isEmpty) {
				// check depth limit
				depth = this.dragDepth - 1 + this.pointEl.parents(opt.listNodeName).length;
				if (depth > opt.maxDepth) {
					return;
				}
				var before = e.pageY < (this.pointEl.offset().top + this.pointEl.height() / 2);
					parent = this.placeEl.parent();
				// if empty create new list to replace empty placeholder
				if (isEmpty) {
					list = $(document.createElement(opt.listNodeName));
					list.append(this.placeEl);
					this.pointEl.replaceWith(list);
				}
				else if (before && !this.pointEl.data(opt.firstInClass)) {
					this.pointEl.before(this.placeEl);
				}
				else {
					this.pointEl.after(this.placeEl);
				}
				if (!this.dragRootEl.find(opt.itemNodeName).length) {
					this.dragRootEl.append('<div class="' + opt.emptyClass + '"/>');
				}
			}
		}

	};

	$.fn.nestable = function(params)
	{
		var lists  = this,
			retval = this;

		lists.each(function()
		{
			var plugin = $(this).data("nestable");

			if (!plugin) {
				$(this).data("nestable", new Plugin(this, params));
				$(this).data("nestable-id", new Date().getTime());
			} else {
				if (typeof params === 'string' && typeof plugin[params] === 'function') {
					retval = plugin[params]();
				}
			}
		});

		return retval || lists;
	};
})(window.jQuery, window, document);