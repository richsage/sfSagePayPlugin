<?php
/**
 *
 */
class sfSagePayRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   * @static
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    // preprend our routes:

    // * The 3DSecure page which wraps the IFRAME
    $r->prependRoute('sf_sagepay_3dsecure', new sfRoute('/sfSagePay/3DSecure', array('module' => 'sfSagePay', 'action' => '3DSecure')));

    // The IFRAME itself
    $r->prependRoute('sf_sagepay_3diframe', new sfRoute('/sfSagePay/3DIFRAME', array('module' => 'sfSagePay', 'action' => '3DIFRAME')));

    // The 3D result page - SagePay's "TermURL"
    $r->prependRoute('sf_sagepay_3dresult', new sfRoute('/sfSagePay/3DResult', array('module' => 'sfSagePay', 'action' => '3DResult')));

    // The "end of order" page to redirect to
    $r->prependRoute('sf_sagepay_endurl', new sfRoute('/sfSagePay/endURL', array('module' => 'sfSagePay', 'action' => 'endURL')));

    // The script that breaks us out of the IFRAME
    $r->prependRoute('sf_sagepay_3dbreakiframe', new sfRoute('/sfSagePay/breakIFRAME', array('module' => 'sfSagePay', 'action' => 'breakIFRAME')));
  }
}