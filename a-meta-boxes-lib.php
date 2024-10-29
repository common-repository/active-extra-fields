<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/


class axfield {

    var $class='';
    var $name='';
    var $label='';
    var $options;
    var $value;
    var $input_type='text';
    var $type='axfield';
    var $slug;
    var $validations=array();
    var $order;
    var $box;
    public static $table='axf_fields';

    public static function field_types() {
        return array('text'=>__('Text','axfields'),
                'select'=>__('Drop Down List','axfields'),
                'checkbox'=>__('Checkbox','axfields'),
                'checkboxlist'=>__('Checkbox List','axfields'),
                'textarea'=>__('Multiple line text','axfields'),
                'wysiwyg'=>__('WYSIWYG','axfields'),
                'date'=>__('Date','axfields'));
    }
    public static function validation_types() {
        return array(
                array('type'=>'required','msg'=>__('Please enter %name%','axfields'),'name'=>__('Required field','axfields')),
                array('type'=>'email','name'=>__('E-mail','axfields'),'msg'=>__('Please enter a valid email address','axfields')),
                array('type'=>'noselect','name'=>__('Not select','axfields'),'msg'=>__('Please select a valid value','axfields'),'option'=>true,'description'=>__('Enter value not to be selecetd. Use this to force user not to select a specifc item n a drop down list','axfields')),
                array('type'=>'maxselect','name'=>__('Maximum selection','axfields'),'msg'=>__('You can only select %n% items','axfields'),'option'=>true,'description'=>__('Enter the max number of item selecteable.Use this to set the maximum number of items selected in a list','axfields')),
                array('type'=>'minselect','name'=>__('Minimum selection','axfields'),'msg'=>__('You should select at least %n% item(s)','axfields'),'option'=>true,'description'=>__('Enter the min number of item selecteable.Use this to set the minimum number of items selected in a list','axfields')),
                array('type'=>'numeric','name'=>__('Numeric input','axfields'),'msg'=>__('Please enter numeric characters only','axfields'),'description'=>__('Use this to force only numeric input','axfields')),
                array('type'=>'maxlen','name'=>__('Maximum string length','axfields'),'msg'=>__('The input for %name% must not be longer than %n% character','axfields'),'option'=>true,'description'=>__('Enter the max length of the text input.Use this to force input max length','axfields')),
                array('type'=>'minlen','name'=>__('Minimum string length','axfields'),'msg'=>__('The input for %name% must not be at least %n% characters','axfields'),'option'=>true,'description'=>__('Enter the max length of the text input.Use this to force input min length','axfields')),
                array('type'=>'minentry','msg'=>__('At least %n% entries for %name% is/are required','axfields'),'name'=>__('Minimum entry','axfields')),
                array('type'=>'maxentry','msg'=>'The maximum number of entry for  %name% is %n%','name'=>__('Maximum entry','axfields')),
                array('type'=>'mustcheck','msg'=>__('Please check  %name%','axfields'),'name'=>__('Required','axfields'))
        );
    }
    /**
     * Create a new meta box object
     *
     * @param int $box the id of the box
     * @param string $key the the key of the field
     * @param string $label The label of the field
     * @param string $input_type the type of input
     * @param int $order the display order compared to other field displaye din the same metabox
     * @param string $options the list of option.Is required for multi input field such as drop down list and checkbox list
     * @param array $validations the validations rule for this field
     * @param string $slug the slug of the field
     * @param string the css class
     **/
    function __construct($box,$key,$label,$input_type,$order,$options='',$validations=array(),$slug='',$class='test') {

        $this->label=$label;
        $this->options=$options;
        //$this->class=$class;
        $this->name=$key;
        $this->input_type=$input_type;
        $this->slug=$slug;
        $this->validations=$validations;
        $this->order=$order;
        $this->box=$box;


    }
    public function display() {
        //
        switch($this->input_type) {
            case 'text':
                echo '<tr><th><label for="'.$this->name.'">'.$this->label.'</label></th><td><input type="text" value="'.$this->value.'" name="'.$this->name.'" id="'.$this->name.'"/></td></tr>';
                break;
            case 'textarea':
                echo  '<tr><th><label for="'.$this->name.'">'.$this->label.'</label></th><td><textarea  style="width:100%;" name="'.$this->name.'" id="'.$this->name.'">'.$this->value.'</textarea></p>';
                break;
            case 'select':
                echo '<tr><th><label for="'.$this->name.'">'.$this->label.'</label></th><td><select name="'.$this->name.'" id="'.$this->name.'">';
                $options=explode(",", $this->options);
                if(count($options)>0) {
                    foreach($options as $opt) {
                        $selected=$this->value==$opt?'selected="selected"':'';
                        echo '<option value="'.$opt.'" '.$selected.'>'.$opt.'</option>';

                    }
                }
                echo '</select></td></tr>';
                break;
            case 'checkbox':

                $selected=$this->value?' checked="checked"':'';
                echo '<tr><th colspan="2"><input type="checkbox" value="'.$this->name.'" '.$selected.' name="'.$this->name.'" />';
                echo '<label for="'.$this->name.'">'.$this->label.'</label></th></tr>';

                break;
            case 'checkboxlist':
                if(!empty($this->options)) {
                    if(!empty($this->value)) {
                        $this->value=explode(',', $this->value);
                    }
                    $options=explode(",", $this->options);
                    if(count($options)>0) {
                        echo '<tr><th>'.$this->label.'</th><td>';
                        foreach($options as $opt) {
                            $selected='';
                            if(!empty($this->value)) {
                                $selected=in_array($opt, $this->value)?' checked="checked"':'';
                            }
                            echo '<input type="checkbox" value="'.$opt.'" '.$selected.' name="'.$this->name.'[]" />';
                            echo '<label for="'.$this->name.'">'.$opt.'</label><br/>';
                        }
                        echo '</td></tr>';
                    }
                }
                break;
            case 'date':
                echo '<tr><th><label for="'.$this->name.'">'.$this->label.'</label></th><td><input type="text" value="'.$this->value.'" name="'.$this->name.'" id="'.$this->name.'"/></td></tr>';
                ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#<?php echo $this->name?>").datepicker({ dateFormat: 'yy-mm-dd' });
        //getter
        //var dateFormat = $( ".selector" ).datepicker( "option", "dateFormat" );
        //setter
        // jQuery( "#<?php echo $this->name?>" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
    });

</script>
                <?php
                break;
            case 'wysiwyg':
                ?>
<tr><th><?php echo $this->label?></th><td></td></tr>
<tr><td colspan="2">
        <script type="text/javascript">

