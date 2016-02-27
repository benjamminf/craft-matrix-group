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
		}
	}

	protected function includeResources()
	{
		if(!craft()->request->isAjaxRequest())
		{
			craft()->templates->includeCssResource('matrixgroup/css/main.css');
			craft()->templates->includeJsResource('matrixgroup/js/main.js');
			craft()->templates->includeJsResource('matrixgroup/js/settings.js');
		}
	}
}