<?php
/*
Copyright (C) License Management Schiavon Maikol

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_action( 'wp_enqueue_scripts', 'license_management_get_content_html_list');
add_action( 'wp_enqueue_scripts', 'license_management_get_content_html_detail');

wp_enqueue_style( 'bootstrap-license', plugins_url( 'license-management', 'license-management' ).'/lib/bootstrap/css/bootstrap.min.css',false,'1.0','all');
wp_enqueue_style( 'fontawesome-license', plugins_url( 'license-management', 'license-management' ).'/lib/fontawesome-free-5.3.1/css/all.css',false,'1.0','all');

wp_enqueue_script('cs_functions_js',true);
wp_localize_script('cs_functions_js', 'langvars', license_management_get_translation_for_js());

function license_management_get_content_html_list($status){
    $customer_portal = new license_management_customer_portal_html;

    $html = $customer_portal->get_js();
    $html .= "<div id='license_management_customerportal'>";
    $data_by_userid = array();
    $userid = get_current_user_id();
    
    $pageid = $customer_portal->license_management_get_pageid();
    $page_permalink = get_permalink($pageid);

    if($userid > 0){
        $data_by_userid = $customer_portal->get_records($userid,"","license_plate",$status);
       
        if(!empty($data_by_userid)){

            $block_status = $customer_portal->get_num_service_user($userid,false);
            $content_deadline = $customer_portal->get_records($userid,"","","",true);
            $n_deadline = count($content_deadline);

            $html .= "<div class='container-fluid'>";
            $html .= $customer_portal->getBlock("your_transport");
            $html .= $customer_portal->getBlock("status",$block_status);
            $html .= $customer_portal->getBlock("deadlines",array("n_deadline"=>$n_deadline));
            $html .= "</div>";

            if(!empty($status)){
                $html .= "<div class='container'>";
                $html .= "<div class='row'><a class='btn btn-default' href='".$page_permalink."'><i class='glyphicon glyphicon-menu-left'></i> ".__('LBL_VIEW_ALL', 'license-management')."</a></div>";
                $html .= "</div>";
            }

            $html .= "<div class='container-fluid container-list'>";
            $html .= "<div class='row'>";

            $html .= "<div class='col-xs-12'>";

                $html .= "<div class='collapse' id='collapseYourTransport'><div class='well'>";
                foreach($data_by_userid as $row){
                    if(isset($row->licenseid)){
                        
                        $row_id = $row->licenseid;
                        $license_plate = $row->license_plate;
                        $messages_license = $row->messages_license;

                        $params = array("id"=>$row_id);
                        $url = $customer_portal->generate_link($params);

                        if($customer_portal->show_message_for_license){
                            $html .= "<div class='col-xs-12 col-license show-message'>";
                            $html .= "<div class='col-sm-6 col-xs-12'>";
                            $html .= "<span>".__('License Plate', 'license-management')."</span>";
                            $html .= "<a href='$url'>$license_plate</a>";
                            $html .= "</div>";
                            $html .= "<div class='col-sm-6 col-xs-12'>";
                            if(!empty($messages_license)){
                                $html .= "<span>".__('Messages', 'license-management')."</span>";
                                $html .= "<h6 class='message'>$messages_license</h6>"; }
                            $html .= "</div>";
                            $html .= "</div>";
                        }
                        else{
                            $html .= "<div class='col-xs-12'><div class='col-license'>";
                            $html .= "<span>".__('License Plate', 'license-management')."</span>";
                            $html .= "<a href='$url'>$license_plate</a>";
                            $html .= "</div></div>";
                        }
                    }
                }
                $html .= "</div></div></div>";

                $html .= "<div class='collapse' id='collapseDeadline'><div class='well'>";

                if(!empty($content_deadline)){
                    
                    //$lbl_deadline =  $customer_portal->timestamp_to_date_italian("",true);

                    $html .= "<div class='col-xs-12 container-deadline'>";
                    // $html .= "<div class='row'><div class='col-xs-12'><h5>".__('LBL_DEADLINE_OF', 'license-management')." ".$lbl_deadline."</h5></div></div>";
                    $html .= "<div class='row'><div class='col-xs-12'><h5>".__('LBL_DEADLINE_60_DAYS', 'license-management')."</h5></div></div>";
                    $html .= "<div class='row'><ul class='timeline'>";

                    foreach($content_deadline as $row){
                        $date_end = $row->date_end;
                        $license_plate = $row->license_plate;
                        $service_name = $row->service_name;
                        $messages_service = $row->messages_service;
                        
                        if($customer_portal->date_view_timeline == 'daymonth'){
                            $date_view = $customer_portal->timestamp_to_date_italian($date_end,false);
                        }else{
                            $date_view = date('d',strtotime($date_end));
                            $date_month = $customer_portal->timestamp_to_date_italian($date_end,true);
                        }
                        $date_end = date("d-m-Y",strtotime($date_end));

                        $html .= '
                            <li class="timeline-inverted">
                                <div class="timeline-badge warning">'.$date_view.'</div>
                                <div class="timeline-badge month"><span>'.$date_month.'</span></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                    <label>'.__("License Plate","license-management").'</label>
                                    <h4 class="timeline-title">'.$license_plate.'</h4>
                                    </div>
                                    <div class="timeline-body">
                                    <h4>'.$service_name.'</h4>';
                        if(!empty($messages_service)){
                            $html .= "<label>".__('Messages','license-management')."</label><br>";
                            $html .= '<h6 class="message"> '.$messages_service.'</h6>';
                        }
                        $html .= '<h6 class="text-right"> '.$date_end.'</h6>
                                    </div>
                                </div>
                            </li>';
                    }
                    $html .= "</ul> </div></div>";
                }

            $html .= "</div></div>";

            $html .= "</div>"; // row

            $html .= "<div class='row'>";
            $html .= "<div class='col-sm-12'>";

            $params_chart = $block_status;
            $params_chart["deadline"] = $n_deadline;
            $html .= "<div class='col-xs-12 container-chart'>";
            $num_service_user = json_encode($params_chart);
            $html .= "<center><canvas id='statusChart' width='500' height='500'></canvas> <input type='hidden' id='n_servide_data' value='$num_service_user'></center>";
            $html .= "</div>";

            if($customer_portal->communication_view){
                $data_messages = $customer_portal->get_records($userid,"","",false,false,true);

                if(!empty($data_messages)){
                $html .= "<div class='col-xs-12 container-messages'>";
                $html .= "<div class='row'><div class='col-xs-12'><b>".__('lbl_communication','license-management')." ".get_bloginfo('name')."</b></div></div>";
                foreach($data_messages as $row){

                    $messages_service = $row->messages_service;
                    $createduserid = $row->createduserid;
                    $service_name = $row->service_name;
                    $license_plate = $row->license_plate;
                    
                    if(!empty($messages_service)){
                    $html .=  '<div>';
                    $html .= '<div class="row row-lbl"><h4 class="timeline-title col-license">'.$license_plate.'</h4>';
                    $html .= '<p> <i class="fa fa-map-signs"></i> '.$service_name.'</p></div>';
                    $html .= $customer_portal->get_message_html($createduserid,$messages_service);
                    $html .=  '</div>';
                    }
                }
                $html .= "</div>";
                }
            }

            $html .= "</div>";            
            $html .= "</div>"; // container

        }else{
            $html = "<h3>".__('LBL_NOT_LICENSE_FOR_USER', 'license-management')."</h3>";
        }
    }else{
        $html = "<h3>".__('LBL_LOGIN_ACCOUNT', 'license-management')."</h3>";
    }

    $html .= "</div>";

    return $html;
}

function license_management_get_content_html_detail($id = '',$status = ''){
    $html = "";

    if( (!empty($id) && $id > 0) || (!empty($status) && $status > 0) ){
        $customer_portal = new license_management_customer_portal_html;
        $userid = get_current_user_id();
        $service_status_color = $customer_portal->service_status_color;
        $records = $customer_portal->get_records($userid,$id,"",$status);

        $pageid = $customer_portal->license_management_get_pageid();

        require_once "utils/license-management-dashboard-utils.php";
        $tool_dachboardUtils = new DashboardUtils;

        $records = $tool_dachboardUtils->get_license_service($records);
        
        $html = "<div class='container' id='license_management_customerportal'>";

        if(!empty($records)){
            
            foreach($records as $licenseid => $value){
                $license_plate = $value["license_plate"];
                $services = $value["services"];

                /*if(!empty($status)){
                    $html .= "<h3> ".__('License Plate','license-management').": $license_plate</h3> <br>";
                }*/

                foreach($services as $serviceid=>$services_val){
                    $lbl_status = "";
                    $color1 = $color2 = $color3 = "#e2e2e2";
                    $date_view = false;

                    $service_status = $services_val[0];
                    $services_name = $services_val[1];
                    $date_end = $services_val[2];
                    $messages = $services_val[3];
                    $createduserid = $services_val[4];
                   
                    if(!empty($date_end) && strtotime($date_end) > 0 && ($customer_portal->view_date_only_service == 0 || $customer_portal->view_date_only_service == $service_status) ){
                        $date_view = true;
                        $date_end = date("d-m-Y", strtotime($date_end));
                    }
            
                    if($service_status == 1){
                        $color1 = $service_status_color[$service_status][0];
                        $lbl_status =  __('status_1', 'license-management');
                        $lbl_status_color = $color1;
                    }
                    if($service_status == 2){
                        $color2 = $service_status_color[$service_status][0];
                        $lbl_status =  __('status_2', 'license-management');
                        $lbl_status_color = $color2;
                    }
                    if($service_status == 3){
                        $color3 = $service_status_color[$service_status][0];
                        $lbl_status =  __('status_3', 'license-management');
                        $lbl_status_color = $color3;
                    }

                    $html .= "<div class='row row-license'>";
                    $html .= "<div class='col-xs-12 col-container'>";
                    $html .= "<div class='col-md-6 col-sm-12'>";
                    $html .= "<label>".__('License Plate','license-management')."</label>";
                    $html .= "<h4 class='no-margin'> $license_plate</h4>";
                    $html .= "<h4>$services_name</h4>";
                    $html .= "<div class='container-status'>";
                    $html .= "<div class='block-status' style='background:$color1'></div>";
                    $html .= "<div class='block-status' style='background:$color2'></div>";
                    $html .= "<div class='block-status' style='background:$color3'></div>";
                    $html .= "<h5 style='color:$lbl_status_color'>$lbl_status</h5>";
                    if($date_view){
                    $html .= "<div class='row'> <div class='col-xs-6'> <h6>".__('LBL_DATE_END','license-management')."</h6> </div> <div class='col-xs-6'><h6>$date_end</h6> </div> </div>";
                    }
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "<div class='col-md-6 col-sm-12'>";
                    if(!empty($messages)){
                        $html .= "<div class='row row-message'>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<span class='box-lbl'>".__('Messages','license-management')."</span>";
                        $html .= "</div>";

                        $html .= $customer_portal->get_message_html($createduserid,$messages);

                        $html .= "</div>";
                    }
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
            
            $html .= "<div class='row call-back'><a class='btn btn-default' href='".get_permalink($pageid)."'><i class='glyphicon glyphicon-menu-left'></i> <span>".__('LBL_BACK', 'license-management')."</span></a></div>";
        }else{

            $pageid = $customer_portal->license_management_get_pageid();
            $page_permalink = get_permalink($pageid);

            if(!empty($status)){
                $html .= "<h3>".__('LBL_NOT_LICENSE_SELECT_STATUS', 'license-management')."</h3>";
            }else{
                $html .= "<h2>".__('LBL_NOT_SERVICE_FOR_LICENSE', 'license-management')."</h2>";
            }
            $html .= "<div class='row call-back'><a class='btn btn-default' href='".$page_permalink."'><i class='glyphicon glyphicon-menu-left'></i> <span>".__('LBL_BACK', 'license-management')."</span></a></div>";
        }
        $html .= "</div>";
    }
    return $html;
}

