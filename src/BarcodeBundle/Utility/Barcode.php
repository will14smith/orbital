<?php


namespace BarcodeBundle\Utility;


/**
 * Class Barcode
 * @package BarcodeBundle\Utility
 */
class Barcode extends \Hackzilla\BarcodeBundle\Utility\Barcode
{
    /**
     * Build barcode
     *
     * @param string $code
     *
     * @return resource
     */
    public function build($code)
    {
        $bars = $this->encode($code);
        if (!$bars) {
            return null;
        }

        $bars = $bars['bars'];
        $barsLength = \strlen($bars);
        $total_y = $this->height();
        $scale = $this->scale();
        $space = $this->space();

        /* count total width */
        $xpos = 0;
        $width = true;
        for ($i = 0; $i < $barsLength; $i++) {
            $val = \strtolower($bars[$i]);
            if ($width) {
                $xpos += (int)$val * $scale;
                $width = false;
                continue;
            }
            if (\preg_match("#[a-z]#", $val)) {
                /* tall bar */
                $val = \ord($val) - \ord('a') + 1;
            }
            $xpos += $val * $scale;
            $width = true;
        }

        /* allocate the image */
        $total_x = $xpos + $space['right'] + $space['right'];
        $xpos = $space['left'];
        $im = \imagecreate($total_x, $total_y);

        /* create two images */
        \imagecolorallocate($im, $this->bgColor(0), $this->bgColor(1), $this->bgColor(2));
        $col_bar = \imagecolorallocate($im, $this->barColor(0), $this->barColor(1), $this->barColor(2));
        $height = \round($total_y - $space['bottom']);

        /* paint the bars */
        $width = true;
        for ($i = 0; $i < $barsLength; $i++) {
            $val = \strtolower($bars[$i]);
            if ($width) {
                $xpos += (int)$val * $scale;
                $width = false;
                continue;
            }

            $h = $height;

            if (\preg_match("#[a-z]#", $val)) {
                /* tall bar */
                $val = \ord($val) - \ord('a') + 1;
            }

            \imagefilledrectangle($im, $xpos, $space['top'], $xpos + ($val * $scale) - 1, $h, $col_bar);
            $xpos += $val * $scale;
            $width = true;
        }

        return $im;
    }
}