            jQuery(document).ready( function () {
                jQuery("#<?php echo $this->name?>").addClass("mceEditor");
                if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
                    tinyMCE.execCommand("mceAddControl", false, "<?php echo $this->name?>");
                }
            });

        </script>
        <div style="width: 100%" id="<?php echo $this->name?>_wrap">
            <textarea class="<?php echo $this->class?> <?php echo $this->name?>"  name="<?php echo $this->name?>" id="<?php echo $this->name?>"><?php echo $this->value?></textarea>
        </div>
    <td></tr>
                <?php
                break;
        }
    }
    public static function get_field($id) {
        global $wpdb,$table_prefix;
        $sql='SELECT * FROM '.$table_prefix.self::$table.' WHERE id='.$id.';';
        return  $wpdb->get_row($sql,OBJECT);
    }
    public function save() {
        global $wpdb,$table_prefix;
        $data=array(
                'box'=>$this->box,
                'key'=>$this->name,
                'label'=>$this->label,
                'input_type'=>$this->input_type,
                'display_order'=>$this->order,
                'options'=>$this->options,
                'validations'=>json_encode($this->validations),
                'slug'=>$this->slug

        );
        $format=array('%d','%s','%s','%s','%d','%s','%s','%s');
        if((int)$this->id>0) {

            $wpdb->update( $table_prefix.self::$table, $data,  array('id'=>$this->id), $format , '%d' );
            return $this->id;
        }
        else
            $wpdb->insert($table_prefix.self::$table,$data,$format);
        return $wpdb->insert_id;
    }

    public static function save_field($box,$key,$label,$input_type,$order=0,$options='',$validations=array(),$slug='',$id=0) {
        $field=new axfield($box, $key, $label, $input_type, $order, $options, $validations, $slug);
        $field->id=$id;
        return $field->save();
    }
    public static function delete_field($id) {
        global $wpdb,$table_prefix;
         $action=array('proceed'=>true,'action'=>'delete_field','element_id'=>$id);
        #make sure that this action is allowed
        $action= apply_filters('axf_action', $action);
        if($action['proceed']==true){
           return $wpdb->query('DELETE FROM '.$table_prefix.self::$table.' WHERE id='.$id.';');
        }
        return $action;
    }
}


