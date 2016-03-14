(function($)
{
	var Field = Garnish.Base.extend({

		init: function(input)
		{
			this.input = input

			MatrixGroup.patchMethod(input, this, 'addBlock')
			MatrixGroup.patchMethod(input, this, 'updateAddBlockBtn')
			MatrixGroup.patchMethod(input, this, 'canAddMoreBlocks')
		},

		postInit: function(args)
		{
			var that = this
			var input = this.input
			var $blocks = input.$blockContainer.children()
			var parentMap = []

			$blocks.each(function()
			{
				var $block = $(this)
				var id = $block.data('id')
				var level = (MatrixGroup.levels.hasOwnProperty(id) ? MatrixGroup.levels[id] : 0)|0;

				that.setupBlock($block, level)

				if(level > 0)
				{
					var $parentBlock = that.findParentBlock($block, level)

					if($parentBlock)
					{
						parentMap.push({
							block: $block,
							parent: $parentBlock
						})
					}
				}
			})

			for(var i = 0; i < parentMap.length; i++)
			{
				var map = parentMap[i]
				var $block = map.block
				var $parentBlock = map.parent

				var $groupInner = $parentBlock.children('.matrixgroup-inner')
				var $blocksContainer = $groupInner.children('.matrixgroup-blocks')
				var $blocksAnchor = $blocksContainer.children('.matrixgroup-anchor')

				$block.insertBefore($blocksAnchor)
			}
		},

		/**
		 * Preconditions:
		 * - Assumes all sibling elements before block have had "setupBlock" invoked on them
		 * - Assumes all matrix blocks are currently siblings, and haven't been nested yet
		 *
		 * @param $block
		 * @param level
		 * @returns {*}
		 */
		findParentBlock: function($block, level)
		{
			var $prevBlock = $block

			while($prevBlock.length > 0)
			{
				$prevBlock = $prevBlock.prev()

				var $prevLevelInput = $prevBlock.children('.matrixgroup-level')
				var prevLevel = $prevLevelInput.val()|0

				if(prevLevel < level)
				{
					return $prevBlock
				}
			}

			return false
		},

		setupBlock: function($block, level)
		{
			level = level|0

			var input = this.input
			var id = $block.data('id')
			var $type = $block.children('input[name$="[type]"]')
			var typeHandle = $type.val()
			var levelInputName = input.inputNamePrefix + '[' + id + '][level]'
			var $levelInput = $('<input type="hidden" name="' + levelInputName + '" value="' + level + '" class="matrixgroup-level">').appendTo($block)

			if(MatrixGroup.groups.hasOwnProperty(typeHandle))
			{
				var $blockGroup = $('<div class="matrixgroup-inner">').appendTo($block)
				var $blocksContainer = $('<div class="matrixgroup-blocks">').appendTo($blockGroup)
				var $blocksAnchor = $('<div class="matrixgroup-anchor">').appendTo($blocksContainer)
				var $buttonsContainer = input.$addBlockBtnContainer.clone().appendTo($blockGroup)
				var $buttonsGroup = $buttonsContainer.children('.btngroup')
				var $buttons = $buttonsGroup.children('.btn')
				var $addBlock = $buttonsContainer.children('.menubtn')
				var $addBlockMenu = $('<div class="menu">').insertAfter($addBlock)
				var $addBlockMenuUl = $('<ul>').appendTo($addBlockMenu)

				$buttons.each(function()
				{
					var $button = $(this)

					var name = $button.text()
					var handle = $button.data('type')

					var $li = $('<li>').appendTo($addBlockMenuUl)
					var $a = $('<a data-type="' + handle + '">').text(name).appendTo($li)
				})

				$buttonsContainer.removeClass('buttons last')
				$buttonsContainer.addClass('matrixgroup-buttons')

				this.addListener($buttons, 'click', function(e)
				{
					var type = $(e.target).data('type');

					input.addBlock(type, $blocksAnchor, level + 1)
				})

				new Garnish.MenuBtn($addBlock, {

					onOptionSelect: function(option)
					{
						var type = $(option).data('type')

						input.addBlock(type, $blocksAnchor, level + 1)

					}.bind(this)
				})

				var buttonsGroupWidth = null
				var buttonsContainerWidth = null
				var showingAddBlockMenu = false

				function setNewBlockBtn()
				{
					if(!buttonsGroupWidth)
					{
						buttonsGroupWidth = $buttonsGroup.width()
						if(!buttonsGroupWidth) return
					}

					if(buttonsGroupWidth !== (buttonsContainerWidth = $buttonsContainer.width()))
					{
						if(buttonsGroupWidth > buttonsContainerWidth)
						{
							if(!showingAddBlockMenu)
							{
								$buttonsGroup.addClass('hidden')
								$addBlock.removeClass('hidden')
								showingAddBlockMenu = true
							}
						}
						else
						{
							if(showingAddBlockMenu)
							{
								$addBlock.addClass('hidden')
								$buttonsGroup.removeClass('hidden')
								showingAddBlockMenu = false

								if(navigator.userAgent.indexOf('Safari') !== -1)
								{
									Garnish.requestAnimationFrame(function()
									{
										$buttonsGroup.css('opacity', 0.99)

										Garnish.requestAnimationFrame(function()
										{
											$buttonsGroup.css('opacity', '')
										});
									});
								}
							}
						}
					}
				}

				input.updateAddBlockBtn();

				this.addListener($buttonsContainer, 'resize', setNewBlockBtn)
				Garnish.$doc.ready(setNewBlockBtn)

				$block.addClass('matrixgroup')
			}
		},

		addBlock: function(args, output)
		{
			var level = args[2]|0
			var input = this.input
			var id = 'new' + input.totalNewBlocks
			var $block = input.$blockContainer.find('.matrixblock[data-id="' + id + '"]')
			var animateInfo = $.data($block[0], 'velocity')

			$block.velocity('stop')

			this.setupBlock($block, level)

			$block.css({
				'opacity': 0,
				'margin-bottom': -$block.outerHeight()
			})

			$block.velocity({
				opacity: 1,
				'margin-bottom': 10
			}, animateInfo.opts.duration, animateInfo.opts.complete)

			return output
		},

		updateAddBlockBtn: function(args, output)
		{
			var input = this.input

			if(input.canAddMoreBlocks())
			{
				input.$blockContainer.find('.matrixgroup-buttons > .btngroup').removeClass('disabled')
				input.$blockContainer.find('.matrixgroup-buttons > .menubtn').removeClass('disabled')
			}
			else
			{
				input.$blockContainer.find('.matrixgroup-buttons > .btngroup').addClass('disabled')
				input.$blockContainer.find('.matrixgroup-buttons > .menubtn').addClass('disabled')
			}

			return output
		},

		canAddMoreBlocks: function(args, output)
		{
			var input = this.input

			return (!input.maxBlocks || input.$blockContainer.find('.matrixblock').length < input.maxBlocks)
		}
	})

	MatrixGroup.onPropertySet(Craft, 'MatrixInput', function()
	{
		MatrixGroup.Field = Field
		MatrixGroup.patchClass(Craft.MatrixInput, Field, true, 'postInit')
	})
	
	// Partial support for the PimpMyMatrix plugin. Initialises Matrix block tabs, but not the buttons.
	MatrixGroup.onPropertySet(window, 'PimpMyMatrix', function()
	{
		MatrixGroup.onPropertySet(PimpMyMatrix, 'FieldManipulator', function()
		{
			var fn = PimpMyMatrix.FieldManipulator.prototype
			var superMethod = fn.processMatrixFields

			fn.processMatrixFields = function()
			{
				var that = this
				superMethod.apply(this, arguments)

				this.$matrixContainer.each(function()
				{
					var $matrixField = $(this)

					$matrixField.find('.matrixgroup-blocks > .matrixblock').each(function()
					{
						that.initBlocks($(this), $matrixField);
					})
				})
			}
		})
	})

})(jQuery)
