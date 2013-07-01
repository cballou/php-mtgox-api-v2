<?php

/**
 * This file is part of the Craftblue package.
 *
 * (c) Corey Ballou <corey@coreyballou.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Craftblue;

/**
 * An API wrapper around MTGox API v2.
 *
 * Sample usage:
 *
 * $mtgox = new MTGox('key', 'secret');
 * $mtgox->money_info();
 * $mtgox->query('money/info');
 * $mtgox->query('getFunds.php');
 * $mtgox->query('buyBTC.php', array('amount' => 1, 'price' => 15));
 */
class MTGox {

    /**
     * The base API url.
     */
    const BASEURL = 'https://data.mtgox.com/api/';

    /**
     * The default API version.
     *
     * @var int
     */
    public $version = 2;

    /**
     * The currently set currency.
     *
     * @var string
     */
    public $currency = 'USD';

    /**
     * The public/private API key. You can obtain from:
     * https://mtgox.com/security
     *
     * @var string
     */
    public $key;

    /**
     * The API secret. You can obtain from:
     * https://mtgox.com/security
     *
     * @var string
     */
    public $secret;

    /**
     * A list of valid MTGox API endpoints. Some are fairly undocumented.
     *
     * @var array
     */
    public $validEndpoints = array(
        'money/bank/register',
        'money/bank/list',
        'money/bitcoin/addpriv',
        'money/bitcoin/addr_details',
        'money/bitcoin/address',
        'money/bitcoin/block_list_tx',
        'money/bitcoin/null',
        'money/bitcoin/send_simple',
        'money/bitcoin/tx_details',
        'money/bitcoin/vanity_lookup',
        'money/bitcoin/wallet_add',
        'money/bitinstant/fee',
        'money/bitinstant/quote',
        'money/code/list',
        'money/code/redeem',
        'money/currency',
        'money/depth/fetch',
        'money/depth/full',
        'money/idkey',
        'money/info',
        'money/japan/lookup_bank',
        'money/japan/lookup_branch',
        'money/merchant/order/create',
        'money/merchant/order/pay',
        'money/merchant/order/details',
        'money/merchant/order/payment',
        'money/merchant/pos/order/create',
        'money/merchant/pos/order/close',
        'money/merchant/pos/order/get',
        'money/merchant/pos/order/add_product',
        'money/merchant/pos/order/edit_product',
        'money/merchant/product/add',
        'money/merchant/product/del',
        'money/merchant/product/get',
        'money/merchant/product/edit',
        'money/order/add',
        'money/order/cancel',
        'money/order/lag',
        'money/order/result',
        'money/order/quote',
        'money/orders',
        'money/swift/details',
        'money/ticker',
        'money/ticker_fast',
        'money/ticket/create',
        'money/token/process',
        'money/trades/fetch',
        'money/trades/cancelled',
        'money/wallet/history',
        'security/hotp/gen',
        'stream/list_public'
    );

    /**
     * Endpoints which handle auxilary conversions from BTC to CURRENCY.
     * Any endpoint listed here has a requirement of passing a currency in the
     * method call.
     *
     * @var array
     */
    public $auxilaryEndpoints = array(
        'money/depth/fetch',
        'money/depth/full',
        'money/info',
        'money/idkey',
        'money/order/add',
        'money/order/cancel',
        'money/order/lag',
        'money/order/result',
        'money/order/quote',
        'money/orders',
        'money/currency',
        'money/ticker',
        'money/ticker_fast',
        'money/trades/fetch'
    );

    /**
     * Array of valid currencies and their conversions from int to decimal
     * (conversion from currency to bitcoins). Anything with {value_int}
     * needs converting.
     *
     * @var array
     */
    public $validCurrencies = array(
        'BTC' => 0.00000001,
        'USD' => 0.00001,
        'AUD' => 0.00001,
        'CAD' => 0.00001,
        'CHF' => 0.00001,
        'CNY' => 0.00001,
        'DKK' => 0.00001,
        'EUR' => 0.00001,
        'GBP' => 0.00001,
        'HKD' => 0.00001,
        'JPY' => 0.001,
        'NZD' => 0.00001,
        'PLN' => 0.00001,
        'RUB' => 0.00001,
        'SEK' => 0.001,
        'SGD' => 0.00001,
        'THB' => 0.00001,
        'NOK' => 0.00001,
        'CZK' => 0.00001
    );

    /**
     * An array of valid rate (money ticker) types.
     *
     * @var array
     */
    public $validRateTypes = array(
        'high',         // 24 hour high?
        'low',          // 24 hour low?
        'avg',
        'vwap',         // the volume-weighted average price
        'vol',
        'last_local',   // the last trade in your selected auxiliary currency
        'last_orig',    // the last trade (any currency)
        'last_all',     // that last trade converted to the auxiliary currency
        'last',         // the same as last_local
        'buy',          // last buy price?
        'sell',         // last sell price?
        'now'           // the unix timestamp, but with a resolution of 1 microsecond
    );

    /**
     * Memcached wrapper.
     *
     * @var Cache
     */
    public $cache;

