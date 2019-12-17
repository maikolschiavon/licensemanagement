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

function license_management_service_tab(){
    $license_management_enterprise = license_management_get_all_service();
    
    $license_management_enterprise_fields = license_management_entity_get_label("service");

    $export_csv = true;
?>
<div class="dashboard-header">

    <div class="row"> <div class="col-xs-12 title"><h2><?php echo _e('Services', 'license-management'); ?> </h2></div></div>

</div>
<div class="container-fluid">
    <?php if($export_csv) { ?>
        <div class="row row-action">
            <div class="col-md-3 col-md-offset-3 col-sm-4 col-xs-12">
                <a class="btn btn-dft-license" href="<?php echo admin_url('admin.php?page=license_management_service'); ?>">
                    <i class="fas fa-plus"></i><?php _e('LBL_CREATE_SERVICE', 'license-management'); ?>
                </a>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <a class="btn btn-dft-license" data-toggle="collapse" data-target="#collapseServiceCreate" aria-expanded="false" aria-controls="collapseServiceCreate">
                    <i class="fas fa-plus"></i><?php _e('LBL_NEW_SERVICE', 'license-management'); ?>
                </a>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <a class="btn btn-dft-license" onclick="license_managment_export_csv('service');" >
                    <i class="fas fa-cloud-download-alt"></i><?php _e('LBL_DOWNLOAD', 'license-management'); ?>
                </a>
            </div>
        </div>
    <?php } ?>

    <div class="collapse" id="collapseServiceCreate" aria-expanded="true" style="margin-top:20px;">
        <div class="collapse-response-message" style="display: none;"></div>
        <div class="well">
            <div class="container-fluid">
                <div class="row">
                    <label><?php _e('LBL_NEW_SERVICE', 'license-management');?></label>
                    <input type="text" id="new_service_name" value="" class="ss-field-width"/>
                    <input type='button' onclick="license_management_save_ajax('create_service');" value='<?php _e('LBL_ADD', 'license-management'); ?>' class='btn btn-dft-license-save'>
                </div>
            </div>
        </div>
    </div>

    <div id="no-more-tables">
        <table class="col-xs-12 table-bordered table-striped table-condensed cf table-list-service">
            <thead class="cf">
                <tr>
                    <?php foreach($license_management_enterprise_fields as $license_management_enterprise_fields_label){  ?>
                    <th><?php echo $license_management_enterprise_fields_label ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($license_management_enterprise as $key => $license_management_enterprise_val) {?>
                <tr id="license_management_service_tab_<?php echo $key; ?>">
                    <?php foreach($license_management_enterprise_fields as $license_management_customers_fields_labels_field => $license_management_enterprise_fields_label){  ?>
                        <td data-title="<?php echo $license_management_enterprise_fields_label; ?>"><?php echo $license_management_enterprise_val[$license_management_customers_fields_labels_field]; ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>