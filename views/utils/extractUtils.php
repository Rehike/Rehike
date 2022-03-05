<?php
class ExtractUtils {
    public static function isolateCount(?string $input, string $substrRegex): string {
        if (!$input) return '';
        return preg_replace(
            $substrRegex,
            '',
            $input
        );
    }
    
    public static function isolateLikeCnt(?string $likeCount): string {
        return self::isolateCount($likeCount, '/(like this video along with )|( other people)/');
    }
    
    public static function isolateSubCnt(?string $subCount): string {
        $a = self::isolateCount($subCount, '/( subscribers)|( subscriber)/');
        if ($a != 'No') {
            return $a;
        } else {
            return '0';
        }
    }
    
    public static function isolateViewCnt(?string $viewCount): string {
        $a = self::isolateCount($viewCount, '/( views)|( view)/');
        if ($a != 'No') {
            return $a;
        } else {
            return '0';
        }
    }
    
    public static function resolveDate($date) {
        if (is_object($date)) $date = $date->simpleText;
        if (!preg_match('/Premiered/', $date) &&
            !preg_match('/Started/', $date) &&
            !preg_match('/Streamed/', $date)
        ) {
            return 'Published on ' . $date;
        } else {
            return $date;
        }
    }
    public static function resolveRedirectUrl(string $url): string {
        $a = betterParseUrl($url);
        if (isset($a->query['q'])) {
            return htmlspecialchars_decode($a->query['q']);
        }
        return '';
    }
}