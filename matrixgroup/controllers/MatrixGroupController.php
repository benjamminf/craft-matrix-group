<?php
namespace Craft;

class MatrixGroupController extends BaseController
{
	public function actionSaveField()
	{
		$this->requirePostRequest();

		craft()->runController('fields/saveField');
	}
}
