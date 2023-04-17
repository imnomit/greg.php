<?php

namespace nomit\Drive\Utility\MimeType;

final class ImageIdentifierUtility
{

    /**
     * List of image extensions
     *
     * @var array|string[]
     */
    protected static array $imageExtensions = [
        'bmp', 'cgm', 'g3', 'gif', 'ief', 'jpeg', 'ktx', 'png', 'btif',
        'sgi', 'svg', 'tiff', 'psd', 'uvi', 'sub', 'djvu', 'dwg', 'dxf',
        'fbs', 'fpx', 'fst', 'mmr', 'rlc', 'mdi', 'wdp', 'npx', 'wbmp',
        'xif', 'webp', '3ds', 'ras', 'cmx', 'fh', 'ico', 'sid', 'pxc', 'pic',
        'pnm', 'pbm', 'pgm', 'ppm', 'rgb', 'tga', 'xbm', 'xwd',
    ];

    /**
     * List of image MIME-types
     *
     * @var array|string[]
     */
    protected static array $imageMimeTypes = [
        'image/bmp',
        'image/x-ms-bmp',
        'image/cgm',
        'image/g3fax',
        'image/gif',
        'image/ief',
        'image/ktx',
        'image/png',
        'image/prs.btif',
        'image/sgi',
        'image/svg+xml',
        'image/tiff',
        'image/vnd.adobe.photoshop',
        'image/vnd.dece.graphic',
        'image/vnd.dvb.subtitle',
        'image/vnd.djvu',
        'image/vnd.dwg',
        'image/vnd.dxf',
        'image/vnd.fastbidsheet',
        'image/vnd.fpx' ,
        'image/vnd.fst',
        'image/vnd.fujixerox.edmics-mmr',
        'image/vnd.fujixerox.edmics-rlc',
        'image/vnd.ms-modi',
        'image/vnd.ms-photo',
        'image/vnd.net-fpx',
        'image/vnd.wap.wbmp',
        'image/vnd.xiff',
        'image/webp',
        'image/x-3ds',
        'image/x-cmu-raster',
        'image/x-cmx',
        'image/x-freehand',
        'image/x-icon',
        'image/x-mrsid-image',
        'image/x-pcx',
        'image/x-pict',
        'image/x-portable-anymap',
        'image/x-portable-bitmap',
        'image/x-portable-graymap',
        'image/x-portable-pixmap',
        'image/x-rgb',
        'image/x-tga',
        'image/x-xbitmap',
        'image/x-xpixmap',
        'image/x-xwindowdump',
    ];

    /**
     * @param string $extension
     * @return bool
     */
    public static function guessByExtension(string $extension): bool
    {
        return in_array($extension, self::$imageExtensions, true);
    }

    /**
     * @param string $mimeType
     * @return bool
     */
    public static function guessByMimeType(string $mimeType): bool
    {
        return in_array($mimeType, self::$imageMimeTypes, true);
    }

}