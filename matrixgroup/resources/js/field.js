(function($)
{
	var Field = Garnish.Base.extend({

		init: function(input)
		{
			this.input = input

			MatrixGroup.patchMethod(input, this, 'addBlock')
		},

		postInit: function(args)
		{
			var that = this
			var input = this.input
			var $blocks = input.$blockContainer.children()

			$blocks.each(function()
			{
				var $block = $(this)
				var id = $block.data('id')

				if(MatrixGroup.parents.hasOwnProperty(id))
				{
					var parentId = MatrixGroup.parents[id]
					var $parent = $blocks.filter('[data-id="' + parentId + '"]')

					if($parent.length > 0)
					{
						that.setupBlock($parent)

						var $parentInner = $parent.children('.matrixgroup-inner')
						var $parentBlocksContainer = $parentInner.children('.matrixgroup-blocks')
						var $parentBlocksAnchor = $parentBlocksContainer.children('.matrixgroup-anchor')

						$block.insertBefore($parentBlocksAnchor)
					}
				}

				that.setupBlock($block)
			})
		},

		setupBlock: function($block)
		{
			if($block.hasClass('matrixgroup'))
			{
				return
			}

			var input = this.input
			var id = $block.data('id')
			var $type = $block.children('input[name$="[type]"]')
			var typeHandle = $type.val()
			var $blockNested = $block.parent('.matrixgroup-blocks')

			if($blockNested.length > 0)
			{
				var $parentBlock = $blockNested.closest('.matrixblock')
				var parentId = $parentBlock.data('id')
				var parentInputName = input.inputNamePrefix + '[' + id + '][parent]'

				$('<input type="hidden" name="' + parentInputName + '" value="' + parentId + '">').appendTo($block)
			}

			if(MatrixGroup.groups.hasOwnProperty(typeHandle))
			{
				var $blockGroup = $('<div class="matrixgroup-inner">').appendTo($block)
				var $blocksContainer = $('<div class="matrixgroup-blocks">').appendTo($blockGroup)
				var $blocksAnchor = $('<div class="matrixgroup-anchor">').appendTo($blocksContainer)
				var $buttonsContainer = input.$addBlockBtnContainer.clone().appendTo($blockGroup)
				var $buttonsGroup = $buttonsContainer.children('.btngroup')
				var $buttons = $buttonsGroup.children('.btn')
				var $addBlock = $buttonsContainer.children('.menubtn')

				$buttonsContainer.removeClass('buttons last')
				$buttonsContainer.addClass('matrixgroup-buttons')

				this.addListener($buttons, 'click', function(e)
				{
					var type = $(e.target).data('type');

					input.addBlock(type, $blocksAnchor)
				})

				new Garnish.MenuBtn($addBlock,
				{
					onOptionSelect: function(option)
					{
						var type = $(option).data('type')

						input.addBlock(type, $blocksAnchor)

					}.bind(this)
				})

				$block.addClass('matrixgroup')
			}
		},

		addBlock: function(args, output)
		{
			var input = this.input
			var id = 'new' + input.totalNewBlocks
			var $block = input.$blockContainer.find('.matrixblock[data-id="' + id + '"]')
			var animateInfo = $.data($block[0], 'velocity')

			$block.velocity('stop')

			this.setupBlock($block)

			$block.css({
				'opacity': 0,
				'margin-bottom': -$block.outerHeight()
			})

			$block.velocity({
				opacity: 1,
				'margin-bottom': 10
			}, animateInfo.opts.duration, animateInfo.opts.complete)

			return output
		}
	})

	MatrixGroup.onPropertySet(Craft, 'MatrixInput', function()
	{
		MatrixGroup.Field = Field
		MatrixGroup.patchClass(Craft.MatrixInput, Field, true, 'postInit')
	})

})(jQuery)
