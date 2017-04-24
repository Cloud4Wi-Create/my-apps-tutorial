# My Apps Tutorial
Tutorial for "My Apps" for Volare release 6.5

## Table of Contents
* [Purpose](#purpose)
* [Prerequisites](#prerequisites)
* [The App](#the-app)
* [Process](#process)
    1. [Register Your App](#1-register-your-app)
    2. [Import Your App](#2-import-your-published-app-into-your-my-apps-section-on-volare)
    3. [Creating the Admin Panel Settings Page](#3-creating-the-admin-panel-settings-page)
    4. [Setting the App to the Access Journey](#4-setting-the-app-to-the-access-journey)
    5. [Creating the End-User Experience](#5-creating-the-end-user-experience)
    6. [Working with the Navbar](#6-working-with-the-navbar)
    

### Purpose
This is a bare-bones app that shows the initial
capabilities that our "My Apps" feature has to offer.
 Hopefully it will capture your imagination and allow you to think of interesting
 ways to leverage Cloud4Wi and its vast capabilities.

### Prerequisites
* An account on [developers.cloud4wi.com](https://developers.cloud4wi.com).  This will have to be approved
by Cloud4Wi.
* A tenant account on [volare.cloud4wi.com](https://volare.cloud4wi.com)
* A hosted domain to link to from our [Welcome Portal](https://splashportal.cloud4wi.com)
    * One page for configuration from our [Admin Panel](https://volare.cloud4wi.com)
    * The page that your end-user will be directed to from the [Welcome Portal](https://splashportal.cloud4wi.com)
    
### The App
Our app will be fairly simple, with two steps:
 * First, we will trigger the app in the pre-authentication phase and show a message welcoming 
 the end-user: "Welcome! Complete the log-in process and get a free coffee!". 
 * Then, we are going to set another trigger for when the end-user is completely authenticated:
  "Thank you for logging in, {first_name} {last_name}, present this to the cashier for your free cup of coffee!"
  
Our app will include a mixture of PHP for server-side processing, JavaScript for interactive
API calls, and of course, HTML/CSS to make it appealing.



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
       Company level users to configure the app.
           * I set this to "Tenant" for the sake of the KISS principle (Keep it Stupid Simple). 
           This can always be changed later as your app evolves.
       * **Enable Pre-Authentication Mode** - This allows the app to be triggered before it is
        authenticated by the wi-fi hotspot.
           * Please enable this, as we will need it enabled for the example.
   * **App Endpoints**
        * **Base URL** - This is the base domain that your app is hosted on. Ex: https://cloud4wi.com
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
   * Go to [volare.cloud4wi.com](https://volare.cloud4wi.com) and log in to your Company account.
   * Click on the "Apps" link inside the "Manage" section of the sidebar.
   * Click on the "Add" button in the top right of the page
   * Enter the _App ID_ that you saved from when you published the app in the previous step, and click "Check".
   * The modal will show your app when the correct _App ID_ is entered. Click "Import".
   * This will close the modal and reload the apps, including your app. Once you find it, 
   click on "Open" and it will take you to the **Admin Panel Settings Page** that you entered in previous step.
   * Great! Now we can get to the fun stuff, like coding and customizing our apps.
   
#### 3. Creating the Admin Panel Settings Page
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

So when you call the API from the Admin Panel Settings Page from the Company level, 
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

With this, you can take the Company ID: `tenantId` and store configurations based on that,
or the Venue ID: `wifiareaId` if you are allowing the Venue owners to configure the app
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
        <input type="text" class="form-control" id="post_auth_message" placeholder="i.e. Welcome {first_name} {last_name} to the coffee shop!">
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
* Company ID

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
        data = typeof(data) === 'string' ? JSON.parse(data) : data;
        
        if(data.status === 'success') {
            apiSuccessMessage.removeClass('hide');

            setTimeout(function() {
                apiSuccessMessage.addClass('hide');
            }, 3000);
        }
        if(data.status === 'error') {
            apiFailureMessage.removeClass('hide');
        }
    },
    error:function(data) {
        apiFailureMessage.removeClass('hide');
    },
    method:'GET'
})
```


Now, please note that this is a call to _your_ third-party API, in order to save
the pre-authentication and post-authentication messages, along with the Company ID
so that you can create your custom logic on your app.

**TIP**:

For the first message, since we won't know anything about the customer in the pre-authentication phase,
we can provide a generic message like: "Welcome! Please sign in for a free cup of 
coffee!"

But for the second message, we are going to have conditional data from the API for the
name, which means that we will have to insert something to specify where the "name" 
variable is going to go. Luckily, we have thought of this in a clever (i.e. completely 
standard) way. Viola:

`Welcome, {first_name} {last_name}, thank you for signing in! Please present this to your cashier
to get a free cup of coffee!`

With the brackets, it provides an easy way to split the string and insert variables.


**OPTIONAL**:

There is one other API call in the front-end for this app, to check and see if there
 are already messages configured for you. Here it is:
 
```
$.ajax({
    url:'/api.php',
    data: {
        tenantId:config.auth.tenantId,
        action:'get_messages'
    },
    success:function(data) {
        data = typeof(data) === 'string' ? JSON.parse(data) : data;

        var preAuthMessageInput = $("#pre_auth_message");
        var postAuthMessageInput = $("#post_auth_message");

        if(data.status === 'success') {
            if(!!data.value.pre) {
                preAuthMessageInput.val(data.value.pre);
            }
            if(!!data.value.post) {
                postAuthMessageInput.val(data.value.post);
            }
        }
    },
    method:'GET'
})
```

Pretty straight-forward, all we are doing here is checking to see if there are messages
already stored for this Company, and if there are, we populate the input fields with them.
Done and done. Let's move on.

#### 4. Setting the App to the Access Journey
Now that we are ready to move on, we have to create a page for the end-user.  This means 
that first, we have to set our app on the Access Journey for both the pre-authentication, 
and post-authentication stages of the Access Journey:
* Go to "Access Journey" under the Guest-Wifi tab in the sidebar.
* You will see, at the top of the form, a section that says "Connect".
* Click on "Add an Application".
* Click on the dropdown and select your app.
* Scroll down and find "Log In".
* Do the same as before, select your app.
* Click Save in the top right corner of the screen.

Great, now we have the wifi-area configured to trigger your app in the pre-authentication
and post-authentication phase.

#### 5. Creating the End-User Experience

Now that we have a functioning settings page, it's time for us to set our focus on the 
end-user. 

Since we're developers, and we've mastered the art, let's copy and paste!

The only differences between this file and the Admin Panel file at the beginning of this step:
* There is no form - there will only be a message there for your customer.
* The API calls at the bottom of the page are not there, we will make a different one.

We can embellish the design a bit more if we would like, but for now we can move on 
to creating the functionality.

If you check the index.php page, you will notice that the API call is _exactly the same_. 
This is on purpose, as we wanted to make sure the full process was as simple as possible.

So, here we are calling the same exact API, and the data returned looks just a little
bit different: 

```
"status": "success",
"data": { 
    "customer": {
        "is_logged": false,
        "lang": "eng"
    },
    "hotspot: {
        "city": "Livorno, Italy",
        "id": "9067",
        "identifier": "685112345D_illiade",
        "latitude": "45.960782503827",
        "longitude": "12.091283106750",
        "mac_address": "685112345D",
        "name": "Odissea",
        "state": "Livorno",
        "tag": "hotspot",
        "zip": "Livorno",
    },
    "tenant": {
        "name": "Taylor's Tenant",
        "read_only": false,
        "tenant_id": "1001"
    },
    "wifiarea": {
        "name": "Livorno Venue",
        "wifiarea_id": "ae092a5b3e283c8373ke2bf18cde0005"
    }
}
```

Now, there are a few points to note here:
* In the pre-authentication phase, the customer object has almost no information.  In the 
 post-authentication phase, it will be populated with more information that we can use.
 
If you test this with a logged in customer, the object will look more like this:

```
"status": "success",
"data": { 
    "customer":{
        "lang":"eng",
        "is_logged":true,
        "id":"rlC.6yTePhzYg",
        "first_name":"John",
        "last_name":"Doe",
        "username":"706B5C1D",
        "gender":"",
        "birth_date":"0000-00-00 00:00:00",
        "phone":"",
        "phone_prefix":"",
        "email":"john.doe@cloud4wi.com",
        "mac_address":[]
    },
    "hotspot: {
        "city": "Livorno, Italy",
        "id": "9067",
        "identifier": "685112345D_illiade",
        "latitude": "45.960782503827",
        "longitude": "12.091283106750",
        "mac_address": "685112345D",
        "name": "Odissea",
        "state": "Livorno",
        "tag": "hotspot",
        "zip": "Livorno",
    },
    "tenant": {
        "name": "Taylor's Tenant",
        "read_only": false,
        "tenant_id": "1001"
    },
    "wifiarea": {
        "name": "Livorno Venue",
        "wifiarea_id": "ae092a5b3e283c8373ke2bf18cde0005"
    }
}
```
So, the only two variables that we have for both customer states are `is_logged` and `lang`, short for 
language.  We will use the `is_logged` variable to check for pre-authentication and post-authentication
customer states.
 
So, naturally since this will be the page for both pre-authentication and post-authentication, 
we will want to put some logic in to determine which stage we are in. I put this API call in the 
bottom of the file, where the API calls were in the cp_index page:

```
$.ajax({
    url:'/api.php',
    data: {
        // tenant id from config object returned from c4w api
        tenantId:config.data.tenant.tenant_id,
        action:'get_messages'
    },
    success:function(data) {
        data = typeof(data) === 'string' ? JSON.parse(data) : data;

        var greetingContainer = $("#greeting");
        var message; // just in case we have to change this in the if statement

        if(data.status === 'success') {
            if(!config.data.customer.is_logged) {
                greetingContainer.text(data.value.pre);
            }
            if(config.data.customer.is_logged) {
                // Process the message to find the brackets and replace them with variables
                message = insertMessageVariables(data.value.post, config.data.customer);
                greetingContainer.text(message);
            }
        }
    },
    method:'GET'
});
```
What you will notice with this API call is that we are getting the messages for the correct
Company: `tenantId`.  Then, we check to make sure the API call is a 
success and if that is true, we display the message based on whether the customer is logged in
or not (pre-authentication or post-authentication).

After a closer look, you will notice that there is an `insertMessageVariables` function call that 
we have not discussed yet.  This is to skim the string and find variables inside brackets.

The function is not perfect, but it will serve our purposes for something as simple as this:

```
function insertMessageVariables(string, object) {
    var arr = string.split(/{}/);

    var processedArr = arr.map(function(element) {
        element = !!object[element] ? object[element] : element;
        return element;
    });

    return processedArr.join('');
}
```
Should be pretty straight forward - this would be how we merge the data from Cloud4Wi and the
data that we have stored already (the pre and post-authentication messages).

Now, I made it as simple as possible and just put the customer object into the arguments
for the function and made the variable names in my string match the customer object.
You may want to elaborate on this, possibly creating a new object or concatenating the name
together first before passing it into the function, to make for more comprehensive functionality.
But, of course, that's up to you.

**Disclaimer**: Make sure that if you take variables from the customer object, 
you require those variables when they log-in/sign-up.  Otherwise the Cloud4Wi customer object
being returned from the API will _not_ provide all of the data that you need.
 
So, now that we have the functionality for the end-user all finished up, let's move on to one
 last crucial part of the application, the one that will bring together each step: The Nav-Bar.
 
#### 6. Working with the Navbar
Last but not least, we have provided a navbar with some exposed methods to help keep your 
end-user along the correct path in the Access Journey.  So without further ado, let's include the
JavaScript file in our page:

```
<script src="https://splashportal.cloud4wi.com/myapps/v1/myapps-sdk.js"></script>
```

Once this is done, we can put this line at the end of the success function in our API call:

```
MYAPPS.renderNavbar();
```

This is a default call to render the navbar.  It will use default styles and no title, and
it will automatically show a "next" button that the end-user can click and move to the next
step in the Access Journey.  We can choose to delay this button for a certain amount of 
seconds, or we can automatically show it, or not show it at all.  For this app's purposes,
the best idea would be to render it after they have read the message in the pre-authentication
phase and then show it automatically in the post-authentication phase. So since we already
have some logic to check whether or not the user is logged in, let's just add it to those blocks
of code:

```
var navbarParams = {
    fontColor:'black',
    backgroundColor:'white',
    apn:'Coffee Works' 
};

if(data.status === 'success') {
    if(!config.data.customer.is_logged) {
        navbarParams.nextBtn = 5;
        greetingContainer.text(data.value.pre);
    }
    if(config.data.customer.is_logged) {
        // Process the message to find the brackets and replace them with variables
        message = insertMessageVariables(data.value.post, config.data.customer);
        greetingContainer.text(message);
    }
}

MYAPPS.renderNavbar(navbarParams);
```

As you can see, we declared a variable that changes the style of the navbar, and then set this:
`navbarParams.nextBtn = 5`.

This sets the "next" button to show after 5 seconds, giving our end-user an ample amount of time
to read the message that we want to convey before moving on.

In the next if statement, we did not declare the `nextBtn` variable, and that is on purpose.
The default value of this is `true`, which automatically renders the "next" button.

There is more to the Navbar that is not mentioned in this tutorial, and if you would like to 
read more about the capabilities of the navbar, go to [link here].
