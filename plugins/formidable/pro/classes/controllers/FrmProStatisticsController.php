<?php

class FrmProStatisticsController{
    function FrmProStatisticsController(){
        add_action('admin_menu', array( &$this, 'menu' ), 24);
        add_shortcode('frm-graph', array(&$this, 'graph_shortcode'));
        add_shortcode('frm-stats', array(&$this, 'stats_shortcode'));
    }
    
    function menu(){
        global $frm_settings;
        add_submenu_page('formidable', 'Formidable | '. __('Reports', 'formidable'), __('Reports', 'formidable'), 'frm_view_reports', 'formidable-reports', array(&$this, 'show'));
    }
    
    function show(){
        global $frmdb, $frm_form, $frm_field, $frm_entry_meta, $frm_entry, $wpdb;
        if  (!isset($_GET['form'])){
            require_once(FRMPRO_VIEWS_PATH.'/frmpro-statistics/show.php');
            return;
        }
        
        $form = $frm_form->getOne($_GET['form']);
        $form_options = maybe_unserialize($form->options);
        $fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','rte','textarea','file','grid','html') and fi.form_id=".$form->id, 'field_order ASC');
        
        $js = '';
        $data = array();
        $odd = true;
        $colors = '#21759B,#EF8C08,#C6C6C6';
        $col_array = explode(',', $colors);
        
        $data['time'] = $this->get_daily_entries($form, array('is3d' => true, 'colors' => $colors));
        $data['month'] = $this->get_daily_entries($form, array('is3d' => true, 'colors' => $colors), 'MONTH');
        
        foreach ($fields as $field){
            //$first = array_shift($col_array);
            //$col_array[] = $first;
            //$colors = implode(',', $col_array);
                
            $data[$field->id] = $this->graph_shortcode(array(
                'id' => $field->id, 'field' => $field, 'is3d' => true, 'min' => 0,
                'colors' => $colors, 'width' => 650
            ));
        }
        
        include(FRMPRO_VIEWS_PATH.'/frmpro-statistics/show.php');
    }
    
    function get_google_graph($field, $args){
        $defaults = array(
            'ids' => false, 
            'colors' => '', 'grid_color' => '#CCC', 'bg_color' => '#FFFFFF', 'is3d' => false,
            'odd' => false, 'truncate' => 40, 'truncate_label' => 15, 'response_count' => 10, 
            'user_id' => false, 'type' => 'default', 'x_axis' => false, 'data_type' => 'count',
            'limit' => '', 'x_start' => '', 'x_end' => '', 'show_key' => false, 'min' => '', 'max' => '',
            'include_zero' => false, 'width' => 400, 'height' => 400, 'allowed_col_types' => array('string', 'number')
        );
        
        $args = wp_parse_args($args, $defaults);
        $vals = $this->get_graph_values($field, $args);
        extract($vals);
        extract($args);
        
        
        $pie = ($type == 'default') ? $pie : (($type == 'pie') ? true : false);
        if ($pie){
            $type = 'pie';
            
            $cols = array('Field' => array('type' => 'string'), 'Entries' => array('type' => 'number')); //map each array position in rows array
            
            foreach ($values as $val_key => $val){
                if($val)
                    $rows[] = array($labels[$val_key], $val);
            }
        }else{
            if(!isset($options['hAxis']))
                $options['hAxis'] = array();
                
            $options['vAxis'] = array('gridlines' => array('color' => $grid_color));
            if($combine_dates and !strpos($width, '%') and ((count($labels) * 50) > (int)$width))
                $options['hAxis']['showTextEvery'] = (ceil((count($labels) * 50) / (int)$width));
            
            $options['hAxis']['slantedText'] = true;
            $options['hAxis']['slantedTextAngle'] = 20;
            
            $rn_order = array();
            foreach($labels as $lkey => $l){
                if(isset($x_field) and $x_field and $x_field->type == 'number'){
                    $l = (float)$l;
                    $rn_order[] = $l;
                }
                
                $row = array($l, $values[$lkey]);
                foreach($f_values as $f_id => $f_vals)
                    $row[] = isset($f_vals[$lkey]) ? $f_vals[$lkey] : 0;
                
                $rows[] = $row;  
                unset($lkey);
                unset($l);
            }
            
            if(isset($max) and !empty($max))
                $options['vAxis']['maxValue'] = $max;
            
            if(!empty($min))
                $options['vAxis']['minValue'] = $min;
        }
        
        if(isset($rn_order) and !empty($rn_order)){
            asort($rn_order);
            $sorted_rows = array();
            foreach($rn_order as $rk => $rv)
                $sorted_rows[] = $rows[$rk];
            
            $rows = $sorted_rows;
        }
        
        $options['backgroundColor'] = $bg_color;
        $options['is3D'] = $is3d ? true : false;
        
        if($type == 'bar' or $type == 'bar_flat' or $type == 'bar_glass')
            $type = 'column';
        else if($type == 'hbar')
            $type = 'bar';
            
        $allowed_types = array('pie', 'line', 'column', 'area', 'SteppedArea', 'geo', 'bar');
        if(!in_array($type, $allowed_types))
            $type = 'column';
        
        $options = apply_filters('frm_google_chart', $options, compact('rows', 'cols', 'type', 'atts'));
        return $this->convert_to_google($rows, $cols, $options, $type);
    }
    
