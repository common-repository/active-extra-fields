<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
$mb_msg=null;
class custom_fields_manager {
    public static  $text_domain='axfields';
    public $option_name='meta_boxes';
    public function __construct() {
        $this->init();
    }
    private function init() {
        add_action('admin_menu',array($this,'admin_menu'));
        load_plugin_textdomain(self::$text_domain,"/wp-content/plugins/".AXF_FOLDER."/languages/");
    }

    public function admin_menu() {
        add_options_page( 'Meta Boxes', 'Meta Boxes', 8, 'meta-boxes',array($this,'options_page'));

    }
    public function options_page() {
        global $mb_msg;

        //form processing
        if(isset($_POST['post_action'])) {
            switch($_POST['post_action']) {
                case 'save_box':
                    $mb_msg=__('Box successfully saved','axfields');
                    $id= ameta_box::save_metabox($_POST['title'],$_POST['post_types'], $_POST['position'], $_POST['priority'],$_POST['box_id']);
                    wp_redirect('options-general.php?page=meta-boxes&tab=box&box='.$id.'&msg='.urlencode($mb_msg));
                    break;
                case 'save_field':
                //create field
                    $validations=array();
                    if(isset($_POST['validations'])) {

                        foreach($_POST['validations'] as $val) {
                            switch($val) {
                                case 'noselect':
                                case 'maxlen':
                                case 'minlen':
                                case 'maxselect':
                                case 'minselect':
                                    $validations[]=array($val,$_POST[$val.'_msg'],$_POST[$val.'_opt']);
                                    break;
                                default://required,email,numeric
                                    $validations[]=array($val,$_POST[$val.'_msg']);
                                    break;
                            }//switch
                        }
                    }
                    $id=axfield::save_field($_POST['box'], $_POST['key'], $_POST['label'], $_POST['type'], $_POST['order'], $_POST['options'], $validations, $_POST['slug'], $_POST['field']);
                    $mb_msg=__('Field succesfully saved!','axfields');
                    wp_redirect('options-general.php?page=meta-boxes&tab=field&box='.$_POST['box'].'&field='.$id.'&msg='.urlencode($mb_msg));
                    break;

                case 'tax_val':
                    $mb_msg=__('Saved!','axfields');
                    ameta_box_manager::save_taxonomies_validation($_POST);
                    wp_redirect('options-general.php?page=meta-boxes&tab=tax_val&msg='.urlencode($mb_msg));
                    break;
            }//switch
        }

        else if(isset($_GET['action']) ) {

            switch($_GET['action']) {
                case 'del':
                    $res=ameta_box::delete_box($_GET['box']);

                    if(is_array($res))
                        wp_redirect($res['redirect_url']);
                    else {
                        $mb_msg='Box deleted!';
                        wp_redirect('options-general.php?page=meta-boxes&tab=boxes&msg='.urlencode($mb_msg));
                    }
                    break;
                case 'delf':
                    $res=  axfield::delete_field((int)$_GET['field']);

                    if(is_array($res))
                        wp_redirect($res['redirect_url']);
                    else {
                        $mb_msg='Field successfully deleted!';
                        wp_redirect('options-general.php?page=meta-boxes&tab=box&box='.$_GET['box'].'&msg='.urlencode($mb_msg));
                    }
                    break;

            }

        }
        //$meta_boxes=ameta_box_manager::get_metaboxes($this->option_name);
        echo '<div class="wrap">';
        ?>
<h2>Custom Fields Management</h2>
        <?php
        if(isset($_GET['msg'])) {
            echo '<div id="message" class="updated bellow-h2">';
            echo'<p>', $_GET['msg'],'</p>';
            echo '</div>';
        }
        ?>
<div>
    <ul class="subsubsub">
        <li><a href="options-general.php?page=meta-boxes&tab=boxes"><?php _e('Meta Boxes','axfields')?></a>|</li>
        <li><a href="options-general.php?page=meta-boxes&tab=box"><?php _e('New Box','axfields')?></a>|</li>
        <li><a href="options-general.php?page=meta-boxes&tab=tax_val"><?php _e('Taxonomy Validation','axfields')?></a></li>
    </ul></div>
        <?php
        if(!empty ($mb_msg)) {
            echo '<div class="message"'.$mb_msg.'</div>';
            $mb_msg=null;
        }
        switch($_GET['tab']) {
            case 'boxes':
                $this->show_boxes_page($mb_msg);
                break;
            case 'box':
                $this->show_box_page($mb_msg);
                break;
            case 'field':
                add_action('admin_footer', 'load_fieldscript');
                $this->show_field_page($mb_msg);
                break;
            case 'tax_val':
            // add_action('admin_footer', 'load_fieldscript');
                $this->show_taxonomy_validation_page($mb_msg);
                break;
            default:
                $this->show_boxes_page($mb_msg);
                break;
        }
        echo '</div>';

    }

