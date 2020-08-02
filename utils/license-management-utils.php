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

function license_management_set_changelog($entityid,$entityname,$action,$entityidrel){
    global $wpdb;
    $now = date("Y-m-d H:i:s");

    $retrive = license_management_retrive($entityid,$entityname,"*",$entityidrel);
    if( !empty($retrive) && isset($retrive[0]) ){
        $js_retrive = json_encode($retrive[0]);
        $wpdb->insert(
            'wp_license_management_changelog', //table
            array('entity' => $entityname, 'entityid' => $entityid, 'createdtime' => $now, 'changelog' => $js_retrive, 'action' => $action) //data
        );
    }

    return true;
}

function license_management_get_field_html($fieldname,$fieldvalue = "",$mandatory = false, $type){
    $html = $required = $selected = "";
    if($fieldname == "enterpriseid"){
        $values = license_management_get_enterprise();
    }elseif($fieldname == "license_status"){
        $values = array("");
    }elseif($fieldname == "licenseid"){
        $values = license_management_get_license();
    }elseif($fieldname == "service_status"){
       $values = array(1=>__('status_1', 'license-management'),2=>__('status_2', 'license-management'),3=>__('status_3', 'license-management'));
    }elseif($fieldname == "serviceid"){
        $values = license_management_get_servicename();
    }

    if($mandatory){
        $required = "required";
    }
   
    if($type == 15){
        $html = "<select class='form-control' $required name='$fieldname' id='$fieldname' >";
        if(!empty($values)){
         
            foreach($values as $key => $value){
                if(!empty($fieldvalue) && ($fieldvalue == $value || $fieldvalue == $key) ){
                    $selected = "selected";
                }
                $html .= "<option $selected value='$key'> $value </option>";
                $selected = "";
            } 
        }
        $html .= "</select>";
    }elseif($type == 1){
        $html = "<input type='text' id='$fieldname' name='$fieldname' value='$fieldvalue' class='ss-field-width form-control' $required/>";
    }elseif($type == 2){
        $html = "<textarea type='text' id='$fieldname' name='$fieldname' class='ss-field-width form-control'>$fieldvalue</textarea>";
    }elseif($type == 56){
        $checked = "";
        if($fieldvalue){
            $checked = "checked";
        }
        $html = "<input type='checkbox' id='$fieldname' name='$fieldname' $checked >";
    }
    return $html;
}
function license_management_get_translation_for_js(){
	$translation_array = array('status_1' =>__('status_1', 'license-management'),
							'status_2' =>__('status_2', 'license-management'),
							'status_3' =>__('status_3', 'license-management'),
                            'LBL_STATE_SERVICES' =>__('LBL_STATE_SERVICES', 'license-management'),
                            'LBL_CHART_COMPLETE_DATEEND' => __('LBL_CHART_COMPLETE_DATEEND', 'license-management'),
                            'LBL_CHART_BECAME_COMPLETE'  => __('LBL_CHART_BECAME_COMPLETE', 'license-management'),
                            'LBL_NEW_DATEEND'  => __('LBL_NEW_DATEEND', 'license-management'),
                            'LBL_CREATE_SERVICE'  => __('LBL_CREATE_SERVICE', 'license-management'),
                            'LBL_YES' => __('LBL_YES', 'license-management'),
                            'LBL_NO' => __('LBL_NO', 'license-management'),
                            'Services' => __('Services', 'license-management'),
                            'Deadline' => __('lbl_deadlines', 'license-management'),
							'January' =>__('January', 'license-management'),
							'February' =>__('February', 'license-management'),
							'March' =>__('March', 'license-management'),
							'April' =>__('April', 'license-management'),
							'May' =>__('May', 'license-management'),
							'June' =>__('June', 'license-management'),
							'July' =>__('July', 'license-management'),
							'August' =>__('August', 'license-management'),
							'September' =>__('September', 'license-management'),
							'October' =>__('October', 'license-management'),
							'November' =>__('November', 'license-management'),
							'December' =>__('December', 'license-management'),
							 );
	return $translation_array;	
}
function license_management_check_duplicate($type,$value,$value2 = ""){
    global $wpdb;
    $value = strtolower($value);
    if($type == "enterprise"){
        $table_name = $wpdb->prefix."license_management_enterprise";

        $sql = " select id from $table_name where LOWER(business_name) = '{$value}' AND deleted = 0";
    }elseif($type == "license"){
        $table_name = $wpdb->prefix."license_management_license";

        $sql = "SELECT enterpriseid FROM $table_name WHERE license_plate = '$value' AND deleted = 0";
    }elseif($type == "service"){
        $table_name = $wpdb->prefix."license_management_service_rel";
        $sql = "SELECT id FROM $table_name WHERE serviceid = $value AND licenseid = $value2 AND deleted = 0";
    }elseif($type == "service_name"){
        $table_name = $wpdb->prefix."license_management_service";
        $sql = "SELECT id FROM $table_name WHERE service_name = '$value' AND deleted = 0";
    }
    
    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){

        if($type == "license"){
            $table_name = $wpdb->prefix . "license_management_enterprise";
            $enterpriseid = $rows[0]->enterpriseid;
            $sql_2 = " select id from $table_name where id = {$enterpriseid} and deleted = 0";
            $rows_2 = $wpdb->get_results($sql_2);
            if(!empty($rows_2)){
                return true;
            }else{
                return false;
            }
        }

        return true;
    }
    return false;
}
function license_management_get_servicename($licenseid = ""){
    global $wpdb;
    $servicename = array();

    $table_name = $wpdb->prefix . "license_management_service";
    $table_name_rel = $wpdb->prefix . "license_management_service_rel";

    $sql = "SELECT id, service_name FROM $table_name ";
    if(!empty($licenseid)){
        $sql .= " INNER JOIN $table_name_rel ON {$table_name_rel}.serviceid = {$table_name}.serviceid AND licenseid = $licenseid AND {$table_name_rel}.deleted = 0";
    }
    $sql .= " WHERE {$table_name}.deleted = 0 ORDER BY service_name";

    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $serviceid = $row->id;
            $servicename[$serviceid] = $row->service_name;
        }
    }
    
    return $servicename;
}
function get_enterprise_user($userid){
    global $wpdb;
    $table_name = $wpdb->prefix . "license_management_enterprise";

    $sql = "SELECT business_name FROM $table_name WHERE userid = '{$userid}' AND deleted = 0";
    $rows = $wpdb->get_results($sql);
    if(!empty($rows) && isset($rows[0]->business_name)){
        return $rows[0]->business_name;
    }
}
function license_management_format_checkbox($value,$lbl,$restore = false){
    $checkbox = "";
    if($lbl){
        if($value == 1){
            $checkbox = __('LBL_YES', 'license-management');
        }else{
            $checkbox =__('LBL_NO', 'license-management');
        }
    }else{
        if($restore){
            if($value == __('LBL_YES', 'license-management')){
                $checkbox = 1;
            }else{
                $checkbox = 0;   
            }
        }else{
            if($value == 'on'){
                $checkbox = 1;
            }elseif($value == 'off'){
                $checkbox = 0;
            }elseif($value == 'true'){
                $checkbox = 1;
            }elseif($value == 'false'){
                $checkbox = 0;
            }
        }
    }
    return $checkbox;
}
function license_management_format_date($value){
    
    if(!empty($value)){
        $value = date("d-m-Y H:i",strtotime($value));
    }

    return $value;
}
function license_management_get_row_table_list($entity,$entityid,$entityname){
    $html = "";

    require_once "license-management-dashboard-utils.php";
    $tool_dachboardUtils = new DashboardUtils;

    $labels = license_management_entity_get_label($entity);

    if($entity == "service"){
        $now = date("d-m-Y H:i:s");

        $params = array("id"=>$entityid,"label"=>"");
        $action_edit = $tool_dachboardUtils->get_actions_table("edit_service_lbl",$params);

        $html .= "<tr id='license_management_service_tab_".$entityid."'>";
        $html .= "<td data-title='Nome Servizio'>".$entityname."</td>";
        $html .= "<td data-title='Data creazione'>".$now."</td>";
        $html .= "<td data-title=''>".$action_edit."</td>";
        $html .= "</tr>";
    }elseif($entity == "enterprise"){
        $now = date("d-m-Y H:i");

        $business_name = $entityname["business_name"];
        $user_name = $entityname["user_name"];

        $params = array("id"=>$entityid,"label"=>"");
        $action_edit = $tool_dachboardUtils->get_actions_table("edit_enterprise",$params);

        $html .= "<tr>";
        $html .= "<td data-title='Nome Azienda'>".$business_name."</td>";
        $html .= "<td data-title='Cliente'>".$user_name."</td>";        
        $html .= "<td data-title='Data creazione'>".$now."</td>";
        $html .= "<td data-title=''>".$action_edit."</td>";
        $html .= "</tr>";
    }elseif($entity == "license"){
        $now = date("d-m-Y H:i");

        $params = array("id"=>$entityid,"label"=>"");
        $action_edit = $tool_dachboardUtils->get_actions_table("edit_license_plate",$params);

        $license_plate = $entityname["license_plate"];
        $business_name = $entityname["business_name"];
        $messages = $entityname["messages"];

        $html = "<tr>";
        $html .= "<td data-title='".$labels["license_plate"]."'>".$license_plate."</td>";
        $html .= "<td data-title='".$labels["business_name"]."'>".$business_name."</td>";
        $html .= "<td data-title='".$labels["createdtime"]."'>".$now."</td>";
        $html .= "<td data-title='".$labels["messages"]."'>".$messages."</td>";
        $html .= "<td data-title='".$labels["action"]."'>".$action_edit."</td>";
        $html .= "</tr>";
    }

    return $html;
}
function license_management_get_license($userid = ""){
    global $wpdb;
    $license = array();

    $table_name = $wpdb->prefix . "license_management_license";

    $sql = "SELECT id, license_plate FROM $table_name WHERE deleted = 0 ORDER BY license_plate";
    if(!empty($userid)){
        $sql .= " AND userid = $userid";
    }

    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $licenseid = $row->id;
            $license[$licenseid] = $row->license_plate;
        }
    }
    
    return $license;
}
function license_management_get_enterprise($userid = ""){
    global $wpdb;
    $enterprise = array();

    $table_name = $wpdb->prefix . "license_management_enterprise";

    $sql = "SELECT * FROM $table_name WHERE deleted = 0 ORDER BY business_name";
    if(!empty($userid)){
        $sql .= " AND userid = $userid";
    }

    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        foreach ($rows as $row) {
            $enterpriseid = $row->id;
            $enterprise[$enterpriseid] = $row->business_name;
        }
    }

    return $enterprise;
}

