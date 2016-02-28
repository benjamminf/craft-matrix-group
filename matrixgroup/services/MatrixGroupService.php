<?php
namespace Craft;

class MatrixGroupService extends BaseApplicationComponent
{
	/**
	 * @param MatrixGroup_BlockTypeModel $model
	 * @return bool
	 * @throws Exception
	 * @throws \Exception
	 */
	public function saveBlockType(MatrixGroup_BlockTypeModel $model)
	{
		$record = null;

		if(is_int($model->id))
		{
			$record = MatrixGroup_BlockTypeRecord::model()->findById($model->id);

			if(!$record)
			{
				throw new Exception(Craft::t('No matrix group block type exists with the ID “{id}”.', array('id' => $model->id)));
			}
		}
		else
		{
			$record = MatrixGroup_BlockTypeRecord::model()->findByAttributes(array('typeId' => $model->typeId));

			if(!$record)
			{
				$record = new MatrixGroup_BlockTypeRecord();
			}
		}

		$blockType = craft()->matrix->getBlockTypeById($model->typeId);

		if(!$blockType)
		{
			throw new Exception(Craft::t('No matrix block type exists with the ID “{id}”.', array('id' => $model->typeId)));
		}

		$record->typeId = $blockType->id;
		$record->typeHandle = $blockType->handle;

		$record->validate();
		$model->addErrors($record->getErrors());

		$success = !$model->hasErrors();

		if($success)
		{
			// Create transaction only if this isn't apart of an already occurring transaction
			$transaction = craft()->db->getCurrentTransaction() ? false : craft()->db->beginTransaction();

			try
			{
				$record->save(false);
				$model->id = $record->id;

				if($transaction)
				{
					$transaction->commit();
				}
			}
			catch(\Exception $e)
			{
				if($transaction)
				{
					$transaction->rollback();
				}

				throw $e;
			}
		}

		return $success;
	}

	/**
	 * @param MatrixGroup_BlockTypeModel $model
	 * @return bool
	 * @throws \Exception
	 */
	public function deleteBlockType(MatrixGroup_BlockTypeModel $model)
	{
		$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
		try
		{
			$condition = array();

			if(is_int($model->id))
			{
				$condition['id'] = $model->id;
			}
			else if(is_int($model->typeId))
			{
				$condition['typeId'] = $model->typeId;
			}
			else if($model->typeHandle)
			{
				$condition['typeHandle'] = $model->typeHandle;
			}
			else
			{
				return false;
			}

			$affectedRows = craft()->db->createCommand()->delete('matrixgroup_blocktypes', $condition);

			if($transaction !== null)
			{
				$transaction->commit();
			}

			return (bool) $affectedRows;
		}
		catch(\Exception $e)
		{
			if($transaction !== null)
			{
				$transaction->rollback();
			}

			throw $e;
		}
	}

	/**
	 * @return array
	 */
	public function getBlockTypes()
	{
		$result = MatrixGroup_BlockTypeRecord::model()->findAll(null, array(
			'id'         => null,
			'typeId'     => null,
			'typeHandle' => null,
		));

		return MatrixGroup_BlockTypeModel::populateModels($result);
	}
}
