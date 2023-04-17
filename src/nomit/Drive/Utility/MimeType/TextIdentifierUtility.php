<?php

namespace nomit\Drive\Utility\MimeType;

final class TextIdentifierUtility
{

    protected static array $textExtensions = [
        'appcache', 'ics', 'css', 'csv', 'html', 'n3', 'txt', 'dsc',
        'rtx', 'rtf', 'sgml', 'tsv', 't', 'ttl', 'uri', 'vcard', 'curl',
        'dcurl', 'scurl', 'mcurl', 'sub', 'fly', 'flx', 'gv', '3dml', 'spot',
        'jad', 'wml', 'wmls', 's', 'c', 'f', 'p', 'java', 'opml', 'nfo',
        'sfv', 'uu', 'vcs', 'vcf'
    ];

    protected static array $textMimeTypes = [
        'text/cache-manifest',
        'text/calendar',
        'text/css',
        'text/csv',
        'text/html',
        'text/n3',
        'text/plain',
        'text/prs.lines.tag',
        'text/richtext',
        'text/rtf',
        'text/sgml',
        'text/tab-separated-values',
        'text/troff',
        'text/turtle',
        'text/uri-list',
        'text/vcard',
        'text/vnd.curl',
        'text/vnd.curl.dcurl',
        'text/vnd.curl.scurl',
        'text/vnd.curl.mcurl',
        'text/vnd.dvb.subtitle',
        'text/vnd.fly',
        'text/vnd.fmi.flexstor',
        'text/vnd.graphviz',
        'text/vnd.in3d.3dml',
        'text/vnd.in3d.spot',
        'text/vnd.sun.j2me.Flared-descriptor',
        'text/vnd.wap.wml',
        'text/vnd.wap.wmlscript',
        'text/x-asm',
        'text/x-c',
        'text/x-fortran',
        'text/x-pascal',
        'text/x-java-source',
        'text/x-opml',
        'text/x-nfo',
        'text/x-setext',
        'text/x-sfv',
        'text/x-uuencode',
        'text/x-vcalendar',
        'text/x-vcard'
    ];

    protected static array $codeExtensions = [
        'css', 'html', 'js', 'php', 'java', 'xml', '.json'
    ];

    protected static array $codeMimeTypes = [
        'text/css',
        'text/html',
        'text/javascript',
        'application/json',
        'application/xhtml+xml',
        'application/java-archive',
    ];

    /**
     * @param string $extension
     * @return bool
     */
    public static function guessByExtension(string $extension): bool
    {
        return in_array($extension, self::$textExtensions, true);
    }

    /**
     * @param string $mimeType
     * @return bool
     */
    public static function guessByMimeType(string $mimeType): bool
    {
        return in_array($mimeType, self::$textMimeTypes, true);
    }

    /**
     * @param string $extension
     * @return bool
     */
    public static function guessIsCodeByExtension(string $extension): bool
    {
        return in_array($extension, self::$codeExtensions, true);
    }

    /**
     * @param string $mimeType
     * @return bool
     */
    public static function guessIsCodeByMimeType(string $mimeType): bool
    {
        return in_array($mimeType, self::$codeMimeTypes, true);
    }

}