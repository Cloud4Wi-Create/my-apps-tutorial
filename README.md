# My Apps Tutorial
Tutorial for "My Apps" for Volare release 6.5

## Table of Contents
* [Purpose](#purpose)
* [Prerequisites](#prerequisites)
* [The App](#the-app)
* [Process](#process)
    1. [Register Your App](#1-register-your-app)
    2. [Import Your App](#2-import-your-published-app-into-your-my-apps-section-on-volare)
    3. [Coding Admin Page](#3-coding-the-admin-panel-settings-page)

### Purpose
This is a bare-bones app that shows the initial
capabilities that our "My Apps" feature has to offer.
 Hopefully it will capture your imagination and allow you to think of interesting
 ways to leverage Cloud4Wi and its vast capabilities.

### Prerequisites
* An account on [developers.cloud4wi.com](https://developers.cloud4wi.com)
* A tenant account on [volare.cloud4wi.com](https://volare.cloud4wi.com)
* A hosted domain to link to from our [Welcome Portal](https://splashportal.cloud4wi.com)
    * One page for configuration from our [Control Panel](https://volare.cloud4wi.com)
    * The page that your end-user will be directed to from the [Welcome Portal](https://splashportal.cloud4wi.com)
    
### The App
Our app will be rather simple - first, we will trigger it in the pre-authentication phase
and show a message welcoming the end-user: "Welcome! Complete the log-in process and get a 
free coffee!". Then we are going to set another trigger for when the end-user
is completely authenticated: "Thank you for logging in, {name}, present this 
to the cashier for your free cup of coffee!"



### Process
Now that you have everything you need to integrate with My Apps, let's get started! *Drum roll*
#### 1. Register your app
**Disclaimer**: All of these settings can be modified as you change and evolve your app. 
   * Go to [developers.cloud4wi.com](https://developers.cloud4wi.com)
   * Log in and click on "Create New App".
   * Fill out the first part of the form.  Try to be descriptive, as 
   you will want to refer back to it when going through your apps.
   * **App Management**
       * **App Visibility** - Allows Venue owners to configure their respective venues, or only allow
       Tenant level users to configure the app.
           * I set this to "Tenant" for the sake of the KISS principle (Keep it Stupid Simple). 
           This can always be changed later as your app evolves.
       * **Enable Pre-Authentication Mode** - This allows the app to be triggered before it is
        authenticated by the wi-fi hotspot.
   * **App Endpoints**
        * **Base URL** - This is the base domain that your app is hosted on. Ex: https://google.com
        * **App Bar Page** - This will display in the app bar if the app is enabled in the Welcome Portal.
        * **Access Journey Page** - This page is meant for when the app is triggered during the Access Journey of the end-user.
        * **Admin Panel Settings Page** - This page will be your configuration page to be 
        imported into the Admin Panel, where you can configure the other two pages 
        (App Bar Page, Access Journey Page).
   * Once you have completely filled out the form, go ahead and click the "Save" button.
   * The next page, once saved, will show your app and there will be a button saying "Publish".
   Once you are ready, click "Publish" and it will provide you with an overview of the app.
     Make sure to keep the _App ID_, as you will need it in the next step.
   
#### 2. Import your published app into your "My Apps" section on Volare
   * Go to [volare.cloud4wi.com](https://volare.cloud4wi.com) and log in to your Tenant account.
   * Click on the "Apps" link inside the "Manage" section of the sidebar.
   * Click on the "Add" button in the top right of the page
   * Enter the _App ID_ that you saved from when you published the app in the previous step, and click "Check".
   * The modal will show your app when the correct _App ID_ is entered. Click "Import".
   * This will close the modal and reload the apps, including your app. Once you find it, 
   click on "Open" and it will take you to the **Admin Panel Settings Page** that you entered in previous step.
   * Great! Now we can get to the fun stuff, like coding and customizing our apps.
   
#### 3. Coding the Admin Panel Settings Page
In this page, you can call our My Apps API in order to get information on the user.  
For both the Admin Panel Settings Page and the Customer Facing page, you can call the same
API but we will return information based on where you are calling from.

We import an iFrame into our Control Panel and inject a variable into the URL named "sk".
You will have to extract that from the URL, as that is the session code we use to authenticate 
and identify your app. Here is a quick way to do it in PHP:

`$sk = $_GET['sk'];`

And then just concatenate it on to the end of the Volare My Apps API:
 
`$url = 'https://volare.cloud4wi.com/controlpanel/1.0/bridge/sessions/' . $sk;`
 
Next, make a function in PHP to call to the Cloud4Wi API. This is located in our /cp_index.php file 
starting at line 22 inside the function named: `function callApi`

```
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url
));
$result = curl_exec($curl);
$session = json_decode($result, true);
```
We return the data in JSON format, so if you intend to use it with a language other than JavaScript, 
make sure to decode it. 

Now, since the next part will require the use of some user interaction, let's declare the returned 
data in a JavaScript variable:
 
`var config = <?php echo json_encode(callApi()); ?>;`

So when you call the API from the Admin Panel Settings Page from the Tenant level, 
we return an object:

```
{ 
     "auth": {
        "level": "tenant"
        "tenantId": "1001"
     }
     "lang": "eng"
} 
```

From the Venue level, you will get an object like this:

```
{
    "lang": "eng",
    "auth": {
        "tenantId": "1001",
        "level": "wifiarea",
        "wifiareaId": "80808080482080f20148asdf3246b0001"
    }
}
```

With this, you can take the `tenantId` and store configurations based on that,
or the `wifiareaId` if you are allowing the Venue owners to configure the app
by themselves.

So, going along with what we talked about in the beginning, let's create a form that has two inputs:
* Pre-authentication greeting, prompting the user to log in for a free cup of coffee.
* Post-authentication greeting, thanking the user for logging in and presenting a free cup of coffee.
 
Here it is, in all its glory:
```
<form id="app_parameters">
    <div class="form-group">
        <label for="pre_auth_message">Pre Auth Message</label>
        <input type="text" class="form-control" id="pre_auth_message" placeholder="i.e. Welcome to the coffee shop!">
    </div>
    <div class="form-group">
        <label for="post_auth_message">Post Auth Message</label>
        <input type="text" class="form-control" id="post_auth_message" placeholder="i.e. Welcome {name} to the coffee shop!">
    </div>
    <p class="bg-success hide" id="api_success_message">Success</p>
    <p class="bg-danger hide" id="api_failure_message">Failure</p>
    <button type="submit" class="btn btn-default">Submit</button>
</form>
```
Now that we have a form, we will handle the data when it is submitted and store it somewhere. Let's create
an AJAX call to an API, consisting of three crucial points of data:
* Pre-authentication message
* Post-authentication message
* Tenant ID

On line 161, there is a jQuery event listener waiting for the form submit. Above that are comments that 
explain why. Here is the AJAX call:

```
$.ajax({
    url:'/api.php',
    data: {
        tenantId:config.auth.tenantId,
        pre:preAuthMessage,
        post:postAuthMessage,
        action:'set_messages'
    },
    success:function(data) {
        // enter 
    },
    error:function(data) {
        apiFailureMessage.removeClass('hide');
    },
    method:'GET'
})
```
