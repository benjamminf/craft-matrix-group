<?php
namespace Craft;

require_once 'MatrixGroup_NodeItem.php';

/**
 * Class MatrixGroup_Node
 *
 * @package Craft
 */
class MatrixGroup_Node extends \Twig_Node_For
{
	/**
	 * @var MatrixGroup_NodeItem
	 */
	protected $matrixGroupItemNode;

	/**
	 *
	 * @param \Twig_Node_Expression_AssignName $keyTarget
	 * @param \Twig_Node_Expression_AssignName $valueTarget
	 * @param \Twig_Node_Expression $seq
	 * @param \Twig_NodeInterface $upperBody
	 * @param \Twig_NodeInterface $lowerBody
	 * @param \Twig_NodeInterface $indent
	 * @param \Twig_NodeInterface $outdent
	 * @param null $lineno
	 * @param null $tag
	 * @return \Craft\MatrixGroup_Node
	 */
	public function __construct(
		\Twig_Node_Expression_AssignName $keyTarget,
		\Twig_Node_Expression_AssignName $valueTarget,
		\Twig_Node_Expression $seq,
		\Twig_NodeInterface $upperBody,
		\Twig_NodeInterface $lowerBody = null,
		\Twig_NodeInterface $indent = null,
		\Twig_NodeInterface $outdent = null,
		$lineno,
		$tag = null
	) {
		$this->matrixGroupItemNode = new MatrixGroup_NodeItem($valueTarget, $indent, $outdent, $lowerBody, $lineno, $tag);
		$body = new \Twig_Node(array($this->matrixGroupItemNode, $upperBody));

		parent::__construct($keyTarget, $valueTarget, $seq, null, $body, null, $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param \Twig_Compiler $compiler
	 * @return null
	 */
	public function compile(\Twig_Compiler $compiler)
	{
		// Remember what 'matrixGroup' was set to before
		$compiler
			->write("if (isset(\$context['matrixGroup'])) {\n")
			->indent()
				->write("\$_matrixGroup = \$context['matrixGroup'];\n")
			->outdent()
			->write("}\n");

		parent::compile($compiler);

		$compiler
			// Were there any items?
			->write("if (isset(\$_thisItemLevel)) {\n")
			->indent()
				// Remember the current context
				->write("\$_tmpContext = \$context;\n")
				// Close out the unclosed items
				->write("if (\$_thisItemLevel > \$_firstItemLevel) {\n")
				->indent()
					->write("for (\$_i = \$_thisItemLevel; \$_i > \$_firstItemLevel; \$_i--) {\n")
					->indent()
						// Did we output an item at that level?
						->write("if (isset(\$_contextsByLevel[\$_i])) {\n")
						->indent()
							// Temporarily set the context to the element at this level
							->write("\$context = \$_contextsByLevel[\$_i];\n")
							->subcompile($this->matrixGroupItemNode->getNode('lower_body'), false)
							->subcompile($this->matrixGroupItemNode->getNode('outdent'), false)
						->outdent()
						->write("}\n")
					->outdent()
					->write("}\n")
				->outdent()
				->write("}\n")
				// Close out the last item
				->write("\$context = \$_contextsByLevel[\$_firstItemLevel];\n")
				->subcompile($this->matrixGroupItemNode->getNode('lower_body'), false)
				// Set the context back
				->write("\$context = \$_tmpContext;\n")
				// Unset out variables
				->write("unset(\$_thisItemLevel, \$_firstItemLevel, \$_i, \$_contextsByLevel, \$_tmpContext);\n")
			->outdent()
			->write("}\n")
			// Bring back the 'matrixGroup' variable
			->write("if (isset(\$_matrixGroup)) {\n")
			->indent()
				->write("\$context['matrixGroup'] = \$_matrixGroup;\n")
				->write("unset(\$_matrixGroup);\n")
			->outdent()
			->write("}\n");
	}
}
