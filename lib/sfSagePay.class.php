<?php
/**
 * Base class for sfSagePayPlugin usage.
 *
 * @author Rich Sage <rich.sage@gmail.com>
 *
 */
abstract class sfSagePay
{
  // SagePay user configuration
  protected $VendorName;
  protected $Service;
  protected $TxType;
  protected $Currency;
  protected $Apply3DSecure;
  protected $ApplyAVSCV2;
  protected $SiteStatus;
  protected $VPSProtocol;

  // SagePay order details
  // NB not all implemented fields are here
  protected $VendorTxCode;
  protected $Amount;
  protected $Description;
  protected $BillingSurname;
  protected $BillingFirstnames;
  protected $BillingAddress1;
  protected $BillingAddress2;
  protected $BillingCity;
  protected $BillingPostCode;
  protected $BillingCountry;
  protected $BillingState;
  protected $BillingPhone;
  protected $DeliverySurname;
  protected $DeliveryFirstnames;
  protected $DeliveryAddress1;
  protected $DeliveryAddress2;
  protected $DeliveryCity;
  protected $DeliveryPostCode;
  protected $DeliveryCountry;
  protected $DeliveryState;
  protected $DeliveryPhone;
  protected $CustomerEMail;
  protected $ClientIPAddress;

  // returned information
  protected $Response;

  // not used...
  protected $Status;
  protected $StatusDetail;
  protected $VpsTxId;
  protected $SecurityKey;
  protected $NextURL;
  protected $SecureStatus3D;
  protected $VPSSignature;
  protected $TxAuthNo;

  // URLs
  protected $PurchaseURL;

  const SP_SIMULATOR = 1;
  const SP_TEST = 2;
  const SP_LIVE = 3;

  const SP_SERVER = "SERVER";
  const SP_FORM = "FORM";
  const SP_DIRECT = "DIRECT";

  /**
   * Class constructor
   * Optionally pass in the status of the class:
   * - sfSagePay::SP_SIMULATOR
   * - sfSagePay::SP_TEST
   * - sfSagePay::SP_LIVE
   *
   * This can be changed later on if required with setStatus()
   *
   * @param const $status
   */
  function __construct($status = self::SP_LIVE)
  {
    $this->VendorName = "";
    $this->Service = "";
    $this->ApplyAVSCV2 = 0;
    $this->Apply3DSecure = 0;
    $this->VPSProtocol = "2.23";
    $this->TxType = "PAYMENT";
    $this->Currency = "GBP";
    $this->SiteStatus = $status;

    $this->PurchaseURL = "";
  }

  /**
   * Handles setting of the Vendor Name. Max 15 chars long
   *
   * @param string $vendorName
   */
  public function setVendorName($vendorName)
  {
    $vendorName = trim($vendorName);
    if (empty($vendorName))
      throw new Exception("SagePay vendor name cannot be empty");

    if (strlen($vendorName) > 15)
      throw new Exception("SagePay vendor name is too long");

    $this->VendorName = $vendorName;
  }

  public function setSiteStatus($status)
  {
    $this->SiteStatus = $status;
    $this->setURLs();
  }

  /**
   * Helper method. Can be overriden.
   *
   */
  abstract public function validateInput();

  /**
   * Registers the initial transaction with SagePay
   *
   * @return array $response
   */
  public function registerTransaction()
  {
    $data = $this->constructData();

    // request built, send to SagePay
    $response = $this->requestPost($this->PurchaseURL, $data);
    $this->Response = $response;
    return $this->Response;
  }

