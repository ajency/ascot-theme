<?php     
$values['name'] = 'financial-adviser-form-SatvinderGata';
$values['description'] = '<p>Contact Form</p>';
$values['editable'] = 0;
$values['logged_in'] = 0;
$values['options'] = array();
$values['options']['email_to'] = 'satvinder.gata-aura@ascotwm.com,mark.insley@ascotwm.com'; 
$values['options']['submit_value'] = 'Submit'; 
$values['options']['success_msg'] = 'Your responses were successfully submitted. Thank you!';
$values['options']['show_form'] = 0;
$values['options']['akismet'] = '';
$values['options']['custom_style'] = 1;
$values['options']['before_html'] = '[if form_name]<h3>[form_name]</h3>[/if form_name]
[if form_description]<div class=\"frm_description\">[form_description]</div>[/if form_description]';
$values['options']['after_html'] = '';
$values['options']['single_entry'] = 0;
$values['options']['single_entry_type'] = 'user';
$values['options']['logged_in_role'] = '';
$values['options']['editable_role'] = '';
$values['options']['open_editable'] = 0;
$values['options']['open_editable_role'] = '';
$values['options']['edit_value'] = 'Update';
$values['options']['edit_msg'] = 'Your submission was successfully saved.';

$values['options']['plain_text'] = 1;
//$values['options']['reply_to'] = '92';
//$values['options']['reply_to_name'] = '';
$values['options']['email_subject'] = '[91] is interested in [pp6uhx]';
$values['options']['email_message'] = '[default-message]';
$values['options']['inc_user_info'] = 0;

$values['options']['auto_responder'] = 0;
$values['options']['ar_plain_text'] = 0;
//$values['options']['ar_email_to'] = '';
$values['options']['ar_reply_to'] = 'mark.insley@ascotwm.com';
$values['options']['ar_reply_to_name'] = 'AWM';
$values['options']['ar_email_subject'] = '';
$values['options']['ar_email_message'] = '';

$values['options']['copy'] = 0;
if ($form){
    $form_id = $form->id;
    $frm_form->update($form_id, $values );
    $form_fields = $frm_field->getAll(array('fi.form_id' => $form_id));
    if (!empty($form_fields)){
        foreach ($form_fields as $field)
            $frm_field->destroy($field->id);
    }
}else
    $form_id = $frm_form->create( $values );

    
$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('text', $form_id));
$field_values['field_key'] = '6t7et02';
$field_values['name'] = 'Name';
$field_values['description'] = '';
$field_values['type'] = 'text';
$field_values['default_value'] = '';
$field_values['options'] = '';
$field_values['required'] = '1';
$field_values['field_order'] = '1';
$field_values['field_options']['size'] = '';
$field_values['field_options']['max'] = '';
$field_values['field_options']['label'] = '';
$field_values['field_options']['blank'] = 'This field cannot be blank';
$field_values['field_options']['required_indicator'] = '*';
$field_values['field_options']['invalid'] = '';
$field_values['field_options']['separate_value'] = '0';
$field_values['field_options']['clear_on_focus'] = '0';
$field_values['field_options']['default_blank'] = '0';
$field_values['field_options']['classes'] = '';
$field_values['field_options']['custom_html'] = '<div id=\"frm_field_[id]_container\" class=\"frm_form_field form-field [required_class][error_class]\">
    <label class=\"frm_primary_label\">[field_name]
        <span class=\"frm_required\">[required_label]</span>
    </label>
    [input]
    [if description]<div class=\"frm_description\">[description]</div>[/if description]
    [if error]<div class=\"frm_error\">[error]</div>[/if error]
