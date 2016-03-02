<?php

namespace Craft;

class MatrixGroupPlugin extends BasePlugin
{
	public function getName()
	{
		return Craft::t('Matrix Group');
	}

	public function getDescription()
	{
		return 'Allow your matrix blocks to group other blocks within';
	}

	public function getVersion()
	{
		return '0.0.1';
	}

	public function getSchemaVersion()
	{
		return '0.0.1';
	}

	public function getDeveloper()
	{
		return 'Benjamin Fleming';
	}

	public function getDeveloperUrl()
	{
		return 'http://benjamminf.github.io';
	}

	public function getDocumentationUrl()
	{
		return 'https://github.com/benjamminf/craft-matrix-group';
	}

	public function getReleaseFeedUrl()
	{
		return 'https://raw.githubusercontent.com/benjamminf/craft-matrix-group/master/releases.json';
	}

	public function isCraftRequiredVersion()
	{
		return version_compare(craft()->getVersion(), '2.5', '>=');
	}

	public function init()
	{
		parent::init();

		if(craft()->request->isCpRequest() && $this->isCraftRequiredVersion())
		{
			$this->includeResources();
			$this->bindEvents();
		}
	}

	protected function includeResources()
	{
		if(!craft()->request->isAjaxRequest())
		{
			craft()->templates->includeCssResource('matrixgroup/css/main.css');
			craft()->templates->includeJsResource('matrixgroup/js/main.js');
			craft()->templates->includeJs('MatrixGroup.groups=' . json_encode($this->_getGroupBlockTypes()));
			craft()->templates->includeJsResource('matrixgroup/js/settings.js');
			craft()->templates->includeJsResource('matrixgroup/js/field.js');
		}
	}

	protected function bindEvents()
	{
		$blockIdMap = array();

		// TODO Save matrix group markers here instead of controller
		craft()->on('elements.saveElement', function(Event $e) use($blockIdMap)
		{
			$element = $e->params['element'];
			$isNewElement = $e->params['isNewElement'];

			if($element->elementType == ElementType::MatrixBlock)
			{
				$block = $element;
				$type = $block->getType();
				$field = craft()->fields->getFieldById($type->fieldId);

				$postBlocks = craft()->request->getPost('fields.' . $field->handle);
				$parentBlockId = null;

				if($isNewElement)
				{
					$postBlockIds = array_keys($postBlocks);
					$postBlockId = $postBlockIds[$block->sortOrder - 1];

					// Save this in case it has any children that need it's ID
					$blockIdMap[$postBlockId] = $block->id;
				}
				else
				{
					$postBlockId = $block->id;
				}

				$postBlock = $postBlocks[$postBlockId];

				if(array_key_exists('parent', $postBlock))
				{
					$postBlockParentId = $postBlock['parent'];
					$isParentNew = (strncmp($postBlockParentId, 'new', 3) === 0);

					if($isParentNew)
					{
						$parentBlockId = $blockIdMap[$postBlockParentId];
					}
					else
					{
						$parentBlockId = $postBlockParentId;
					}
				}

				if($parentBlockId)
				{
					// TODO Save parent
				}
				else
				{
					// TODO Delete parent
				}
			}
		});
	}

	private function _getGroupBlockTypes()
	{
		$return = array();
		$groups = craft()->matrixGroup->getBlockTypes();

		foreach($groups as $group)
		{
			$return[$group->typeId] = $group->id;
			$return[$group->typeHandle] = $group->id;
		}

		return $return;
	}
}