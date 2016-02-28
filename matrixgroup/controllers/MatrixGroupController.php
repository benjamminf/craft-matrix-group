<?php
namespace Craft;

class MatrixGroupController extends BaseController
{
	/**
	 * Copied from Craft\FieldsController::actionSaveField()
	 * @throws HttpException
	 */
	public function actionSaveField()
	{
		$this->requirePostRequest();

		$field = new FieldModel();

		$field->id           = craft()->request->getPost('fieldId');
		$field->groupId      = craft()->request->getRequiredPost('group');
		$field->name         = craft()->request->getPost('name');
		$field->handle       = craft()->request->getPost('handle');
		$field->instructions = craft()->request->getPost('instructions');
		$field->translatable = (bool) craft()->request->getPost('translatable');

		$field->type = craft()->request->getRequiredPost('type');

		$typeSettings = craft()->request->getPost('types');
		if(isset($typeSettings[$field->type]))
		{
			$field->settings = $typeSettings[$field->type];
		}

		if(craft()->fields->saveField($field))
		{
			/**********************************************************************************************************
			 * <inject>
			 */

			if($field->type === 'Matrix')
			{
				$postSettings = $typeSettings[$field->type];
				$fieldType = $field->getFieldType();
				$typeSettings = $fieldType->getSettings();
				$blockTypes = $typeSettings->getBlockTypes();

				foreach($postSettings['blockTypes'] as $postId => $postBlockType)
				{
					$group = array_key_exists('group', $postBlockType);
					$handle = $postBlockType['handle'];

					foreach($blockTypes as $id => $blockType)
					{
						if($blockType->handle === $handle)
						{
							$groupBlockType = new MatrixGroup_BlockTypeModel();
							$groupBlockType->typeId = (int) $blockType->id;

							if($group)
							{
								craft()->matrixGroup->saveBlockType($groupBlockType);
							}
							else
							{
								craft()->matrixGroup->deleteBlockType($groupBlockType);
							}
						}
					}
				}
			}

			/**
			 * </inject>
			 **********************************************************************************************************/

			craft()->userSession->setNotice(Craft::t('Field saved.'));

			if(isset($_POST['redirect']) && mb_strpos($_POST['redirect'], '{fieldId}') !== false)
			{
				craft()->deprecator->log('FieldsController::saveField():fieldId_redirect', 'The {fieldId} token within the ‘redirect’ param on fields/saveField requests has been deprecated. Use {id} instead.');
				$_POST['redirect'] = str_replace('{fieldId}', '{id}', $_POST['redirect']);
			}

			$this->redirectToPostedUrl($field);
		}
		else
		{
			craft()->userSession->setError(Craft::t('Couldn’t save field.'));
		}

		// Send the field back to the template
		craft()->urlManager->setRouteVariables(array(
			'field' => $field
		));
	}
}
