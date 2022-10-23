# Simple Unifi Management Interface

Some features of the Unifi Network Controller are frustrating to manage or browse.
This project aims to circumvent those issues by presenting a simple and clean interface
by which to manage them.

## Available Interfaces

* **RADIUS Users**: Create, Update, Delete RADIUS users.
* **AP Groups**: Rename AP Groups, Change which APs are members of an AP Group, and change
SSID membership in an AP Group. Also lists the number of APs and SSIDs in an AP Group and
the number of SSIDs currently broadcast from each AP. Page also tells you the status of
Wireless Uplink Monitor and, therefore, how many SSIDs each AP can broadcast.

<p align="center">
  <img width="480" src="https://user-images.githubusercontent.com/31744530/197379888-78c42fe3-ec0d-480c-bc0f-aaaedb261bbf.png">
</p>
<p align="center">
  <img width="480" src="https://user-images.githubusercontent.com/31744530/197380078-cb6d37da-a14b-4f9c-bc48-549ac267c277.png">
</p>

## Features

* SQLite database to store users. No interface to manage this, so use whatever
tool you want to manage the users here. Password is expected to be the result of PHP's
`password_hash($password, PASSWORD_DEFAULT)` function. Run `php -r 
"echo password_hash("your_password", PASSWORD_DEFAULT);` to get something to insert here.
* Simple permissions for users to access each page. 1 to allow, 0 to disallow.
* Bootstrap + Datatables to present information. DataTables presents information in a way
that is sortable and searchable.
* JQuery to make asynchronous calls so you don't have to keep reloading the page.
* `.env` file for Unifi instance information

## Requirements

* PHP >=7.4 with `sqlite`, `pdo`, `json`, `ctype`, `curl` extensions.
* Composer

## Installation

1. Clone the repo
1. Run `composer install`
1. Rename `.env.example` to `.env` and set your Unifi instance information. I recommend creating
a Unifi user specifically for this tool.
1. Point your webserver to the `public/` directory of this project (this is the hand-wavey magic part)
1. Navigate to `<host-ip>/login.php` in your browser
1. Login with user `test` and password `asdf`.
1. (Optionally) Delete the `test` user in `database.db` and create your own.

## Why?

Two reasons:

1. Unifi permissions are not granular. I wanted to give access to a customer's onsite IT
technician to update RADIUS users, but to do that I would've had to give them full Administrator
access for the entire site -- which can easily lead to Bad Things (tm).
2. Some (most?) of Unifi's interfaces are annoying to manage. Specifically, browsing a large
list of RADIUS users was cumbersome, especially because there was no search; and forget about
managing AP Groups and SSID/AP membership in one interface. I mean, *why* do I have to click to
edit a Wireless Network just to get to the interface for AP Groups? It's dumb.

## TODO

1. Site selection. Currently, the Unifi site is hardcoded in `.env`.
1. Deduplicate code. I didn't use any kind of template engine so I've got copy/pasted HTML everywhere,
plus duplicated JavaScript.
1. Permissions on API Calls.
1. Better loading indicators.
1. User management
1. Allow logins from users in `database.db` AND existing superadmins in the Network Controller.
1. Other interfaces? RADIUS Users and AP Groups are the main two interfaces that bugged me enough to make this.
