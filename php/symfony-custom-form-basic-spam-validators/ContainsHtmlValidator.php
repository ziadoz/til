<?php
namespace Ziadoz\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsLinksValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
	{
		$links = (int) (substr_count($value, 'http://') + substr_count($value, 'https://'));
		if ($links > $constraint->max) {
			$this->context->addViolation($constraint->message, array('%max%' => $constraint->max));
		}
	}
}