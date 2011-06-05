<?php

class sfSagePayDirect extends sfSagePay
{
  /**
   * Account types:
   * E - E-commerce merchant account (default)
   * C - Continuous authority merchant account (if present)
   * M - Mail order, telephone order accoutn (if present)
   *
   * @var const
   */
  const ACCOUNT_ECOMMERCE = "E";
  const ACCOUNT_CONTINUOUS = "C";
  const ACCOUNT_MOTO = "M";

  /**
   * 3D-authentication URL
   *
   * @var string
   */
  protected $Auth3DURL = "";

  /**
   * Refund URL - for purchase transaction not from today
   *
   * @var string
   */
  protected $RefundURL = "";

  /**
   * Release URL - release pre-auth/deferred payments
   *
   * @var string
   */
  protected $ReleaseURL = "";

  /**
   * Repeat URL - for repeated payments
   *
   * @var string
   */
  protected $RepeatURL = "";

  /**
   * Void URL - for purchase transactions from today
   *
   * @var string
   */
  protected $VoidURL = "";

  /**
   * Direct-specific fields. Self-explanatory
   *
   * @var string
   */
  protected $CardHolder = "";
  protected $CardNumber = "";
  protected $StartDate = "";
  protected $ExpiryDate = "";
  protected $IssueNumber = "";
  protected $CV2 = "";
  protected $CardType = "";

  /**
   * Account type to use for transactions
   *
   * @var string
   */
  protected $AccountType = "";

  /**
   * 3D-Secure values
   *
   * @var string
   */
  protected $MDValue = "";
  protected $PaResValue = "";

  /**
   * Class constructor.
   * We're using the Direct service
   *
   * @param string $status
   */
  function __construct($status = parent::SP_TEST)
  {
    parent::__construct($status);

    $this->Service = sfSagePay::SP_DIRECT;
    $this->setURLs();

    $this->AccountType = self::ACCOUNT_ECOMMERCE;
  }

  /**
   * VSP Direct-specific URLs
   *
   */
  protected function setURLs()
  {
    switch ($this->SiteStatus)
    {
      case self::SP_SIMULATOR:
        $this->PurchaseURL = "https://test.sagepay.com/simulator/VSPDirectGateway.asp";
        $this->Auth3DURL = "https://test.sagepay.com/simulator/VSPDirectCallback.asp";
        $this->RefundURL = "https://test.sagepay.com/simulator/VSPServerGateway.asp?service=VendorRefundTx";
        $this->ReleaseURL = "https://test.sagepay.com/simulator/VSPServerGateway.asp?service=VendorReleaseTx";
        $this->RepeatURL = "https://test.sagepay.com/simulator/VSPServerGateway.asp?service=VendorRepeatTx";
        $this->VoidURL = "https://test.sagepay.com/simulator/VSPServerGateway.asp?service=VendorVoidTx";

        break;

      case self::SP_TEST:
        $this->PurchaseURL = "https://test.sagepay.com/gateway/service/vspdirect-register.vsp";
        $this->Auth3DURL = "https://test.sagepay.com/gateway/service/direct3dcallback.vsp";
        $this->RefundURL = "https://test.sagepay.com/gateway/service/refund.vsp";
        $this->ReleaseURL = "https://test.sagepay.com/gateway/service/release.vsp";
        $this->RepeatURL = "https://test.sagepay.com/gateway/service/repeat.vsp";
        $this->VoidURL = "https://test.sagepay.com/gateway/service/void.vsp";

        break;

      case self::SP_LIVE:
        $this->PurchaseURL = "https://live.sagepay.com/gateway/service/vspdirect-register.vsp";
        $this->Auth3DURL = "https://live.sagepay.com/gateway/service/direct3dcallback.vsp";
        $this->RefundURL = "https://live.sagepay.com/gateway/service/refund.vsp";
        $this->ReleaseURL = "https://live.sagepay.com/gateway/service/release.vsp";
        $this->RepeatURL = "https://live.sagepay.com/gateway/service/repeat.vsp";
        $this->VoidURL = "https://live.sagepay.com/gateway/service/void.vsp";

        break;

      default:
        $this->PurchaseURL = "";
        $this->Auth3DURL = "";
        $this->RefundURL = "";
        $this->ReleaseURL = "";
        $this->RepeatURL = "";
        $this->VoidURL = "";
    }
  }

  /**
   * Construct data for SagePay
   *
   * @return string $data
   */
  protected function constructData()
  {
    // Defaults in
    $data = parent::constructData();

    // Add card details in here
    $data .= "&CardHolder=" . urlencode($this->CardHolder);
    $data .= "&CardNumber=" . urlencode($this->CardNumber);
    $data .= "&ExpiryDate=" . urlencode($this->ExpiryDate);
    $data .= "&CardType=" . urlencode($this->CardType);

    if (strlen($this->StartDate))
    {
      $data .= "&StartDate=" . urlencode($this->StartDate);
    }
    if (strlen($this->IssueNumber))
    {
      $data .= "&IssueNumber=" . urlencode($this->IssueNumber);
    }
    if (strlen($this->CV2))
    {
      $data .= "&CV2=" . urlencode($this->CV2);
    }

    return $data;
  }

  public function validateInput()
  {
  }

  public function sendResponse()
  {
  }

  /**
   * Sends 3D-secure MD and PaRes values back to SagePay
   *
   * @return array $response
   */
  public function send3DPostback()
  {
    // Note: capitalisation of fieldnames is important here
    $data = "MD=" . $this->MDValue . "&PARes=" . urlencode($this->PaResValue);

    return $this->requestPost($this->Auth3DURL, $data);
  }
}