class ameta_box_manager {
    public static $meta_boxes_opt='a_metas';

    public function init() {
        if(is_admin()) {
            $url=$_SERVER['PHP_SELF'];
            if(strpos($url, 'post.php',10) || strpos($url, 'post-new.php',10)) {
                add_action('admin_init',array($this,'admin_init'));
                add_action('save_post',array($this,'save_post'));

                //add_action('wp_insert_post',array($this,'validate_fields') );
            }
        }
        // remove_action('wp_insert_post');
        //add_action('wp_print_script',array($this,'load_scripts') );
    }

    public function admin_init() {
        /*PROCESS META BOXES*/
        global $post;
        //get meta boxes
        $metaboxes=ameta_box::get_metaboxes('meta_boxes');
        if(is_array($metaboxes) && count($metaboxes)>0) {
            if(isset($_GET['post_type']))
                $pt=$_GET['post_type'];
            else if (isset($_GET['post']))
                $pt=get_post_type($_GET['post']);
            else $pt='post';

            $validations=array();
            foreach($metaboxes as $box) {
                if(in_array($pt,explode(',',$box->post_types))) {
                    $box_fields=ameta_box::get_box_fields($box->id);
                    if(is_array($box_fields) && count($box_fields)>0) {
                        foreach($box_fields as &$field) {
                            $field->value=$custom[$field->key][0];
                            $field_validations=json_decode($field->validations);
                            //format validation messages
                            foreach($field_validations as &$rules) {
                                $rules[1]=str_replace('%name%', $field->label,$rules[1]);
                                if($rules[2])
                                    $rules[1]=str_replace('%n%', $rules[2],$rules[1]);
                            }
                            if(is_array($field_validations) && count($field_validations)>0) {
                                foreach($field_validations as $val) {
                                    $validations[]= isset($val[2])?array($field->key,$val[0],$val[1],$val[2]):array($field->key,$val[0],$val[1]);
                                }
                            }
                        }
                        #filter box title
                        $box->title= apply_filters('axf_box_title_b4_display', $box->title);
                        add_meta_box(str_replace(' ', '_', $box->title).'div', $box->title, array(&$this,'render_box_content'), $pt, $box->position,$box->priority,$box_fields)    ;
                    }
                }
            }
            /*
             * Add taxonomy validations to validation list
            */
            //get taxonomies of post_type
            $post_tax=get_object_taxonomies($pt);
            //get taxonomy validations
            $tax_vals=self::get_taxonomies_validation();

            if(is_array($tax_vals) && count($tax_vals)>0) {
                foreach($post_tax as $tax) {
                    $tax_obj=get_taxonomy($tax);
                    if($tax_obj->hierarchical) {
                        foreach($tax_vals as $k=>$v) {
                            $fieldname=$tax=='category'?'post_category':'tax_input['.$tax.']';
                            if($tax.'_minselect'==$k && isset($v)) {//there is a minimum selection setting for this taxonomy
                                $err_msg=str_replace('%name%', $tax, $tax_vals[$tax.'_minselect_msg']);
                                $err_msg=str_replace('%n%', $tax_vals[$tax.'_minselect_opt'], $err_msg);
                                $validations[]=array($fieldname,'minselect',$err_msg,$tax_vals[$tax.'_minselect_opt']);
                            }
                            else if($tax.'_maxselect'==$k && isset($v)) {//there is a maximum selection setting for this taxonomy
                                $err_msg=str_replace('%name%', $tax, $tax_vals[$tax.'_maxselect_msg']);
                                $err_msg=str_replace('%n%', $tax_vals[$tax.'_maxselect_opt'], $err_msg);
                                $validations[]=array($fieldname,'maxselect',$err_msg,$tax_vals[$tax.'_maxselect_opt']);
                            }
                        }
                    }
                    else {
                        foreach($tax_vals as $k=>$v) {
                            //$fieldname=$tax=='category'?'post_category':'tax_input['.$tax.']';
                            $fieldname=$tax;
                            if($tax.'_minentry'==$k && isset($v)) {//there is a minimum selection setting for this taxonomy
                                $err_msg=str_replace('%name%', $tax, $tax_vals[$tax.'_minentry_msg']);
                                $err_msg=str_replace('%n%', $tax_vals[$tax.'_minentry_opt'], $err_msg);
                                $validations[]=array($fieldname,'minentry',$err_msg,$tax_vals[$tax.'_minentry_opt']);
                            }
                            else if($tax.'_maxentry'==$k && isset($v)) {//there is a maximum selection setting for this taxonomy
                                $err_msg=str_replace('%name%', $tax, $tax_vals[$tax.'_maxentry_msg']);
                                $err_msg=str_replace('%n%', $tax_vals[$tax.'_maxentry_opt'], $err_msg);
                                $validations[]=array($fieldname,'maxentry',$err_msg,$tax_vals[$tax.'_maxentry_opt']);
                            }
                        }
                    }
                }
            }

            //taxonomy validations
            global $fields_validations;
            $fields_validations=$validations;

        }
        /*LOAD VALIDATION SCRIPTS*/
        /* Register our scripts. */
        add_action('admin_print_scripts', array($this,'load_scripts'));
        add_action('admin_print_styles', array($this,'load_styles'));
    }
    public function save_post() {

        //get boxes
        global $post;
        //var_dump($_POST);
        $metaboxes=ameta_box::get_metaboxes('meta_boxes');
        //foreach get field name
        if(is_array($metaboxes) && count($metaboxes)>0) {
            foreach($metaboxes as $box) {
                $box_fields=ameta_box::get_box_fields($box->id);
                if(is_array($box_fields) && count($box_fields)>0) {
                    foreach($box_fields as $field) {
                        if(is_array($_POST[$field->key]) && count($_POST[$field->key])>0) {
                            foreach($_POST[$field->key] as $val) {
                                update_post_meta($post->ID, $field->key, implode(',',$_POST[$field->key]));
                            }
                        }
                        else
                            update_post_meta($post->ID, $field->key, $_POST[$field->key]);
                    }
                }

            }
        }
        //foreach field name save post variable
    }

