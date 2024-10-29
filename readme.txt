=== Active Extra Fields ===
Contributors: Lod Lawson
Donate link: http://fab-freelance.com/
Tags: custom field,validation,taxonomy,post tag
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.0.1

This plugin allows validation of custom fields.

== Description ==
This plugin is used to validate custom fields and post taxonomies .Custom field  can be organised under metaboxes.
Meta boxes settings include location(normal,side),priority(high,low) and the post type to display them for.With it you can create custom
fields using the following type of input:
<ul>
<li>Single line input text</li>
<li>Multi line text</li>
<li>WYSIWYG editor</li>
<li>Drop down list</li>
<li>Checkbox</li>
<li>Checkbox list</li>
<li>Date</li>
</ul>

With the input also comes validations
<ul>
<li>Required field validation (text,textarea,WYSIWYG)</li>
<li>String length (max and min)</li>
<li>Maximum selection (checkboxes,hierarchical taxonomies)</li>
<li>Minimum selection (checkboxes,hierarchical taxonomies)</li>
<li>Maximum entry (non hierarchical taxonomies)</li>
<li>Minimum entry (non hierarchical taxonomies)</li>
<li>No selection ,usefull for drop down list when the is a value that must not be selected</li>
</ul>


== Installation ==


1. Upload `active-extra-fields` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can access the plugin admin under the site setting menu


== Screenshots ==

1. An example of meta box
2. Upon form submission an alert display the errors list.This screenshot is a field that is highlighted .
3. Admin settings menu
4. Admin ,Meta boxes page
5. Admin ,Meta box fields
6. Admin ,Field page
7. Admin ,Taxonomy validations page

== Frequently Asked Questions ==

Frequently saked questions


== Upgrade Notice ==

= 1.0.1 =
This version has made few changes to tables in the database.The column name 'order'
should be changed to 'display_order' in the table named 'wp_axf_fields' where 'wp_'
is the table prefix set in the config file.


== Changelog ==

= 1.0.1 =
<ul>
    <li>Minor bug fixes</li>
    <li>Support for Date Input Type</li>
    <li>Added action hooks to allow use by other plugins
          <ul>
            <li>Meta box deletion</li>
            <li>Fields deletion</li>
            <li>Metaboxes list</li>
         </ul>
    </li>

</ul>


== Notes ==

There are few limitations that would be addressed in future versions

<ul>
    <li>Duplicate fields</li>
    <li>Radio buttons ,but a drop down list could do the same job</li>
    <li> No support for multiple drop down list ,but using  check boxes is a good alternative</li>
    <li>For each field there are options that could be used to populate checkboxes/drop down list;
    Updating an option in the option lists would cause lost of save data for a post.In other words when you
    save an option while editing a custom field ,if this option value is changed later on then all  post fields that have this value
    selected for their field would loose this value.So choose you list values carefully.</li>
</ul>