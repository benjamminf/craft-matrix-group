<?php
namespace Craft;

class MatrixGroup_BlockTypeModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'id'         => AttributeType::Number,
			'typeId'     => AttributeType::Number,
			'typeHandle' => AttributeType::Handle,
		);
	}
}
