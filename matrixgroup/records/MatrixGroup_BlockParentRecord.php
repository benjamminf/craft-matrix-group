<?php
namespace Craft;

class MatrixGroup_BlockParentRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'matrixgroup_blockparents';
	}

	public function defineRelations()
	{
		return array(
			'block'  => array(static::BELONGS_TO, 'MatrixBlockRecord', 'required' => true, 'onDelete' => static::CASCADE),
			'parent' => array(static::BELONGS_TO, 'MatrixBlockRecord', 'required' => true, 'onDelete' => static::CASCADE),
		);
	}
}
