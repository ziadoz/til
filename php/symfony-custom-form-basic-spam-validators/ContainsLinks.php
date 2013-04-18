<?php
namespace Ziadoz\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsLinks extends Constraint
{
    public $message = 'This value must not contain more than %max% links.';
	public $max = 2;

    /**
     * {@inheritDoc}
     */
	public function getDefaultOption()
	{
		return 'max';
	}
}