function get_license_plate_by_licensid($licensid){
    global $wpdb;
    $table_name_license = $wpdb->prefix . "license_management_license";
    $table_name_enterprise = $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
    $userid = get_current_user_id();

    $sql = "SELECT license_plate
            FROM $table_name_license
            INNER JOIN $table_name_enterprise ON $table_name_enterprise.id = $table_name_license.enterpriseid
            WHERE $table_name_enterprise.deleted = 0 and $table_name_license.deleted = 0 AND $table_name_license.id = $licensid AND userid = $userid";
    $rows = $wpdb->get_results($sql);
    if(!empty($rows)){
        return $rows;
    }

    return false;
}

function license_management_get_header_license(){
    $html = $secondvalue = $id_element = "";
    $message_welcome = true;
    
    if(isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0){
        $title = "Richieste Mezzo";
        $licensid = intval($_REQUEST["id"]);
        $license_plate = get_license_plate_by_licensid($licensid);
        if(!empty($license_plate) && isset($license_plate[0]->license_plate)){
            $secondvalue = $license_plate[0]->license_plate;
            $secondvalue = "<h5 class='second-value'>$secondvalue</h5>";
        }
    }else{
        $login = is_user_logged_in();
        if($login && $message_welcome && ( !isset($_SESSION["view_message_welcome"]) || $_SESSION["view_message_welcome"] == 0) ){
            $title = __('lbl_WELCOME', 'license-management');
            $_SESSION["view_message_welcome"] = 1;
            $id_element = "message_welcome";
        }elseif(!$login){
            $title = get_the_title();
            $_SESSION["view_message_welcome"] = 0;
        }

        if(!empty($title)){
            $current_user_id = get_current_user_id();
            if($current_user_id > 0){
                $user_value = get_user_meta($current_user_id);
                if(!empty($user_value["last_name"][0]) && !empty($user_value["last_name"][0])){
                    $user_name = $user_value["last_name"][0]." ".$user_value["first_name"][0];
                }else{
                    $user_name = $user_value["nickname"][0];
                }
                $secondvalue = "<h5 class='second-value'>$user_name</h5>";
            }
        }
    }

    if(!empty($title)){
        $html .= "<section class='page-title-section bg-secondary' id='$id_element'>
                        <div class='container'>
                            <div class='row'>
                                <div class='col-xs-12'>
                                    <h3 class='page-title'>$title</h3>
                                    $secondvalue
                                </div>
                            </div>
                        </div>
                    </section>";
        $html .= '';
    }
    return $html;
}