  /**
   * Constructs the basic data that needs to go to SagePay.
   * Can be overriden but the service class method MUST call
   * parent::constructData() otherwise validation by SagePay
   * will fail.
   *
   * @return array $data
   */
  protected function constructData()
  {
    $data = "";

    $data .= "VPSProtocol=" . urlencode($this->VPSProtocol);
    $data .= "&TxType=" . urlencode($this->TxType);
    $data .= "&Vendor=" . urlencode($this->VendorName);
    $data .= "&VendorTxCode=" . urlencode($this->VendorTxCode);
    $data .= "&Amount=" . urlencode(sprintf("%.2f", $this->Amount));
    $data .= "&Currency=" . urlencode($this->Currency);
    $data .= "&Description=" . urlencode($this->Description);

    // These fields are compulsory in protocol version 2.23
    $data .= "&BillingFirstnames=" . urlencode($this->BillingFirstnames);
    $data .= "&BillingSurname=" . urlencode($this->BillingSurname);
    $data .= "&BillingAddress1=" . urlencode($this->BillingAddress1);
    if (strlen($this->BillingAddress2))
    {
      $data .= "&BillingAddress2=" . urlencode($this->BillingAddress2);
    }
    $data .= "&BillingCity=" . urlencode($this->BillingCity);
    $data .= "&BillingPostCode=" . urlencode($this->BillingPostCode);
    $data .= "&BillingCountry=" . urlencode($this->BillingCountry);
    if (strlen($this->BillingState))
    {
      $data .= "&BillingState=" . urlencode($this->BillingState);
    }
    if (strlen($this->BillingPhone))
    {
      $data .= "&BillingPhone=" . urlencode($this->BillingPhone);
    }

    $data .= "&DeliveryFirstnames=" . urlencode($this->DeliveryFirstnames);
    $data .= "&DeliverySurname=" . urlencode($this->DeliverySurname);
    $data .= "&DeliveryAddress1=" . urlencode($this->DeliveryAddress1);
    if (strlen($this->DeliveryAddress2))
    {
      $data .= "&DeliveryAddress2=" . urlencode($this->DeliveryAddress2);
    }
    $data .= "&DeliveryCity=" . urlencode($this->DeliveryCity);
    $data .= "&DeliveryPostCode=" . urlencode($this->DeliveryPostCode);
    $data .= "&DeliveryCountry=" . urlencode($this->DeliveryCountry);
    if (strlen($this->DeliveryState))
    {
      $data .= "&DeliveryState=" . urlencode($this->DeliveryState);
    }
    if (strlen($this->DeliveryPhone))
    {
      $data .= "&DeliveryPhone=" . urlencode($this->DeliveryPhone);
    }

    // other misc
    $data .= "&CustomerEMail=" . urlencode($this->CustomerEMail);
    $data .= "&ApplyAVSCV2=" . urlencode($this->ApplyAVSCV2);
    $data .= "&Apply3DSecure=" . urlencode($this->Apply3DSecure);
    if (strlen($this->ClientIPAddress))
    {
      $data .= "&ClientIPAddress=" . urlencode($this->ClientIPAddress);
    }

    return $data;
  }

  /**
   * Helper method. Copies the billing details to the shipping details
   *
   */
  public function setDeliveryAsBilling()
  {
    $this->DeliveryAddress1 = $this->BillingAddress1;
    $this->DeliveryAddress2 = $this->BillingAddress2;
    $this->DeliveryCity = $this->BillingCity;
    $this->DeliveryPostCode = $this->BillingPostCode;
    $this->DeliveryCountry = $this->BillingCountry;
    $this->DeliveryState = $this->BillingState;
    $this->DeliveryPhone = $this->BillingPhone;
    $this->DeliveryFirstnames = $this->BillingFirstnames;
    $this->DeliverySurname = $this->BillingSurname;
  }

  /**
   * Magic setters/getters.
   * XXX not sure why they're in here
   *
   * @param string $name
   * @param mixed $args
   */
  public function __call($name, $args)
  {
    // set?
    if (substr($name, 0, 3) === "set")
    {
      // yes
      // try and set - exception thrown if not possible
      $var = substr($name, 3);
      $this->{$var} = $args[0];
    }
    elseif (substr($name, 0, 3) === "get")
    {
      // get
      $var = substr($name, 3);
      return $this->{$var};
    }
  }

  /**
   * If we get here, our variable doesn't exist
   *
   * @param string $name
   */
  public function __get($name)
  {
    throw new Exception("SagePay variable '$name' does not exist");
  }

  /**
   * If we get here, our variable doesn't exist
   *
   * @param string $name
   * @param mixed $args
   */
  public function __set($name, $args)
  {
    throw new Exception("SagePay variable '$name' cannot be set");
  }

  /**
   * Handles the POST requests to SagePay
   *
   * @param string $url
   * @param string $data
   */
  protected function requestPost($url, $data)
  {
    set_time_limit(60);

    $output = array();

    $curlSession = curl_init();

    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_HEADER, 0);
    curl_setopt($curlSession, CURLOPT_POST, 1);
    curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

    $rawresponse = curl_exec($curlSession);
    $response = explode(chr(10), $rawresponse);
    if (curl_error($curlSession))
    {
      $output["Status"] = "FAIL";
      $output["StatusDetail"] = curl_error($curlSession);
    }

    curl_close ($curlSession);

    for ($i = 0; $i < count($response); $i++)
    {
      $splitAt = strpos($response[$i], "=");
      $output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
    }

    return $output;
  }

  /**
   * This is implemented by the Server usage
   *
   */
  abstract public function sendResponse();

  /**
   * URL information. Set by the individual service classes
   *
   */
  abstract protected function setURLs();
}