    public function load_scripts($args=null) {
        wp_register_script('a_meta_boxes', CFM_DIR . '/FormValidator.js');
        wp_enqueue_script('a_meta_boxes');
        wp_register_script('jquery.ui.datepicker', CFM_DIR . '/js/ui.datepicker.js');
        wp_enqueue_script('jquery.ui.datepicker');
        add_action('admin_footer', array($this,'print_validation_script'));

    }
    public function load_styles($args=null) {
        wp_register_style('datepicker-style', CFM_DIR . '/css/datepicker/ui.datepicker.css', array());
        wp_enqueue_style('datepicker-style');
        // wp_register_style('datepicker-style2', WP_PLUGIN_URL . '/custom-fields-manager/css/datepicker/ui.datepickercosa.css', array());
        //wp_enqueue_style('datepicker-style2');
    }
    public function print_validation_script() {
        global $fields_validations;
        ?>

<script type="text/javascript">
    /*document.getElementById('post').onsubmit=
        function(){return false;alert('yes');};
         cleanValidator.init({
    formId: 'post',
    inputColors: ['#EDEDED', '#FFFFFF'],
    errorColors: ['#FFFF99', '#CF3339'],
    isRequired: ['rest','price']

  });*/
    /* var frmvalidator = new Validator("post");
    frmvalidator.addValidation("price","req","Please enter  a price");
    frmvalidator.addValidation("rest","req","Rest is required");*/

    /**/

    var settings={"form":"post",
        "rules":<?php echo json_encode($fields_validations);?>,
        "error_display":"alert",
        "inputColors": ['white', 'silver'],
        "errorColors": ['#FFFF99', '#CF3339'],
        "beforeErrorsDisplay":function(){
            jQuery('#ajax-loading').css('visibility','hidden');
            jQuery('#publish').toggleClass('button-primary-disabled');}};
    formValidator.init(settings);
</script>
        <?php
    }
    public function render_box_content($p,$args) {

        $fields=$args['args'];
        global $cur_box,$post;
        if(is_array($fields) && count($fields)>0) {
            $custom = get_post_custom($post->ID);//var_dump($custom);
            echo '<table class="form-table">';
            foreach($fields as $field) {
                #filter for field label before display
                $field->label=apply_filters('axf_field_label_b4_display', $field->label);
                $f=new axfield(1,$field->key, $field->label, $field->input_type,$field->order, $field->options);

                $f->value=$custom[$f->name][0];
                $f->display();
            }
            echo '</table>';
        }
    }
    public static function save_taxonomies_validation($settings,$opt_name='axf_tax_val') {
        update_option($opt_name,json_encode($settings));

    }
    public static function get_taxonomies_validation($opt_name='axf_tax_val') {
        $tax_vals=get_option($opt_name);
        $arr_tax_vals=json_decode($tax_vals,true);
        if(is_array($arr_tax_vals) && count($arr_tax_vals)>0)
            return $arr_tax_vals;
        else return false;
    }
}
class ameta_box {
    public $id=0;
    public $title;
    public $fields=array();
    public $position='side';
    public $post_types=array();
    public $priority;
    public static $table='axf_boxes';


