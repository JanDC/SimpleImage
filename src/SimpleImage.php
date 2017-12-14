<?php


namespace SimpleImage;


class SimpleImage extends \claviska\SimpleImage
{
    /**
     * @param      $file
     * @param null $mimeType
     * @param int  $quality
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function toFile($file, $mimeType = null, $quality = 100)
    {
        if ($mimeType === 'image/webp') {
            $pngPath = '/tmp/simpleimage/'.microtime(false).'.png';
            $this->toFile($pngPath, 'image/png', $quality);
            $this->convertPNGToWebp($pngPath, $file, $quality, true);

            return $this;
        }

        return parent::toFile($file, $mimeType, $quality);
    }

    /**
     * @param $source
     * @param $destination
     * @param $quality
     * @param $strip_metadata
     */
    private function convertPNGToWebp($source, $destination, $quality, $strip_metadata)
    {

        $options = '-q '.$quality;
        $options .= ($strip_metadata ? ' -metadata none' : '-metadata all');
        // comma separated list of metadata to copy from the input to the output if present.
        // Valid values: all, none (default), exif, icc, xmp

        $parts = explode('.', $source);
        $ext = array_pop($parts);
        if ($ext == 'png') {
            $options .= ' -lossless';
        }

        if (defined("WEBPCONVERT_CWEBP_METHOD")) {
            $options .= ' -m '.WEBPCONVERT_CWEBP_METHOD;
        } else {
            $options .= ' -m 6';
        }

        if (defined("WEBPCONVERT_CWEBP_LOW_MEMORY")) {
            $options .= (WEBPCONVERT_CWEBP_LOW_MEMORY ? ' -low_memory' : '');
        } else {
            $options .= ' -low_memory';
        }

        //$options .= ' -low_memory';

        // $options .= ' -quiet';
        $options .= ' '.($source).' -o '.($destination).' 2>&1';

        $cmd = __DIR__.'../bin/cwebp '.$options;

        exec($cmd, $output, $return_var);
    }

}