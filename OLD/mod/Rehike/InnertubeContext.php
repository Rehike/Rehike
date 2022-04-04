<?php
namespace Rehike;

class InnertubeContext
{
    public static function base64url_encode($data) 
    {
        return str_replace("=", "%3D", strtr(base64_encode($data), '+/', '-_'));
    } 

    public static function int2uleb128($int)
    {
        // this is awful
        // i hate the person who wrot ethis
        
        if ($int < 128) return chr($int);
        
        $out = decbin($int);
        
        while (0 != strlen($out) % 7)
        {
            $out = '0' . $out;
        }
        
        $out = str_split($out, 7);
        
        
        for ($i = 0; $i < count($out); $i++)
        {
            if (0 != $i)
            {
                $out[$i] = '1' . $out[$i];
            }
            else
            {
                $out[$i] = '0' . $out[$i];
            }
        }
        
        $out = array_reverse($out);
        $out = implode('', $out);
        
        $out = str_split($out, 8);
        
        $out2 = "";
        
        for ($i = 0; $i < count($out); $i++)
        {
            $out2 .= chr( bindec($out[$i]) );
        }
        
        return $out2;
    }

    public static function genVisitorData($visitor)
    {
        // Generate visitorData string
        
        $date = time();
        
        return self::base64url_encode(
            chr(0x0a) . self::int2uleb128( strlen($visitor) ) . $visitor . chr(0x28) . self::int2uleb128($date)
        );
    }

    public static function generate($cname, $cver, $visitorData = null, $hl = 'en', $gl = 'US')
    {
        if (is_null($visitorData)) $visitorData = ContextManager::$visitorData;
        return (object) [
            'context' => (object) [
                'client' => (object) [
                    'hl' => $hl,
                    'gl' => $gl,
                    'visitorData' => self::genVisitorData($visitorData),
                    'clientName' => $cname,
                    'clientVersion' => $cver,
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