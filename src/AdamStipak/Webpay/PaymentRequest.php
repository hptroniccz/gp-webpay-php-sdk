<?php

namespace AdamStipak\Webpay;

/**
 * Payment Requester class
 */
class PaymentRequest {

  const EUR = 978;
  const CZK = 203;
  const GBP = 826;
  const HUF = 348;
  const PLN = 985;
  const RUB = 643;
  const USD = 840;

  const PM_CRD = "CRD"; // credit/debit card
  const PM_MCM = "MCM"; // MasterCard Mobile
  const PM_MPS = "MPS"; // MasterPass
  const PM_GPAY = "GPAY"; // GooglePay

  /** @var array */
  private $validPayMethods = [
    self::PM_CRD,
    self::PM_MCM,
    self::PM_MPS,
    self::PM_GPAY,
  ];

  /** @var array */
  private $params = [];

  /**
   * Payment Requester
   * 
   * @param int         $orderNumber    Payments number - must be in each request from trader unique. 
   * @param float       $amount         Price to pay
   * @param int         $currency       Currency code ISO 4217
   * @param int         $depositFlag    Request Indicates whether the payment is to be paid automatically. Allowed values: 0 = no immediate payment required 1 = payment is required
   * @param string      $url            Full Merchant URL. A result will be sent to this address  request. The result is forwarded over customer browser    
   * @param string|null $merOrderNumber Order Number. In case it is not specified, it will be used  value $orderNumber It will appear on the bank statement.
   */
  public function __construct (int $orderNumber, float $amount, int $currency, int $depositFlag, string $url, string $merOrderNumber = null) {
    $this->params['MERCHANTNUMBER'] = "";
    $this->params['OPERATION'] = 'CREATE_ORDER';
    $this->params['ORDERNUMBER'] = $orderNumber;
    $this->params['AMOUNT'] = $amount * 100;
    $this->params['CURRENCY'] = $currency;
    $this->params['DEPOSITFLAG'] = $depositFlag;

    if ($merOrderNumber) {
      $this->params['MERORDERNUM'] = $merOrderNumber;
    }

    $this->params['URL'] = $url;
  }

  /**
   * Set Digest for current request
   * 
   * @internal
   * @param string $digest Verification signature of the string that is generated by concatenating all fields in the order given.
   */
  public function setDigest ($digest) {
    $this->params['DIGEST'] = $digest;
  }

  /**
   * Gives You all Request params
   * @return array 
   */
  public function getParams (): array {
    return $this->params;
  }

  /**
   * Set The Merchant Number for request
   * 
   * @internal
   * @param $number Attributed merchant number.
   */
  public function setMerchantNumber ($number) {
    $this->params['MERCHANTNUMBER'] = $number;
  }
  
  /**
   * Add Description parameter to request fields
   * 
   * @param string  $value field value
   */
  public function setDescription($value){
    $this->params['DESCRIPTION'] = $value;
  }

  /**
   * Set preferred payment method
   *
   * @param string $method
   */
  public function setPayMethod(string $method): void {
    if($this->isValidPayMethod($method)) {
      $this->params['PAYMETHOD'] = $method;
    }
  }

  /**
   * Set disabled payment method for current request
   *
   * @param string $method
   */
  public function disablePayMethod(string $method): void {
    if($this->isValidPayMethod($method)) {
      $this->params['DISABLEPAYMETHOD'] = $method;
    }
  }

  /**
   * Set allowed payment methods for current request
   *
   * @param array $methods
   */
  public function allowedPayMethods(array $methods): void {
    if(!empty($validMethods = array_filter($methods, [$this, "isValidPayMethod"]))) {
      $this->params['PAYMETHODS'] = implode(",", $validMethods);
    }
  }

    /**
     * Validate pay method identificator
     *
     * @param string $payMethod
     * @return bool
     */
  private function isValidPayMethod(string $payMethod): bool {
    return in_array($payMethod, $this->validPayMethods, true);
  }
}
