# CleanStop

CleanStop is a tiny php-based application that enabled organizations to crowdsource funds for multiple projects spread out on a map. The first use of this was to fund the installation of trash cans at a large number of bus stops. Using CleanStop, all the bus stops were represented on a map, and citizens could chose the stop of their liking and donate.

![Screenshot](/screenshot.png?raw=true)

This is different from other crowdsourcing platforms like kicstarter, indigogo, ioby, gofundme etc in a few important ways:

  - You are dealing with the code: you'll need to buy PHP hosting (cheap hosting will do) and host this platform yourself. You will need technical help for that. More in "Technical Installation" section).
  - CleanStop integrates directly with Stripe (a payments gateway provider), and gets the money in your account directly. This means you'll have to create your own stripe account and furnish them with all your details before you can get started.

##### Technical Installation
This section is for the person installing CleanStop for use by an organization. Here are the steps involved:

1. **Install the code:** This application is a rather simple PHP application, and can be simply copy-pasted into the hosting server's public web directory. Any directory can be used (the application is not path dependent), and no .htaccess configuration is required.
    - *Installing the Stripe library*: If you look at the `php/stripe-php-4.0.0/` directory, you'll find it empty. Download the stripe-php library from the [releases of stripe-php](https://github.com/stripe/stripe-php/releases), and unzip it here, making sure to not create an extra level of directories. We have used stripe-php 4.0.0, and not tested later versions.
2. **Setup the database:** The next step would be to create a database in your host's mysql server. Once created, note down its name, and the required username & password to access this database - this will be used later. Locate the `setup.sql` file in `php/db/` directory, and create the two tables as shown in that file. This creates two tables: `busstops` and `donations`. Populate the first table with bus stops (or any crowdsourcing projects) that you want to make appear on CleanStop's map. A sample CSV of bus stops is provided.
3. **Create a Stripe Account:** Go to [stripe.com](https://stripe.com/) and create a new account. This will require you to furnish many details about your organization like your EIN number, bank details, organization incorporation details etc. Be sure to have them handy. Once created, you should be able to use the Stripe dashboard, and retrieve your "public key" and "private key". Note them down, you will need them later. 
    - *Note about testing:* You will see the option to download "Test" keys as well as "Production" keys. Use the test keys initially in step 4 - this will let you configure the application and test it without having to run real credit card transactions. Instead you can use test cards provided by Stripe. There is more information in [Stripe's testing documentation.](https://stripe.com/docs/testing)
4. **Configure the application:** Once all the above steps are done, the last step is to update a few configuration values in the code:
   - In `js/app.js`, search for "your-stripe-public-key" and replace it with your own stripe public key.
    - In `php/variables.php`, update the placeholder values for the database connection parameters and the stripe private key with actual values noted earlier in steps 2 and 3.

That's all, you are done!

#### Issues and Troubleshooting
If you face issues during installation or usage, or have general questions, use the Issues section in Github to direct questions to us - we will be glad to answer and help!