class license_management_customer_portal_html{
    var $service_status_color = array(array(),array("#F44336","da_prenotare"),array("#ffce56","in_lavorazione"),array("#4CAF50","completato"));
    var $link_block_status = "detail";
    /*
        link_block_status
        Detail = view detail only single license and service for status
        List   = view only license for status
    */

    var $view_date_only_service = 3;
    var $date_view_timeline = 'day'; // 'daymonth';
    var $communication_view = false;
    var $show_message_for_license = true;

    public function generate_link($params){
        
        $pageid = $this->license_management_get_pageid();
        $link = get_permalink(203);

        $separator = "?";
        if (strpos($link, '?') !== false) {
            $separator = "&";
        }

        if(isset($params["id"])){
            $link .= $separator."id=".$params["id"];
        }

        if(isset($params["status"])){
            $link .= $separator."status=".$params["status"];
        }

        if(!empty($this->link_block_status)){
            $link .= "&action=".$this->link_block_status;
        }

        return $link;
    }

    public function getBlock($type,$params = ""){
        $html = "";
        if($type == "status"){
            $status_license_plate = array(
                            1 => array( "lbl_status"=>__('status_1', 'license-management'),"css_id"=>"headingOne","css_class"=>"licenses-tab status1","css_class_service"=>"tab_n_service_1"), 
                            2 => array( "lbl_status"=>__('status_2', 'license-management'),"css_id"=>"headingTwo","css_class"=>"licenses-tab status2","css_class_service"=>"tab_n_service_2"),
                            3 => array( "lbl_status"=>__('status_3', 'license-management'),"css_id"=>"headingThree","css_class"=>"licenses-tab status3","css_class_service"=>"tab_n_service_3")
                            );

            if(!empty($params)){
                foreach($params as $params_key => $num_service){
                    $css_id = $status_license_plate[$params_key]["css_id"];
                    $css_class = $status_license_plate[$params_key]["css_class"]." ".$status_license_plate[$params_key]["css_class_service"];

                    $params_link = array("status"=>$params_key);
                    $params_block_html = array("css_id"=>$css_id,"css_class"=>$css_class,"h2"=>$num_service,"h4"=>$status_license_plate[$params_key]["lbl_status"],"params_link"=>$params_link);
                    $html .= $this->getBlockHTML($params_block_html);
                }
            }
        }
        elseif($type == "your_transport"){
            $params_block_html = array("css_id"=>"","css_class"=>"licenses-tab tab_your_transpor tab_your_transport","h2"=>"<i class='fa fa-truck'></i>","h4"=>__('lbl_your_transport', 'license-management'),"params_link"=>"","col"=>"col-md-4 col-sm-6 col-sm-offset-0 col-xs-12 col-xs-offset-0");
            $html = $this->getBlockHTML($params_block_html,"collapseYourTransport");
        }
        elseif($type == "deadlines"){
            $n_deadline = $params["n_deadline"];
            $params_block_html = array("css_id"=>"","css_class"=>"licenses-tab tab_deadlines tab_deadlines","h2"=>$n_deadline,"h4"=>__('lbl_deadlines', 'license-management'),"params_link"=>"");
            $html = $this->getBlockHTML($params_block_html,"collapseDeadline");
        }
    
        return $html;
    }

