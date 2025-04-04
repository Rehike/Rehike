<?php
namespace Rehike\Validation;

/**
 * Interface for objects which provide validation functionalities.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IValidator
{
    public function validateString(string $input): bool;
}