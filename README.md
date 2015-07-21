# ABOUT

Commenting provides a beefier implementation of comments than the earlier Comment plugin. It allows for comments
on any record type (in theory, at least), and includes both ReCaptchas and Akismet spam detection.

Commenting also allows for moderation and commenting permissions based on role. Logged-in users are not
required to submit recaptchas.

# REQUIREMENTS

The commenting plugin makes use of both ReCaptchas and the Akismet spam-detection service. You will want to get
API keys to both of these services and add them to the plugin's configuration.

# CONFIGURATION

* Threaded Comments: Check to display threaded comments
* ReCaptcha Keys: These keys duplicate the fields found in the Omeka Security settings. ReCaptcha challenges only appear when there
is no user logged in.
* Permissions
  * Options are available to specify what roles can approve, add, and view comments, and add comments without approval
  * If the GuestUser plugin is installaed and activated, the guest role is available in these options
  * Allowing Public commenting will override the permissions set for adding and viewing comments -- they will be open to all
* Akismet API key: You should get an API key to the Akismet spam management service if you use public commenting


See USE CASES below for examples of configuration combinations

# DISPLAYING COMMENTS

Commenting will automatically add commenting options to Item and Collection show pages. To enable commenting on other
record types from modules (e.g. SimplePages or ExhibitBuilder), you will have to add the following lines
to the appropriate place in the plugin's public view script:

```php
<?php CommentingPlugin::showComments(); ?>
```
Keep in mind that updating themes or plugins will clobber your addition of the commenting functions.

The Commenting plugin knows how to work with SimplePages and ExhibitBuilder. Due to variability
in how plugins store their data in the database, other record types and views supplied by other plugins 
might or might not work out of the box. Please ask on the forums or dev list if you would like Commenting
to work with other plugins. 


# USE CASES

## Limited, moderated commenting

An institution wants only trusted people to leave comments for anyone to read. It doesn't trust some of them enough to allow 
comments to be automatically public.

The semi-trusted people could have the Researcher role in Omeka, with commenting configured to allow Researchers to comment.
The Admin role is the only role that can moderate comments to approve them, and Public is allowed to view comments.

## Open commenting, with registered users getting to submit comments without approval

Install and configure the GuestUser plugin. Set commenting to Public so that anyone can comment, and give the Guest User role permission to submit comments without approval.