    public function getBlockHTML($params,$collaspe = false){
        $html = "";
        $col = "col-lg-2 col-md-4 col-sm-6 col-xs-12";

        $css_id = $params["css_id"];
        $css_class = $params["css_class"];
        $h2 = $params["h2"];
        $h4 = $params["h4"];       
        if(isset($params["col"])){ $col = $params["col"]; }

        if($collaspe){
            $html .= "<a class='' role='button' data-toggle='collapse' href='#$collaspe' aria-expanded='false' aria-controls='$collaspe'>"; 
        }else{
            $params_link = $params["params_link"];
            $link = $this->generate_link($params_link);

            $html .= "<a href='$link'>";
        }
        $html .= "<div class='$col block'>";
        $html .= "<div id='$css_id' class='col-xs-12 $css_class'>";
        $html .= "<h2>".$h2."</h2> <h4>".$h4."</h4>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</a>";

        return $html;
    }

    function get_records($userid,$id = "",$groupby = "",$status = "",$deadline = false, $messages = false){
        global $wpdb;
       
        if(!empty($userid)){
            $userid = get_current_user_id();
        }
    
        $table_name_license = $wpdb->prefix . "license_management_license";
        $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
        $table_name_service = $wpdb->prefix . "license_management_service";
        $table_name_service_rel = $wpdb->prefix . "license_management_service_rel";
    
        $sql = "SELECT $table_name_license.id licenseid, $table_name_enterprise.id enterpriseid, license_plate, userid, {$table_name_license}.messages messages_license
                FROM $table_name_license
                INNER JOIN $table_name_enterprise ON $table_name_enterprise.id = $table_name_license.enterpriseid
                WHERE $table_name_enterprise.deleted = 0 and $table_name_license.deleted = 0";
        $sql .= " AND userid = ".$userid;

        if(!empty($id) || !empty($status) || $deadline || $messages){
            $sql = "SELECT $table_name_license.id licenseid, $table_name_enterprise.id enterpriseid, license_plate, userid, {$table_name_service_rel}.service_status, {$table_name_service_rel}.messages messages_service, 
                        $table_name_service.id serviceid, $table_name_service.service_name,
                        $table_name_enterprise.business_name, date_end, {$table_name_service_rel}.createduserid
                        FROM $table_name_license
                        INNER JOIN $table_name_enterprise ON $table_name_enterprise.id = $table_name_license.enterpriseid
                        INNER JOIN $table_name_service_rel ON  $table_name_service_rel.licenseid = $table_name_license.id
                        INNER JOIN $table_name_service ON $table_name_service_rel.serviceid = $table_name_service.id
                        WHERE $table_name_enterprise.deleted = 0 and $table_name_license.deleted = 0 AND $table_name_service.deleted = 0 AND $table_name_service_rel.deleted = 0 ";
             $sql .= " AND userid = ".$userid;

            if(!empty($id)){
                $sql .= " AND $table_name_license.id = $id";
            }
            elseif(!empty($status)){
                $sql .= " AND $table_name_service_rel.service_status = ".$status." ";
            }
            elseif($deadline){
                $deadline = date('Y-m-d', strtotime("+2 months", strtotime(date("Y-m-d"))));

                $sql .= " AND {$table_name_service_rel}.service_status <> 2 AND DATE({$table_name_service_rel}.date_end) <= '$deadline' AND DATE({$table_name_service_rel}.date_end) <> '0000-00-00' ORDER BY {$table_name_service_rel}.date_end ";
            }

            if(!empty($groupby) && $groupby == "license_plate"){
                $sql .= " GROUP BY license_plate ";
            }

            if($messages){
                $sql .= " ORDER BY license_plate ";
            }

        }
    
        if(!empty($groupby) && $groupby == "status"){
            $sql = "SELECT count($table_name_license.id), $table_name_service_rel.service_status
                FROM $table_name_license 
                INNER JOIN $table_name_enterprise ON $table_name_enterprise.id = $table_name_license.enterpriseid 
                INNER JOIN $table_name_service_rel ON $table_name_service_rel.licenseid = $table_name_license.id 
                INNER JOIN $table_name_service ON $table_name_service_rel.serviceid = $table_name_service.id
                WHERE $table_name_enterprise.deleted = 0 
                AND $table_name_license.deleted = 0 
                AND $table_name_service.deleted = 0
                AND $table_name_service_rel.deleted = 0
                AND userid = $userid
                GROUP BY $table_name_service_rel.service_status";
        }   

        $rows = $wpdb->get_results($sql);

        return $rows;
    }

