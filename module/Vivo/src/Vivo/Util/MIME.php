<?php
namespace Vivo\Util;

/**
 * MIME provides methods to works with Content-types and MIME types.
 * @todo cleanup & refactoring
 */
class MIME
{
    /**
     * @var array
     */
    static $types = array(
        'text/html'                     => array('html', 'htm', 'php', 'phtml'),
        'text/xml'                      => array('xml'),
        'text/plain'                    => array('txt', 'text', 'c', 'c++', 'pl', 'cc', 'h', 'entity', 'messages'),
        'text/javascript'               => array('js'),
        'text/vbscript'                 => array('vb', 'vbs'),
        'text/sgml'                     => array('sgml'),
        'text/css'                      => array('css'),
        'text/x-smarty'                 => array('tpl'),
        'text/x-vcard'                    => array('vcf'),
        'text/x-speech'                    => array('talk'),
        'text/x-speech'                    => array('talk'),
        'image/gif'                        => array('gif'),
        'image/png'                        => array('png'),
        'image/ief'                        => array('ief'),
        'image/jpeg'                    => array('jpg', 'jpeg', 'jpe'),
        'image/pjpeg'                    => array('jpg', 'jpeg', 'jpe'),
        'image/tiff'                    => array('tiff', 'tif'),
        'image/rgb'                        => array('rgb'),
        'image/g3fax'                    => array('g3f'),
        'image/x-png'                    => array('png'),
        'image/x-xbitmap'                => array('xbm'),
        'image/x-xpixmap'                => array('xpm'),
        'image/x-pict'                    => array('pict'),
        'image/x-portable-pixmap'        => array('ppm'),
        'image/x-portable-graymap'        => array('pgm'),
        'image/x-portable-bitmap'        => array('pbm'),
        'image/x-portable-anymap'        => array('pnm'),
        'image/x-ms-bmp'                => array('bmp'),
        'image/x-cmu-raster'            => array('ras'),
        'image/x-photo-cd'                => array('pcd'),
        'image/x-cals'                    => array('cal'),
        'image/x-mgx-dsf'                => array('dsf'),
        'image/x-cmx'                    => array('cmx'),
        'image/cgm'                        => array('cgm'),
        'image/fif'                        => array('fif'),
        'image/wavelet'                    => array('wi'),
        'image/vnd.dwg'                    => array('dwg'),
        'image/vnd.dxf'                    => array('dxf'),
        'image/vnd.svf'                    => array('svf'),
        'audio/mpeg'                    => array('mp3'),
        'audio/x-aiff'                    => array('aif', 'aiff', 'aifc'),
        'audio/x-wav'                    => array('wav'),
        'audio/x-mpeg'                    => array('mpa', 'abs', 'mpega'),
        'audio/x-mpeg-2'                => array('mp2a', 'mpa2'),
        'audio/echospeech'                => array('es'),
        'audio/voxware'                    => array('vox'),
        'audio/x-ms-wma'                => array('wma'),
        'video/mpeg'                    => array('mpeg', 'mpg', 'mpe'),
        'video/mpeg-2'                    => array('video', 'mpv2', 'mp2v'),
        'video/mp4'                        => array('mp4'),
        'video/avi'                        => array('avi'),
        'video/quicktime'                => array('mov', 'qt'),
        'video/x-msvideo'                => array('avi'),
        'video/vdo'                        => array('vdo'),
        'video/vivo'                    => array('viv'),
        'video/x-flv'                    => array('flv'),
        'video/ogg'                        => array('ogg', 'ogv'),
        'video/webm'                    => array('webm'),
        'video/x-ms-wmv'                => array('wmv'),
        'application/php'                => array('php'),
        'application/phtml'                => array('phtml'),
        'application/fastman'            => array('lcc'),
        'application/x-pn-realaudio'    => array('ra', 'ram'),
        'application/vnd.koan'            => array('skp'),
        'application/postscript'        => array('ai', 'eps', 'ps'),
        'application/rtf'                => array('rtf'),
        'application/pdf'                => array('pdf'),
        'application/vnd.mif'            => array('mif'),
        'application/x-troff'            => array('t', 'tr', 'roff'),
        'application/x-troff-man'        => array('man'),
        'application/x-troff-me'        => array('me'),
        'application/x-troff-ms'        => array('ms'),
        'application/x-latex'            => array('latex'),
        'application/x-tex'                => array('tex'),
        'application/x-texinfo'            => array('texinfo', 'texi'),
        'application/x-dvi'                => array('dvi'),
        'application/msword'            => array('doc'),
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array('docx'),
        'application/envoy'                => array('evy'),
        'application/x-gtar'            => array('gtar'),
        'application/x-tar'                => array('tar'),
        'application/x-bcpio'            => array('bcpio'),
        'application/x-cpio'            => array('cpio'),
        'application/x-shar'            => array('shar'),
        'application/zip'                => array('zip'),
        'application/xml'                => array('gpx'),
        'application/mac-binhex40'        => array('hqx'),
        'application/x-shockwave-flash'    => array('swf'),
        'application/x-stuffit'            => array('sit', 'sea'),
        'application/fractals'            => array('fif'),
        'application/octet-stream'        => array('bin', 'uu'),
        'application/octet-stream'        => array('exe'),
        'application/x-wais-source'        => array('src', 'wsrc'),
        'application/hdf'                => array('hdf'),
        'application/x-sh'                => array('sh'),
        'application/x-csh'                => array('csh'),
        'application/x-perl'            => array('pl'),
        'application/x-tcl'                => array('tcl'),
        'application/andrew-inset'        => array('inset'),
        'application/futuresplash'        => array('spl'),
        'application/mbedlet'            => array('mbd'),
        'application/mspowerpoint'        => array('ppt', 'ppz'),
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => array('pptx'),
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => array('ppsx'),
        'application/vnd.ms-powerpoint'    => array('ppt'),
        'application/astound'            => array('asn'),
        'application/x-olescript'        => array('axs'),
        'application/x-oleobject'        => array('ods'),
        'application/x-webbasic'        => array('wba'),
        'application/x-alpha-form'        => array('frm'),
        'application/x-pcn'                => array('pcn'),
        'application/vnd.ms-excel'        => array('xls'),
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('xlsx'),
        'application/ccv'                => array('ccv'),
        'application/x-p3d'                => array('p3d'),
        'application/vis5d'                => array('v5d'),
        'application/iges'                => array('igs'),
        'application/x-showcase'        => array('sc', 'sho', 'show'),
        'application/x-insight'            => array('ins', 'insight'),
        'application/x-annotator'        => array('ano'),
        'application/x-dirview'            => array('dir'),
        'application/x-enterlicense'    => array('lic'),
        'application/x-iconbook'        => array('icnbk'),
        'application/x-inpview'            => array('wb'),
        'application/x-install'            => array('inst'),
        'application/x-mailfolder'        => array('mail'),
        'application/x-ppages'            => array('pp'),
        'application/x-wingz'            => array('wkz'),
        'x-form/x-openscape'            => array('opp'),
        'x-music/x-midi'                => array('mid'),
        'x-conference/x-cooltalk'        => array('ice'),
        'x-script/x-wfxclient'            => array('wfx'),
        'x-world/x-vrml'                => array('wrl', 'vrml'),
        'x-world/x-vream'                => array('vrw'),
        'x-world/x-svr'                    => array('svr'),
        'x-world/x-wvr'                    => array('wvr'),
        'x-world/x-3dmf'                => array('3dmf'),
        'x-model/x-mesh'                => array('msh'),
        'drawing/x-dwf'                    => array('dwf'),
        'graphics/x-inventor'            => array('iv'),
        'font/woff'                        => array('woff'),
        'font/ttf'                        => array('ttf'),
        'font/opentype'                    => array('otf')
    );

    /**
     * Returns the Content-type for file extension.
     * @param string $ext
     * @return string Default is application/octet-stream.
     */
    static function getType($ext) {
        foreach (self::$types as $type => $exts)
            if (in_array(strtolower($ext), $exts))
                return $type;
        return 'application/octet-stream';
    }

    /**
     * Returns the file extension for Content-type. It is reverse method to self::getType().
     * @param string $type
     * @return string|null
     */
    static function getExt($type) {
        return isset(self::$types[strtolower($type)][0]) ? self::$types[strtolower($type)][0] : null;
    }

}