function license_management_get_html_after_save_no_ajax($params,$entity_type){
    $css_button = "background: #fff;
    color: #9f9f9f;
    border-radius: 30px;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 3px 1px -2px rgba(0,0,0,0.12),0 1px 5px 0 rgba(0,0,0,0.2);margin-right: 10px;";
    $css_button_i = "font-size: 16px;margin-right: 5px;";

    $css = " background: #F44336; ";
    if(isset($params["result"]) && $params["result"] == 1){
        $css = " background: #4CAF50; ";
    }

    $html = "<div class='container-fluid' style='margin-top:20px;'>
               <a class='btn btn-dft-license' href='admin.php?page=license_management_service' style='$css_button'>
                 <i class='fas fa-map-signs' style='$css_button_i'></i>".__('LBL_CREATE_SERVICE','license-management')."</a>
                <a class='btn btn-dft-license margin-right-ten' href='admin.php?page=license_management_license' style='$css_button'>
                 <i class='fas fa-truck-moving' style='$css_button_i'></i>".__('LBL_CREATE_LICENSE','license-management')."</a>
                <a class='btn btn-dft-license margin-right-ten' href='admin.php?page=license_management_enterprise' style='$css_button'>
                 <i class='fas fa-industry' style='$css_button_i'></i>".__('LBL_ADD_ENTERPRISE','license-management')."</a>
                <a class='btn btn-dft-license pull-left' href='admin.php?page=license_management_list' style='$css_button'>
                 <i class='fas fa-solar-panel' style='$css_button_i'></i>".__('LBL_RETURN_DASHBOARD','license-management')."</a>
            </div>";

    $html .= "<div class='container-fluid'><div class='after-form response-message' style='margin-top:20px; ".$css."'>".$params["result_message"]."</div></div>";

    return $html;
}

?>