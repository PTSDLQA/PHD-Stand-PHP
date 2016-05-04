<?

/*
 * This file is part of the SmartSelect package.
  * (c) 2007 Alexander Zinchuk <alx@vingrad.ru>
 * (c) 2007 Endeveit <endeveit@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * IllegalStateException is thrown while attempting to create new SmartSelect implementation instance
 *
 * @package  SmartSelect
 */
class IllegalStateException extends Exception {}

/**
 * CloneNotSupportedException is thrown while attempting to clone SmartSelect instance
 *
 * @package  SmartSelect
 */
class CloneNotSupportedException extends Exception {}

/**
 * SmartSelect manages new SmartSelect listboxes
 *
 * @package  SmartSelect
 * @autor    Alexander Zinchuk	<alx@vingrad.ru>
 * @author   Endeveit		<endeveit@gmail.com>
 */
class SmartSelect
{
    /**
     * The instance of SmartSelect object
     *
     * @var  $instance
     */
    private static $instance = null;

    /**
     * Returns self instance
     *
     * @return  SmartSelect
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self || self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Return new SmartSelect listbox
     *
     * @param   string            $xml_file      Path to xml-file with data, or JSON-string
     * @param   string            $attributes    List attributes
     * @throws  RuntimeException
     * @return  string
     */
    public function newSmartSelect($data, $attributes = array())
    {
        $xml = simplexml_load_string('<fwc:select id="sselect" xmlns:fwc="http://alx.vingrad.ru/fwc" />');
        foreach($data as $id=>$value)
		{ $opt = $xml->addChild('fwc:option', $value);
          $opt->addAttribute('value', $id);
        }

        if(null === $xml['skin'])
		$xml->addAttribute('skin', (strpos($_SERVER['HTTP_USER_AGENT'],'Opera') === false) ? 'ss_winxp' : 'ss_opera');

        $a = $xml->attributes();
        foreach($attributes as $i => $v)
		{ if(!empty($a[$i]))
		  $a[$i] = $v;
          else
          $xml->addAttribute($i, $v);
        }


        $attributes = $xml->attributes();

        $xslfile = 'templates/admin/fwc/design/design.xsl';
	    $xsl = simplexml_load_file($xslfile);
        $xslt = new XSLTProcessor();
        $xslt->importStyleSheet($xsl);

        return $xslt->transformToXML($xml);
    }

    /**
     * Class constructor
     *
     * @throws  IllegalStateException , RuntimeException
     */
    private function __construct()
    {
        if (!extension_loaded('SimpleXML')) {
            throw new RuntimeException('SimpleXML extension not loaded');
        }
        if (!extension_loaded('XSL')) {
            throw new RuntimeException('XSL extension not loaded');
        }
    }

    /**
     * Method for object clone.
     * Throws CloneNotSupportedException because of using singleton-pattern in schema
     *
     * @throws  CloneNotSupportedException
     */
    private function __clone()
    {
        throw new CloneNotSupportedException('Singleton should not be cloned');
    }
}
?>