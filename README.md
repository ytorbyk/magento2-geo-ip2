<h2>Welcome</h2>

We are happy that you have chosen our free extension ToBai GeoIP2. This extension was created especially for Magento 2 developers to make it easier for them to work with different IP databases.

*   It is pre-defined 2 free databases for Country and City detection from maxmind.com
*   It is possible to easily add any other IP database
*   There are special settings in extension configs to set connection with other databases using web service client, e.g. paid maxmind.com databases

<h2>Installation</h2>

Please follow next instructions to successfully install ToBai GeoIP2 in your Magento 2 store.

1. Disable the cache with this command:

        bin/magento cache:disable

2. Add extension to composer require section using this command:

        composer require tobai/magento2-geo-ip2

3. Enable module and upgrade with this commands:

        bin/magento module:enable Tobai_GeoIp2
        bin/magento setup:upgrade

4. Check under Stores->Configuration->Advanced->Advanced that the module Tobai_GeoIp2 is present. If Tobai_GeoIp2 displays in alphabetical order, you successfully installed the reference module!

5. Flush and enable the cache with this commands:
        
        bin/magento cache:flush
        bin/magento cache:enable

Now you should see new ToBai tab at Stores > Configuration. When you click at this tab you will see GeoIP2 section.
