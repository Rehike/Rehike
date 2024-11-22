<?php
namespace Rehike;

use Rehike\Util\Base64Url;
use Rehike\i18n\Internal\Core as I18nCore;

/**
 * Generates a valid InnerTube client context required
 * for requesting the API.
 * 
 * TODO: Deprecate?
 * 
 * @author The Rehike Maintainers
 */
class InnertubeContext
{
    /**
     * Convert an integer to a ULEB128 binary for use in
     * synthesising protobuf.
     * 
     * ULEB128 utilizes the most-significant bit of each byte to signify if it
     * should continue parsing. For instance, the number 12000 is represented
     * as a 16-bit integer as something like this:
     * 
     *     E0          2E
     * 
     *     1110 0000   0010 1110
     * 
     * (Note that, while you would often write this number in hexadecimal like
     * 0x2EE0, it's often stored in memory in the reversed byte order because
     * most processors use this order)
     * 
     * and in ULEB128 like this:
     * 
     *     E0         DD         00
     *     1110 0000  1101 1101  0000 0000
     * 
     * The contents of the number stay in the little-endian byte order (i.e.
     * this works quite efficiently in native machine code of most processors).
     * The number is simply split up into multiple bytes to introduce a new use
     * for the most-significant bit as a continuation check.
     * 
     * @param int $int to convert
     * @return string uleb128 binary
     */
    public static function int2uleb128(int $int): string
    {
        if ($int < 128)
        {
            // Integers less than 128 don't differ between standard in-memory
            // representation and ULEB128 representation.
            return chr($int);
        }
        
        $buffer = $int;
        $bytes = "";
        
        $LOW_ORDER_7_BITS_MASK = 0b0111_1111;
        $HIGHEST_ORDER_BIT_MASK = 0b1000_0000;
        $SINGLE_BYTE_MASK = 0b1111_1111;
        
        do
        {
            $byte = $buffer & $LOW_ORDER_7_BITS_MASK;
            $buffer >>= 7;
            
            if ($buffer != 0)
            {
                $byte |= $HIGHEST_ORDER_BIT_MASK;
            }
            
            $bytes .= chr($byte & $SINGLE_BYTE_MASK);
        }
        while ($buffer != 0);
        
        return $bytes;
    }

    /**
     * Generate an encoded YouTube visitor data string.
     * 
     * This is encoded in the Protocol Buffers format specifying the following
     * information:
     * {
     *     1: VISITOR_INFO1_LIVE cookie value,
     *     5: current unix timestamp
     * }
     * 
     * @param string $visitor
     * @return string encoded visitor data
     */
    public static function genVisitorData(string $visitor): string
    {
        $PB_WIRE_TYPE_VARINT = 0;
        $PB_WIRE_TYPE_DOUBLE = 1;
        $PB_WIRE_TYPE_LENGTH_DETERMINED = 2;
        $PB_WIRE_TYPE_GROUP_START = 3;
        $PB_WIRE_TYPE_GROUP_END = 4;
        $PB_WIRE_TYPE_FLOAT = 5;
        
        // Generate visitorData string
        if (is_null($visitor))
            return "";
        
        $date = time();
        
        return Base64Url::encode(
            chr( 1 << 3 | $PB_WIRE_TYPE_LENGTH_DETERMINED ) . self::int2uleb128( strlen($visitor) ) . $visitor .
            chr( 5 << 3 | $PB_WIRE_TYPE_VARINT ) . self::int2uleb128($date)
        );
    }
    
    /**
     * Generate a protobuf-encoded vistor data string with more information.
     * 
     * This specifies the following information:
     * {
     *     1: VISITOR_INFO1_LIVE cookie value,
     *     5: current unix timestamp,
     *     6: {
     *         1: current GL value
     *     }
     * }
     */
    public static function genVisitorData2(string $visitor, string $gl): string
    {
        $PB_WIRE_TYPE_VARINT = 0;
        $PB_WIRE_TYPE_DOUBLE = 1;
        $PB_WIRE_TYPE_LENGTH_DETERMINED = 2;
        $PB_WIRE_TYPE_GROUP_START = 3;
        $PB_WIRE_TYPE_GROUP_END = 4;
        $PB_WIRE_TYPE_FLOAT = 5;
        
        $date = time();
        
        $localeInnerBin =
            // This group is always empty for some reason.
            chr( 3 << 3 | $PB_WIRE_TYPE_LENGTH_DETERMINED ) . chr(0) .
            chr( 4 << 3 | $PB_WIRE_TYPE_VARINT ) . self::int2uleb128(17);
            
        $localeBin =
            chr( 1 << 3 | $PB_WIRE_TYPE_LENGTH_DETERMINED ) . self::int2uleb128( strlen($gl) ) . $gl .
            chr( 2 << 3 | $PB_WIRE_TYPE_LENGTH_DETERMINED ) . self::int2uleb128( strlen($localeInnerBin) ) . $localeInnerBin;
        
        $bin =
            chr( 1 << 3 | $PB_WIRE_TYPE_LENGTH_DETERMINED ) . self::int2uleb128( strlen($visitor) ) . $visitor .
            chr( 5 << 3 | $PB_WIRE_TYPE_VARINT ) . self::int2uleb128($date) .
            chr( 6 << 3 | $PB_WIRE_TYPE_LENGTH_DETERMINED ) . self::int2uleb128( strlen($localeBin) ) . $localeBin;
            
        return Base64Url::encode($bin);
    }

    /**
     * Generate an InnerTube context template.
     * 
     * @param string|int $cname (client name) enum or index
     * @param string $cver (client version) number
     * @param string|null $visitorData
     * @param string $hl (host language)
     * @param string $gl (global location)
     * 
     * @return object InnerTube context
     */
    public static function generate(
            string|int $cname, 
            string $cver, 
            ?string $visitorData = null, 
            ?string $hl = null, 
            ?string $gl = null
    ): object
    {
        if (is_null($hl))
        {
            $hl = I18nCore::getInnertubeLanguageId();
        }

        if (is_null($gl))
        {
            $gl = I18nCore::getInnertubeGeolocation();
        }

        if (is_null($visitorData)) $visitorData = ContextManager::$visitorData;
        return (object) [
            'context' => (object) [
                'client' => (object) [
                    'hl' => $hl,
                    'gl' => $gl,
                    'visitorData' => self::genVisitorData($visitorData),
                    'clientName' => $cname,
                    'clientVersion' => $cver,
                    'userAgent' => $_SERVER['HTTP_USER_AGENT']  ?? ""
                ],
                'user' => (object) [
                    'lockedSafetyMode' => false
                ],
                'request' => (object) [
                    'useSsl' => true,
                    'internalExperimentFlags' => [],
                    'consistencyTokenJars' => []
                ]
            ]
        ];
    }
}