</div>';
$field_values['field_options']['slide'] = '0';
$field_values['field_options']['form_select'] = '';
$field_values['field_options']['show_hide'] = 'show';
$field_values['field_options']['any_all'] = 'any';
$field_values['field_options']['align'] = 'block';
$field_values['field_options']['hide_field'] = 'a:0:{}';
$field_values['field_options']['hide_field_cond'] = 'a:1:{i:0;s:2:"==";}';
$field_values['field_options']['hide_opt'] = 'a:0:{}';
$field_values['field_options']['star'] = '0';
$field_values['field_options']['ftypes'] = 'a:0:{}';
$field_values['field_options']['data_type'] = '';
$field_values['field_options']['restrict'] = '0';
$field_values['field_options']['start_year'] = '2000';
$field_values['field_options']['end_year'] = '2020';
$field_values['field_options']['read_only'] = '0';
$field_values['field_options']['admin_only'] = '0';
$field_values['field_options']['locale'] = '';
$field_values['field_options']['attach'] = '';
$field_values['field_options']['minnum'] = '0';
$field_values['field_options']['maxnum'] = '9999';
$field_values['field_options']['step'] = '1';
$field_values['field_options']['clock'] = '12';
$field_values['field_options']['start_time'] = '00:00';
$field_values['field_options']['end_time'] = '23:59';
$field_values['field_options']['unique'] = '0';
$field_values['field_options']['use_calc'] = '0';
$field_values['field_options']['calc'] = '';
$field_values['field_options']['duplication'] = '1';
$field_values['field_options']['rte'] = 'nicedit';
$field_values['field_options']['dyn_default_value'] = '';
$field_values['field_options']['dependent_fields'] = '';
$field_values['field_options']['custom_field'] = '';
$field_values['field_options']['post_field'] = '';
$field_values['field_options']['taxonomy'] = 'category';
$field_values['field_options']['exclude_cat'] = '0';
$frm_field->create( $field_values );

    
$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('email', $form_id));
$field_values['field_key'] = 'cblqpg2';
$field_values['name'] = 'Email';
$field_values['description'] = '';
$field_values['type'] = 'email';
$field_values['default_value'] = '';
$field_values['options'] = '';
$field_values['required'] = '1';
$field_values['field_order'] = '2';
$field_values['field_options']['size'] = '';
$field_values['field_options']['max'] = '';
$field_values['field_options']['label'] = '';
$field_values['field_options']['blank'] = 'This field cannot be blank';
$field_values['field_options']['required_indicator'] = '*';
$field_values['field_options']['invalid'] = 'Email is invalid';
$field_values['field_options']['separate_value'] = '0';
$field_values['field_options']['clear_on_focus'] = '0';
$field_values['field_options']['default_blank'] = '0';
$field_values['field_options']['classes'] = '';
$field_values['field_options']['custom_html'] = '<div id=\"frm_field_[id]_container\" class=\"frm_form_field form-field [required_class][error_class]\">
    <label class=\"frm_primary_label\">[field_name]
        <span class=\"frm_required\">[required_label]</span>
    </label>
    [input]
    [if description]<div class=\"frm_description\">[description]</div>[/if description]
    [if error]<div class=\"frm_error\">[error]</div>[/if error]
