# VerusPay Verus Gateway

- Contributors: veruspay, joliverwestbrook
- Donate link: https://veruspay.io/donate/
- Tags: woocommerce, payment gateway, gateway, cryptocurrency, blockchain, verus, verus coin, vrsc, pirate, arrr, komodo, kmd
- Requires at least: 5.0.0
- Tested up to: 5.2.3
- Requires PHP: 7.0
- Stable tag: 0.5.0
- Requires WooCommerce at least: 3.5.6
- Tested WooCommerce up to: 3.7.0
- License: MIT
- License URI: https://opensource.org/licenses/MIT

## Description

### Requires: WooCommerce 3.5.6+

### Supporting Repos:

>> Verus Chain Tools - https://github.com/joliverwestbrook/VerusChainTools

and 

>> VerusPay Install Scripts - https://github.com/joliverwestbrook/VerusPayInstallScripts

This plugin extends WooCommerce and integrates with the Verus blockchain, adding the ability to accept cryptocurrency payments in Verus Coin (VRSC) using either an on-store wallet daemon (not recommended) or in conjunction with a remote wallet server (recommended).

When an order is submitted via the VerusPay gateway, the order will be placed "on-hold" while awaiting payment from the customer. The customer has a limited time wherein to send the payment and the store monitors the wallet/address to confirm payment received before releasing the order and redirecting the customer to the Thank You page.

VerusPay uses limited API functionality for Manual Mode, to communicate with the blockchain explorer in verifying payments and with the veruspay.io API to get up-to-date, volume-weighted price data.  These API's do not receive any private data either about the store owner, store, or customer.  The only data sent to the block explorer API is the public/transparent blockchain transaction and address used.  For VerusPay.io API price data, only the store-set currency is sent to retrieve the current fiat exchange rate for Verus Coin.

API's used periodically by VerusPay:

1 - https://veruspay.io/api/

2 - https://explorer.veruscoin.io

## Installation

### Requirements

**WooCommerce Store**

If only acting as a store, with your crypto wallets on a remote server, virtually any WordPress hosted site will do.

**Crypto Wallet Daemon Server**

This can be the same server as your web store (NOT RECOMMENDED) or a remote and dedicated wallet daemon server (RECOMMENDED).  It is recommended that the Wallet server have a minimum of 2GB of RAM only if it also has a 4GB SWAP.  Otherwise more RAM.

