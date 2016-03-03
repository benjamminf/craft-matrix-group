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
			craft()->templates->includeJs('MatrixGroup.levels=' . json_encode($this->_getGroupBlockLevels()));
			craft()->templates->includeJsResource('matrixgroup/js/settings.js');
			craft()->templates->includeJsResource('matrixgroup/js/field.js');
		}
	}

	protected function bindEvents()
	{
		$levelsToSave = array();
		$levelsToDelete = array();

		// TODO Save matrix group markers here instead of controller
		craft()->on('elements.saveElement', function(Event $e) use(&$levelsToSave, &$levelsToDelete)
		{
			$element = $e->params['element'];
			$isNewElement = $e->params['isNewElement'];

			switch($element->elementType)
			{
				case ElementType::MatrixBlock:
				{
					$block = $element;
					$type = $block->getType();
					$field = craft()->fields->getFieldById($type->fieldId);
					$level = 0;

					$postBlocks = craft()->request->getPost('fields.' . $field->handle);

					if($isNewElement)
					{
						$postBlockIds = array_keys($postBlocks);
						$postBlockId = $postBlockIds[$block->sortOrder - 1];
					}
					else
					{
						$postBlockId = $block->id;
					}

					$postBlock = $postBlocks[$postBlockId];

					if(array_key_exists('level', $postBlock))
					{
						$level = $postBlock['level'];
					}

					$blockLevel = new MatrixGroup_BlockLevelModel();
					$blockLevel->blockId = (int) $block->id;
					$blockLevel->level = (int) $level;

					if($blockLevel->level > 0)
					{
						$levelsToSave[] = $blockLevel;
					}
					else
					{
						$levelsToDelete[] = $blockLevel;
					}

					break;
				}
				case ElementType::Entry:
				{
					foreach($levelsToSave as $blockLevel)
					{
						craft()->matrixGroup->saveBlockLevel($blockLevel);
					}

					foreach($levelsToDelete as $blockLevel)
					{
						craft()->matrixGroup->deleteBlockLevel($blockLevel);
					}

					break;
				}
			}
		});
	}

	private function _getGroupBlockTypes()
	{
		$return = array();
		$groups = craft()->matrixGroup->getAllBlockTypes();

		foreach($groups as $group)
		{
			$return[$group->typeId] = $group->id;
			$return[$group->typeHandle] = $group->id;
		}

		return $return;
	}

	private function _getGroupBlockLevels()
	{
		$return = array();
		$levels = craft()->matrixGroup->getAllBlockLevels();

		foreach($levels as $blockLevel)
		{
			$return[$blockLevel->blockId] = $blockLevel->level;
		}

		return $return;
	}
}