    public function show_box_page($msg='') {
        $title=__('New box','axfields');
        $post_types=get_post_types();
        //var_dump($post_types);
        $exclude=array('revision','attachment','nav_menu_item');
        foreach($post_types as $key=>$val) {
            if(in_array($val,$exclude)) {
                unset($post_types[$key]);
            }
        }
        if(isset($_GET['box'])) {
            $box=ameta_box::get_metabox((int)$_GET['box']);
            $title=__('Edit box','axfields');
        }
        else {
            $box=new stdClass();
        }


        if(!is_array($box->post_types))$box->post_types=explode(',', $box->post_types);

        ?>
<br/>
<h3><?php echo $title;?></h3>
<form method="post" action="options-general.php?page=meta-boxes&action=edit&tab=box&noheader=true">

    <table class="form-table">
        <tr>
            <th scope="row" valign="top"><?php _e('Box title','axfields');?></th>
            <td>
                <input type="text" name="title" value="<?php echo $box->title; ?>">

            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><?php _e('Position','axfields');?></th>
            <td>
                <select name="position">
                    <option value="side" <?php if ($box->position == 'side') echo 'selected="selected"'; ?>><?php _e('Side', 'axfields'); ?></option>
                    <option value="normal" <?php if ($box->position == 'normal') echo 'selected="selected"'; ?>><?php _e('Normal', 'axfields'); ?></option>
                    <option value="advanced" <?php if ($box->position == 'advanced') echo 'selected="selected"'; ?>><?php _e('Advanced', 'axfields'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><?php _e('Priority','axfields');  ?></th>
            <td>
                <select name="priority">
                    <option value="default" <?php if ($box->priotity == 'default') echo 'selected="selected"'; ?>><?php _e('Default', 'axfields'); ?></option>
                    <option value="high" <?php if ($box->priotity == 'high') echo 'selected="selected"'; ?>><?php _e('High', 'axfields'); ?></option>
                    <option value="low" <?php if ($box->priotity == 'low') echo 'selected="selected"'; ?>><?php _e('Low', 'axfields'); ?></option>
                </select>
            </td>
        </tr>

        <tr>
            <th scope="row" valign="top"><?php _e('Post types','axfields');  ?></th>
            <td>

                        <?php
                        foreach($post_types as $pt) {
                            echo '<label><input type="checkbox" name="post_types[]" value="'.$pt;
                            if(in_array($pt, $box->post_types)) {
                                echo ' checked="checked"';
                            }
                            echo '" />'.$pt.' </label><br/>';
                        }
                        ?>
            </td>
        </tr>
    </table>

    <br />
    <input type="hidden" name="post_action" value="save_box">
    <input type="hidden" name="box_id" value="<?php echo $box->id; ?>">

    <p><input class="button-primary" type="submit" value="<?php _e('Save','axfields'); ?>"></p>
</form>

        <?php

        if(count($box->fields)>0) {
            ?>
<table class="widefat">
    <thead>
        <tr>
            <th><?php _e('Key' ,'axfields'); ?></th>
            <th><?php _e('Title' ,'axfields'); ?></th>
            <th><?php _e('Type' ,'axfields'); ?></th>
            <th><?php _e('Values' ,'axfields'); ?></th>
            <th><?php _e('Actions' ,'axfields'); ?></th>
        </tr>
    </thead>
    <tbody>
                    <?php
                    $fieldtypes=axfield::field_types();
                    foreach($box->fields as $field) {

                        echo '<tr><td>'.$field->key.'</td><td>'.$field->label.'</td><td>'.$fieldtypes[$field->input_type].'</td><td>'.$field->options.'</td><td><a href="options-general.php?page=meta-boxes&tab=field&box='.$box->id.'&field='.$field->id.'">Edit</a>|<a href="options-general.php?page=meta-boxes&tab=box&noheader=true&box='.$box->id.'&action=delf&field='.$field->id.'">Delete</a></td></tr>'  ;
                    }?>
    </tbody>
</table>
            <?php
        }//endif
        if($box->title) {
            ?>
<form method="GET" ACTION="options-general.php">
    <input type="hidden" name="page" value="meta-boxes">
    <input type="hidden" name="tab" value="field">
    <input type="hidden" name="box" value="<?php echo $box->id; ?>">
    <p><input class="button" type="submit" value="<?php _e('Add new field' ,'axfields'); ?>!"></p>
</form>
            <?php

        }//end if
    }

    public function show_boxes_page(&$msg='') {
        $meta_boxes=ameta_box::get_metaboxes();

        ?>
<br/>
<h3>Meta Boxes</h3>

        <?php
        if(!empty($msg)) {
            echo '<div id="message">';
            echo '<p>'.urldecode($msg).'</p>';
            echo '</div>';
            $msg=null;
        }

        if(is_array($meta_boxes) && count($meta_boxes)>0) {
            ?>
<table class="widefat">
    <thead>
        <tr>
            <th><?php _e('Box Name', self::$text_domain)?></th> <th><?php _e('Fields count', self::$text_domain)?></th><th><?php _e('Position', self::$text_domain)?></th><th><?php _e('Actions', self::$text_domain)?></th>
        </tr>
    </thead>
    <tbody>
                    <?php

                    foreach($meta_boxes as $mb) {
                        echo '<tr><td>'.$mb->title.'</td><td>'.$mb->fields_count.'</td><td>'.$mb->position.'</td><td><a href="options-general.php?page=meta-boxes&tab=box&box='.$mb->id.'">Edit</a>|<a href="options-general.php?page=meta-boxes&noheader=true&tab=boxes&box='.$mb->id.'&action=del">Delete</a></td></tr>'  ;
                    }?>
    </tbody>
</table>
            <?php

        }

    }

    public function show_field_page($msg='') {
        $field=null;
        $title=__('New field','axfields');
        if(isset($_GET['field'])) {

            $field=axfield::get_field((int)$_GET['field']);
            $title=__('Edit field','axfields');
        }
        else {
            $field=new stdClass();
        }


        ?>
<br/>
<h3><?php echo $title;?></h3>
<form method="post" action="options-general.php?page=meta-boxes&action=edit_box&noheader=true&box=<?php echo ($_GET['box']); ?>">
                <!--<h4><a href="options-general.php?page=more-fields.php"><?php _e('Boxes', 'more-fields'); ?></a> &gt; <a href="options-general.php?page=more-fields&action=edit_box&box=<?php echo urlencode($_GET['box']); ?>"><?php echo $_GET['box']; ?></a> &gt; <?php echo $nav; ?></h4>-->
    <table class="form-table">
        <tr>
            <th scope="row" valign="top"><?php _e('Box', 'axfields'); ?></th>
            <td>
                        <?php  $boxes=ameta_box::get_metaboxes();

                        if(is_array($boxes) && count($boxes)>0) {
                            echo '<select name="box">';
                            foreach($boxes as $box) {
                                $selected=$_GET['box']==$box->id?' selected="selected" ':'';
                                echo '<option value="'.$box->id.'" '. $selected.'>'.$box->title.'</option>';
                            }
                            echo '</select>';
                        }?>
                        <?php _e('The container box of this field', 'axfields'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><?php _e('Key', 'axfields'); ?></th>
            <td>
                <input type="text" name="key" value="<?php echo $field->key; ?>">
                        <?php _e('The key that is used to access the value of this field', 'axfields'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><?php _e('Label', 'more-fields'); ?></th>
            <td>
                <input type="text" name="label" value="<?php echo $field->label; ?>">
                        <?php _e('The title of the field as it appears on the Write/Edit pages', 'axfields'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><?php _e('Slug', 'more-fields'); ?></th>
            <td>
                <input type="text" name="slug" value="<?php echo $field->slug; ?>">
                        <?php _e('URL path for listing based on this field, e.g. \'/baseurl/fieldname/value\'', 'axfields'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><?php _e('Type', 'axfields'); ?></th>
            <td>

                <select name="type" id="type">
                            <?php foreach (axfield::field_types() as $key=>$val) : ?>
                                <?php $selected = ($field->input_type == $key )? ' selected="selected"' : ''; ?>
                    <option value='<?php echo $key; ?>' <?php echo $selected; ?>><?php echo $val; ?></option>
                            <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr id="values_container">
            <th scope="row" valign="top"><?php _e('Values', 'axfields'); ?></th>
            <td>
                <textarea name="options"><?php echo stripslashes($field->options); ?></textarea>
                <br><em><?php _e('Separate values with comma (,). Preceed default value by a *. E.g: red, *green, blue','axfields');?></em>
            </td>
        </tr>

    </table>
    <!--<div class="handlediv">-->
    <h3 class="hndl"><span><?php _e('Validations', 'axfields'); ?></span></h3>
    <!-- <div class="inside">-->
    <table  class="form-table" style="vertical-align:top" id="f-validations">
        <thead>
            <tr><th><strong><?php _e('Validation','axfields')?></strong></th><th><strong><?php _e('Error message','axfields')?></strong></th><th><strong><?php _e('Options','axfields')?></strong></th></tr>
        </thead>
        <tbody>
                    <?php
                    $fvalidations=json_decode($field->validations);
                    $validation_types=axfield::validation_types();
                    //remove tag validation types
                    unset($validation_types[8]);//minentry
                    unset($validation_types[9]);//maxentry

                    foreach($validation_types as $validation) {
                        echo '<tr id="tr_'.$validation['type'].'">';
                        $selected='';
                        $vopt=null;
                        $vmsg=$validation['msg'];
                        if(is_array($fvalidations)  && count($fvalidations)>0) {
                            foreach($fvalidations as $v) {
                                if($validation['type']==$v[0]) {
                                    $selected='checked="checked"';
                                    if($v[2])$vopt=$v[2];
                                    $vmsg=isset($v[1])?$v[1]:$validation['msg'];
                                    break;
                                }
                            }
                        }

                        echo '<td valign="top"><input type="checkbox" '.$selected.'  name="validations[]" value="'.$validation['type'].'"/><label for='.$validation['type'].'">'.$validation['name'].'</label></td>';
                        echo '<td valign="top"><input style="width:360px;" type="text" name="'.$validation['type'].'_msg'.'" value="'.$vmsg.'"/></td>';
                        echo '<td valign="top">';
                        if($validation['option'])
                            echo '<input type="text" name="'.$validation['type'].'_opt'.'" value="'.$vopt.'"/><br/><em>'.$validation['description'].'</em>';
                        else
                            echo 'N/A';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
        </tbody>
    </table>
    <!-- </div></div>-->
    <input type="hidden" name="field" value="<?php echo isset($_GET['field'])? $_GET['field']:0; ?>">
    <input type="hidden" name="post_action" value="save_field">
    <p><input class="button-primary" type="submit" value="<?php _e('Save', 'axfields'); ?>"></p>

</form>

        <?php
    }
    public  function show_taxonomy_validation_page($msg='') {
        echo '<br/><h3>'.__('Taxonomies validation','axfield').'</h3>';
        $taxonomies=get_taxonomies();
        $exclude=array('nav_menu','link_category');
        foreach($taxonomies as $key=>$val) {
            if(in_array($val,$exclude)) {
                unset($taxonomies[$key]);
            }
        }
        //only checkbox validation required
        $validations=axfield::validation_types();
        $vals=array();//hierarchical
        $vals2=array();//non hierarchical
        foreach($validations as $key=>$val) {
            if($val['type']=='minselect' || $val['type']=='maxselect' ) {
                $vals[]=$val;
            }
            if($val['type']=='minentry' || $val['type']=='maxentry' ) {
                $vals2[]=$val;
            }
        }
        if(is_array($taxonomies) && count($taxonomies)>0) {
            ?>
<form method="post" action="options-general.php?page=meta-boxes&tab=tax_val&noheader=true">
                <?php
                $tax_vals=ameta_box_manager::get_taxonomies_validation();
                echo '<table class="form-table"><tr><th><strong>'.__('Taxonomies','axfields').'</strong></th><th><strong>'.__('Validation','axfield').'</strong></th><th><strong>'.__('Error message','axfields').'</strong></th><th><strong>'.__('Option','axfields').'</strong></th></tr>';
                foreach($taxonomies as $tax) {
                    $tax_obj=get_taxonomy($tax);
                    if($tax_obj->hierarchical) {
                        echo '<tr>';
                        echo '<td rowspan="2">'.$tax. '</td>';
                        $checked=isset($tax_vals[$tax.'_'.$vals[0]['type']])?' checked="checked" ':'';
                        echo '<td><input type="checkbox" '.$checked.' name="'.$tax.'_'.$vals[0]['type'].'" value="1"/><label for="'.$tax.'_'.$vals[0]['type'].'">'.$vals[0]['name'].'</label></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals[0]['type'].'_msg" value="'.(isset($tax_vals[$tax.'_'.$vals[0]['type'].'_msg'])?$tax_vals[$tax.'_'.$vals[0]['type'].'_msg']:$vals[0]['msg']).'" style="width:350px;"/></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals[0]['type'].'_opt" value="'.(isset($tax_vals[$tax.'_'.$vals[0]['type'].'_opt'])?$tax_vals[$tax.'_'.$vals[0]['type'].'_opt']:'').'" style="width:50px;"/></td>';
                        echo '</tr>';

                        echo '<tr>';
                        $checked=isset($tax_vals[$tax.'_'.$vals[1]['type']])?' checked="checked" ':'';
                        echo '<td><input type="checkbox" '.$checked.' name="'.$tax.'_'.$vals[1]['type'].'" value="1"/><label for="'.$tax.'_'.$vals[1]['type'].'">'.$vals[1]['name'].'</label></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals[1]['type'].'_msg" value="'.(isset($tax_vals[$tax.'_'.$vals[1]['type'].'_msg'])?$tax_vals[$tax.'_'.$vals[1]['type'].'_msg']:$vals[1]['msg']).'" style="width:350px;" /></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals[1]['type'].'_opt" value="'.(isset($tax_vals[$tax.'_'.$vals[1]['type'].'_opt'])?$tax_vals[$tax.'_'.$vals[1]['type'].'_opt']:'').'"  style="width:50px;"/></td>';
                        echo '</tr>';
                    }
                    else {
                        echo '<tr>';
                        echo '<td rowspan="2">'.$tax. '</td>';
                        $checked=isset($tax_vals[$tax.'_'.$vals2[0]['type']])?' checked="checked" ':'';
                        echo '<td><input type="checkbox" '.$checked.' name="'.$tax.'_'.$vals2[0]['type'].'" value="1"/><label for="'.$tax.'_'.$vals2[0]['type'].'">'.$vals2[0]['name'].'</label></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals2[0]['type'].'_msg" value="'.(isset($tax_vals[$tax.'_'.$vals2[0]['type'].'_msg'])?$tax_vals[$tax.'_'.$vals2[0]['type'].'_msg']:$vals2[0]['msg']).'" style="width:350px;"/></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals2[0]['type'].'_opt" value="'.(isset($tax_vals[$tax.'_'.$vals2[0]['type'].'_opt'])?$tax_vals[$tax.'_'.$vals2[0]['type'].'_opt']:'').'" style="width:50px;"/></td>';
                        echo '</tr>';

                        echo '<tr>';
                        $checked=isset($tax_vals[$tax.'_'.$vals2[1]['type']])?' checked="checked" ':'';
                        echo '<td><input type="checkbox" '.$checked.' name="'.$tax.'_'.$vals2[1]['type'].'" value="1"/><label for="'.$tax.'_'.$vals2[1]['type'].'">'.$vals2[1]['name'].'</label></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals2[1]['type'].'_msg" value="'.(isset($tax_vals[$tax.'_'.$vals2[1]['type'].'_msg'])?$tax_vals[$tax.'_'.$vals2[1]['type'].'_msg']:$vals[1]['msg']).'" style="width:350px;" /></td>';
                        echo '<td><input type="text" name="'.$tax.'_'.$vals2[1]['type'].'_opt" value="'.(isset($tax_vals[$tax.'_'.$vals2[1]['type'].'_opt'])?$tax_vals[$tax.'_'.$vals2[1]['type'].'_opt']:'').'"  style="width:50px;"/></td>';
                        echo '</tr>';
                    }

                }
                echo '</table>';
                ?>

    <input type="hidden" name="post_action" value="tax_val">
    <p><input class="button-primary" type="submit" value="<?php _e('Save', 'axfields'); ?>"></p>

</form>
            <?php

        }
    }
}//end class

function load_fieldscript() {
    //wp_register_script('axfieldpagescript', WP_PLUGIN_URL.'/custom-fields-manager/field_validations.js','1.0',true);
    // wp_enqueue_script('axfieldpagescript');
    ?>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/'.AXF_FOLDER.'/field_validations.js';?>"></script>
    <?php
}

?>