Consider setting up a DigitalOcean server for this using this guide: [How to Setup a DigitalOcean $5 VerusCoin Server](http://bit.ly/2Ca6LIK) but choose a 2GB minimum server as your Wallet Server.

Follow this guide to setup your crypto wallet daemon server and the Verus Chain Tools, required for full blockchain integrated features of VerusPay:

[Wallet Server & Verus Chain Tools Install Guide and Script](https://github.com/joliverwestbrook/VerusPayInstallScripts/blob/master/README.md)

After the install finishes, it will display IMPORTANT information for you to write down in a secure location. BE SURE TO WRITE THIS INFORMATION DOWN. 

### Upgrade Steps

To upgrade from any version below v0.3.0 you MUST first upgrade your VerusChainTools and wallet daemon server settings, following the steps found [here](https://github.com/joliverwestbrook/VerusPayInstallScripts)

### Install & Configuration Steps

**Configure Your Wallet Daemon Server:**

1. Configure Wallet Settings

After you install VerusPay, configure each coin's Wallet Settings.  You must use the Verus Chain Tools installation script either on a remote Wallet Server (recommended) or on your store server, by following [this guide](https://github.com/joliverwestbrook/VerusPayInstallScripts/blob/master/README.md)

If using a remote wallet server, it is recommended to enable SSL.  If your wallet deamons are local to your store server, https is disabled.

2. As best practice for all Daemons with transparent addresses (e.g. Verus), it is required to generate and input a LOT of additional transaparent wallet addresses as a means of backup in case your Wallet Daemon server goes down.  

To do so, from a DIFFERENT server (not your new wallet daemon server...we'll call this your "offline" wallet), run the script to generate many additional transparent VRSC addresses.  Download the appropriate script for your OS to your "Offline" Verus wallet system from this link: [VerusPay Helper Scripts](https://veruspay.io/setup/scripts/)

Place the script in your "offline" wallet's main folder (verus-cli) and execute it.  In Linux or Mac run with: `./getaddresses.sh 500` where "500" is the number of addresses to generate (I recommend a min of 500).  In Windows run it with `getaddresses.bat 500`

The script will create a file in the same folder called VerusPayGeneratedAddresses.txt. You'll copy and paste these addresses in the VerusPay settings in a later step.

3. Lastly, customize store messages and any other settings and options to your liking.

## Frequently Asked Questions

**What is the text domain for translations?

The text domain is "veruspay-verus-gateway".

**How can I change the styling (CSS) of the checkout pages?

CSS options are being added in a later release to the Admin section. For now, most themes come with a section where you can add your own CSS styling and you can add customizations this way.

**Does this plugin work with a shared hosting plan or a host that does not have SSH access?

Yes! Because it is recommended you setup a remotely dedicated wallet server, your store can be on any capable hosting plan.  You can also use "manual mode" for any transparent-capable wallet (e.g. Verus)

**Can I "enforce" privacy Sapling payments?

Yes, there is an option in the payment gateway settings within WooCommerce->Settings->Payments to enforce privacy "zs" payments.

**I'm running a Woocommerce shop on an Azure hosted WebApp / Shared Host / Godaddy / Dreamhost / etc. Can I use this guide: [veruspay.io/setup](https://veruspay.io/setup)?

Yes, you can follow the published guide from GitHub and just follow the recommended procedure (your wallet server will be a seperate/new server you setup with DigitalOcean).

**How do I withdraw funds from my store wallet(s)?

*NEW* You can now withdraw funds from any of your wallet stores using the 1-Click Cashout feature added in v0.3.0.  Simply navigate to the VerusPay settings within WordPress and expand the Wallet Management section, from which you can withdraw funds to your preset Cashout Address for the wallet in question.

Or, the "hard way" is described as follows:

If you are withdrawing "transparent" funds (e.g. from sales made to Verus "R" addresses):

Login with SSH to the server hosting the wallet daemon and issue the following command, replacing the appropriate address and amount variables:

For Verus: `/opt/verus/verus.sh sendtoaddress "RECEIVEADDRESS" AMOUNT`

If you are withdrawing "private" or "sapling" funds (e.g. from sales made to a "zs" address):

Login with SSH to the server hosting the wallet daemon and issue the following command for the applicable wallet, replacing the appropriate address and amount variables:

For Verus: `/opt/verus/verus.sh z_sendmany "STOREADDRESSSENDINGFROM" "[{\"address\": \"RECEIVEADDRESS\", \"amount\":AMOUNT}]"`

For Pirate: `/opt/pirate/pirate.sh z_sendmany "STOREADDRESSSENDINGFROM" "[{\"address\": \"RECEIVEADDRESS\", \"amount\":AMOUNT}]"`

## Screenshots
 
1. This is the main settings area for VerusPay within WooCommerce's "Settings->Payments" section. Each of the headings expand upon clicking.

2. The Wallet Settings section is where the store owner enters the connection settings for blockchain integration.

3. Store owners input VRSC transparent addresses in the "Store VRSC Addresses" text field which are used when the store is in manual mode, either by the store owner's choice or as a fallback if there is any issue in connecting with the blockchain.

4. The Message and Content Customizations section allows store owners to create custom messages to be used throughout the purchase process when a customer pays with Verus.

5. Store Options allow the store owner to define many of the attributes of the checkout and purchase process with regards to timeouts, privacy, and QR codes.

6. The Discount/Fee option allows a store owner to define either an additional fee or a discount which is applied to customers who checkout using Verus.

7. At checkout, this is what the Verus payment option looks like to the customer, although CSS may be applied by advanced users to slightly alter this.

8. After a customer proceeds to purchase, this is the screen they are presented with where they are able to pay and see payment receipt occur live.

9. After payment is detected on the blockchain, the purchase waits for the store-set minimum confirmations and delivers the digital product or completes the sale when it's reached.

## Changelog

= 2019.09.19 - version 0.5.0 =

- Major release, change entire codebase and layout, function, with new functions for next release configured
- PBaaS Support

= 2019.08.03 - version 0.4.0-beta =
*** Beta Testing ONLY release for upcoming version 0.4.0 ***

= 2019.06.08 - version 0.3.6 =

- Minor bug fixes

= 2019.05.25 - version 0.3.5 =

- Bug fixes for WP 5.2.1 & WC 3.6.3 compatibility
- Performance improvements

= 2019.05.20 - version 0.3.4 =

- Fix minor bugs
- Add mining and staking capabilities with feedback
- Prepare for PBaaS compatibility

= 2019.05.17 - version 0.3.2 =

- Fix errors with KMD compatibility, wallet management
- Improve functionality of wallet management

= 2019.04.26 - version 0.3.1 =

- Remove Sapling support from KMD Komodo

= 2019.04.24 - version 0.3.0 =

- Rewrite chaintools functions for multi explorer support
- Add KMD and ZEC explorer data
- Add KMD support for the plugin
- Add wallet management support
- Implement new Access Code security feature
- 1-Click Cashout from VerusPay Wallet Management section
- Future feature backend code implemented

= 2019.03.12 - version 0.2.0 =

- Rewrite primary admin form to include multiple coins
- Rewrite primary blockchain integration scripts
- Implement new blockchain integration mechanism
- Rewrite all payment functions for multi-coin inclusion
- Rewrite checkout process functions for multicoin compatibility
- Added PIRATE ARRR as a payment option
- Add PIRATE ARRR media icon files
- Add ability for remote wallet daemons
- Add SSL for remote wallet daemons

= 2019.02.28 - version 0.1.2 =

- Simplify code functions
- Remove cURL, phpexttools, easybitcoin
- Create wp_remote_post and get functions for all cURL requests
- Consolidate all blockchain integration functions into "wc-veruspay-chaintools.php"
- Rename all functions to unique names
- Edit text domain and slug to match as veruspay-verus-gateway
- Include uninstall script for clean uninstallation of plugin
- Improve readme file
- Rename screenshots

= 2019.02.25 - version 0.1.1-a =

- Fix bug with QR Invoice format
- Add hide function of QR Invoice if Sapling purchase (sapling not currently supported in Verus Mobile)


= 2019.02.24 - version 0.1.1 =

- Add Verus Mobile Invoice compatible QR codes for orders
- Add admin customization for memo and image url within Invoice QRs


= 2019.02.11 - version 0.1.0 =

 * Initial Release Includes the Following Features:
 
- Verus Coin (VRSC) support
- Sapling zk-SNARK private payment support as an option to the customer or enforced by the store
- Verus blockchain integration in Live mode allows dynamic address generation and live status monitoring of payments for both transparent and private.
- Manual mode allows use of the plugin with a pool of addresses imported by the store owner.
- A script is included for exporting a list of transparent addresses to a text file to import into the store.
- Fallback to manual mode if Live mode fails
- Enforce Sapling option for store owner removes the option at checkout, all Verus payments must be Sapling.
- Message customization for store owners.
- VRSC Decimal definition for store owners.
- Price timeout allows the store owner to define for how many minutes a Verus / Fiat price will display before getting a new price at checkout.
- Order wait time allows the store owner to define how long an order will remain open before cancelled while waiting for the full payment amount to arrive from the customer at the VRSC address.
- Confirmations required allows the store owner to define how many blockchain confirmations are required before a payment is considered final and the customer's order is considered completed.
- QR Code size option in PX for the store owner
- Discount or Fee option for store owners allows a discount or fee % to be set for when a customer chooses to pay in Verus Coin (VRSC).
- Test mode to allow enabling the plugin only for logged in Admins

## Upgrade Notice

= 2019.06.08 - version 0.3.6 =

Bug Fixes

= 2019.05.25 - version 0.3.5 =

Performance and Bug Fix Release

= 0.3.4 =
Feature Release - Mining and Staking from Store

= 0.3.2 =
Important - Bug Fixes

= 0.3.1 =
Important - Sapling support removed from KMD Komodo 

= 0.3.0 = 
Major Release - KMD Komodo Support and Wallet Management

= 0.2.0 =
Major Release - New Crypto Payment Method Pirate ARRR Implemented

= 0.1.2 =
Code improvements

= 0.1.1-a =
Bug fixes

= 0.1.1 =
Add QR Invoice functionality

## Support

I'm presently unavailable but will be back in a couple weeks (roughly early Nov) to help anyone.  In the meantime, please visit the Verus Coin official Discord at [VerusCoin Official Discord](https://discord.gg/VRKMP2S)

* Learn about the [Verus Coin official site](https://veruscoin.io) for more information about the community project
* Join the [Verus Coin Discord](https://discord.gg/VRKMP2S) for support. 
* More documentation is available at [VerusPay Official Site](https://veruspay.io)
