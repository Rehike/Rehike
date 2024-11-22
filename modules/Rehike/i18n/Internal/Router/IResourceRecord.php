<?php
namespace Rehike\i18n\Internal\Router;

/**
 * Represents a resource record.
 * 
 * A resource record must be able to be converted into an object, and otherwise
 * does not represent much value.
 * 
 */
interface IResourceRecord
{
    public function toObject(): object;
}