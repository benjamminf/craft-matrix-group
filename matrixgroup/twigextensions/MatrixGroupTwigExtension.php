<?php
namespace Craft;

require_once 'MatrixGroup_TokenParser.php';

/**
 * Class MatrixGroupTwigExtension
 *
 * @package Craft
 */
class MatrixGroupTwigExtension extends \Twig_Extension
{
	public function getName()
	{
		return 'matrixgroup';
	}

	public function getTokenParsers()
	{
		return array(
			new MatrixGroup_TokenParser(),
		);
	}
}
