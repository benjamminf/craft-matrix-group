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
				throw new Exception(Craft::t('No matrix group block type exists with the ID \'{id}\'.', array('id' => $model->id)));
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
			throw new Exception(Craft::t('No matrix block type exists with the ID \'{id}\'.', array('id' => $model->typeId)));
		}

		$record->typeId = $blockType->id;
		$record->typeHandle = $model->typeHandle ?: $blockType->handle;

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
	public function getAllBlockTypes()
	{
		$result = MatrixGroup_BlockTypeRecord::model()->findAll(null, array(
			'id'         => null,
			'typeId'     => null,
			'typeHandle' => null,
		));

		return MatrixGroup_BlockTypeModel::populateModels($result);
	}

	public function saveBlockLevel(MatrixGroup_BlockLevelModel $model)
	{
		$record = null;

		if(is_int($model->id))
		{
			$record = MatrixGroup_BlockLevelRecord::model()->findById($model->id);

			if(!$record)
			{
				throw new Exception(Craft::t('No matrix group block level exists with the ID \'{id}\'.', array('id' => $model->id)));
			}
		}
		else
		{
			$record = MatrixGroup_BlockLevelRecord::model()->findByAttributes(array('blockId' => $model->blockId));

			if(!$record)
			{
				$record = new MatrixGroup_BlockLevelRecord();
			}
		}

		$block = craft()->matrix->getBlockById($model->blockId);

		if(!$block)
		{
			throw new Exception(Craft::t('No matrix block exists with the ID \'{id}\'.', array('id' => $model->blockId)));
		}

		$record->blockId = $block->id;
		$record->level   = $model->level;

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

	public function deleteBlockLevel(MatrixGroup_BlockLevelModel $model)
	{
		$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
		try
		{
			$condition = array();

			if(is_int($model->id))
			{
				$condition['id'] = $model->id;
			}
			else if(is_int($model->blockId))
			{
				$condition['blockId'] = $model->blockId;
			}
			else
			{
				return false;
			}

			$tableName = (new MatrixGroup_BlockLevelRecord())->getTableName();
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

	public function getAllBlockLevels()
	{
		$result = MatrixGroup_BlockLevelRecord::model()->findAll(null, array(
			'id'      => null,
			'blockId' => null,
			'level'   => 0,
		));

		return MatrixGroup_BlockLevelModel::populateModels($result);
	}

	public function getBlockLevelById($id)
	{
		$result = MatrixGroup_BlockLevelRecord::model()->findByAttributes(array('blockId' => $id));

		return MatrixGroup_BlockLevelModel::populateModel($result);
	}

	public function getBlockLevel(MatrixBlockModel $block)
	{
		return $this->getBlockLevelById($block->id);
	}

	public function getTopLevelBlocks($blocks)
	{
		$topBlocks = array();

		foreach($blocks as $i => $block)
		{
			$blockLevel = $this->getBlockLevel($block);

			if($blockLevel->level == 0)
			{
				$topBlocks[$i] = $block;
			}
		}

		return $topBlocks;
	}

	public function getBlockChildren(MatrixBlockModel $block)
	{
		$owner = $block->getOwner();
		$type = $block->getType();
		$field = craft()->fields->getFieldById($type->fieldId);

		$blockLevel = $this->getBlockLevel($block);
		$blocks = $this->_getBlocks($owner, $field);

		$childLevel = ((int) $blockLevel->level) + 1;
		$children = array();
		$foundBlock = false;

		foreach($blocks as $testBlock)
		{
			if($foundBlock)
			{
				$testBlockLevel = $this->getBlockLevel($testBlock);
				if($testBlockLevel->level == $childLevel)
				{
					$children[] = $testBlock;
				}
				else
				{
					$foundBlock = false;
				}
			}

			if($testBlock->id == $block->id)
			{
				$foundBlock = true;
			}
		}

		return $children;
	}

	private function _getBlocks(BaseElementModel $owner, FieldModel $field)
	{
		$result = MatrixBlockRecord::model()->findAllByAttributes(array(
			'ownerId' => $owner->id,
			'fieldId' => $field->id,
		));

		$models = MatrixBlockModel::populateModels($result);

		usort($models, function(MatrixBlockModel $a, MatrixBlockModel $b)
		{
			return $a->sortOrder - $b->sortOrder;
		});

		return $models;
	}
}