    function get_graph_values($field, $args){
        global $frm_entry_meta, $frm_field, $frmdb, $wpdb;           
        $values = $labels = $f_values = $f_labels = $rows = $cols = array();
        $pie = false;
        
        extract($args);
        
        $show_key = (int)$show_key;
        if($show_key and $show_key < 5)
            $show_key = 10;
            
        $options = array('width' => $width, 'height' => $height, 'legend' => 'none');
        if(!empty($colors))
            $options['colors'] = $colors;
        $options['title'] = preg_replace("/&#?[a-z0-9]{2,8};/i", "", FrmAppHelper::truncate($field->name, $truncate, 0));
             
        if($show_key)
            $options['legend'] = array('position' => 'right', 'textStyle' => array('fontSize' => $show_key));
            
        $fields = $f_inputs = array();
        $fields[$field->id] = $field;

        if($ids){ 
            $ids = explode(',', $ids);

            foreach($ids as $id_key => $f){
                $ids[$id_key] = $f = trim($f);
                if(!$f or empty($f)){
                    unset($ids[$id_key]);
                    continue;
                }

                if($add_field = $frm_field->getOne($f)){
                    $fields[$add_field->id] = $add_field;
                    $ids[$id_key] = $add_field->id;
                }
                unset($f);
                unset($id_key);
            }
        }else{
            $ids = array();
        }
        
        $cols['xaxis'] = array('type' => 'string');
        
        if($x_axis){
            $x_field = $frm_field->getOne($x_axis);
            
            $query = $x_query = "SELECT meta_value, item_id FROM $frmdb->entry_metas em";
            if(!$x_field)
                $x_query = "SELECT id, {$x_axis} FROM $frmdb->entries e";
            
            if($user_id){
                $query .= " LEFT JOIN $frmdb->entries e ON (e.id=em.item_id)";
                if($x_field)
                    $x_query .= " LEFT JOIN $frmdb->entries e ON (e.id=em.item_id)";
            }
              
            if($x_field){
                if(isset($allowed_col_types))
                    $cols['xaxis'] = array('type' => ((in_array($x_field->type, $allowed_col_types)) ? $x_field->type : 'string'), 'id' => $x_field->id);
                $options['hAxis'] = array('title' => $x_field->name);
                
                $x_query .= " WHERE em.field_id='{$x_field->id}'";
                if(!empty($x_start)){
                    if($x_field->type == 'date')
                        $x_start = date('Y-m-d', strtotime($x_start));
                    
                    $x_query .= " and meta_value >= '$x_start'";
                }
                
                if(!empty($x_end)){
                    if($x_field->type == 'date')
                        $x_end = date('Y-m-d', strtotime($x_end));
                        
                    $x_query .= " and meta_value <= '$x_end'";
                }
            }else{
                $cols['xaxis'] = array('type' => 'string');
                                
                $x_query .= " WHERE form_id=". $field->form_id;
                if(!empty($x_start)){
                    if(in_array($x_axis, array('created_at', 'updated_at')))
                        $x_start = date('Y-m-d', strtotime($x_start));
                    $x_query .= " and e.{$x_axis} >= '$x_start'";
                }

                if(!empty($x_end)){
                    if(in_array($x_axis, array('created_at', 'updated_at')))
                        $x_end = date('Y-m-d', strtotime($x_end)) .' 23:59:59';
                    $x_query .= " and e.{$x_axis} <= '$x_end'";
                }
            }            
            
            $q = array();
            foreach($fields as $f_id => $f){
                if($f_id != $field->id)
                    $q[$f_id] = $query ." WHERE em.field_id='{$f_id}'". ( ($user_id) ? " AND user_id='$user_id'" : '');
                unset($f);
                unset($f_id);
            }
                
            $query .= " WHERE em.field_id='{$field->id}'";
            
            if($user_id){
                $query .= " AND user_id='$user_id'";
                $x_query .= " AND user_id='$user_id'";
            }

            $inputs = $wpdb->get_results($query, ARRAY_A);
            $x_inputs = $wpdb->get_results($x_query, ARRAY_A);
            
            if(!$x_inputs)
                $x_inputs = array('id' => '0');
 
            unset($query);
            unset($x_query);
            
            foreach($q as $f_id => $query){
                $f_inputs[$f_id] = $wpdb->get_results($query, ARRAY_A);
                unset($query);
            }
            
            unset($q);            
        }else{            
            if($user_id)
                $inputs = $wpdb->get_col("SELECT meta_value FROM $frmdb->entry_metas em LEFT JOIN $frmdb->entries e ON (e.id=em.item_id) WHERE em.field_id='{$field->id}' AND user_id='$user_id'");
            else
                $inputs = $frm_entry_meta->get_entry_metas_for_field($field->id);
            
            foreach($fields as $f_id => $f){
                if($f_id != $field->id)
                    $f_inputs[$f_id] = $wpdb->get_col("SELECT meta_value FROM $frmdb->entry_metas em LEFT JOIN $frmdb->entries e ON (e.id=em.item_id) WHERE em.field_id='{$f_id}'". ( ($user_id) ? " AND user_id='$user_id'" : ''));
                unset($f_id);
                unset($f);
            }
        }
        
        foreach($f_inputs as $f_id => $f){
            $f = array_map('maybe_unserialize', $f);
            $f_inputs[$f_id] = stripslashes_deep($f);
            unset($f_id);
            unset($f);
        }
        
        if(isset($allowed_col_types)){
        //add columns for each field
            foreach($fields as $f_id => $f){
                $cols[$f->name] = array('type' => ((in_array($f->type, $allowed_col_types)) ? $f->type : 'number'), 'id' => $f->id);
                unset($f);
                unset($f_id);
            }
            unset($allowed_col_types);
        }
        
        $field_options = maybe_unserialize($field->options);
        $field->field_options = maybe_unserialize($field->field_options);
        
        global $frm_posts;
        
        if($user_id){
            $form_posts = $frmdb->get_records($frmdb->entries, array('form_id' => $field->form_id, 'post_id >' => 1, 'user_id' => $user_id), '', '', 'id,post_id');
        }else if($frm_posts and isset($frm_posts[$field->form_id])){
            $form_posts = $frm_posts[$field->form_id];
        }else{
            $form_posts = $frmdb->get_records($frmdb->entries, array('form_id' => $field->form_id, 'post_id >' => 1), '', '', 'id,post_id');
            
            $frm_posts = array($field->form_id => $form_posts);
        }
       
        if(!empty($form_posts)){
            if(isset($field->field_options['post_field']) and $field->field_options['post_field'] != ''){
                if($field->field_options['post_field'] == 'post_category'){
                    $field_options = FrmProFieldsHelper::get_category_options($field);
                }else if($field->field_options['post_field'] == 'post_custom' and $field->field_options['custom_field'] != ''){
                    //check custom fields
                    foreach($form_posts as $form_post){
                        $meta_value = get_post_meta($form_post->post_id, $field->field_options['custom_field'], true);
                        if($meta_value){
                            if($x_axis)
                                $inputs[] = array('meta_value' => $meta_value, 'item_id' => $form_post->id);
                            else
                                $inputs[] = $meta_value;
                        }
                    }
                }else{ //if field is post field
                    if($field->field_options['post_field'] == 'post_status')
                        $field_options = FrmProFieldsHelper::get_status_options($field);
                    
                    foreach($form_posts as $form_post){
                        $post_value = $wpdb->get_var("SELECT ". $field->field_options['post_field'] ." FROM $wpdb->posts WHERE ID=".$form_post->post_id);
                        if($post_value){
                            if($x_axis)
                                $inputs[] = array('meta_value' => $post_value, 'item_id' => $form_post->id);
                            else
                                $inputs[] = $post_value;
                        }
                    }
                }
            }
        }
        
        if($field->type == 'data'){
            foreach($inputs as $k => $i){
                if(is_numeric($i)){
                    if(is_array($inputs[$k]) and isset($inputs[$k]['meta_value'])){
                        $inputs[$k]['meta_value'] = FrmProFieldsHelper::get_data_value($inputs[$k]['meta_value'], $field, array('truncate' => 'truncate_label'));
                    }else{
                       $inputs[$k] = FrmProFieldsHelper::get_data_value($inputs[$k], $field, array('truncate' => 'truncate_label'));
                    }
                }
                unset($k);
                unset($i);
            }
        }
        
        if(isset($x_inputs) and $x_inputs){
            $x_temp = array();
            foreach($x_inputs as $x_input){
                if($x_field)
                    $x_temp[$x_input['item_id']] = $x_input['meta_value'];
                else
                    $x_temp[$x_input['id']] = $x_input[$x_axis];
            }
            $x_inputs = apply_filters('frm_graph_value', $x_temp, ($x_field ? $x_field : $x_axis), $args);
            
            unset($x_temp);
            unset($x_input);
        }
        
        if($x_axis and $inputs){
            $y_temp = array();
            foreach($inputs as $input)
                $y_temp[$input['item_id']] = $input['meta_value'];
            
            foreach($ids as $f_id){
                if(!isset($f_values[$f_id]))
                    $f_values[$f_id] = array();
                $f_values[$f_id][key($y_temp)] = 0;
                unset($f_id);
            }

            $inputs = $y_temp;
            
            unset($y_temp);
            unset($input);
        }
        
        $inputs = apply_filters('frm_graph_value', $inputs, $field, $args);

        foreach($f_inputs as $f_id => $f){
            $temp = array();
            foreach($f as $input){
                if(is_array($input)){
                    $temp[$input['item_id']] = $input['meta_value'];
                    
                    foreach($ids as $d){
                        if(!isset($f_values[$d][$input['item_id']]))
                            $f_values[$d][$input['item_id']] = 0;
                        
                        unset($d);
                    }
                }else{
                    $temp[] = $input;
                }
                
                unset($input);
            }

            $f_inputs[$f_id] = apply_filters('frm_graph_value', $temp, $fields[$f_id], $args);
            
            unset($temp);
            unset($input);
            unset($f);
        }
        
        
        if (in_array($field->type, array('select', 'checkbox', 'radio', '10radio', 'scale')) and (!isset($x_inputs) or !$x_inputs)){ 
            if($limit == '') $limit = 10;
            $field_opt_count = count($field_options);

            if($field_options){
            foreach ($field_options as $opt_key => $opt){
                $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field->field_options);
                $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);
                $count = 0;
                
                if(empty($opt))
                    continue;
                    
                //$opt = stripslashes_deep($opt);
                
                foreach ($inputs as $in){
                    if (FrmAppHelper::check_selected($in, $field_val)){
                        if($data_type == 'total')
                            $count += $field_val;
                        else
                            $count++;
                    }
                }
                
                $new_val = FrmAppHelper::truncate($opt, $truncate_label, 2);
                
                if($count > 0 or $field_opt_count < $limit or (!$count and $include_zero)){
                    $labels[$new_val] = $new_val;
                    $values[$new_val] = $count;
                }
                unset($count);
                
                foreach($f_inputs as $f_id => $f){
                    
                    foreach($f as $in){
                        if(!isset($f_values[$f_id]))
                            $f_values[$f_id] = array();    

                        if(!isset($f_values[$f_id][$new_val]))
                            $f_values[$f_id][$new_val] = 0;
                            
                        if (FrmAppHelper::check_selected($in, $field_val)){
                            if($data_type == 'total')
                                $f_values[$f_id][$new_val] += $field_val;
                            else
                                $f_values[$f_id][$new_val]++;
                        }
                        
                        unset($in);
                    }
                        
                    unset($f_id);
                    unset($f);
                }
                
            }
            
            if($limit != 10 and count($values) > $limit){
                $ordered_vals = $values;
                arsort($ordered_vals);
                $l_count = 0;
                foreach($ordered_vals as $vkey => $v){
                    $l_count++;
                    if($l_count > $limit){
                        unset($values[$vkey]);
                        unset($labels[$vkey]);
                    }
                    unset($vkey);
                    unset($v);
                }
                unset($l_count);
                unset($ordered_vals);
            }
            
            }
            
