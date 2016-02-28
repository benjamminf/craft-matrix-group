(function($)
{
	/**
	 *
	 * @param configurator
	 * @constructor
	 */
	function Settings(configurator)
	{
		this.configurator = configurator

		MatrixGroup.patchMethod(configurator, this, 'addBlockType')
		MatrixGroup.patchMethod(configurator, this, 'getBlockTypeSettingsModal')
	}

	/**
	 *
	 * @param args
	 */
	Settings.prototype.postInit = function(args)
	{
		var configurator = this.configurator
		var blockTypes = configurator.blockTypes

		for(var id in blockTypes) if(blockTypes.hasOwnProperty(id))
		{
			var blockType = blockTypes[id]

			this.initBlockType(blockType)
			this.setBlockTypeGroup(blockType, MatrixGroup.groups.hasOwnProperty(id))
		}
	}

	/**
	 *
	 * @param blockType
	 */
	Settings.prototype.initBlockType = function(blockType)
	{
		var inputName = 'types[Matrix][blockTypes][' + blockType.id + '][group]'

		blockType.$groupHiddenInput = $('<input type="hidden" name="' + inputName + '" value="1">')

		MatrixGroup.patchMethod(blockType, {applySettings: function()
		{
			this.setBlockTypeGroup(blockType)

		}.bind(this)}, 'applySettings')
	}

	/**
	 *
	 * @param blockType
	 * @param isGroup
	 */
	Settings.prototype.setBlockTypeGroup = function(blockType, isGroup)
	{
		var configurator = this.configurator
		var modal = configurator.blockTypeSettingsModal

		isGroup = typeof isGroup === 'boolean' ? isGroup : modal.$groupInput.is(':checked')

		if(isGroup)
		{
			blockType.$groupHiddenInput.appendTo(blockType.$item)
		}
		else
		{
			blockType.$groupHiddenInput.remove()
		}

		blockType.$item.toggleClass('matrixgroup', isGroup)
	}

	/**
	 *
	 * @param args
	 * @param output
	 * @returns {*}
	 */
	Settings.prototype.addBlockType = function(args, output)
	{
		var configurator = this.configurator
		var modal = configurator.blockTypeSettingsModal

		MatrixGroup.patchMethod(modal, {onSubmit: function()
		{
			var id = 'new' + configurator.totalNewBlockTypes
			var blockType = configurator.blockTypes[id]

			this.initBlockType(blockType)
			this.setBlockTypeGroup(blockType)

		}.bind(this)}, 'onSubmit')

		return output
	}

	/**
	 *
	 * @param args
	 * @param output
	 * @returns {*}
	 */
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
		MatrixGroup.patchClass(Craft.MatrixConfigurator, Settings, true, 'postInit')
	})

	$('input[name="action"]').each(function()
	{
		var $this = $(this)

		if($this.val() === 'fields/saveField')
		{
			$this.val('matrixGroup/saveField')
		}
	})

})(jQuery)
