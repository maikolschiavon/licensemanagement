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

function license_management_retrive($retrive_id,$entity,$collumn_name = "",$licenseid = ""){
    global $wpdb;
    if($entity == "service"){
        $table_name = $wpdb->prefix . "license_management_service";
        $table_name_2 = $wpdb->prefix . "license_management_service_rel";
    }elseif($entity == "enterprise"){
        $table_name = $wpdb->prefix . "license_management_enterprise";
    }elseif($entity == "license"){
        $table_name = $wpdb->prefix . "license_management_license";
    }

    if(empty($collumn_name)){
        $collumn_name = " * ";
    }

    if($retrive_id > 0){
        
        if($licenseid > 0 && $entity == "service"){
            $sql = "SELECT {$collumn_name} FROM {$table_name}
                INNER JOIN {$table_name_2} ON {$table_name_2}.serviceid = {$table_name}.id
                WHERE {$table_name}.deleted = 0 AND {$table_name_2}.deleted = 0 
                AND {$table_name}.id = {$retrive_id} AND {$table_name_2}.licenseid = {$licenseid}";
        }else{
            $sql = "SELECT {$collumn_name} FROM $table_name WHERE deleted = 0 AND id = $retrive_id";
        }

        $rows = $wpdb->get_results($sql);
        return $rows;
    }
}
function license_management_get_all_user(){
    global $wpdb;

    $all_user = array();

    $users_wp = get_users( array('role__in'=>array('author', 'subscriber'),'fields'=>array('ID','user_registered','display_name','user_email') ) );
    if(!empty($users_wp)){
        foreach($users_wp as $users_wp_values){
            $userid = $users_wp_values->ID;
            $all_user[$userid]["name"] = $users_wp_values->display_name;    // license_management_get_user_name($userid);
            $all_user[$userid]["user_email"] = $users_wp_values->user_email;
            $all_user[$userid]["enterprise"] = get_enterprise_user($userid);
            $all_user[$userid]["user_registered"] = license_management_format_date($users_wp_values->user_registered);
        }
    }

    return $all_user;
}
function license_management_get_all_enterprise($action = ""){
    global $wpdb;

    require_once "license-management-dashboard-utils.php";
    $tool_dachboardUtils = new DashboardUtils;

    $all_enterprise = array();
    
    $table_name = $wpdb->prefix . "license_management_enterprise";
    $sql = "SELECT id, business_name, userid, createdtime FROM $table_name WHERE deleted = 0 ORDER BY business_name";
    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $enterpriseid = $row->id;
            $all_enterprise[$enterpriseid]["business_name"] = $row->business_name;
            $all_enterprise[$enterpriseid]["name"] = $tool_dachboardUtils->get_user_name($row->userid);
            $all_enterprise[$enterpriseid]["createdtime"] = license_management_format_date($row->createdtime);

            if($action != 'export'){
                $params = array("id"=>$enterpriseid,"label"=>"");
                $action_edit = $tool_dachboardUtils->get_actions_table("edit_enterprise",$params);

                $all_enterprise[$enterpriseid]["action"] = $action_edit;
            }
        }
    }

    return $all_enterprise;
}
function license_management_get_all_service($action = ""){
    global $wpdb;

    require_once "license-management-dashboard-utils.php";
    $tool_dachboardUtils = new DashboardUtils;

    $all_service = array();

    $table_name = $wpdb->prefix . "license_management_service";
    $sql = "SELECT id, service_name, createdtime FROM $table_name WHERE deleted = 0 ORDER BY service_name";
    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $serviceid = $row->id;
            $all_service[$serviceid]["service_name"] = $row->service_name;
            $all_service[$serviceid]["createdtime"] = license_management_format_date($row->createdtime);

            if($action != 'export'){
              $params = array("id"=>$serviceid,"label"=>"");
              $all_service[$serviceid]["action"] = $tool_dachboardUtils->get_actions_table("edit_service_lbl",$params);
            }
        }
    }
    return $all_service;
}
function license_management_get_all_license($action = ""){
    global $wpdb;

    require_once "license-management-dashboard-utils.php";
    $tool_dachboardUtils = new DashboardUtils;

    $all_license = array();
    $table_name = $wpdb->prefix . "license_management_license";
    $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
    $sql = "SELECT {$table_name}.id, license_plate, business_name, {$table_name}.createdtime, {$table_name}.messages  FROM $table_name 
            LEFT JOIN {$table_name_enterprise} ON enterpriseid = {$table_name_enterprise}.id 
            WHERE {$table_name}.deleted = 0 AND {$table_name_enterprise}.deleted = 0  ORDER BY license_plate";
    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $id = $row->id;
            $all_license[$id]["license_plate"] = $row->license_plate;
            $all_license[$id]["business_name"] = $row->business_name;
            $all_license[$id]["createdtime"] = license_management_format_date($row->createdtime);
            $all_license[$id]["messages"] = $row->messages;
            
            if($action != 'export'){
                $params = array("id"=>$id,"label"=>"");
                $all_license[$id]["action"] = $tool_dachboardUtils->get_actions_table("edit_license_plate",$params);
            }
        }
    }
    return $all_license;
}

