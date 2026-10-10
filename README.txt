PUNE FURNITURES - HOSTINGER UPLOAD GUIDE

1. Edit config.js (one file for all pages):
   (WhatsApp number 919130563172 is already set; change only if it changes)
   phoneDisplay -> "+91 XXXXX XXXXX"
   Also replace https://www.example.com/ (canonical tags in index.html and about.html) with your real domain.
2. Edit sitemap.xml and robots.txt: replace "yourdomain.com" with your domain.
3. Edit contact.php: set NOTIFY_EMAIL (where enquiries are emailed) and FROM_EMAIL
   (a mailbox you created on your domain in Hostinger).
4. Hostinger hPanel > Websites > Manage > File Manager > open public_html.
   Upload everything inside the public_html folder of this zip (including the hidden
   .htaccess files and the data folder). Or upload the zip and use Extract.
5. hPanel > SSL: make sure SSL is active (the .htaccess forces https).
6. Test: open the site on your phone, submit the form, then check data/enquiries.csv
   in File Manager (and your email if set). Test the WhatsApp and Call buttons.
7. Google Search Console: add the site and submit /sitemap.xml. Verify Google Business Profile.

Notes:
- Enquiries are stored in data/enquiries.csv. The data folder is blocked from the web.
- Photos are embedded in index.html. Replace placeholder style-reference photos with your own when ready.
- Hindi text was written by us: have a native speaker check it before launch.
