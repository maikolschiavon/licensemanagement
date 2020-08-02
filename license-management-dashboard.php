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

// lmv@1.2
// Upload file
if(isset($_POST['but_submit'])){

    if($_FILES['file']['name'] != ''){
        $uploadedfile = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );
        // $upload_overrides = array( 'license_management_upload' => false );

        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        $imageurl = "";

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $type = $_REQUEST["type"];
            $serviceid = intval($_REQUEST["serviceid"]);
            $licenseid = intval($_REQUEST["licenseid"]);

            $send_mail = "off";
            if(isset($_REQUEST["mail"])){
                $send_mail = $_REQUEST["mail"];
            }

            $lm_upload_file = new license_management_upload_file();
		    $lm_upload_file->save($serviceid,$licenseid,$movefile["file"],$send_mail,$type);
        } else {
            echo $movefile['error'];
        }
    }
}
// lmv@1.2e

?>

 <div class="dashboard-header">

    <div class="row"> <div class="col-xs-12 title"><h2><?php echo _e('LBL_DASHBOARD', 'license-management'); ?> </h2></div></div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-second" role="tablist">
        <li role="presentation" class="nav-item active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab" class="nav-link active">Home</a></li>
        <li role="presentation" class="nav-item"><a href="#charts" aria-controls="charts" role="tab" data-toggle="tab" class="nav-link"><?php _e('LBL_CHARTS', 'license-management'); ?></a></li>
        <li role="presentation" class="nav-item"><a href="#enterprise_license" aria-controls="enterprise_license" role="tab" data-toggle="tab" class="nav-link"><?php _e('enterprise_license', 'license-management'); ?></a></li>
       <!-- <li role="presentation" class="nav-item"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab" class="nav-link">Messages</a></li>
        <li role="presentation" class="nav-item"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab" class="nav-link">Settings</a></li> -->
        <li role="presentation" class="nav-item"><a href="#calendar" aria-controls="calendar" role="tab" data-toggle="tab" class="nav-link"><?php _e('calendar', 'license-management'); ?></a></li>
    </ul>

</div>

<!-- TODO
<div id="license_management_editor" style="display:none;">
    <?php /*
    $content = '';
    $editor_id = 'license_management_wpeditor';
    $settings =   array(
        'wpautop' => true, // use wpautop?
        'media_buttons' => true, // show insert/upload button(s)
        'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
        'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
        'tabindex' => '',
        'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
        'editor_class' => '', // add extra class(es) to the editor textarea
        'teeny' => false, // output the minimal editor config used in Press This
        'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
        'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
        'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
    );

    wp_editor( $content, $editor_id, $settings );*/
    ?>
</div> -->

