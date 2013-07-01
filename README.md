An API wrapper around MTGox API v2 that's slightly more sane than some alternatives.

By sane, I simply mean there's some internal method param validation that occurs to ensure
you are passing valid values to the API.

## Pre-reqs ##

Usage of Composer or direct inclusion of the library via `require_once 'src/craftblue/mtgox.php'`

## Usage ##

There's two primary ways to make API calls that accomplish the same thing:

1. Using PHP's internal magic method `__call()`, allowing you to create human readable calls.
1. Using the built-in `request()` method where you supply the API endpoint as a string.

It's personal preference!

```php
<?php
// include the composer autoloader
require_once 'vendor/autoloader.php';

// use the namespace
use Craftblue;

// initialize the class with your public/private key pair
// obtained from https://mtgox.com/security
$mtgox = new MTGox('key', 'secret');

// make a call (both do the same thing)
$mtgox->money_info();
$mtgox->query('money/info');

// make a call to an underscored endpoint (both do the same thing)
$mtgox->stream_listPublic();
$mtgox->query('stream/list_public');
```

## API Overview ##

I won't cover the broadness of the API, but rather link you to the docs and highlight the endpoints available with short descriptions:

https://bitbucket.org/nitrous/mtgox-api/
https://en.bitcoin.it/wiki/MtGox/API/HTTP/v2

#### Documented API Endpoints ####

*  `money/currency`
   Get information for a given currency.
   **Sample Response:**
        ```javascript
        {
             "result":"success",
             "data": {
                 "currency":"USD",
                 "name":"Dollar",
                 "symbol":"$",
                 "decimals":"5",
                 "display_decimals":"2",
                 "symbol_position":"before",
                 "virtual":"N",
                 "ticker_channel":"abc123-def456",
                 "depth_channel":"abc123-def456"
             }
         }
         ```
*  `money/depth/fetch`
*  `money/depth/full`
*  `money/idkey`
*  `money/info`
*  `money/order/add`
*  `money/order/cancel`
*  `money/order/lag`
*  `money/order/result`
*  `money/order/quote`
*  `money/orders`
*  `money/ticker`
   Get the most recent information for a currency pair.
   **Sample Response:**
   ```javascript
    {
        "result":"success",
        "data": {
            "high":       **Currency Object - USD**,
            "low":        **Currency Object - USD**,
            "avg":        **Currency Object - USD**,
            "vwap":       **Currency Object - USD**,
            "vol":        **Currency Object - BTC**,
            "last_local": **Currency Object - USD**,
            "last_orig":  **Currency Object - ???**,
            "last_all":   **Currency Object - USD**,
            "last":       **Currency Object - USD**,
            "buy":        **Currency Object - USD**,
            "sell":       **Currency Object - USD**,
            "now":        "1364689759572564"
        }
    }
    ```
*  `money/ticker_fast`
   Get the most recent information for a currency pair. This method is similar to money/ticker, except it returns less information, and is supposedly lag-free.
   **Sample Response:**
   ```javascript
    {
        "result":"success",
        "data": {
            "last_local": **Currency Object - USD**,
            "last":       **Currency Object - USD**,
            "last_orig":  **Currency Object - EUR**,
            "last_all":   **Currency Object - USD**,
            "buy":        **Currency Object - USD**,
            "sell":       **Currency Object - USD**,
            "now":        "1366230242125772"
        }
    }
    ```
*  `money/trades/fetch`
*  `money/trades/cancelled`
*  `money/wallet/history`
*  `security/hotp/gen`
*  `stream/list_public`

#### Undocumented API Endpoints ####

If you have any information on these, please consider a PULL request.

*  `money/bank/register`
   Undocumented.
*  `money/bank/list`
   Undocumented.
*  `money/bitcoin/addpriv`
   Undocumented.
*  `money/bitcoin/addr_details`
   Undocumented.
*  `money/bitcoin/address`
   Undocumented.
*  `money/bitcoin/block_list_tx`
   Undocumented.
*  `money/bitcoin/null`
   Undocumented.
*  `money/bitcoin/send_simple`
   Undocumented.
*  `money/bitcoin/tx_details`
   Undocumented.
*  `money/bitcoin/vanity_lookup`
   Undocumented.
*  `money/bitcoin/wallet_add`
   Undocumented.
*  `money/bitinstant/fee`
   Undocumented.
*  `money/bitinstant/quote`
   Undocumented.
*  `money/code/list`
   Undocumented.
*  `money/code/redeem`
   Undocumented.
*  `money/japan/lookup_bank`
   Undocumented.
*  `money/japan/lookup_branch`
   Undocumented.
*  `money/merchant/order/create`
   Undocumented.
*  `money/merchant/order/pay`
   Undocumented.
*  `money/merchant/order/details`
   Undocumented.
*  `money/merchant/order/payment`
   Undocumented.
*  `money/merchant/pos/order/create`
   Undocumented.
*  `money/merchant/pos/order/close`
   Undocumented.
*  `money/merchant/pos/order/get`
   Undocumented.
*  `money/merchant/pos/order/add_product`
   Undocumented.
*  `money/merchant/pos/order/edit_product`
   Undocumented.
*  `money/merchant/product/add`
   Undocumented.
*  `money/merchant/product/del`
   Undocumented.
*  `money/merchant/product/get`
   Undocumented.
*  `money/merchant/product/edit`
   Undocumented.
*  `money/swift/details`
   Undocumented.
*  `money/ticket/create`
   Undocumented.
*  `money/token/process`
   Undocumented.

## Happy? Please Consider Donating me BTC! ##