</div>';
$field_values['field_options']['slide'] = '0';
$field_values['field_options']['form_select'] = '';
$field_values['field_options']['show_hide'] = 'show';
$field_values['field_options']['any_all'] = 'any';
$field_values['field_options']['align'] = 'block';
$field_values['field_options']['hide_field'] = 'a:0:{}';
$field_values['field_options']['hide_field_cond'] = 'a:1:{i:0;s:2:"==";}';
$field_values['field_options']['hide_opt'] = 'a:0:{}';
$field_values['field_options']['star'] = '0';
$field_values['field_options']['ftypes'] = 'a:0:{}';
$field_values['field_options']['data_type'] = '';
$field_values['field_options']['restrict'] = '0';
$field_values['field_options']['start_year'] = '2000';
$field_values['field_options']['end_year'] = '2020';
$field_values['field_options']['read_only'] = '0';
$field_values['field_options']['admin_only'] = '0';
$field_values['field_options']['locale'] = '';
$field_values['field_options']['attach'] = '';
$field_values['field_options']['minnum'] = '0';
$field_values['field_options']['maxnum'] = '9999';
$field_values['field_options']['step'] = '1';
$field_values['field_options']['clock'] = '12';
$field_values['field_options']['start_time'] = '00:00';
$field_values['field_options']['end_time'] = '23:59';
$field_values['field_options']['unique'] = '0';
$field_values['field_options']['use_calc'] = '0';
$field_values['field_options']['calc'] = '';
$field_values['field_options']['duplication'] = '1';
$field_values['field_options']['rte'] = 'nicedit';
$field_values['field_options']['dyn_default_value'] = '';
$field_values['field_options']['dependent_fields'] = '';
$field_values['field_options']['custom_field'] = '';
$field_values['field_options']['post_field'] = '';
$field_values['field_options']['taxonomy'] = 'category';
$field_values['field_options']['exclude_cat'] = '0';
$frm_field->create( $field_values );

    
$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('phone', $form_id));
$field_values['field_key'] = 'nob0vo2';
$field_values['name'] = 'Phone';
$field_values['description'] = '';
$field_values['type'] = 'phone';
$field_values['default_value'] = '';
$field_values['options'] = '';
$field_values['required'] = '1';
$field_values['field_order'] = '3';
$field_values['field_options']['size'] = '15';
$field_values['field_options']['max'] = '';
$field_values['field_options']['label'] = '';
$field_values['field_options']['blank'] = 'This field cannot be blank';
$field_values['field_options']['required_indicator'] = '*';
$field_values['field_options']['invalid'] = 'Phone is invalid';
$field_values['field_options']['separate_value'] = '0';
$field_values['field_options']['clear_on_focus'] = '0';
$field_values['field_options']['default_blank'] = '0';
$field_values['field_options']['classes'] = '';
$field_values['field_options']['custom_html'] = '<div id=\"frm_field_[id]_container\" class=\"frm_form_field form-field [required_class][error_class]\">
    <label class=\"frm_primary_label\">[field_name]
        <span class=\"frm_required\">[required_label]</span>
    </label>
    [input]
    [if description]<div class=\"frm_description\">[description]</div>[/if description]
    [if error]<div class=\"frm_error\">[error]</div>[/if error]
