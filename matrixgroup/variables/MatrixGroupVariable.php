<?php

namespace Craft;

class MatrixGroupVariable
{
	public function topLevel($blocks)
	{
		return craft()->matrixGroup->getTopLevelBlocks($blocks);
	}

	public function children(MatrixBlockModel $block)
	{
		return craft()->matrixGroup->getBlockChildren($block);
	}
}
