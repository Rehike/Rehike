<?php
namespace YukisCoffee\CoffeeRequest\Attributes;

use Attribute;

/**
 * Denotes a class member that is required on a trait.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class RequireMember
{
    /**
     * True if the member is a method.
     */
    private bool $isMethod = false;

    /**
     * The name of the class member.
     */
    private string $name;

    /**
     * A map of the argument types if specifying a method.
     * 
     * @var string[]
     */
    private array $arguments;

    public function __construct(string $memberName)
    {
        // Don't include argument types for methods.
        if (strstr($memberName, "("))
        {
            $signature = explode("(", $memberName);
            $this->name = $signature[0];
            $this->arguments = $this->extractArgsTypes($signature[1]);
            $this->isMethod = true;
        }
        else
        {
            $this->name = $memberName;
            $this->isMethod = false;
        }
    }

    /**
     * Extract the argument types into an ordered array.
     * 
     * @return string[]
     */
    private function extractArgsTypes(string $args): array
    {
        $args = explode(")", $args)[0];

        return str_replace(" ", "", explode(",", $args));
    }
}