            if (!in_array($field->type, array('checkbox', '10radio', 'scale'))) //and count($field_options) == 2
                $pie = true;
            
        }else if ($field->type == 'user_id'){
            $form = $frmdb->get_one_record($frmdb->forms, array('id' => $field->form_id));
            $form_options = maybe_unserialize($form->options);
            $id_count = array_count_values($inputs);
            if ($form->editable and (isset($form_options['single_entry']) and $form_options['single_entry'] and isset($form_options['single_entry_type']) and $form_options['single_entry_type'] == 'user')){
                //if only one response per user, do a pie chart of users who have submitted the form
                $users_of_blog = (function_exists('get_users')) ? get_users() : get_users_of_blog();
                $total_users = count( $users_of_blog );
                unset($users_of_blog);
                $id_count = count($id_count);
                $not_completed = (int)$total_users - (int)$id_count;
                $labels = array(__('Completed', 'formidable'), __('Not Completed', 'formidable'));
                $values = array($id_count, $not_completed);
                $pie = true;
            }else{
                //arsort($id_count);
                foreach ($id_count as $val => $count){
                    $user_info = get_userdata($val);
                    $labels[] = ($user_info) ? $user_info->display_name : __('Deleted User', 'formidable');
                    $values[] = $count;
                }
                if (count($labels) < 10)
                    $pie = true;
            }
        }else{
            if(isset($x_inputs) and $x_inputs){
                $calc_array = array();
                
                foreach ($inputs as $entry_id => $in){
                    $entry_id = (int)$entry_id;
                    if(!isset($values[$entry_id]))
                        $values[$entry_id] = 0;
                        
                    $labels[$entry_id] = (isset($x_inputs[$entry_id])) ? $x_inputs[$entry_id] : ''; 
                    
                    if(!isset($calc_array[$entry_id]))
                        $calc_array[$entry_id] = array('count' => 0);
                        
                    if($data_type == 'total' or $data_type == 'average'){    
                        $values[$entry_id] += (float)$in;
                        $calc_array[$entry_id]['total'] = $values[$entry_id];
                        $calc_array[$entry_id]['count']++;
                    }else{
                        $values[$entry_id]++;
                    }
                    
                    unset($entry_id);
                    unset($in);
                }
                
                if($data_type == 'average'){
                    foreach($calc_array as $entry_id => $calc){
                        $values[$entry_id] = ($calc['total'] / $calc['count']);
                        unset($entry_id);
                        unset($calc);
                    }
                }
                
                $calc_array = array();
                foreach($f_inputs as $f_id => $f){
                    if(!isset($calc_array[$f_id]))
                        $calc_array[$f_id] = array();
                        
                    foreach($f as $entry_id => $in){
                        $entry_id = (int)$entry_id;
                        if(!isset($labels[$entry_id])){
                            $labels[$entry_id] = (isset($x_inputs[$entry_id])) ? $x_inputs[$entry_id] : '';
                            $values[$entry_id] = 0;
                        }
                        
                        if(!isset($calc_array[$f_id][$entry_id]))
                            $calc_array[$f_id][$entry_id] = array('count' => 0);
                            
                        if(!isset($f_values[$f_id][$entry_id]))
                            $f_values[$f_id][$entry_id] = 0;
                        
                        if($data_type == 'total' or $data_type == 'average'){    
                            $f_values[$f_id][$entry_id] += (float)$in;
                            $calc_array[$f_id][$entry_id]['total'] = $f_values[$f_id][$entry_id];
                            $calc_array[$f_id][$entry_id]['count']++;
                        }else{
                            $f_values[$f_id][$entry_id]++;
                        }
                        
                        unset($entry_id);
                        unset($in);
                    }
                    
                    unset($f_id);
                    unset($f);
                }
                
                if($data_type == 'average'){
                    foreach($calc_array as $f_id => $calc){
                        foreach($calc as $entry_id => $c){
                            $f_values[$f_id][$entry_id] = ($c['total'] / $c['count']);
                            unset($entry_id);
                            unset($c);
                        }
                        unset($calc);
                        unset($f_id);
                    }
                }
                unset($calc_array);
                
            }else{
                if(FrmProAppHelper::is_multi($inputs)){
                    $id_count = array();
                    foreach($inputs as $k => $i){
                        foreach((array)$i as $v){
                            if(trim($v) != '')
                                $id_count[] = $v;
                            unset($v);
                        }
                        
                        unset($k);
                        unset($i);
                    }

                    $id_count = array_count_values($id_count);
                }else{
                    $id_count = array_count_values(array_map('strtolower', $inputs));
                }
                arsort($id_count);
                
                $i = 0;
                foreach ($id_count as $val => $count){
                    if ($i < $response_count){
                        if ($field->type == 'user_id'){
                            $user_info = get_userdata($val);
                            $new_val = $user_info->display_name;
                        }else{
                            $new_val = ucwords($val);
                        }
                        $labels[$new_val] = $new_val;
                        $values[$new_val] = $count;
                        
                    }
                    $i++;
                }
                
                foreach($f_inputs as $f_id => $f){
                    $id_count = array_count_values(array_map('strtolower', $f));
                    arsort($id_count);

                    $i = 0;
                    foreach ($id_count as $val => $count){
                        if ($i < $response_count){
                            if ($field->type == 'user_id'){
                                $user_info = get_userdata($val);
                                $new_val = $user_info->display_name;
                            }else{
                                $new_val = ucwords($val);
                            }
                            $position = array_search($new_val, $labels);
                            if(!$position){
                                end($labels);
                                $position = key($labels);
                                $labels[$new_val] = $new_val;
                                $values[$new_val] = 0;
                                
                            }
                            $f_values[$f_id][$new_val] = $count;
                        }
                        $i++;

                    }

                    unset($f_id);
                    unset($f);
                }
                
            }
        }

         if(isset($x_inputs) and $x_inputs){
            $used_vals = $calc_array = array();
            foreach($labels as $l_key => $label){
                if(empty($label) and (!empty($x_start) or !empty($x_end))){
                    unset($values[$l_key]);
                    unset($labels[$l_key]);
                    continue;
                }
                
                if(in_array($x_axis, array('created_at', 'updated_at'))){
                    if($type == 'pie')
                        $labels[$l_key] = $label = $inputs[$l_key]; 
                    else
                        $labels[$l_key] = $label = date('Y-m-d', strtotime($label)); 
                }
                 
                if(isset($used_vals[$label])){
                    $values[$l_key] += $values[$used_vals[$label]];
                    unset($values[$used_vals[$label]]);
                    
                    foreach($ids as $f_id){
                        if(!isset($f_values[$f_id][$l_key]))
                            $f_values[$f_id][$l_key] = 0;
                        if(!isset($f_values[$f_id][$used_vals[$label]]))
                            $f_values[$f_id][$used_vals[$label]] = 0;
                            
                        $f_values[$f_id][$l_key] += $f_values[$f_id][$used_vals[$label]];
                        unset($f_values[$f_id][$used_vals[$label]]);
                        unset($f_id);
                    }
                    
                    unset($labels[$used_vals[$label]]);
                }
                $used_vals[$label] = $l_key;
                
                if($data_type == 'average'){
                    if(!isset($calc_array[$label]))
                        $calc_array[$label] = 0;
                    $calc_array[$label]++;
                }
                
                unset($label);
                unset($l_key);
            }
            
            if(!empty($calc_array)){
                foreach($calc_array as $label => $calc){
                    if(isset($used_vals[$label])){
                        $values[$used_vals[$label]] = ($values[$used_vals[$label]] / $calc);
                        
                        foreach($ids as $f_id){
                            $f_values[$f_id][$used_vals[$label]] = ($f_values[$f_id][$used_vals[$label]] / $calc);
                            unset($f_id);
                        }
                    }
                    
                    unset($label);
                    unset($calc);
                }
            }
            unset($used_vals);
        }
        
        $combine_dates = false;
        if((isset($x_field) and $x_field and $x_field->type == 'date') or in_array($x_axis, array('created_at', 'updated_at')))
            $combine_dates = apply_filters('frm_combine_dates', true, $x_field);
            
        if($combine_dates){
            if($include_zero){
                $start_timestamp = (empty($x_start)) ? time() : strtotime($x_start);
                $end_timestamp = (empty($x_end)) ? time() : strtotime($x_end);
                $dates_array = array();
                
                // Get the dates array
                for($e = $start_timestamp; $e <= $end_timestamp; $e += 60*60*24)
                    $dates_array[] = date('Y-m-d', $e);
                
                unset($e);

                // Add the zero count days
                foreach($dates_array as $date_str){
                    if(!in_array($date_str, $labels)){
                        $labels[$date_str] = $date_str;
                        $values[$date_str] = 0;
                        foreach($ids as $f_id){
                            if(!isset($f_values[$f_id][$date_str]))
                                $f_values[$f_id][$date_str] = 0;
                        }
                    }
                }
                
                unset($dates_array);
                unset($start_timestamp);
                unset($end_timestamp);
            }
            
            asort($labels);
            
            global $frmpro_settings;
            foreach($labels as $l_key => $l){
                if((isset($x_field) and $x_field and $x_field->type == 'date') or in_array($x_axis, array('created_at', 'updated_at'))){
                    if ($type != 'pie' and preg_match('/^\d{4}-\d{2}-\d{2}$/', $l)){ 
                        global $frmpro_settings;
                        $labels[$l_key] = FrmProAppHelper::convert_date($l, 'Y-m-d', $frmpro_settings->date_format);
                    }
                }
                unset($l_key);
                unset($l);
            }
            
            $values = FrmProAppHelper::sort_by_array($values, array_keys($labels));
            foreach($ids as $f_id){
                $f_values[$f_id] = FrmProAppHelper::sort_by_array($f_values[$f_id], array_keys($labels));
                $f_values[$f_id] = FrmProAppHelper::reset_keys($f_values[$f_id]);
                ksort($f_values[$f_id]);
                unset($f_id);
            }
        }else{
            if(isset($x_inputs) and $x_inputs){
                foreach($labels as $l_key => $l){
                    foreach($ids as $f_id){
                        //do a last check to make sure all bars/lines have a value for each label
                        if(!isset($f_values[$f_id][$l_key]))
                            $f_values[$f_id][$l_key] = 0;
                        unset($fid);
                    }
                    unset($l_key);
                    unset($l);
                }
            }
            
            foreach($ids as $f_id){
                $f_values[$f_id] = FrmProAppHelper::reset_keys($f_values[$f_id]);
                ksort($f_values[$f_id]);
                unset($f_id);
            }
            
            ksort($labels);
            ksort($values);
        }
        
        $labels = FrmProAppHelper::reset_keys($labels);
        $values = FrmProAppHelper::reset_keys($values);
        
        $return = array('total_count' => count($inputs), 'f_values' => $f_values, 'labels' => $labels, 
            'values' => $values, 'pie' => $pie, 'combine_dates' => $combine_dates, 'ids' => $ids, 'cols' => $cols, 
            'rows' => $rows, 'options' => $options, 'fields' => $fields
        );
        
        if(isset($x_inputs))
            $return['x_inputs'] = $x_inputs;
        
        return $return;
    }
    
    function convert_to_google($rows, $cols, $options, $type){
        $gcontent = '';
        if(!empty($cols)){
            foreach((array)$cols as $col_name => $col){
                $gcontent .= "data.addColumn('". $col['type'] ."','". addslashes($col_name) ."');";
                unset($col_name);
                unset($col);
            }
        }
        
        if(!empty($rows)){
            if($type == 'table'){
                $last = end($rows);
                $count = $last[0]+1;
                $gcontent .= "data.addRows($count);\n";
                
                foreach($rows as $row){
                    $gcontent .= "data.setCell(". implode(',', $row). ");"; //data.setCell(0, 0, 'Mike');
                    unset($row);
                }
            }else{
                $gcontent .= "data.addRows(". json_encode($rows). ");\n";
            }
        }
        
        if(!empty($options))
            $gcontent .= "var options=". json_encode($options) ."\n";

        return compact('gcontent', 'type');
    }
    
    function get_daily_entries($form, $opts=array(), $type="DATE"){
        global $wpdb, $frmdb;
        
        $options = array();
        if(isset($opts['colors']))
            $options['colors'] = explode(',', $opts['colors']);
          
        if(isset($opts['bg_color']))  
            $options['backgroundColor'] = $opts['bg_color'];
        
        $type = strtoupper($type); 
        //Chart for Entries Submitted
        $values = $labels = array();
        if($type == 'HOUR'){
            $start_timestamp = strtotime('-48 hours');
            $title =  __('Hourly Entries', 'formidable');
        }else if($type == 'MONTH'){
            $start_timestamp = strtotime('-1 year');
            $title =  __('Monthly Entries', 'formidable');
        }else if($type == 'YEAR'){
            $start_timestamp = strtotime('-10 years');
            $title =  __('Yearly Entries', 'formidable');
        }else{
            $start_timestamp = strtotime('-1 month');
            $title =  __('Daily Entries', 'formidable');
        }
        $end_timestamp = time();
        
        if($type == 'HOUR'){
            $query = "SELECT en.created_at as endate,COUNT(*) as encount FROM $frmdb->entries en WHERE en.created_at >= '".date("Y-n-j H", $start_timestamp).":00:00' AND en.form_id=$form->id GROUP BY endate";
        }else{
            $query = "SELECT DATE(en.created_at) as endate,COUNT(*) as encount FROM $frmdb->entries en WHERE en.created_at >= '".date("Y-n-j", $start_timestamp)." 00:00:00' AND en.form_id=$form->id GROUP BY $type(en.created_at)";
        }

        $entries_array = $wpdb->get_results($query);

        $temp_array = $counts_array = $dates_array = array();

        // Refactor Array for use later on
        foreach($entries_array as $e){
            $e_key = $e->endate;
            if($type == 'HOUR')
                $e_key = date('Y-m-d H', strtotime($e->endate)) .':00:00';
            else if($type == 'MONTH')
                $e_key = date('Y-m', strtotime($e->endate)) .'-01';
            else if($type == 'YEAR')
                $e_key = date('Y', strtotime($e->endate)) .'-01-01';
            $temp_array[$e_key] = $e->encount;
        }
        
        // Get the dates array
        if($type == 'HOUR'){
            for($e = $start_timestamp; $e <= $end_timestamp; $e += 60*60){
                if(!in_array(date('Y-m-d H', $e) .':00:00' , $dates_array))
                    $dates_array[] = date('Y-m-d H', $e) .':00:00';
            }
        
            $date_format = get_option('time_format');
        }else if($type == 'MONTH'){
            for($e = $start_timestamp; $e <= $end_timestamp; $e += 60*60*24*25){
                if(!in_array(date('Y-m', $e) .'-01', $dates_array))
                    $dates_array[] = date('Y-m', $e) .'-01';
            }
        
            $date_format = 'F Y';
        }else if($type == 'YEAR'){
            for($e = $start_timestamp; $e <= $end_timestamp; $e += 60*60*24*364){
                if(!in_array(date('Y', $e) .'-01-01', $dates_array))
                    $dates_array[] = date('Y', $e) .'-01-01';
            }
        
            $date_format = 'Y';
        }else{
            for($e = $start_timestamp; $e <= $end_timestamp; $e += 60*60*24)
                $dates_array[] = date("Y-m-d", $e);

            $date_format = get_option('date_format');
        }
        
        // Make sure counts array is in order and includes zero click days
        foreach($dates_array as $date_str){
          if(isset($temp_array[$date_str]))
              $counts_array[$date_str] = $temp_array[$date_str];
          else
              $counts_array[$date_str] = 0;
        }
        
        $rows = array();
        $max = 3;
        foreach ($counts_array as $date => $count){
            $rows[] = array(date_i18n($date_format, strtotime($date)), (int)$count);
            if((int)$count > $max)
                $max = $count+1;
            unset($date);
            unset($count);
        }
          
        if (empty($rows))
            return;
            
        $options['title'] = $title;
        $options['legend'] = 'none';
        $cols = array('xaxis' => array('type' => 'string'), __('Count', 'formidable') => array('type' => 'number'));
        
        $options['vAxis'] = array('maxValue' => $max, 'minValue' => 0);
        $options['hAxis'] = array('slantedText' => true, 'slantedTextAngle' => 20);
        
        $height = 400;
        $width = '100%';
        
        $options['height'] = $height;
        $options['width'] = $width;  
        
        $graph = $this->convert_to_google($rows, $cols, $options, 'line');
        
        $html = $js = $js_content2 = '';
        
        global $frm_google_chart;
        $js_content = '<script type="text/javascript">';
        if(!$frm_google_chart){
            $js_content = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
            $js_content .= '<script type="text/javascript">';
            $js_content .= "google.load('visualization','1.0',{'packages':['corechart']});\n";
            $frm_google_chart = true;
        }
            
        $this_id = $form->id .'_'. strtolower($type);
        $html .= '<div id="chart_'. $this_id .'" style="height:'. $height .';width:'. $width .'"></div>';
        $js_content2 .= "google.setOnLoadCallback(get_data_{$this_id});\n";
        $js_content2 .= "function get_data_{$this_id}(){var data=new google.visualization.DataTable();";
        $js_content2 .= $graph['gcontent'];
        $js_content2 .= "var chart=new google.visualization.". ucfirst($graph['type']) ."Chart(document.getElementById('chart_{$this_id}')); chart.draw(data, options);}";  
        
            
        $js_content .= $js . $js_content2;
        $js_content .= '</script>';
        
        return $js_content . $html;
    }
    
    function graph_shortcode($atts){
        $type = isset($atts['type']) ? $atts['type'] : 'default';

        $defaults = array(
            'id' => false, 'id2' => false, 'id3' => false, 'id4' => false, 'ids' => false,
            'include_js' => true, 'colors' => '', 'grid_color' => '#CCC', 'is3d' => false,
            'height' => 400, 'width' => 400, 'truncate_label' => 7,
            'bg_color' => '#FFFFFF', 'truncate' => 40, 'response_count' => 10, 'user_id' => false, 
            'type' => 'default', 'x_axis' => false, 'data_type' => 'count', 'limit' => '',
            'x_start' => '', 'x_end' => '', 'show_key' => false, 'min' => '', 'max' => '',
            'include_zero' => false, 'field' => false
        );
        
        if($type == 'geo'){
            $defaults['truncate_label'] = 100;
            $defaults['width'] = 600;
        }
        
        extract(shortcode_atts($defaults, $atts));
        
        if (!$id) return;
        global $frm_field, $frm_google_chart;
        
        if(!$ids and ($id2 or $id3 or $id4)){
            $ids = array($id2, $id3, $id4);
            $ids = implode(',', $ids);
        }
 
        $x_axis = (!$x_axis or $x_axis == 'false') ? false : $x_axis;
        
        $user_id = FrmProAppHelper::get_user_id_param($user_id);
        
        $html = $js = $js_content2 = '';
        
        if(is_object($field))
            $fields = array($field);
        else
            $fields = $frm_field->getAll("fi.id in ($id)");
            
        if(!empty($colors))
            $colors = explode(',', $colors);
        
        $js_content = '<script type="text/javascript">';
        if($include_js and !$frm_google_chart){
            $js_content = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
            $js_content .= '<script type="text/javascript">';
            $js_content .= "google.load('visualization', '1.0', {'packages':['". ($type == 'geo' ? 'geochart' : 'corechart')."']});\n";
            if($type != 'geo')
                $frm_google_chart = true;
        }else if($type == 'geo'){
            $js_content .= "google.load('visualization', '1', {'packages': ['geochart']});\n";
        }
        
        global $frm_gr_count;
        if(!$frm_gr_count)
            $frm_gr_count = 0;
        foreach ($fields as $field){
            $data = $this->get_google_graph($field, compact('ids', 'colors', 'grid_color', 'bg_color', 'is3d', 'truncate', 'truncate_label', 'response_count', 'user_id', 'type', 'x_axis', 'data_type', 'limit', 'x_start', 'x_end', 'show_key', 'min', 'max', 'include_zero', 'width', 'height'));
            
            $frm_gr_count++;
            $this_id = $field->id .'_'. $frm_gr_count;
            $html .= '<div id="chart_'. $this_id .'" style="height:'.$height.';width:'.$width.'"></div>';
            $js_content2 .= "google.setOnLoadCallback(get_data_{$this_id});\n";
            $js_content2 .= "function get_data_{$this_id}(){var data=new google.visualization.DataTable();";
            $js_content2 .= $data['gcontent'];
            $js_content2 .= "var chart=new google.visualization.". ucfirst($data['type']) ."Chart(document.getElementById('chart_{$this_id}')); chart.draw(data, options);}";  
        }
            
        $js_content .= $js . $js_content2;
        $js_content .= '</script>';
        
        return $js_content . $html;
    }
    
    
    /**
	 * Returns stats requested through the [frm-stats] shortcode
	 *
	 * @param array $atts 
	 */
    function stats_shortcode($atts){
        $defaults = array(
            'id' => false, //the ID of the field to show stats for
            'type' => 'total', //total, count, average, median, deviation, star, minimum, maximum
            'user_id' => false, //limit the stat to a specific user id or "current"
            'value' => false, //only count entries with a specific value
            'round' => 100, //how many digits to round to
            'limit' => '' //limit the number of entries used in this calculation
            //any other field ID in the form => the value it should be equal to
            //'entry_id' => show only for a specific entry ie if you want to show a star rating for a single entry
            
        );

        extract(shortcode_atts($defaults, $atts));
        if (!$id) return;
        
        $user_id = FrmProAppHelper::get_user_id_param($user_id);
        
        foreach($defaults as $unset => $val)
            unset($atts[$unset]);
            
        return FrmProFieldsHelper::get_field_stats($id, $type, $user_id, $value, $round, $limit, $atts);
    }
    
}
