<?php
namespace Craft;

require_once 'MatrixGroup_Node.php';

/**
 * Class MatrixGroup_TokenParser
 *
 * @package Craft
 */
class MatrixGroup_TokenParser extends \Twig_TokenParser
{
	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'matrixgroup';
	}

	/**
	 * Parses a token and returns a node.
	 *
	 * @param \Twig_Token $token
	 * @return \Twig_NodeInterface
	 */
	public function parse(\Twig_Token $token)
	{
		$lineno = $token->getLine();
		$stream = $this->parser->getStream();

		$targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
		$stream->expect(\Twig_Token::OPERATOR_TYPE, 'in');
		$seq = $this->parser->getExpressionParser()->parseExpression();
		$stream->expect(\Twig_Token::BLOCK_END_TYPE);

		$upperBody = $this->parser->subparse(array($this, 'decideMatrixGroupFork'));
	    $lowerBody = new \Twig_Node();
	    $indent = new \Twig_Node();
	    $outdent = new \Twig_Node();

	    $nextValue = $stream->next()->getValue();

		if ($nextValue != 'endmatrixgroup')
		{
			$stream->expect(\Twig_Token::BLOCK_END_TYPE);

			if ($nextValue == 'ifchildren')
			{
			    $indent = $this->parser->subparse(array($this, 'decideChildrenFork'), true);
			    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
			    $outdent = $this->parser->subparse(array($this, 'decideChildrenEnd'), true);
			    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
			}

			$lowerBody = $this->parser->subparse(array($this, 'decideMatrixGroupEnd'), true);
		}

		$stream->expect(\Twig_Token::BLOCK_END_TYPE);

		if (count($targets) > 1)
		{
		    $keyTarget = $targets->getNode(0);
		    $keyTarget = new \Twig_Node_Expression_AssignName($keyTarget->getAttribute('name'), $keyTarget->getLine());
		    $valueTarget = $targets->getNode(1);
		    $valueTarget = new \Twig_Node_Expression_AssignName($valueTarget->getAttribute('name'), $valueTarget->getLine());
		}
		else
		{
		    $keyTarget = new \Twig_Node_Expression_AssignName('_key', $lineno);
		    $valueTarget = $targets->getNode(0);
		    $valueTarget = new \Twig_Node_Expression_AssignName($valueTarget->getAttribute('name'), $valueTarget->getLine());
		}

		return new MatrixGroup_Node($keyTarget, $valueTarget, $seq, $upperBody, $lowerBody, $indent, $outdent, $lineno, $this->getTag());
	}

	/**
	 * @param \Twig_Token $token
	 * @return bool
	 */
	public function decideMatrixGroupFork(\Twig_Token $token)
	{
		return $token->test(array('ifchildren', 'children', 'endmatrixgroup'));
	}

	/**
	 * @param \Twig_Token $token
	 * @return bool
	 */
	public function decideChildrenFork(\Twig_Token $token)
	{
		return $token->test('children');
	}

	/**
	 * @param \Twig_Token $token
	 * @return bool
	 */
	public function decideChildrenEnd(\Twig_Token $token)
	{
		return $token->test('endifchildren');
	}

	/**
	 * @param \Twig_Token $token
	 * @return bool
	 */
	public function decideMatrixGroupEnd(\Twig_Token $token)
	{
		return $token->test('endmatrixgroup');
	}
}
