ces-parser
==========

Consumer Expenditure Survey Parser

There's a lot of great data at the bls but it's not programmatically accessible unless you want to decipher pages like this ftp://ftp.bls.gov/pub/time.series/bp/bp.data.1.AllData and create a database out of them.

Enter ces-parser, a simple script to transform many of the report pages in to json.

Usage
-----
It's a simple script.  Drop it on any php enabled server or install MAMP, WAMP or something along those lines locally.

To consume: 

> ftp://ftp.bls.gov/pub/special.requests/ce/standard/2011/income.txt


Simply pass it as a url param: 

> http://localhost:8888/pi/platforms/data/bls.php?url=ftp://ftp.bls.gov/pub/special.requests/ce/standard/2011/income.txt

I find this bookmarklet helpful when I am manually digging for gold:

> javascript:(window.location='http://localhost:8888/pi/platforms/data/bls.php?url='+window.location.href)


Happy Mining!