    /**
     * Create a new meta box object
     *
     * @param string $title the heading of the meta box
     * @param string $post_types the post type that the meta box is valid for
     * @param string $position The position of the metaboxe .Value can be side or normal
     * @param string priority The priority affect the order at which the box is displayed compared to other boxes
     * @param int $id the id of the metabox if any.Default is 0
     */
    public function __construct($title,$post_types,$position='side',$priority='default',$id=0) {
        $this->title=$title;
        $this->fields=$fields;
        $this->position=$position;
        $this->post_types=$post_types;
        $this->priority=$priority;
        $this->id=$id;
    }
    public static function delete_box($id) {
        global $wpdb,$table_prefix;
        $action=array('proceed'=>true,'action'=>'delete_box','element_id'=>$id);
        #make sure that this action is allowed
        $action= apply_filters('axf_action', $action);
        if($action['proceed']==false) {
            wp_redirect($action['redirect_url']);
        }
        else {
            $sql='DELETE FROM '.$table_prefix. self::$table.' WHERE id='.$id.';';
            $wpdb->query($sql);
            $sql=' DELETE FROM '.$table_prefix.axfield::$table.' WHERE box='.$id.';';
            return $wpdb->query($sql);
            #note:cannot execute more than 1 statement with  $wpdb->query($sql)
        }
return $action;
    }
    public function save($opt_name='a_metas') {

        global $wpdb,$table_prefix;
        $result=false;
        $sql='';
        if((int)$this->id>0) {
            $sql="UPDATE ".$table_prefix. self::$table." SET title='$this->title' , position='$this->position',priority='$this->priority',post_types='$this->post_types' WHERE id=$this->id;";
            $wpdb->query($sql);
            $result=$this->id;
        }
        else {
            $sql="INSERT INTO  ".$table_prefix. self::$table."(title,position,priority,post_types) VALUES('$this->title','$this->position','$this->priority','$this->post_types');";
            $wpdb->query($sql);
            $result=$wpdb->insert_id;
        }
        return $result;
    }
    public static function get_metaboxes($meta_boxes_opt='a_metas') {

        global $wpdb,$table_prefix;
        $sql='SELECT b. * , (
        SELECT count( * )
        FROM '.$table_prefix.axfield::$table.' f
        WHERE f.box = b.id
        ) AS fields_count
        FROM '.$table_prefix.self::$table.' b';
        $meta_boxes=$wpdb->get_results($sql,OBJECT);
        $meta_boxes =apply_filters('axf_get_boxes', $meta_boxes);
        return $meta_boxes;
    }
    public static function get_box_fields($id) {
        global $wpdb,$table_prefix;
        $sql='SELECT *  FROM '.$table_prefix.axfield::$table.' WHERE box = '.$id.' ORDER BY display_order;';
        return $wpdb->get_results($sql,OBJECT);
    }
    public static function get_metabox($id) {

        global $table_prefix,$wpdb;
        $sql='SELECT * FROM '.$table_prefix.self::$table.' WHERE id='.$id.';';
        $obj= $wpdb->get_row($sql, OBJECT);
        $obj->fields=self::get_box_fields($id);
        return $obj;
    }
    public static function remove_metaboxes($opt_name='a_metas') {
        delete_option($opt_name);
        return true;
    }
    /**
     * Saves a metabox to storage
     *
     * @param string $title the heading of the meta box
     * @param mixed $post_types the post type that the meta box is valid for.Shoul be an array or a list of post types seperated by commas
     * @param string $position The position of the metaboxe .Value can be side or normal
     * @param string priority The priority affect the order at which the box is displayed compared to other boxes
     * @param int $id the id of the metabox if any.Default is 0
     * @return int the id of the saved object
     */
    public static function save_metabox($title,$post_types,$position='normal',$priority='default',$id=0) {

        $box_id=!empty($id)?$id:0;
        if(is_array($post_types)) $post_types=implode(',', $post_types);
        $mb=new ameta_box($title, $post_types, $position, $priority, $box_id);
        return $mb->save();
    }
}

$fields_validations=array();
?>
