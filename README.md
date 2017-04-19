# My Apps Tutorial
Tutorial for "My Apps" for Volare release 6.5

### Purpose
This is a bare-bones app that shows the initial
capabilities that the "My Apps" feature has to offer.
 Hopefully it will capture your imagination to think of interesting
 ways to leverage Cloud4Wi and its vast capabilities.

### Prerequisites
* An account on [developers.cloud4wi.com](https://developers.cloud4wi.com)
* A tenant account on [volare.cloud4wi.com](https://volare.cloud4wi.com)
* A hosted domain to link to from our [Welcome Portal](https://splashportal.cloud4wi.com)
    * One page for configuration from our [Control Panel](https://volare.cloud4wi.com)
    * The page that your end-user will be directed to from the [Welcome Portal](https://splashportal.cloud4wi.com)

### Process
All right, now that you have everything you need to integrate with My Apps, let's get started!
##### 1. Register your app
   * Go to [developers.cloud4wi.com](https://developers.cloud4wi.com)
   * Log in and click on "Create New App".
   * Fill out the first part of the form.  Try to be descriptive, as 
   you will want to refer back to it when going through your apps.
   * **App Management**
       * **App Visibility** - Allows Venue owners to configure their respective venues.
       * **Enable Pre-Authentication Mode** - This allows the app to be triggered before it is
        authenticated by the wi-fi hotspot.
   * **App Endpoints**
        * **Base URL** - This is your base domain that your app is hosted on. Ex: https://google.com
        * **App Bar Page** - This will display in the app bar if the app is enabled in the Welcome Portal.
        * **Access Journey Page** - This page is meant for when the app is triggered during the Access Journey of the end-user.
        * **Admin Panel Settings Page** - This page will be your page to be imported into the Admin Panel, 
        where you can configure the other two pages (App Bar Page, Access Journey Page).
   * Once you have completely filled out the form, go ahead and click "Save".
   * The next page, once saved, will show your app and there will be a button saying "Publish".
   Once you are ready, click "Publish" and it will provide you with a code.  Make sure to keep 
   the _App ID_, you will need it in the next step.
   
##### 2. Register your published app in your "My Apps" section on Volare
   * Go to [volare.cloud4wi.com](https:volare.cloud4wi.com) and log in to your Tenant account.
   * Click on the "Apps" link inside the "Manage" section of the sidebar.
   * Click on the "Add" button in the top right of the page
   * Enter the _App ID_ that you saved from when you published the app in the previous step, and click "Check".
   * The modal will show your app when the correct _App ID_ is entered. Click "Import".
   * This will close the modal and reload the apps, including your app. Once you find it, 
   click on "Open" and it will take you to the **Admin Panel Settings Page** that you entered in previous step.
   * Great! Now we can get to the fun stuff, like coding and customizing our apps.
   