    function get_num_service_user($userid,$data_json=false){
        $result = array(1=>0,2=>0,3=>0);
        if(!empty($userid) && intval($userid)){
            global $wpdb;
            $sql = "SELECT count(wp_license_management_service_rel.id) as num, service_status FROM wp_license_management_service_rel
                    INNER JOIN wp_license_management_license ON licenseid = wp_license_management_license.id
                    INNER JOIN wp_license_management_enterprise ON enterpriseid = wp_license_management_enterprise.id
                    WHERE wp_license_management_enterprise.deleted = 0 
                    AND wp_license_management_license.deleted = 0 
                    AND wp_license_management_service_rel.deleted = 0
                    AND userid = $userid group by service_status";
            $rows_values = $wpdb->get_results($sql);
            if(!empty($rows_values)){
                foreach($rows_values as $obj){
                    $status = $obj->service_status;
                    $result[$status] = $obj->num;
                }
            }
        }
    
        if($data_json){
            return json_encode($result);
        }
        
        return $result;
    }

    public function get_contactUs_html(){

        $contact_form_id = 1;

        $html = "<div class='col-md-6 col-sm-12'>";

        $phone = get_post_meta( get_the_ID(), 'license_management_phone', true );
        $html .= "<div class='col-xs-12'> <h2>".__('Contact_us', 'license-management')."</h2> </div>";
        $html .= "<div class='col-xs-12 container-phone'>";
        $html .= "<div class='col-lg-4 col-sm-6 col-xs-12'><h5>".__('LBL_CALL_TO', 'license-management').": </h5></div>";
        $html .= "<div class='col-lg-4 col-sm-6 col-xs-12'><a class='phone' href='tel:$phone'>$phone</a></div>";
        // $html .= "<div class='btn-circle'><a href='tel:$phone'><i class='fa fa-phone'></i></a></div>";
        $html .= "</div>";

        $html .= "<div class='col-xs-12'>";
        $html .= "<a class='btn btn-default' role='button' data-toggle='collapse' href='#collapseLicenseCustomerPortal' aria-expanded='false' aria-controls='collapseLicenseCustomerPortal'>".__('LBL_SEND_MESSAGE', 'license-management')."</a>";
        $html .= "</div>";

        $html .= "</div>";

        $html .= "<div class='col-xs-12'>";
        $html .= "<div class='collapse' id='collapseLicenseCustomerPortal'><div class='well'>";
        $html .= " [contact-form-7 id='$contact_form_id' title='Customer Portal'] </div>";
        $html .= "</div></div>";
        $html .= "</div>";

        return $html;
    }

