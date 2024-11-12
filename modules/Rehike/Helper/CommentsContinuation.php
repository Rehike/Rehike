<?php
namespace Rehike\Helper;

use Rehike\Util\Base64Url;

use stdClass;

/**
 * Custom comments continuation.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class CommentsContinuation extends stdClass
{
    public const HEADER = "RHCUSTOM";
    
    public string $originalContinuation = "";
    public object $displayNameMap;
    
    public function __construct(string $originalContinuation = "")
    {
        $this->originalContinuation = $originalContinuation;
        
        $this->displayNameMap = (object)[];
    }
    
    public function supplyDisplayNameMap(object $displayNames): self
    {
        $this->displayNameMap = $displayNames;
        
        return $this;
    }
    
    public function toString(): string
    {
        return $this->__toString();
    }
    
    public function __toString(): string
    {
        return Base64Url::encode(self::HEADER . json_encode($this));
    }
    
    public static function isCustom(string $continuation): bool
    {
        try
        {
            return substr(@Base64Url::decode($continuation), 0, strlen(self::HEADER)) == self::HEADER;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
    
    public static function parse(string $continuation): ?self
    {
        if (self::isCustom($continuation))
        {
            $instance = new self();
            $obj = json_decode(substr(Base64Url::decode($continuation), strlen(self::HEADER)));
            
            foreach ($obj as $prop => $value)
            {
                $instance->{$prop} = $value;
            }
            
            return $instance;
        }
        
        return null;
    }
}