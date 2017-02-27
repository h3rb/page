# Page framework v1.12
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

Page was written for PHP5 but works just fine in PHP7 since it uses a minimal set of PHP language features.  The one thing you will have to watch out for is the use of the ampersand (&) since some shreds and demo pages may use it in the form of &$p but you can simply replace it with $p.

Uses:

With this framework so far I've made several fully functioning and in-use corporate systems.  I made a secure corporate intranet, a database-based online shared content management system and editing tool for a design department, an integrated product metrics website complete with data processing, and several utility applications.  It's easy to deploy and then begin developing as soon as you know the basics.  Works only on LAMP stack.

To install:

 1. Place in a folder
 2. chmod 644 all necessary .htaccess files, use .htaccess_alternate if .htaccess doesn't work to create the rewrite conditions.
 3. chmod +x clear, plog and last scripts located in root folder
 4. Modify contents of core/path.php to reflect site root.
 5. Create a database using docs/Page_AuthDB.sql
 6. Modify (if needed) contents of core/Auth.php to add other database globals. For instance, if you want to start with your own application-specific database, you don't need to modify the contents of your Page_AuthDB, instead you can just start adding tables for your application to another database.  In core/Auth.php, add some lines after the $auth_database to connect your application database and assign the global of $database to the DB object.
 7. Modify files found in settings/config*.php
 8. Read the source code to understand how everything works, and see the example below.

You will need to <i>a2enmod expires include rewrite mcrypt php5</i> and possibly some others.

Default admin username: admin
Password for admin: A single space ' '
(Change once you log in)

Provides the basics:

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

Philosophy:

 1. Models are good for handling data acquisition and processing tasks.
 2. Controllers and views are really the same thing most of the time.
 3. You need a templating language sometimes.
 4. You want the freedom to add support for jQuery and Bootstrap (and angular) and you need some helper functions.
 5. You want a nice class-based PHP framework.
 6. You know what you are doing with the whole toolchain.
 7. You want to break conventions whenever needed to avoid wrangling gangly interfaces or scouring crufty docs.
 8. You're planning on using FontAwesome and Foundation Icons.

Example of a typical "Page" controller-view php file:

<pre>
include "core/Page.php";

$p=new Page;

$p->title="My wickid page";

if ( Session::is_logged_in() ) // Checks to see if user is logged in or not...
 $p->HTML("Hey I'm logged in!!!! YAY!");
 else $p->HTML("not logged in.. :(");

$p->Render(); // Puts page to screen.

</pre>

Example of a typical "automated PHP from a script" php file:

<pre>
include "core/automation.php";   // Does everything Page does except create the Page class.. no Auth either.

//... load models and do stuff to db ...
</pre>

To set up full granular logging (stored in cache/logs/last-log.txt):

<pre>
global $plog_level; $plog_level=1; // must appear before Page.php is included
include 'core/Page.php';
</pre>

Example of how to make a new model:

 1. Create a table in your database. For example, one called <b>tableName</b>
 2. Add a column that is PRIMARY KEY AUTO INCREMENT as an UNSIGNED INT NOT NULL named 'ID'
 3. Create a php file with the exact same name in the model folder: <b>model/tableName.php</b>
 4. Extend the model class with an empty boilerplate: class tableName extends Model {}
 5. Put to use.  Add custom data acquisition / processing features as public functions.

How to attach to a database's table using a model:

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

__Run-down of what's in each folder:__

view/

Contains anything you want to expressly call a "view" -- not included automatically, invoke with:
include "view/myview.php"

automation/

Contains anything you don't mind having in the web folder (otherwise, use a folder called /offline) usually in the form of bash and/or PHP scripts that use the core/automation.php entry point.

cache/

Contains log files (from the plog function) and caching for any plugins you might be using, or anything else you want to cache.

Make sure this is writeable and all of its sub-folders...

core/

Contains the core of the Page Framework.  core/Page.php is the one you want to include all the time.  core/automation.php is for offline scripts that need to access your models.  core/utility.php is where I keep all of the useful helper functions.

core/PDO/

Contains the PDO-related functionality.  The one you need to extend is Model.  You should read them to get a feel for the options and the interface.

css/

Contains your main.css and other css files for plugins or special areas of your site.  You can include these files like this:

<pre>
 $p->CSS('main.css');  // Includes css/main.css
 $p->CSS('myplugin/plugin.css');  // Includes css/myplugin/plugin.css. 
</pre>

engines/

This folder contains anything that is more "engine-like", and the files are automatically included so they should, with the exception of .htaccess, end in .php and be valid code (no syntax errors).

forms/

Contains specialized files for the DataForm class, usually named like form_name.txt, used to directly map a classic web form to fields in a database for data entry and editing.

examples/

Contains some examples and notes. 

global/

This folder contains anything that is "global-like", and the files are automatically included so they should, with the exception of .htaccess, end in .php and be valid code (no syntax errors).  Generally you are just invoking the global directive and setting up defaults, though you could make it "smarter" any way you wish.

html/

Contains html snippets that can be loaded into a page's ->HTML by file reference, example $p->HTML('myfile.html') is automatically discovered in html/

js/

Put your javascript files here.  When you $p->JS('somefile.js') it will look here, and it also recognizes CDN urls.

phtml/

Anything you want evaluated and includes mixed-mode HTML should be in here.  Not really a recommended feature, but I added it if you want to do this sort of thing.

schemas/

Whatever database schemas you used to create your site can be stored here, but it's insecure.  Delete this folder if you wish.

settings/

Contains configuration files.  All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

ui/

Contains UI snippet files that make use of the UI base class in core/ui.php --  I ended up making mine in shreds/ instead, but you can use this if you want. All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

model/

Contains configuration files.  All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

shreds/

Contains configuration files.  All files in this folder are loaded automatically, and should end in .php and contain valid PHP code.

modules/

Files you want to include manually.  Similar to Vendor/ folder in Cake.  include 'module/whatever.php'

i/

Your images!  These are all referenced in the .html files for instance.  Or you can reference them with i/

docs/

Documentation you want to make available on your site or to other people.
