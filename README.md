# Page framework v1.13
A super simple starting place for a PHP-under-the-hood website
---

Page, the ultra-simplistic PHP MVC framework

Copyright (c) 2015-2017, H. Elwood Gilliland III All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

    Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

    Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

---

Page framework is super easy but has very little docs.  Just read index.php and dig in from there to see how logins work.  Signup page is not yet made, but login is available for the default admin account.

From this point you can do anything.  Page is specifically good for developing minimal PHP->PDO based web applications.  All you need to install page is to put it into a folder and double check its .htaccess and one path setting, and you're golden. Multiple Page deployments can share the same authentication database.  Why use some gargantuan framework after all?  Everything you need is right here to start building collaborative tools for teams, or public webservices like blogs, custom social media sites, whatever!

Please note that all you really need appears in the "core" folder, everything else is just implementation on top of that core.  All core provides is the Page object, PDO wrapper and some really fundamentally useful features, everything else is implemented in the main folder or one of the other sub-folders.  If you understand everything in /core/, everything else can be removed (except maybe the cache folder).  To get back to the minimum, simply cut "core" out with a cookie cutter.

Page was written for PHP5 but works just fine in PHP7 since it uses a minimal set of PHP language features.  The one thing you will have to watch out for is the use of the ampersand (&) since some shreds and demo pages may use it in the form of &$p but you can simply replace it with $p.  The project was started in 2008, but didn't have the name "Page" then.  It was revived, refocused and renamed in 2014, when it became a "seed" for a 3D printing startup's internal intranet systems.  I use it to make tools for LostAstronaut.com, and as a basic "agar" for other LAMP websites where I want a lot of custom back-end functionality.  It has also been used on WAMP.

__Uses:__

With this framework so far I've made several fully functioning and in-use corporate systems.  I made a secure corporate intranet, a database-based online shared content management system and editing tool for a design department, an integrated product metrics website complete with data processing, and several utility applications.  It's easy to deploy and then begin developing as soon as you know the basics.  Works most reliably only on LAMP stack.  WAMP implementations may work with some modifications, but no gaurantees.  Has been deployed to AWS instances and can be reduced to a minimal footprint if you remove the sample functionality that is packaged with it.

You can just stick Page in a folder and attach it to the database.  You can repeat that process if you want to create multiple sets of functionality.

__Provides the basics:__

 * Authentication using a single cookie and a database table.
 * Keeps the basic structure of PHP without sandboxing your development.
 * Contains lazy ajax calls for versatile low-impact AJAX-enabled widgetry
 * Super easy to make new models
 * A way to lock individual rows from being edited until they are unlocked.
 * A way to auto-lock rows to avoid two people editing the same information at the same time.
 * A way to instantly modify database information in a Google Sheets style way using the Bind* functionality of Page class
 * A bunch of jQuery object supports.
 * Lots of undocumented examples.
 * ACL support for limiting who can edit and what they can edit.
 * Granular edit logs when desired (see Modifications feature).

__Philosophy:__

 1. Simple and unencumbered.
 2. Models are good for handling data acquisition and processing tasks.
 3. Controllers and views are really the same thing most of the time.
 4. You need a simple server-side templating language sometimes.
 5. You want the freedom to add support for jQuery and Bootstrap (and angular) and you need some helper functions.
 6. You want a nice class-based PHP framework that uses PDO and password_hash.
 7. You know what you are doing with the whole toolchain.
 8. You want to break conventions whenever needed to avoid wrangling gangly interfaces or scouring crufty docs.
 9. You're planning on using FontAwesome and Foundation Icons.
 10. All of the best web properties do it custom, and so will you.

__Example of a typical "Page" controller-view php file:__

<pre>
include "core/Page.php";

$p=new Page;

$p->title="My wickid page";

if ( Session::is_logged_in() ) // Checks to see if user is logged in or not...
 $p->HTML("Hey I'm logged in!!!! YAY!");
 else $p->HTML("not logged in.. :(");

$p->Render(); // Puts page to screen.

</pre>

__Example of a typical "automated PHP from a script" php file:__

<pre>
include "core/automation.php";   // Does everything Page does except create the Page class.. no Auth either.

//... load models and do stuff to db ...
</pre>

__To set up full granular logging (stored in cache/logs/last-log.txt):__

