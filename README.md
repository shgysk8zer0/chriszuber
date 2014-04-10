blog
====
# A work in progress for a simple blogging site.
Also, much of this is meant to explore the capabilities of HTML5 and CSS3.

Requires:
* PHP 5.4+
* MySQL with PDO extension
* OpenSSL
* Apache with working setup (.htaccess will be modified later)

I have made great efforts to implement the latest technologies, particularly when
it comes to security. Also, as much as is practical, I have created .ini files for
configuration.

This project is by no means ready for public use, but use by developers is welcome.
Certain resources are not available and must be created by anyone intending on making use
of the code. For example, there is no connect.ini in this repo, since it only contains credentials
for my database. Also, as of now, it assumes connecting to an existing database, and will simply fail
if one does not exist. Future changes will create the database and any other resources if needed.
