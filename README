sfSagePayPlugin
===============

This plugin aims to provide integration with SagePay's payment services,
for Symfony 1.x projects.

Currently this has only been tested with Symfony 1.4.

Installation
------------

Download the plugin files into `plugins/sfSagePayPlugin`.
Enable the plugin in `config/ProjectConfiguration.class.php`:

  `$this->enablePlugins(..., "sfSagePayPlugin");`

Enable the sfSagePay module in your application (optional),
in `apps/yourapp/config/settings.yml`:

    all:
      .settings:
        enabled_modules: [sfSagePay]


Clear your cache: `symfony cc`

Usage
-----

Currently, the only implemented service is SagePay's VSP Direct service.

    $sp = new sfSagePayDirect();
    $sp->setVendorName("myvendorname");
    $sp->setSiteStatus(sfSagePay::SP_LIVE);
    $sp->setVendorTxCode("order12345");
    $sp->setAmount(12.50);

    // ... other fields are $sp->setFieldName($value) as per SagePay documentation

    $response = $sp->registerTransaction();

    // $response is an array containing fields returned from SagePay
    // eg $response["Status"], $response["VPSTxId"] and so on


Routing
-------

The plugin registers routes for ease of 3D-Secure processing.  You can disable
this automatic registration by including the following setting in your app's
settings.yml:

    sf_sagepay_plugin:
      routes_register: false


3D-Secure payments
------------------

If a payment requires 3D-Secure authentication:

    $this->getUser()->setAttribute("3DAuthenticationInProgress", true);
    $this->getUser()->setAttribute("MD", $response["MD"]);
    $this->getUser()->setAttribute("ACSURL", $response["ACSURL"]);
    $this->getUser()->setAttribute("PAReq", $response["PAReq"]);
    $this->redirect("@sf_sagepay_3dsecure");

You should also set up some routes to handle the callback:

    sf_sagepay_3dresult
    sf_sagepay_endurl

The `sf_sagepay_3dresult` route will be posted the results of the 3D-Secure transaction.
The `sf_sagepay_endurl` route will handle the 'End of order' page, usually shown to
the customer.
