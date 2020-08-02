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

function license_management_list() {

    global $wpdb;

    require_once "utils/license-management-dashboard-utils.php";
    $tool_dachboardUtils = new DashboardUtils;

    $table_name_license = $wpdb->prefix . "license_management_license";
    $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
    $table_name_service = $wpdb->prefix . "license_management_service";
    $table_name_service_rel = $wpdb->prefix . "license_management_service_rel";

    $sql = "SELECT $table_name_license.id licenseid, $table_name_enterprise.id enterpriseid, license_plate, userid, {$table_name_service_rel}.service_status, 
            {$table_name_license}.messages messages_license, {$table_name_service_rel}.messages messages_service,
            $table_name_service.id serviceid, $table_name_service.service_name,
            $table_name_enterprise.business_name, date_end, {$table_name_service_rel}.createduserid
            FROM $table_name_license
            INNER JOIN $table_name_enterprise ON $table_name_enterprise.id = $table_name_license.enterpriseid
            INNER JOIN $table_name_service_rel ON  $table_name_service_rel.licenseid = $table_name_license.id
            INNER JOIN $table_name_service ON $table_name_service_rel.serviceid = $table_name_service.id
            WHERE $table_name_enterprise.deleted = 0 and $table_name_license.deleted = 0 AND $table_name_service.deleted = 0 AND $table_name_service_rel.deleted = 0";
    $sql .= $tool_dachboardUtils->list_sql_search($_REQUEST);
    
    // license_management_process_before_load($sql);   Deactivate

    $rows = $wpdb->get_results($sql);

    $dashboard_count = $tool_dachboardUtils->get_license_plate_status($rows);

    $n_status_1 = $n_status_2 = $n_status_3 = 0;
    if (isset($dashboard_count["1"])){ $n_status_1 = $dashboard_count["1"]["count"]; } 
    if (isset($dashboard_count["2"])){ $n_status_2 = $dashboard_count["2"]["count"]; }
    if (isset($dashboard_count["3"])){ $n_status_3 = $dashboard_count["3"]["count"]; }

   $create_bolt_premium = true;
   $create_bolt = array( array("link"=>admin_url('admin.php?page=license_management_enterprise'),"seq"=>1,"label"=>__('LBL_CREATE_STEP_1', 'license-management')),
                        array("link"=>admin_url('admin.php?page=license_management_license'),"seq"=>2,"label"=>__('LBL_CREATE_STEP_2', 'license-management')),
                        array("link"=>admin_url('admin.php?page=license_management_service'),"seq"=>3,"label"=>__('LBL_CREATE_STEP_3', 'license-management')));

    $license_managment_module = array(array('label'=>__('LBL_DASHBOARD', 'license-management'),"icon"=>"fas fa-solar-panel","link"=>"dashboard"),
                                    array('label'=>__('LBL_USERS', 'license-management'), "icon"=>"fas fa-users","link"=>"customers"),
                                    array('label'=>__('LBL_INDUSTRIES', 'license-management'), "icon"=>"fas fa-industry","link"=>"industries"),
                                    array('label'=>__('Services', 'license-management'), "icon"=>"fas fa-map-signs","link"=>"services"),
                                    array('label'=>__('LBL_LICENSES', 'license-management'), "icon"=>"fas fa-truck-moving","link"=>"license"),
                                    // TODO
                                    // array('label'=>__('LBL_SUPPORT', 'license-management'), "icon"=>"fas fa-life-ring","link"=>"support"),
                                    // array('label'=>__('LBL_MESSAGES', 'license-management'), "icon"=>"fas fa-mail-bulk","link"=>"messages"),
                                    );
    ?>
    <div id="license-management-container">

        <div class="col-lg-12">
            <button type="button" class="btn btn-primary btn-lg btn-footer" onclick="license_management_ScrollTo(1);" style="display: inline-block;">
                <i class="fa fa-arrow-down" aria-hidden="true"></i>
            </button>
            <button type="button" id="btn-return-top" class="btn btn-primary btn-lg btn-footer" onclick="license_management_ScrollTo(2);" style="display: inline-block;">
                <i class="fa fa-arrow-up" aria-hidden="true"></i>
            </button>
        </div>
                    
        <div class="modal fade" id="license_management_modal" tabindex="-1" role="dialog" aria-labelledby="license_management_label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="license_management_label"></h4>
                </div>
                <div class="modal-response-message" style="background: rgb(76, 175, 80) none repeat scroll 0% 0%;"></div>
                <div class="modal-body"></div>
                </div>
            </div>
        </div>

        <div class="wrap">

            <div class="row module-header">
                <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" id="license-managment-module">
                    <?php foreach($license_managment_module as $key => $modules_val){
                        if($key == 0){?>
                            <li role="presentation" class="active">
                        <?php } else { ?>
                            <li role="presentation">
                        <?php } ?>
                            <a href="#<?php echo $modules_val['link']; ?>" aria-controls="<?php echo $modules_val['link']; ?>" role="tab" data-toggle="tab">
                                <div class="col-xs-12"><i class="<?php echo $modules_val['icon']; ?>"></i></div>
                                <div class="col-xs-12"><?php echo $modules_val['label']; ?></div>
                            </a></li>
                    <?php } ?>
                </ul>
            </div>

            <div class="tab-content">

                <div role="tabpanel" class="tab-pane active in" id="dashboard">
                    <?php require_once "license-management-dashboard.php"; ?>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="customers"></div>

                <div role="tabpanel" class="tab-pane fade" id="industries"></div>

                <div role="tabpanel" class="tab-pane fade" id="services"></div>

                <div role="tabpanel" class="tab-pane fade" id="license"></div>

                <!-- TODO
                <div role="tabpanel" class="tab-pane fade" id="support"></div>
                <div role="tabpanel" class="tab-pane fade" id="messages"></div> -->

            </div>
        </div>
    </div>
<?php
    }
?>