    public function get_user_display_name($userid){
        $display_name = "";
        if(intval($userid) > 0){
           $user = get_user_by( 'id', $userid );
           if(!empty($user) && isset($user->data)){
                $display_name = $user->data->display_name;
           }
        }
        return $display_name;
    }

    public function get_logo(){
        $logo_img = "";
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if(!empty($custom_logo_id)){
            $logo_img =  wp_get_attachment_url($custom_logo_id);
        }
        return $logo_img;
    }

    public function timestamp_to_date_italian($date,$onlymonth) {       
        $months = array(
                '01' => __('January', 'license-management'),
                '02' => __('February', 'license-management'),
                '03' => __('March', 'license-management'),
                '04' => __('April', 'license-management'),
                '05' => __('May', 'license-management'),
                '06' => __('June', 'license-management'), 
                '07' => __('July', 'license-management'), 
                '08' => __('August', 'license-management'),
                '09' => __('September', 'license-management'), 
                '10' => __('October', 'license-management'), 
                '11' => __('November', 'license-management'),
                '12' => __('December', 'license-management'));
        if($onlymonth){
            if(empty($date)){
                $date = date('m', strtotime("+1 months", strtotime(date("Y-m-d"))));
            }else{
                $date = date('m', strtotime($date));
            }
            return $months[$date];
        }else{
            list($day, $month, $year) = explode('-',date('d-m-Y', strtotime($date)));
            return $day . ' ' . $months[$month];
        }
    }

