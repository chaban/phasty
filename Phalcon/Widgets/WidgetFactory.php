<?php namespace Phalcon\Widgets;

class WidgetFactory {

    protected $namespace;

    /**
     * Constructor
     * @param $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }


    /**
     * Magic method that catches all widget calls
     *
     * @param $widgetName
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function __call($widgetName, $params = [])
    {
        $config = isset($params[0]) ? $params[0] : [];

        $widgetName = ucwords(str_replace(array('-', '_'), '', $widgetName));

        $widgetClass = $this->namespace . '\\' . $widgetName;

        $widget = new $widgetClass($config);

        if ($widget instanceof AbstractWidget === false)
        {
            throw new InvalidWidgetClassException;
        }

        return $widget->run();
    }
}