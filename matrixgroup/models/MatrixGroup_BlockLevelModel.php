<?php
namespace Craft;

class MatrixGroup_BlockLevelModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'id'      => AttributeType::Number,
			'blockId' => AttributeType::Number,
			'level'   => array(AttributeType::Number, 'default' => 0),
		);
	}
}
