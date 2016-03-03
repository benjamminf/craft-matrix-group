<?php
namespace Craft;

class MatrixGroup_BlockLevelRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'matrixgroup_blocklevels';
	}

	public function defineRelations()
	{
		return array(
			'block'  => array(static::BELONGS_TO, 'MatrixBlockRecord', 'required' => true, 'onDelete' => static::CASCADE),
		);
	}

	protected function defineAttributes()
	{
		return array(
			'level' => array(AttributeType::Number, 'required' => true, 'default' => 0),
		);
	}
}
