<?php

/**
 * @package modx
 * @subpackage dashboard
 */
class ModxDashboardWidgetPackWidgetWelcome extends modDashboardWidgetInterface
{
    /**
     * ModxDashboardWidgetPackWidgetWelcome constructor.
     * @param \xPDO\xPDO $modx
     * @param modDashboardWidget $widget
     * @param modManagerController $controller
     */
    public function __construct(\xPDO\xPDO &$modx, modDashboardWidget &$widget, modManagerController &$controller)
    {
        parent::__construct($modx, $widget, $controller);

        $this->modx->modxdashboardwidgetpack = $this->modx->getService(
            'modxdashboardwidgetpack',
            'MODXDashboardWidgetPack',
            $this->modx->getOption(
                'modxdashboardwidgetpack.core_path',
                null,
                $this->modx->getOption('core_path') . 'components/modxdashboardwidgetpack/'
            ) . 'model/modxdashboardwidgetpack/'
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render()
    {
        $this->modx->lexicon->load('modxdashboardwidgetpack:default');

        $data = [
            'background' => $this->getBackgroundImage(),
            'title'      => $this->modx->lexicon('mdwp.welcome.welcome_' . $this->getCurrentDayPart(), [
                'name'      => $this->modx->getUser()->Profile->get('fullname') ?: $this->modx->getUser()->get('username')
            ])
        ];

        $this->modx->getService('smarty', 'smarty.modSmarty');
        foreach ($data as $key => $value) {
            $this->modx->smarty->assign($key, $value);
        }

        $this->controller->addCss(rtrim($this->modx->modxdashboardwidgetpack->config['assets_url'], '/') . '/css/style.css');

        return $this->modx->smarty->fetch(dirname(__DIR__) . '/dashboard/welcome.tpl');
    }

    /**
     * @return string
     */
    public function process()
    {
        $this->cssBlockClass = 'widget--welcome';

        return parent::process();
    }

    /**
     * This will set the background image for the welcome widget.
     *
     * @return string
     */
    protected function getBackgroundImage()
    {
        $image = rtrim($this->modx->modxdashboardwidgetpack->config['assets_url'], '/') . '/img/default-welcome-widget-bg.jpg';

        $properties = $this->widget->get('properties');
        if (isset($properties['background_image_path']) && !empty($properties['background_image_path'])) {
            $image = str_replace(
                [
                    '[[++base_path]]',
                    '[[++core_path]]',
                    '[[++manager_path]]',
                    '[[++assets_path]]',
                    '[[++manager_theme]]',
                ], [
                    $this->modx->getOption('base_path',null,MODX_BASE_PATH),
                    $this->modx->getOption('core_path',null,MODX_CORE_PATH),
                    $this->modx->getOption('manager_path',null,MODX_MANAGER_PATH),
                    $this->modx->getOption('assets_path',null,MODX_ASSETS_PATH),
                    $this->modx->getOption('manager_theme',null,'default'),
                ],
                $properties['background_image_path']
            );
        }

        return $image;
    }

    /**
     * Returns a string for the current part of the day.
     *
     * @return string
     */
    protected function getCurrentDayPart()
    {
        $hour = date('H');

        $timeOfDay = 'evening';
        if ($hour > 6 && $hour <= 11) {
            $timeOfDay = 'morning';
        } else if($hour > 11 && $hour <= 16) {
            $timeOfDay = 'afternoon';
        }

        return $timeOfDay;
    }
}

return 'ModxDashboardWidgetPackWidgetWelcome';