<!-- Tab panes -->
<div class="tab-content">

    <!-- TAB HOME -->
    <div role="tabpanel" class="tab-pane fade active in" id="home">

        <!-- Create Bolt -->
        <div class="row">
            <div class="col-xs-4 col-xs-offset-8 col-btn-bolt-plus">
                <div class="col-xs-2" style="float: right;">
                     <ul class="nav nav-pills btn-search">
                        <li role="presentation" class="dropdown"> 
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> 
                                <i class="fas fa-search"></i>
                            </a>
                            </a> 
                            <ul class="dropdown-menu"> 
                                <li><input type="text" class="form-control" id="in_service_name" placeholder="<?php _e("Service Name","license-management"); ?>"></li>
                                <li><input type="text" class="form-control" id="in_business_name" placeholder="<?php _e("Business Name","license-management"); ?>"></li>
                                <li><input type="text" class="form-control" id="in_license_plate" placeholder="<?php _e("Num License plate","license-management"); ?>"></li>
                                <li><a onclick="license_management_search();"><?php _e("LBL_SEARCH","license-management"); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <?php if (!$create_bolt_premium) { ?>
                    <a class="btn-bolt-add" role="button" data-toggle="collapse" href="#collapseBoltCreate" aria-expanded="false" aria-controls="collapseBoltCreate">
                        <i class="fas fa-plus"></i> <?php _e('LBL_CREATE_BOLT', 'license-management'); ?>
                    </a>
                <?php }else{ ?>
                    <a class="btn-bolt-add" role="button" data-toggle="collapse" href="#collapseBoltCreate2" aria-expanded="false" aria-controls="collapseBoltCreate2">
                    <i class="fas fa-plus"></i> <?php _e('LBL_CREATE_BOLT', 'license-management'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
        
        <?php if (!$create_bolt_premium) { ?>
            <div class="row row-collapse-crea-bolt">
                <div class="col-xs-4 col-xs-offset-8" style="display:none;">
                    <a class="btn btn-bolt" role="button" data-toggle="collapse" href="#collapseBoltCreate" aria-expanded="false" aria-controls="collapseBoltCreate">
                        <span><?php echo _e('LBL_CREATE_BOLT', 'license-management'); ?></span> <i class="fas fa-bolt"></i>
                    </a>
                </div>
                <div class="col-xs-12"> 
                    <div class="collapse" id="collapseBoltCreate">
                        <div class="well">
                            <div class="row">
                                <?php foreach($create_bolt as $create_bolt_val) { ?>
                                <div class="col-xs-4">
                                    <a href=" <?php echo $create_bolt_val["link"]; ?>" target="_blank">
                                        <div class="col-xs-3">
                                            <h2><?php echo $create_bolt_val["seq"]; ?></h2>
                                        </div>
                                        <div class="col-xs-9">
                                            <?php echo $create_bolt_val["label"]; ?>
                                        </div>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } else { ?>

            <div class="row row-collapse-crea-bolt">
                <div class="col-xs-4 col-xs-offset-8" style="display:none;">
                    <a class="btn btn-bolt" role="button" data-toggle="collapse" href="#collapseBoltCreate2" aria-expanded="false" aria-controls="collapseBoltCreate2">
                        <span><?php echo _e('LBL_CREATE_BOLT', 'license-management'); ?></span> <i class="fas fa-bolt"></i>
                    </a>
                </div>
                <div class="col-xs-12"> 
                    <div class="collapse" id="collapseBoltCreate2">
                        <div class="well">
                            <div class="row">

                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <?php   $create_bolt_i = 0; 
                                    foreach($create_bolt as $create_bolt_val) { 
                                        $panel_heading_id = "heading".$create_bolt_val["seq"];
                                        $collapse_id = "collapse".$create_bolt_val["seq"];
                                    //  if ($create_bolt_i == 0){ $aria_expanded = "true"; $collapse = "in"; }else{ $aria_expanded = "false"; $collapse = ""; }
                                    ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="<?php echo $panel_heading_id; ?>">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $collapse_id; ?>" aria-expanded="false" aria-controls="<?php echo $collapse_id; ?>">
                                                    <?php echo $create_bolt_val["seq"]." ".$create_bolt_val["label"]; ?>
                                                </a>
                                            </h4>
                                            </div>
                                            <div id="<?php echo $collapse_id; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?php echo $panel_heading_id; ?>">
                                            <div class="response-message"></div>
                                            <div class="panel-body"></div>
                                            </div>
                                        </div>
                                        
                                <?php $create_bolt_i ++;
                                        } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <!-- Create Bolt End -->
                            
        <div class="row">
            <?php $token = $tool_dachboardUtils->generate_search_token_html();
            if(!empty($token)){ ?>
                 <div class="col-xs-1"><h4><?php _e('SEARCH_FOR','license-management'); ?></h4></div>
                 <div class="col-xs-1"><?php echo $token; ?></div>
            <?php } ?>
        </div>

        <div class="row">
            <?php echo $tool_dachboardUtils->get_html_registry_not_complete(); ?>
        </div>

        <?php   
            $collapse_html = $collapse_detail = "";
            $status_license_plate = array(1 => array( "lbl_status"=>__('status_1', 'license-management'),"collapsename"=>"collapseStatusOne","css_id"=>"headingOne","css_class"=>"status1","css_class_service"=>"n_service_1","value"=>$n_status_1), 
                                        2 => array( "lbl_status"=>__('status_2', 'license-management'),"collapsename"=>"collapseStatusTwo","css_id"=>"headingTwo","css_class"=>"status2","css_class_service"=>"n_service_2","value"=>$n_status_2),
                                        3  => array( "lbl_status"=>__('status_3', 'license-management'),"collapsename"=>"collapseStatusThree","css_id"=>"headingThree","css_class"=>"status3","css_class_service"=>"n_service_3","value"=>$n_status_3)
                                    ); 
         ?>

        <!-- BLOCK STATUS -->
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="row">
                <?php foreach ($status_license_plate as $status=>$status_val ) { ?>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="container-status <?php echo $status_val["css_class"]; ?>" id="<?php echo $status_val["css_id"]; ?>">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $status_val["collapsename"]; ?>" aria-expanded="true" aria-controls="<?php echo $status_val["collapsename"]; ?>">
                                <div class="col-xs-4 <?php echo $status_val["css_class_service"]; ?> ">
                                    <?php echo $status_val["value"]; ?>
                                </div>
                                <div class="col-xs-8">
                                    <?php echo $status_val["lbl_status"]; ?>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <?php
                     
                        if( isset($dashboard_count[$status]) ){
                            // lm@1.2
                            $actions = array("status","documents");
                            if($status == 3){
                                $actions[] = "authorization";
                            }
                            // lm@1.2e

                            $dashboard_num = $dashboard_count[$status]["record"];
                            $collapse_detail = $tool_dachboardUtils->simple_table($dashboard_num,$actions); // lm@1.2
                            $collapse_html .= "<div id='".$status_val["collapsename"]."' class='panel-collapse collapse' role='tabpanel' aria-labelledby='".$status_val["css_class"]."'>
                                                <div class='panel-body'>".$collapse_detail."</div></div>";
                        }
                    } ?>
            </div>

            <div class="row">
                <?php echo $collapse_html; ?>
            </div>
        </div>
        <!-- BLOCK STATUS END-->

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <canvas id="statusChart" width="400" height="400"></canvas>
            </div>

            <div class="col-md-6 col-sm-12">
               <?php echo $tool_dachboardUtils->get_date_end_html($rows); ?>
            </div>
        </div>

        <div class="row row-block">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <?php echo $tool_dachboardUtils->get_html_service_license("",$rows); ?>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <?php echo $tool_dachboardUtils->get_html_license_service("",$rows); ?>
            </div>
        </div>
    </div>  
    <!-- END TAB HOME -->

    <!-- TAB GRAFICI -->
    <div role="tabpanel" class="tab-pane fade" id="charts">

        <div class="row">
            
            <?php $chart_license_group_status = false; ?>
            <?php if($chart_license_group_status){ ?>
            <div class="col-xs-12">
                <?php  $filename_chart_became_complete = date("Y-m-d_His")."_".__('LBL_CHART_BECAME_COMPLETE', 'license-management').".png";  ?>
                <a id="dw_img_chart_became_complete" class="btn btn-dft-license" href="" download="<?php echo $filename_chart_became_complete; ?>" onclick="license_managment_download_canvas('dw_img_chart_became_complete','analyticsChart');">
                    <i class="fas fa-cloud-download-alt"></i> <?php _e('LBL_DOWNLOAD', 'license-management'); ?>                
                </a>
            </div>

            <div class="col-xs-12">                
                <input type="hidden" id="infoAnalytics" value='<?php echo $tool_dachboardUtils->get_data_chart_license_change_status(3); ?>'>
                <canvas id="analyticsChart"></canvas>
            </div>
            <?php } ?>
        </div>

        <div class="row">

            <div class="col-xs-12">
                <?php  $filename_chart_num_service_dateend = date("Y-m-d_His")."_".__('LBL_CHART_COMPLETE_DATEEND', 'license-management').".png";  ?>
                <a id="dw_img_chart_num_service_dateend" class="btn btn-dft-license" href="" download="<?php echo $filename_chart_num_service_dateend; ?>" onclick="license_managment_download_canvas('dw_img_chart_num_service_dateend','analyticsChart');">
                    <i class="fas fa-cloud-download-alt"></i> <?php _e('LBL_DOWNLOAD', 'license-management'); ?>
                </a>
            </div>

            <div class="col-xs-12">
                <input type="hidden" id="chartCompleteDateendVal" value='<?php echo $tool_dachboardUtils->get_service_group_status_for_month(3,$rows); ?>'>
                <canvas id="chartCompleteDateend"></canvas>
            </div>

        </div>

        <div class="row">
            
            <div class="row row-action">
                <div class="col-xs-4 col-xs-offset-8">
                    <?php $filename_chart_status_service = date("Y-m-d_His")."_".__('LBL_CHART_STATUS', 'license-management').".png"; ?>
                    <a id="dw_img_chart_status_service" class="btn btn-dft-license" href="" download="<?php echo $filename_chart_status_service; ?>" onclick="license_managment_download_canvas('dw_img_chart_status_service','statusServiceChart');">
                        <i class="fas fa-cloud-download-alt"></i> <?php _e('LBL_DOWNLOAD', 'license-management'); ?>                
                    </a>
                </div>
            </div>

            <div class="col-xs-12">
                <input type="hidden" id="infoStatusServiceChart" value='<?php echo $tool_dachboardUtils->get_num_for_service($rows); ?>'>
                <canvas id="statusServiceChart"></canvas>
            </div>
        </div>
        
    </div>
    <!-- END TAB GRAFICI -->

    <div role="tabpanel" class="tab-pane fade" id="enterprise_license">
        <div class="row row-block">

            <div class="col-lg-6 col-md-12 col-sm-12">
                <?php echo $tool_dachboardUtils->get_html_enteprise_user("",$rows); ?>

            </div>

            <div class="col-lg-6 col-md-12 col-sm-12">
                <?php // echo "<pre>";print_r( $tool_dachboardUtils->get_html_enteprise_license("",$rows)); echo "</pre>";?>
                <?php echo $tool_dachboardUtils->get_html_enteprise_license("",$rows); ?>
            </div>

        </div>
    </div>

    <div role="tabpanel" class="tab-pane fade" id="calendar">
        <?php  echo license_management_calendar($rows); ?>
    </div>

    <!-- TODO -->
    <!-- <div role="tabpanel" class="tab-pane fade" id="messages">..messages.</div> -->
    <!-- <div role="tabpanel" class="tab-pane fade" id="settings">..settings.</div> -->

</div>