function license_management_entity_get_label($entity){
    
    $license_management_labels = array();

    if($entity == "customers"){
        $license_management_labels = array("name"=> __('Customer User', 'license-management'),
                                            "user_email"=>__('LBL_USER_EMAIL', 'license-management'),
                                            "enterprise"=>__('Business Name', 'license-management'),
                                            "user_registered"=>__('LBL_USER_REGISTERED', 'license-management'));
    }elseif($entity == "enterprise"){
        $license_management_labels = array("business_name"=>__('Business Name', 'license-management'),
                                            "name"=> __('Customer User', 'license-management'),
                                            "createdtime"=>__('LBL_CREATEDTIME', 'license-management'),
                                            "action"=>"");
    }elseif($entity == "service"){
        $license_management_labels = array("service_name"=>__('Service Name', 'license-management'),
                                            "createdtime"=>__('LBL_CREATEDTIME', 'license-management'),
                                            "action"=>"");
    }elseif($entity == "license"){
        $license_management_labels = array("license_plate"=>__('License Plate', 'license-management'),
                                            "business_name"=>__('Business Name', 'license-management'),
                                            "createdtime"=>__('LBL_CREATEDTIME', 'license-management'),
                                            "messages"=>__('Messages', 'license-management'),
                                            "action"=>"");
    }elseif($entity == "tml_messages"){
        $license_management_labels = array("template_name"=>__('template_name', 'license-management'),
                                            "active"=>__('active', 'license-management'),
                                            "createdtime"=>__('LBL_CREATEDTIME', 'license-management'),
                                            "action"=>"");
    }

    return $license_management_labels;
}

function license_management_export_csv($entity){

    $header = $values = $data_csv = array();
    $filename = date("Y-m-d_His")."_".$entity.".csv";
    $file_csv = plugin_dir_path(__DIR__)."download/".$filename;

    $delimiter = ";";

    if($entity == "customers"){
        $header["header"] = license_management_entity_get_label("customers");
        $values = license_management_get_all_user();
    }elseif($entity == "enterprise"){
        $header["header"] = license_management_entity_get_label("enterprise");
        $values = license_management_get_all_enterprise("export");
    }elseif($entity == "service"){
        $header["header"] = license_management_entity_get_label("service");
        $values = license_management_get_all_service("export");
    }elseif($entity == "license"){
        $header["header"] = license_management_entity_get_label("license");
        $values = license_management_get_all_license("export");
    }
        
    if( !empty($header["header"]) ){
        $data_csv = array_merge($header,$values);

        $file = fopen($file_csv, "w");
        foreach ($data_csv as $data) {
            fputcsv($file, $data, $delimiter); 
        }
        return plugins_url( 'license-management', 'license-management' )."/download/".$filename."?key=0d2cebb756a8c6bbe52635a8f91e64fd";
    }
}


function license_management_get_date_end($sql_rows){
    $date_end_block = array();
    // $alertDate = date('Y-m', strtotime("+1 months", strtotime(date("Y-m-d"))));
    $alertDate = date('Y-m-d', strtotime("+50 days", strtotime(date("Y-m-d"))));
    $i = 0;
    if(!empty($sql_rows)){ 
        foreach($sql_rows as $obj){
            $date_end = $obj->date_end;
            if(!empty($date_end) && $date_end != '0000-00-00'){
                //$date_end_10 = date('Y-m-d', strtotime("-10 months", strtotime($date_end)));
                //echo "date_end = ".$date_end." alertDate = ".$alertDate." date_end_10 = ".$date_end_10."<br>";
                //if(date("m",(strtotime($date_end) - strtotime($alertDate))) <= 10){
                if(date('Y-m-d',strtotime($date_end)) <= $alertDate){
                    $date_end_block[$date_end][$i]["licenseid"] = $obj->licenseid;
                    $date_end_block[$date_end][$i]["serviceid"] = $obj->serviceid;
                    $date_end_block[$date_end][$i]["business_name"] = $obj->business_name;
                    $date_end_block[$date_end][$i]["license_plate"] = $obj->license_plate;
                    $date_end_block[$date_end][$i]["service_name"] = $obj->service_name;
                    $date_end_block[$date_end][$i]["service_status"] = $obj->service_status;
                    $date_end_block[$date_end][$i]["messages_service"] = $obj->messages_service;
                }
            }
            $i++;
        }
    }
    return $date_end_block;
}
function license_management_get_all_tmp_messages($action=""){
    global $wpdb;

    require_once "license-management-dashboard-utils.php";
    $tool_dachboardUtils = new DashboardUtils;

    $messages = array();
    
    $table_name = $wpdb->prefix . "license_management_messages_tmp";
    $sql = "SELECT id, template_name, html, active, createdtime FROM $table_name WHERE deleted = 0 ORDER BY template_name";
    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $messagesid = $row->id;
            $messages[$messagesid]["template_name"] = $row->template_name;
            $messages[$messagesid]["html"] = wp_kses_post($row->html);
            $messages[$messagesid]["active"] = license_management_format_checkbox($row->active,true);
            $messages[$messagesid]["createdtime"] = license_management_format_date($row->createdtime);

            if($action != 'export'){
                $params = array("id"=>$messagesid,"label"=>"","tab_name"=>"edit_tmp_messages");
                $action_edit = $tool_dachboardUtils->get_actions_table("edit_tmp_messages",$params);

                $messages[$messagesid]["action"] = $action_edit;
            }
        }
    }

    return $messages;
}

function license_management_process_before_load($sql){
    global $wpdb;

    $params = $wpdb->get_results($sql);

    // Le stadenze devono cambiare stato in "Da Prenotare"
    $service = new license_management_service();
    if(!empty($params)){
        $entity = license_management_get_date_end($params);
        foreach($entity as $date_end => $entity_val){
            foreach($entity_val as $value){
                
                $serviceid = $value["serviceid"];
                $licenseid = $value["licenseid"];
                $messages_service = $value["messages_service"];
                $service_status = $value["service_status"];

                $params = array("serviceid"=>$serviceid,"licenseid"=>$licenseid,"service_status"=>$service_status,"date_end"=>$date_end,"messages"=>$messages_service);
                $service->change_status_rel($params,1);
            }
        }
    }

}
?>