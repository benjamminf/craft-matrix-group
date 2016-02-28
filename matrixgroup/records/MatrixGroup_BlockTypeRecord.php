<?php
namespace Craft;

class MatrixGroup_BlockTypeRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'matrixgroup_blocktypes';
	}

	public function defineRelations()
	{
		return array(
			'type' => array(static::BELONGS_TO, 'MatrixBlockTypeRecord', 'required' => true, 'onDelete' => static::CASCADE),
		);
	}

	protected function defineAttributes()
	{
		return array(
			'group' => array(AttributeType::Bool, 'required' => true),
		);
	}
}
