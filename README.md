moodle-assignsubmission_mahara
============================

Mahara assignment submission plugin for Moodle 2.6
- https://github.com/MaharaProject/moodle-assignsubmission_mahara

This plugin adds Mahara pages submission functionality to assignments in Moodle.
The plugin works with the new "mod/assign" type introduced in 2.3. It requires 
at least one Mahara site linked to Moodle via MNet.

This plugin allows a teacher to add a "Mahara" item to the submission options for 
a Moodle assignment. Students can then select one of the pages or collections from
their Mahara portfolio as part of their assignment submission.

The submitted Mahara page or collection will be locked from editing in Mahara, the
same as if it had been submitted to a Mahara group. However, unlike group submissions,
pages and collections submitted to Moodle remain permanently locked even after grading.
If you'd like the submitted pages and collections to be unlocked after grading, install 
the Mahara assignment feedback plugin for Moodle:
https://github.com/MaharaProject/moodle-assignfeedback_mahara

The plugin also allows migrating old Mahara "mod/assignment" assignments to the new
type. The plugin does not include featues to communicate with the outcomes artefact
plugin.

This release is meant to merge the two forks of the Mahara assignment submission plugin for Moodle 2.3+:
 - The version developed by the University of Portland: https://github.com/fellowapeman/moodle-assign_mahara
 - The version developed by Lancaster University: https://github.com/lucisgit/assignsubmission_mahara

Installation
------------
1. Make sure that your Moodle and Mahara versions are up to date.
2. If you are using Mahara 1.9 or earlier, apply the patch "mahara-patch.txt" to your Mahara site.
3. If you are using Moodle 2.6 or earlier, apply the patch "moodle-patch.txt" to your Moodle site.
4. Copy the contents of this project to mod/assign/submission/mahara in your Moodle site.
5. Proceed with plugin installation in Moodle.
6. On the Moodle page "Site Admin" -> "Networking" -> "Peers", choose the Mahara site.
      Open the "Services" tab and enable "Assign Submission Mahara" services.
7. Now you may create your first Mahara assignment.


Upgrading
---------

This plugin is designed to allow you to upgrade from either the University of Portland
version, or the Lancaster University version. It will automatically detect which version
of the plugin you have installed, and migrate it accordingly. So all you need to do is:

1. Remove the current contents of your mod/assign/submission/mahara directory
2. Follow the steps under "Installation" above. (This will trigger the database upgrade script.)
3. If you have also installed the Mahara assignment feedback plugin (mod/assign/feedback/mahara), you should now upgrade it to the version at https://github.com/MaharaProject/moodle-assignfeedback_mahara/tree/moodle26-merged
4. If you have also installed the Mahara local plugin (local/mahara), you should now uninstall it.

About those patches
-------------------

As you may have noticed in the installation instructions, this plugin requires you to apply a patch to your Mahara site and possibly another patch to your Moodle site. The Moodle patch provides an additional hook for the assignment submission plugin to respond when an assignment is reopened. The Mahara patch provides support for the Mahara web services to handle collections.

The Moodle patch has been upstreamed into Moodle 2.7, so if you are using that version or later, you do not need to manually apply the patch file. The Mahara patch is still in the process of code review as of May 2014 (see https://reviews.mahara.org/#/c/3239/ ), targetted for Mahara 1.10.

For information about how to apply a patch file, try Google. If you are using Linux, the process will look something like this:

```Shell
cd /var/www/path/to/mahara
patch -p0 < /path/to/mahara-patch.txt
cd /var/www/path/to/moodle
patch -p0 < /path to/moodle-patch.txt
```

A little info about what it does:
---------------------------------

This plugin adds a "Mahara" submission method to "assignment" activities in Moodle.
When a teacher creates an "assignment" activity, they'll see "Mahara" as one of the
submission method options. This submission method will ask students to select one
of their pages or collections from Mahara, to include as part of their assignment
submission. (Therefore, this plugin requires your Moodle site to be connected to a
Mahara site via MNet.)

* Individual pages that are part of collections cannot be picked on their own (the entire collection must be picked instead)
* Pages or collections that are already locked due to being submitted to a Mahara group or another Moodle assignment, are also not available

Optionally, the assignment may lock the submitted pages and collections from being edited
in Mahara. This is recommended, because otherwise students will be able to continue
editing part of their assignment submission even after the assignment deadline. If you
would like the submitted pages and collections to become unlocked after grading, you
may use the related assignment feedback plugin for that purpose.

If you choose to use the page locking feature (which, again, is the default behavior and
is recommended) note that:
* Pages & collections that are part of a draft submission will be not be locked until the draft is submitted.
* The Mahara page will be locked if the submission is submitted OR the submission is "locked" via the Moodle gradebook.
* If a submission is "reopened" via the Moodle gradebook, the page will become unlocked.

If you need help, try the Moodle-Mahara Integration forum on mahara.org: https://mahara.org/interaction/forum/view.php?id=30

Bugs and Improvements?
----------------------

If you've found a bug or if you've made an improvement to this plugin and want to share your code, please
open an issue in our Github project:
* https://github.com/MaharaProject/moodle-assignsubmission_mahara/issues

Credits
-------

The original Moodle 1.9 version of the assignment submission pluginwas funded through a grant from the New Hampshire Department of Education to a collaborative group of the following New Hampshire school districts:

 - Exeter Region Cooperative
 - Windham
 - Oyster River
 - Farmington
 - Newmarket
 - Timberlane School District

The upgrade to Moodle 2.0 and 2.1 was written by Aaron Wells at Catalyst IT, and supported by:

 - NetSpot
 - Pukunui Technology

The assignment feedback plugin was developed by:

 - University of Portland by Philip Cali and Tony Box (box@up.edu)

Subsequent updates to the plugin were implemented by Aaron Wells at Catalyst IT, with funding from:

 - University of Brighton
 - Canberra University

License
-------

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, version 3 or later of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
