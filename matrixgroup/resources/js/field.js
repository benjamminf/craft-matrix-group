(function($)
{
	var Field = Garnish.Base.extend({

		init: function(input)
		{
			this.input = input
		},

		postInit: function(args)
		{
			var that = this
			var input = this.input
			var $blocks = input.$blockContainer.children()

			$blocks.each(function()
			{
				var $block = $(this)

				that.setupBlock($block)
			})
		},

		setupBlock: function($block)
		{
			var $type = $block.children('input[name$="[type]"]')
			var typeHandle = $type.val()

			if(MatrixGroup.groups.hasOwnProperty(typeHandle))
			{
				var $blockGroup = $('<div class="matrixgroup-inner">').appendTo($block)

				$block.addClass('matrixgroup')
			}
		}
	})

	MatrixGroup.onPropertySet(Craft, 'MatrixInput', function()
	{
		MatrixGroup.Field = Field
		MatrixGroup.patchClass(Craft.MatrixInput, Field, true, 'postInit')
	})

})(jQuery)
