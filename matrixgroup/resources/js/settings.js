(function($)
{
	function Settings(configurator)
	{
		this.configurator = configurator

		MatrixGroup.patchMethod(configurator, this, 'addBlockType')
		MatrixGroup.patchMethod(configurator, this, 'getBlockTypeSettingsModal')
	}

	Settings.prototype.addBlockType = function(args, output)
	{
		var configurator = this.configurator
		var modal = configurator.blockTypeSettingsModal

		MatrixGroup.patchMethod(modal, {onSubmit: function()
		{
			var id = 'new' + configurator.totalNewBlockTypes
			var blockType = configurator.blockTypes[id]
			var isGroup = modal.$groupInput.is(':checked')
			var inputName = 'types[Matrix][blockTypes][' + id + '][group]'

			blockType.$groupHiddenInput = $('<input type="hidden" name="' + inputName + '">').appendTo(blockType.$item)

			blockType.$groupHiddenInput.val(isGroup)
			blockType.$item.toggleClass('matrixgroup', isGroup)

			MatrixGroup.patchMethod(blockType, {applySettings: function()
			{
				var isGroup = modal.$groupInput.is(':checked')

				blockType.$groupHiddenInput.val(isGroup)
				blockType.$item.toggleClass('matrixgroup', isGroup)

			}}, 'applySettings')

		}}, 'onSubmit')

		return output
	}

	Settings.prototype.getBlockTypeSettingsModal = function(args, output)
	{
		var configurator = this.configurator
		var modal = configurator.blockTypeSettingsModal

		if(!modal.$groupField)
		{
			modal.$groupField = $('<div class="field checkboxfield">').insertAfter(modal.$handleField)
			modal.$groupInput = $('<input type="checkbox" value="1" id="new-block-type-group" class="checkbox">').appendTo(modal.$groupField)
			modal.$groupLabel = $('<label for="new-block-type-group">' + Craft.t('This is a group block type') + '</label>').appendTo(modal.$groupField)
		}

		return output
	}

	MatrixGroup.onPropertySet(Craft, 'MatrixConfigurator', function()
	{
		MatrixGroup.Settings = Settings
		MatrixGroup.patchClass(Craft.MatrixConfigurator, MatrixGroup.Settings, true)
	})

})(jQuery)
