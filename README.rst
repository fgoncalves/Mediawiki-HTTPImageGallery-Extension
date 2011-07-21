Description
===========

This was my first extention to mediawiki. It was done for version 1.12, but we ended up using it in 1.15. By the time I got it working mediawiki didn't support
external images in galleries. Since we were planning on having multiple galleries we found the need to build an extension that could render images from other sites.

This version is very old and it's done in a stupid way. As soon as I got the time I'll change the way it parses tags.

Usage
=====

This extension adds two tags to mediawiki parser: ``httpimagegallery`` and ``httpimage``. For the first one, you can specify 3 attributes

+-------------+----------+-----------------------------------------------------------------+
| Name        | Required |             Description                                         |
+=============+==========+=================================================================+
| cellspacing |   no     | Same as normal HTML                                             |
+-------------+----------+-----------------------------------------------------------------+
| cellpadding |   no     | Same as normal HTML                                             |
+-------------+----------+-----------------------------------------------------------------+
| ncolumns    |   no     | This attribute specifies the number of columns for the gallery  |
+-------------+----------+-----------------------------------------------------------------+

(More to come...)