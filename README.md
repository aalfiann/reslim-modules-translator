### Detail module information

1. Namespace >> **modules/translator**
2. Zip Archive source >> 
    https://github.com/aalfiann/reSlim-modules-translator/archive/master.zip

### How to Integrate this module into reSlim?

1. Download zip then upload to reSlim server to the **modules/**
2. Extract zip then you will get new folder like **reSlim-modules-translator-master**
3. Rename foldername **reSlim-modules-translator-master** to **translator**
4. Done

### How to Integrate this module into reSlim with Packager?

1. Make AJAX GET request to >>
    http://**{yourdomain.com}**/api/packager/install/zip/safely/**{yourusername}**/**{yourtoken}**/?lang=en&source=**{zip archive source}**&namespace=**{modul namespace}**

### Known limitations
 - `503 Service Unavailable` response:  
   If you are getting this error, it is most likely that Google has banned your external IP address and/or **requires you to solve a CAPTCHA**. This is not a bug in this package. Google has become stricter, and it seems like they keep lowering the number of allowed requests per IP per a certain amount of time. Try sending less requests to stay under the radar, or change your IP frequently (**using proxies**). Please note that once an IP is banned, even if it's only temporary, the ban can last from a few minutes to more than 2-48 hours, as each case is different.
 - `No Proxies in this module`, the reason is:  
   Using Proxies is not the solution, because using Proxies can also be banned and trying to refresh proxies in each request is bad.

## Disclaimer
This package is developed for educational purposes only. Do not depend on this package as it may break anytime as it is based on crawling the Google Translate website. Consider buying [Official Google Translate API](https://cloud.google.com/translate/) for other types of usage.