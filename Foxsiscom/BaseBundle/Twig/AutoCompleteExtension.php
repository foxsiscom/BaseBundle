<?php
namespace Foxsiscom\BaseBundle\Twig;

class AutoCompleteExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    const SKELETON = '<input type="text" name="%s" id="%s" class="%s" %s >';

    const SCRIPT = '<script type="text/javascript">
        $(function(){
            autoComplete("#%s","%s");
        });
        </script>';

    /**
     * @return string
     */
    public function getName()
    {
        return 'auto_complete_extension';
    }

    /**
     * @return mixed[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('auto_complete', array($this, 'createInput'), array('is_safe' => array('html')))
        );
    }

    /**
     * @param string $name
     * @param string $routeName
     * @param string $id
     * @param string $class
     * @param string[] $attrs
     * @return string
     */
    public function createInput($name, $routeName, $id, $class = "form-control", $attrs = array())
    {
        $strAttrs = null;

        foreach ($attrs as $key => $value) {
            $strAttrs .= "{$key}=\"{$value}\" ";
        }

        $output = null;
        $output .= sprintf(
            self::SKELETON,
            $name,
            $id,
            $class,
            $strAttrs
        );

        $output .= sprintf(
            self::SCRIPT,
            $id,
            $routeName
        );

        return $output;
    }
}
