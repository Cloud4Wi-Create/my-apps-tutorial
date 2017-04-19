# My Apps Tutorial
Tutorial for "My Apps" for Volare release 6.5

## Table of Contents
* [Purpose](#Purpose)
* [Prerequisites](#Prerequisites)
* [The App](#The--App)
* [Process](#Process)
    1. [Register Your App](#1.--Register--your--app)
    2. [Import Your App](#2.--Import--your--published--app--into--your--"My--Apps"--section--on--Volare)
    3. [Coding Admin Page](#3.--Coding--the--Admin--Panel--Settings--Page)

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
   * Go to [developers.cloud4wi.com](https://developers.cloud4wi.com)
   * Log in and click on "Create New App".
   * Fill out the first part of the form.  Try to be descriptive, as 
   you will want to refer back to it when going through your apps.
   * **App Management**
       * **App Visibility** - Allows Venue owners to configure their respective venues, or only allow
       Tenant level users to configure the app.
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
   

