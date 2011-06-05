<?php
/**
 * sfSagePayPlugin configuration.
 *
 * @package    sfSagePayPlugin
 * @subpackage config
 * @author     Rich Sage <rich.sage@gmail.com>
 */
class sfSagePayPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if (sfConfig::get('app_sf_sagepay_plugin_routes_register', true) && in_array('sfSagePay', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('sfSagePayRouting', 'listenToRoutingLoadConfigurationEvent'));
    }
  }
}
