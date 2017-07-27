<?php
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * This file contains the keyhighlighter class that highlight the chosen keyword in the current output buffer.
 *
 * @package keyhighlighter
 */

/**
 * nw_keyhighlighter class
 *
 * This class highlight the chosen keywords in the current output buffer
 *
 * @package   keyhighlighter
 * @author    Setec Astronomy
 * @version   1.0
 * @abstract  Highlight specific keywords.
 * @copyright 2004
 * @example   sample.php A sample code.
 * @link      http://setecastronomy.stufftoread.com
 */
class nw_keyhighlighter
{

    /**
     * @access private
     */
    public $preg_keywords = '';
    /**
     * @access private
     */
    public $keywords = '';
    /**
     * @access private
     */
    public $singlewords = false;
    /**
     * @access private
     */
    public $replace_callback = null;

    public $content;

    /**
     * Main constructor
     *
     * This is the main constructor of nw_keyhighlighter class. <br>
     * It's the only public method of the class.
     * @param string   $keywords         the keywords you want to highlight
     * @param boolean  $singlewords      specify if it has to highlight also the single words.
     * @param callback $replace_callback a custom callback for keyword highlight.
     *                                   <code>
     *                                   require ('nw_keyhighlighter.class.php');
     *
     * function my_highlighter ($matches) {
     *   return '<span style="font-weight: bolder; color: #FF0000;">' . $matches[0] . '</span>';
     * }
     *
     * new nw_keyhighlighter ('W3C', false, 'my_highlighter');
     * readfile ('http://www.w3c.org/');
     * </code>
     */
    // public function __construct ()
    public function keyhighlighter($keywords, $singlewords = false, $replace_callback = null)
    {
        $this->keywords         = $keywords;
        $this->singlewords      = $singlewords;
        $this->replace_callback = $replace_callback;
    }

    /**
     * @access private
     */
    public function replace($replace_matches)
    {
        $patterns = array();
        if ($this->singlewords) {
            $keywords = explode(' ', $this->preg_keywords);
            foreach ($keywords as $keyword) {
                $patterns[] = '/(?' . '>' . $keyword . '+)/si';
            }
        } else {
            $patterns[] = '/(?' . '>' . $this->preg_keywords . '+)/si';
        }

        $result = $replace_matches[0];

        foreach ($patterns as $pattern) {
            if (!is_null($this->replace_callback)) {
                $result = preg_replace_callback($pattern, $this->replace_callback, $result);
            } else {
                $result = preg_replace($pattern, '<span class="highlightedkey">\\0</span>', $result);
            }
        }

        return $result;
    }

    /**
     * @access private
     */
    public function highlight($buffer)
    {
        $buffer              = '>' . $buffer . '<';
        $this->preg_keywords = preg_replace('/[^\w ]/si', '', $this->keywords);
        $buffer              = preg_replace_callback("/(\>(((?" . ">[^><]+)|(?R))*)\<)/is", array(&$this, 'replace'), $buffer);
        $buffer              = xoops_substr($buffer, 1, -1);

        return $buffer;
    }
}
