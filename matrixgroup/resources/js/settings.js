(function($)
{
	var Settings = Garnish.Base.extend({

		/**
		 *
		 * @param configurator
		 * @constructor
		 */
		init: function(configurator)
		{
			this.configurator = configurator

			MatrixGroup.patchMethod(configurator, this, 'addBlockType')
			MatrixGroup.patchMethod(configurator, this, 'getBlockTypeSettingsModal')
		},

		/**
		 *
		 * @param args
		 */
		postInit: function(args)
		{
			var configurator = this.configurator
			var blockTypes = configurator.blockTypes

			for(var id in blockTypes) if(blockTypes.hasOwnProperty(id))
			{
				var blockType = blockTypes[id]

				this.initBlockType(blockType)
				this.setBlockTypeGroup(blockType, MatrixGroup.groups.hasOwnProperty(id))
			}
		},

		/**
		 *
		 * @param blockType
		 */
		initBlockType: function(blockType)
		{
			var configurator = this.configurator
			var inputName = 'types[Matrix][blockTypes][' + blockType.id + '][group]'

			blockType.$groupHiddenInput = $('<input type="hidden" name="' + inputName + '" value="1">')

			MatrixGroup.patchMethod(blockType, {applySettings: function()
			{
				this.setBlockTypeGroup(blockType)

			}.bind(this)}, 'applySettings')

			this.addListener(blockType.$settingsBtn, 'click', function()
			{
				var modal = configurator.getBlockTypeSettingsModal()
				var isGroup = blockType.$groupHiddenInput.parent().length > 0

				modal.$groupInput.prop('checked', isGroup)
			});
		},

		/**
		 *
		 * @param blockType
		 * @param isGroup
		 */
		setBlockTypeGroup: function(blockType, isGroup)
		{
			var configurator = this.configurator
			var modal = configurator.getBlockTypeSettingsModal()

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
		},

		/**
		 *
		 * @param args
		 * @param output
		 * @returns {*}
		 */
		addBlockType: function(args, output)
		{
			var configurator = this.configurator
			var modal = configurator.getBlockTypeSettingsModal()

			modal.$groupInput.prop('checked', false)

			MatrixGroup.patchMethod(modal, {onSubmit: function()
			{
				var id = 'new' + configurator.totalNewBlockTypes
				var blockType = configurator.blockTypes[id]

				this.initBlockType(blockType)
				this.setBlockTypeGroup(blockType)

			}.bind(this)}, 'onSubmit')

			return output
		},

		/**
		 *
		 * @param args
		 * @param output
		 * @returns {*}
		 */
		getBlockTypeSettingsModal: function(args, output)
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
	});

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
