# papi v1.0
Papi ("Pappy" or "Pawpi") is a small footprint API framework and example based on "Pagemin" (with some additions from "Page") for building web APIs

Pagemin: http://github.com/h3rb/pagemin
Page: http://github.com/h3rb/page

_Uses_

You can use Papi to build a secure API back-end for mobile and web applications.  It has a light footprint and the strength of simple, effective ACLs.  It's sole purpose is to police access to data, provide the data to an external web application or mobile application, and monitor sessions and login information. 

You could build a site mainly as static pages / javascript on S3, and then use this as a scalable backend in an auto-load balancing group.

The seed schema is located in the main folder as "Papi_Schema.sql" and contains the sample application schema, make sure to modify the CREATE DATABASE and USE clauses at the top of the file.

Out-of-the-box, gives you the following very, very simple sample implementation of:

* User, Session implemented
* All users can see all other users
* Only admins may modify users other than yourself
* Auth requires two tokens included in the headers, seen in shreds/Auth.php, called from core/Auth.php
* Provides session tokens that expire over time, can be logged out of, can be logged in to
* Uses password_hash for storing passwords
* Has a "forgot" password link generator that would mail to someone if you set that up.

Models:  User, Settings
Shreds:  Auth (logic for checking API and handling authentication-related tasks)

The folder backend/ lets you test the API and shows you the json format of the $_POST['data']

By default has no SSL support, but easily added if you follow the comments in the index.php --- This is done because presumably you don't have your certs set up when you are just trying this out or starting a new API at the beginning.  If you do have certs it can be easily required of users to be accessing the API over HTTPS.  It should be required to be used over HTTPS/SSL unless in a VPN or VPC environment, since data can be sniffed, unless you want to create publicly accessible API calls (some rarer cases).  Most applications should not do this, and instead should require authentication by the remote user/device/app.

I also recommend writing your ORM and other logic in modules/ so that it can be loaded as-needed.

It's possible and also recommended that you implement user-specific tokens aside from sessions, for their application channel or any other channel you need to establish with a user so that you can track user activity and route requests more effectively.

All of the major action occurs in the index.php, but you can easily break functionality out into a object-oriented implementation.  You can put that stuff in a folder of your choice.

![Alt text](/kiuwan-papi.png?raw=true "Kiuwan.com Code Review Output")
[![Kiuwan](https://www.kiuwan.com/github/h3rb/papi/badges/quality.svg)](https://www.kiuwan.com/github/h3rb/papi) (Kiuwan.com)
