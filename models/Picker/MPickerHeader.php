<?php
namespace Rehike\Model\Picker;

/**
 * Model for the picker header.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPickerHeader
{
    public string|object $titleText;
    public string|object|null $notesText;
    public string $closeButtonTargetId;
}