<pre>
global $plog_level; $plog_level=1; // must appear before Page.php is included
include 'core/Page.php';
</pre>

__Example of how to make a new model:__

 1. Create a table in your database. For example, one called <b>tableName</b>
 2. Add a column that is PRIMARY KEY AUTO INCREMENT as an UNSIGNED INT NOT NULL named 'ID'
 3. Create a php file with the exact same name in the model folder: <b>model/tableName.php</b>
 4. Extend the model class with an empty boilerplate: class tableName extends Model {}
 5. Put to use.  Add custom data acquisition / processing features as public functions.

__How to attach to a database's table using a model:__

<pre>

 global $database;

 $my_model=new tableName($database);

</pre>

See files in core/PDO/ to learn how everything works.


__Installing and using JQuery__

Page supports any version of jQuery, and has some basic features that let you programmatically deploy jQuery.  Unlike other frameworks or code organizational methods, you'll want to break up your jQuery plugin into the css/ and js/ folders, placing the CSS and images in css/ and the Javascript files in js/ so that you can use $page->JS() $page->CSS() to load them.  Use $page->JQ() to stick lines in the document Ready() area and use $page->JS() to add to the page's global javascript.

<pre>
 include 'core/Page.php';
 
 $p=new Page();
 $p->JS('somejsfile.js');
 $p->JS('http://cdn.url.com');
 $p->JQuery(); // Loads Jquery automatically
 $p->JQ('
   $("#docisready").on("click" ... );
 ');
  $p->JS('var myGlobal=1; setInterval(function(){alert('foo');},1000);');
</pre>

Also, you'll want to be aware that if you load your own custom jQuery, either modify the loading sequence in core/Page.php to the version of your choice in the location of your choice, or load it via another method and inform Page that it is already installed so as not to install it twice, by doing:

<pre>
$page->jq_loaded=TRUE;
</pre>


__Built-in "Live Editing" Common Data Widgetry__

(Methods in Page core class, but part of the non-core functionality)

Page has been used to edit database data -- as a database front-end for a corporate intranet -- and lets you and others edit data on the fly.  You can even use built-in features like Auto-Locking and Row-Locking to protect data from edit sniping.  Row locking support is in shreds/AutoLocks.php

All of these widgets are tied to ajax.*.php files and have a couple of minor drawbacks.  They are written to work, but at the expense of network (it doesn't cache or delay outgoing messages).  Also, if you quickly leave the page after changing something, the request may not complete.  So, if you are going to leave the page wait at least 1-2 seconds depending on your current network latency.

Also, you need to use ACLs to secure your database users from editing things they aren't allowed.  You may use ACLs by either table- or field-level of granularity, in the form of edit-TableName or edit-TableName-FieldName.  ACL class is defined in shreds/ACL.php and these ACL "tags" are checked in some of the ajax.*.php files.  They must be stored on the user's profile.  The special ACLs "admin" and "su" let you bypass this security!  Be careful out there.

You must use jQuery and the support plug-ins they require, and you must activate these features using $page->Bind_LoadPlugins();

Once activated, you use the $page->Bind* options to modify your primary global $database.  You should read them in core/Page.php

<pre>
 $p=new Page();
 $p->Bind_LoadPlugins(); // Automatically loads jQuery and the required jQuery plugins.
 
 $p->BindString(...);
</pre>

Run-down of what's in each folder:
---

**view/**

Contains anything you want to expressly call a "view" -- not included automatically, invoke with:
include "view/myview.php"

**automation/**

Contains anything you don't mind having in the web folder (otherwise, use a folder called /offline) usually in the form of bash and/or PHP scripts that use the core/automation.php entry point.

**cache/**

Contains log files (from the plog function) and caching for any plugins you might be using, or anything else you want to cache.

Make sure this is writeable and all of its sub-folders...

**core/**

Contains the core of the Page Framework.  core/Page.php is the one you want to include all the time.  core/automation.php is for offline scripts that need to access your models.  core/utility.php is where I keep all of the useful helper functions.

**core/PDO/**

Contains the PDO-related functionality.  The one you need to extend is Model.  You should read them to get a feel for the options and the interface.

**css/**

Contains your main.css and other css files for plugins or special areas of your site.  You can include these files like this:

<pre>
 $p->CSS('main.css');  // Includes css/main.css
 $p->CSS('myplugin/plugin.css');  // Includes css/myplugin/plugin.css. 
</pre>

**engines/**

This folder contains anything that is more "engine-like", and the files are automatically included so they should, with the exception of .htaccess, end in .php and be valid code (no syntax errors).

**forms/**

Contains specialized files for the DataForm class, usually named like form_name.txt, used to directly map a classic web form to fields in a database for data entry and editing.

**examples/**

Contains some examples and notes. 

**global/**

This folder contains anything that is "global-like", and the files are automatically included so they should, with the exception of .htaccess, end in .php and be valid code (no syntax errors).  Generally you are just invoking the global directive and setting up defaults, though you could make it "smarter" any way you wish.

**html/**

Contains html snippets that can be loaded into a page's ->HTML by file reference, example $p->HTML('myfile.html') is automatically discovered in html/

**js/**

Put your javascript files here.  When you $p->JS('somefile.js') it will look here, and it also recognizes CDN urls.

**phtml/**

Anything you want evaluated and includes mixed-mode HTML should be in here.  Not really a recommended feature, but I added it if you want to do this sort of thing.

**schemas/**

Whatever database schemas you used to create your site can be stored here, but it's insecure.  Delete this folder if you wish.

**settings/**

Contains configuration files.  All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

**ui/**

Contains UI snippet files that make use of the UI base class in core/ui.php --  I ended up making mine in shreds/ instead, but you can use this if you want. All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

**model/**

Contains configuration files.  All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

**shreds/**

Contains auto-loaded modules, snippets, functions, whatever.  I use it to wrap jQuery plug-ins (or other Javascript pieces) in PHP for pre-processing when I don't want to handle it another way.  For instance I implemented Muuri.js related functionality this way, so that you can use it in an endpoint rather than in an html/ folder inclusion page fragment, or an included js file.

**modules/**

Files you want to include manually.  Similar to Vendor/ folder in Cake.  include 'module/whatever.php'

**i/**

Your images!  These are all referenced in the .html files for instance.  Or you can reference them with i/

**docs/**

Documentation you want to make available on your site or to other people.  If there is an .sql here, delete it after you use it.


To install:
---

 1. Place in a folder
 2. chmod 644 all necessary .htaccess files, use .htaccess_alternate if .htaccess doesn't work to create the rewrite conditions.
 3. chmod +x clear, plog and last scripts located in root folder
 4. Modify contents of core/path.php to reflect site root.
 5. Create a database using docs/Page_AuthDB.sql
 6. Modify (if needed) contents of core/Auth.php to add other database globals. For instance, if you want to start with your own application-specific database, you don't need to modify the contents of your Page_AuthDB, instead you can just start adding tables for your application to another database.  In core/Auth.php, add some lines after the $auth_database to connect your application database and assign the global of $database to the DB object.
 7. Modify files found in settings/config*.php
 8. Read the source code to understand how everything works, and see the example below.

You will need to <i>a2enmod expires include rewrite mcrypt</i> and possibly some others.

For convenience, I've included a guidelet here to walk you through Ubuntu/PHP/Apache2 setup.  This works well on AWS EC2 instances.  After, it may require a machine reboot.

If you are installing php7.1 with FPM, on Ubuntu 16.04 LTS, you would have done this before any of the above:
```
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get upgrade -y
sudo apt-get install -y unzip apache2 php7.1 php7.1-cli php7.1-common libapache2-mod-php7.1 php7.1-mysql php7.1-fpm php7.1-curl php7.1-gd php7.1-bz2 php7.1-mcrypt php7.1-json php7.1-tidy php7.1-imagick php7.1-mbstring php-redis php-memcached
sudo a2enmod expires include rewrite mcrypt
sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php7.1-fpm
sudo service apache2 restart
```


Default admin username: admin
Password for admin: A single space ' '
(Change once you log in)


Setup Notes
---

Fatal error: Call to a member function Select() on a non-object in /var/www/core/PDO/model.php on line 104

This means your database isn't set up properly.  Either it doesn't exist, or it is simply not available.


How to reduce to core functionality
---

To start a project completely from scratch using just the core functionality
* Remove all files from main folder except _htaccess
* Remove all files from css and js folder except jQuery and those required for Bind* functions in Page (if desired)
* Remove all files in i/ view/ example/ shreds/ ui/ docs/ html/  .. pretty much anything except core/ settings/ global/
* Remove cache/ folder or redirect to a different folder by modifying core/utility.php and changing plog() function target file
* Clear out all model files except model/Auth model/Session, otherwise modify the way core/Auth.php works and remove those models too.

How to increase security
---

___Performance and logging___

You should never allow $plog_level=1 settings on a production server.

You should not keep your schemas in your public web folder, this includes the pre-packaged Page_AuthDB.sql, for obvious reasons.

___Ownership over data___

Note that you must vet all database-related requests for ownership and viewability.  Eventually, once I have time to implement the core module PORM, Page will handle some of this for you regarding ownership over data, but it's up to you to perform the necessary hardening and validation of data to be retrieved and stored from the database.  Knowing this, many of the ajax.?.php may not check for ownership over data because Page was originally written for a transparent, internally-used tool.  You should implement your own database ownership system (group and individual, public and private permissions).

___Hiding private source code___

Page relies on apache2's .htaccess file feature (or general configuration specificity inside vhost or httpd or ports or whichever .conf you are using) to set special permissions and parameters of each web folder and its subfolders.  This is done to allow Page to be inserted into other projects, or for it to be placed in multiple places on the same webserver.  Out-of-the-box, Page can be placed in a folder and almost all of the files will be hidden except in the main folder.  Even new subfolders you make will not be publicly accessible unless you create an .htaccess file that permits it.

However, some people seem to think that this is a bad idea.  As long as you are careful what kind of code you add to Page, you should be able to make a secure website.  In the past, this was done for PHP by testing at the top of each included file whether or not the resource was being loaded by a remote browser, or being included in a file (CodeIgniter, Zend, for example).  Page doesn't do this, because the entire folder is inaccessible, and also if they did manage to run one of the "class" files, nothing would happen of any significance.  Regardless, some people seem to believe that hackers can somehow fool apache2 into ignoring .htaccess files.  Page, and its upload capabilities, do not permit this.  Also, Page doesn't require eval() to be used for anything, because it doesn't use the same methods to implement MVC as other frameworks (CodeIgniter, Zend, for example).

If you believe the rationale that putting code into an exposed but non-publicly accessible folder is a bad idea, then for you Page can be made more secure by moving it to an offline folder, and exposing only php public endpoints (files you want people to browse to) in your web server folder.  I personally don't see the benefit, except that it makes it impossible for you to mess up and not have the .htaccess files in place that you need.  One side-effect of doing this is that if you have multiple sites built on Page, and want to maintain a single core, you can use this same method to do so.

To keep Page out-of-scope and in an offline web folder:
 1. Move your Page site to an offline folder
 2. Move your public endpoints to a folder on the web, including the i/ css/ js/ folders
 3. Create a folder in your web folder called 'core' 
 4. Create a file in it called Page.php and add one line:
    include_once '/path/to/page/folder/core/Page.php';

You may have to adjust the first few lines of your actual core/Page.php to reflect this path difference, where you see include_all and include_once related to the core, but it should be fine since at this point it is relative to the included core/Page.php file.

If you've manually included any modules, you'll have to add /path/to/page/folder/ or create a define in your core/Page.php short-form file (step 4) that defines this path:  define('pagepath','/path/to/page/folder/');

Upcoming features
---

One day I will implement PORM, which will facilitate database seeding and schema migration features to make life easier.  You will be able to create an entire description of a database in a specialized text file (it will look like class declarations in C++ / Java), and convert that to a JSON tree (which you could also read from a file) and then feed that to a PORM class constructor, and attempt to deploy the database, or migrate it using PORM->Deploy() (where migration will add missing table columns, and modify existing columns detecting the old type first)

_Thoughts_

As I use Page more and more I find it addicting, but I've begun to admit to myself that it probably signals the end of my use of PHP as a structural feature of a website.  PHP is becoming a means to an end only for me, but I still enjoy using it.  I think I'm becoming a NodeJS developer.  I liked my experience with Expressjs, but these old hands still type PHP.  I use PHP for internal tools at LostAstronaut.com quite effectively because it is relatively similar to C++.  I'm also working on some other websites that will use it.  There is still nothing that feels more powerful than being able to render custom Javascript using PHP, even if it is a syntax nightmare.
