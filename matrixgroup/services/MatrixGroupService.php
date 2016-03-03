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

			$tableName = (new MatrixGroup_BlockTypeRecord())->getTableName();
			$affectedRows = craft()->db->createCommand()->delete($tableName, $condition);

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

	/**
	 * @param MatrixGroup_BlockParentModel $model
	 * @return bool
	 * @throws Exception
	 * @throws \Exception
	 */
	public function saveBlockParent(MatrixGroup_BlockParentModel $model)
	{
		$record = null;

		if(is_int($model->id))
		{
			$record = MatrixGroup_BlockParentRecord::model()->findById($model->id);

			if(!$record)
			{
				throw new Exception(Craft::t('No matrix group block parent exists with the ID “{id}”.', array('id' => $model->id)));
			}
		}
		else
		{
			$record = MatrixGroup_BlockParentRecord::model()->findByAttributes(array(
				'blockId'  => $model->blockId,
				'parentId' => $model->parentId,
			));

			if(!$record)
			{
				$record = new MatrixGroup_BlockParentRecord();
			}
		}

		$block = craft()->matrix->getBlockById($model->blockId);

		if(!$block)
		{
			return false;
			throw new Exception(Craft::t('No matrix block exists with the ID “{id}”.', array('id' => $model->blockId)));
		}

		$parent = craft()->matrix->getBlockById($model->parentId);

		if(!$parent)
		{
			return false;
			throw new Exception(Craft::t('No matrix block exists with the ID “{id}”.', array('id' => $model->parentId)));
		}

		$record->blockId  = $block->id;
		$record->parentId = $parent->id;

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
	 * @param MatrixGroup_BlockParentModel $model
	 * @return bool
	 * @throws \Exception
	 */
	public function deleteBlockParent(MatrixGroup_BlockParentModel $model)
	{
		$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
		try
		{
			$condition = array();

			if(is_int($model->id))
			{
				$condition['id'] = $model->id;
			}
			else
			{
				if(is_int($model->blockId))
				{
					$condition['blockId']  = $model->blockId;
				}

				if(is_int($model->parentId))
				{
					$condition['parentId']  = $model->parentId;
				}
			}

			if(empty($condition))
			{
				return false;
			}

			$tableName = (new MatrixGroup_BlockParentRecord())->getTableName();
			$affectedRows = craft()->db->createCommand()->delete($tableName, $condition);

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
	public function getBlockParents()
	{
		$result = MatrixGroup_BlockParentRecord::model()->findAll(null, array(
			'id'       => null,
			'blockId'  => null,
			'parentId' => null,
		));

		return MatrixGroup_BlockParentModel::populateModels($result);
	}
}