</div>';
$field_values['field_options']['slide'] = '0';
$field_values['field_options']['form_select'] = '';
$field_values['field_options']['show_hide'] = 'show';
$field_values['field_options']['any_all'] = 'any';
$field_values['field_options']['align'] = 'block';
$field_values['field_options']['hide_field'] = 'a:0:{}';
$field_values['field_options']['hide_field_cond'] = 'a:1:{i:0;s:2:"==";}';
$field_values['field_options']['hide_opt'] = 'a:0:{}';
$field_values['field_options']['star'] = '0';
$field_values['field_options']['ftypes'] = 'a:0:{}';
$field_values['field_options']['data_type'] = '';
$field_values['field_options']['restrict'] = '0';
$field_values['field_options']['start_year'] = '2000';
$field_values['field_options']['end_year'] = '2020';
$field_values['field_options']['read_only'] = '0';
$field_values['field_options']['admin_only'] = '0';
$field_values['field_options']['locale'] = '';
$field_values['field_options']['attach'] = '';
$field_values['field_options']['minnum'] = '0';
$field_values['field_options']['maxnum'] = '9999';
$field_values['field_options']['step'] = '1';
$field_values['field_options']['clock'] = '12';
$field_values['field_options']['start_time'] = '00:00';
$field_values['field_options']['end_time'] = '23:59';
$field_values['field_options']['unique'] = '0';
$field_values['field_options']['use_calc'] = '0';
$field_values['field_options']['calc'] = '';
$field_values['field_options']['duplication'] = '1';
$field_values['field_options']['rte'] = 'nicedit';
$field_values['field_options']['dyn_default_value'] = '';
$field_values['field_options']['dependent_fields'] = '';
$field_values['field_options']['custom_field'] = '';
$field_values['field_options']['post_field'] = '';
$field_values['field_options']['taxonomy'] = 'category';
$field_values['field_options']['exclude_cat'] = '0';
$frm_field->create( $field_values );

    
$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('select', $form_id));
$field_values['field_key'] = 'pp6uhx2';
$field_values['name'] = 'Services';
$field_values['description'] = '';
$field_values['type'] = 'select';
$field_values['default_value'] = 'INVESTMENTS  ';
$field_values['options'] = 'a:5:{i:0;s:13:"INVESTMENTS  ";i:1;s:8:"PENSIONS";i:2;s:10:"PROTECTION";i:3;s:19:"BUSINESS TAX ADVICE";i:4;s:21:"IHT / ESTATE PLANNING";}';
$field_values['required'] = '1';
$field_values['field_order'] = '4';
$field_values['field_options']['size'] = '';
$field_values['field_options']['max'] = '';
$field_values['field_options']['label'] = '';
$field_values['field_options']['blank'] = 'This field cannot be blank';
$field_values['field_options']['required_indicator'] = '*';
$field_values['field_options']['invalid'] = '';
$field_values['field_options']['separate_value'] = '0';
$field_values['field_options']['clear_on_focus'] = '0';
$field_values['field_options']['default_blank'] = '0';
$field_values['field_options']['classes'] = '';
$field_values['field_options']['custom_html'] = '<div id=\"frm_field_[id]_container\" class=\"frm_form_field form-field [required_class][error_class]\">
    <label class=\"frm_primary_label\">[field_name]
        <span class=\"frm_required\">[required_label]</span>
    </label>
    [input]
    [if description]<div class=\"frm_description\">[description]</div>[/if description]
    [if error]<div class=\"frm_error\">[error]</div>[/if error]
</div>';
$field_values['field_options']['slide'] = '0';
$field_values['field_options']['form_select'] = '';
$field_values['field_options']['show_hide'] = 'show';
$field_values['field_options']['any_all'] = 'any';
$field_values['field_options']['align'] = 'block';
$field_values['field_options']['hide_field'] = 'a:0:{}';
$field_values['field_options']['hide_field_cond'] = 'a:1:{i:0;s:2:"==";}';
$field_values['field_options']['hide_opt'] = 'a:0:{}';
$field_values['field_options']['star'] = '0';
$field_values['field_options']['ftypes'] = 'a:0:{}';
$field_values['field_options']['data_type'] = '';
$field_values['field_options']['restrict'] = '0';
$field_values['field_options']['start_year'] = '2000';
$field_values['field_options']['end_year'] = '2020';
$field_values['field_options']['read_only'] = '0';
$field_values['field_options']['admin_only'] = '0';
$field_values['field_options']['locale'] = '';
$field_values['field_options']['attach'] = '';
$field_values['field_options']['minnum'] = '0';
$field_values['field_options']['maxnum'] = '9999';
$field_values['field_options']['step'] = '1';
$field_values['field_options']['clock'] = '12';
$field_values['field_options']['start_time'] = '00:00';
$field_values['field_options']['end_time'] = '23:59';
$field_values['field_options']['unique'] = '0';
$field_values['field_options']['use_calc'] = '0';
$field_values['field_options']['calc'] = '';
$field_values['field_options']['duplication'] = '1';
$field_values['field_options']['rte'] = 'nicedit';
$field_values['field_options']['dyn_default_value'] = '';
$field_values['field_options']['dependent_fields'] = '';
$field_values['field_options']['custom_field'] = '';
$field_values['field_options']['post_field'] = '';
$field_values['field_options']['taxonomy'] = 'category';
$field_values['field_options']['exclude_cat'] = '0';
$frm_field->create( $field_values );

