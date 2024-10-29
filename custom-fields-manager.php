<?php
/*  Copyright 2010  Active Custom Fields  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 Plugin Name: Active Extra Fields
Plugin URI: http://fab-freelance.com/blog/itemlist/category/4-active-extra-fields.html
Description: A plugin to manage custom fields
Version: 1.0.1
Author: Lod Lawson
Author URI: http://fab-freelance.com
*/

define('CFM_DIR', dirname(__FILE__));
define('AXF_FOLDER','active-extra-fields');
include_once CFM_DIR.'/a-meta-boxes-lib.php';
include_once CFM_DIR.'/custom-fields-manager.class.php';
$custom_fields_manager=new custom_fields_manager();
$meta_box_manager=new ameta_box_manager();
$meta_box_manager->init();
register_activation_hook(__FILE__, 'axfield_activate');

function axfield_activate() {
    global $wpdb,$table_prefix;
    $sql="CREATE TABLE IF NOT EXISTS `".$table_prefix.ameta_box::$table."` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `post_types` varchar(60) NOT NULL,
  `position` varchar(10) NOT NULL,
  `priority` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
    $wpdb->query($sql);
    $sql="CREATE TABLE IF NOT EXISTS `".$table_prefix.axfield::$table."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `label` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `box` tinyint(4) NOT NULL,
  `display_order` tinyint(4) NOT NULL,
  `options` text NOT NULL,
  `input_type` varchar(12) NOT NULL,
  `validations` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
   $wpdb->query($sql);
}



?>
