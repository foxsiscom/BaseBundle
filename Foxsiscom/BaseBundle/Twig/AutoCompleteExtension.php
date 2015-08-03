<?php
namespace Foxsiscom\BaseBundle\Twig;

class AutoCompleteExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    const SKELETON = '<input type="text" name="%s" id="%s" value="%s" %s >';

    const SCRIPT = '<script type="text/javascript">
        $(function(){
            autoComplete("#%s","%s", %s, %s);
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
     * @param string $id
     * @param string $routeName
     * @param string[] $params
     * @param string $class
     * @param string[] $attrs
     * @return string
     */
    public function createInput($name, $id, $value = null, $routeName, $routeParams = null, $attrs = array(), $otherParams = null)
    {
        $strAttrs = null;
        $routeParams = json_encode($routeParams);
        if (!isset($otherParams['allowClear'])) {
            $otherParams['allowClear'] = false;
        }
        $otherParams = json_encode($otherParams);
        
        if (!isset($attrs['placeholder'])) {
            $attrs['placeholder'] = "digite para pesquisar";
        }
        foreach ($attrs as $key => $val) {
            $strAttrs .= "{$key}=\"{$val}\" ";
        }
        
        $output = null;
        $output .= sprintf(
            self::SKELETON,
            $name,
            $id,
            $value,
            $strAttrs
        );

        $output .= sprintf(
            self::SCRIPT,
            $id,
            $routeName,
            $routeParams,
            $otherParams
        );

        return $output;
    }
}
