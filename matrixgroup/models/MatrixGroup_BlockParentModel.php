<?php
namespace Craft;

class MatrixGroup_BlockParentModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'id'       => AttributeType::Number,
			'blockId'  => AttributeType::Number,
			'parentId' => AttributeType::Number,
		);
	}
}