    public function get_message_html($userid,$messages){
        $html = "";

        if(!empty($messages)){
            $user_display_name = $this->get_user_display_name($userid);
            $logo_img = $this->get_logo();

            $html .= "<div class='col-xs-12'>";
            $html .= "<span class='box-user'>".$user_display_name."</span>";
            $html .= "<h6>$messages</h6>";
            $html .= "</div>";
            if(!empty($logo_img)){
                $html .= "<div class='col-lg-2 col-md-4 col-sm-4 col-xs-4'>";
                $html .= "<img src='$logo_img' style='width: 50px;height: 50px;' class='img-circle img-responsive'>";
                $html .= "</div>";
            }
        }

        return $html;
    }

    public function get_js(){
        $html = "<script>
                jQuery( document ).ready(function($) {
                    if (typeof(jQuery('#message_welcome').html()) != undefined){
                        setTimeout(function(){ jQuery('#message_welcome').hide(); }, 2000);
                    }
                });
                </script>";
        return $html;
    }

    public function license_management_get_pageid(){
        global $wpdb;
        $pageid = 0;
    
        $sql = "SELECT id FROM wp_posts WHERE post_content LIKE '%[license-management-view]%' AND post_parent = 0";
        $rows = $wpdb->get_results($sql);
        if(!empty($rows)){
            foreach ($rows as $row) {
                $pageid = $row->id;
            }
        }
    
        return $pageid;
    }
}