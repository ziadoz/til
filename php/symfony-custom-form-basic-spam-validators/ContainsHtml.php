<?php
namespace Ziadoz\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsHtml extends Constraint
{
    public $message = 'This value must not contain HTML markup.';
}