    /**
     * Default constructor. Allows for setting the key and secret.
     *
     * @access  public
     * @param   Cache   $cache
     * @param   string  $key
     * @param   string  $secret
     * @return  void
     */
    public function __construct(Cache $cache, $key = '', $secret = '')
    {
        if (!empty($key) && !empty($secret)) {
            $this->authenticate($key, $secret);
        }
    }

    /**
     * Set the authentication parameters.
     *
     * @access  public
     * @param   string  $key
     * @param   string  $secret
     * @return  void
     */
    public function authenticate($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Set/override the API version.
     *
     * @access  public
     * @param   int     $version
     * @return  void
     */
    public function setVersion($version = 2)
    {
        $this->version = (int) $version;
    }

    /**
     * Returns the currently set currency.
     *
     * @access  public
     * @return  string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the currency.
     *
     * @access  public
     * @param   string  $currency
     * @return  void
     */
    public function setCurrency($currency = 'USD')
    {
        $currency = strtoupper($currency);
        if (!isset($this->validCurrencies[$currency])) {
            throw new Exception('Invalid currency.');
        }

        $this->currency = $currency;
    }

    /**
     * Triggers an API request.
     *
     * @access  public
     * @param   string  $path
     * @param   array   $req
     */
    public function query($path, array $req = array())
    {
        // check if we need to add the currency to the endpoint
        if ($this->_endpointRequiresCurrency($path)) {
            $path = $this->_prependCurrency($path);
        }

        // add version to path
        $path = self::BASEURL . $this->version . '/' . $path;

        // generate a nonce as microtime, with as-string handling to avoid
        // problems with 32-bit systems
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);

        // generate the POST data string
        $post_data = http_build_query($req, '', '&');

        // API v2 has a special data prefix
        $prefix = '';
        if (substr($path, 0, 2) == '2/') {
            $prefix = substr($path, 2) . "\0";
        }

        // generate the extra headers
        $headers = array(
            'Rest-Key: ' . $this->key,
            'Rest-Sign: ' . base64_encode(
                hash_hmac(
                    'sha512',
                    $prefix . $post_data,
                    base64_decode($this->secret),
                    true
                )
            )
        );

        // get the result
        return $this->_request($path, $post_data, $headers);
    }

    /**
     * Handles making the actual API request and decoding the response.
     *
     * @access  protected
     * @param   string      $path
     * @param   string      $post_data
     * @param   array       $headers
     */
    protected function _request($path, $post_data, $headers)
    {
        static $ch = null;

        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_USERAGENT,
                'Mozilla/4.0 (compatible; MtGox PHP client; '
                    . php_uname('s') . '; PHP/' . phpversion() . ')'
            );
        }

        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // run the query
        $res = curl_exec($ch);
        if ($res === false) {
            throw new Exception('No API response: ' . curl_error($ch));
        }

        $dec = json_decode($res, true);
        if (!$dec) {
            throw new Exception(
                'Invalid API response data received. Make sure connection and endpoint exist.'
            );
        }

        return $dec;
    }

    /**
     * Converts a value into bitcoins.
     *
     * @access  public
     * @param   int     $value
     */
    public function convertToBtc($value)
    {
        if (!is_int($value)) {
            throw new Exception('The value must be an integer.');
        }

        return $value * ($this->validCurrencies[$this->getCurrency()]);
    }

    /**
     * A magic method for making class API calls look cleaner. Can take any
     * endpoint formatted with underscore namespacing and convert it to
     * an API call. Validates the api call exists via the whitelist.
     *
     * @access  public
     * @param   string  $apiEndpoint
     * @param   array   $arguments
     * @return  void
     */
    public function __call($apiEndpoint, array $arguments)
    {
        $apiEndpoint = str_replace('_', '/', $apiEndpoint);
        $apiEndpoint = strtolower(preg_replace('/([A-Z])/', '_$1', $apiEndpoint));
        if (!$this->_endpointExists($apiEndpoint)) {
            throw new Exception('The API endpoint ' . $apiEndpoint . ' does not exist!');
        }

        // merge endpoint with arguments
        $params = !empty($arguments[0]) ? $arguments[0] : array();
        return call_user_func(array(&$this, 'query'), $apiEndpoint, $params);
    }

    /**
     * Prepend a currency conversion for an API endpoint because it requires one.
     *
     * @access  protected
     * @param   string      $endpoint
     * @return  string
     */
    protected function _prependCurrency($endpoint)
    {
        return 'BTC' . $this->getCurrency() . '/' . $endpoint;
    }

    /**
     * Validates whether the API endpoint exists.
     *
     * @access  protected
     * @param   string  $endpoint
     * @return  bool
     */
    protected function _endpointExists($endpoint)
    {
        return in_array($endpoint, $this->validEndpoints);
    }

    /**
     * Checks if the endpiont needs to be prefixed with a currency conversion.
     *
     * @access  protected
     * @param   string      $endpoint
     * @param   bool
     */
    protected function _endpointRequiresCurrency($endpoint)
    {
        return in_array($endpoint, $this->auxilaryEndpoints);
    }

}
