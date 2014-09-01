<?php
/**
 * @var $this \yimaTheme\Theme\Theme
 */
use Zend\Stdlib\ArrayUtils;

/**
 * Theme Resolver Run This Bootstrap And
 * Fall into Next Theme With Resolver Till Get
 * Into Final Theme
 *
 * By Default is True
 */
$this->isFinal = false;
$this->setTemplate('partial/builderfront/management-template');

// inject request token into jscripts
$storage = $this->getServiceLocator()->get('Widgetizer.PersistStorage');
$this->setVariable('widgetizer_rest_token', $storage->getToken());

// ---- Register Assets File Into AssetManager Service --------------------------------------------------------------------------------------------\
/*
 * These Config must merged to application config at last
 * : see below
 */
$ovverideConfigs = array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__.DS.'www',
            ),
        ),
    ),
);

// ---- Merge new config to application merged config ---------------------------------------------------------------------------------------------\
$mergedConf = $this->getServiceLocator()->get('Config');
$config     = ArrayUtils::merge($mergedConf, $ovverideConfigs);

$this->getServiceLocator()
    ->setAllowOverride(true)
    ->setService('config', $config)
    ->setAllowOverride(false);
