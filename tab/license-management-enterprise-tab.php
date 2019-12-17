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

function license_management_enterprise_tab() {
    $license_management_enterprise = license_management_get_all_enterprise();

    $license_management_enterprise_fields = license_management_entity_get_label("enterprise");

    $export_csv = true;
?>

<div class="dashboard-header">

    <div class="row"> <div class="col-xs-12 title"><h2><?php echo _e('LBL_ENTERPRISES', 'license-management'); ?> </h2></div></div>

</div>
<div class="container-fluid">
    <?php if($export_csv) { ?>
        <div class="row row-action">
            <div class="col-xs-4 col-xs-push-8">
                <a class="btn btn-dft-license" onclick="license_managment_export_csv('enterprise');" >
                    <i class="fas fa-cloud-download-alt"></i><?php _e('LBL_DOWNLOAD', 'license-management'); ?>
                </a>
            </div>
            <div class="col-xs-4 col-xs-push-2">
                <a class="btn btn-dft-license" data-toggle="collapse" data-target="#collapseEnterpriseCreate" aria-expanded="false" aria-controls="collapseEnterpriseCreate" onclick="license_management_collapse('collapseEnterpriseCreate');">
                    <i class="fas fa-plus"></i><?php _e('LBL_ADD_ENTERPRISE', 'license-management'); ?>
                </a>
            </div>
        </div>

        <div class="collapse" id="collapseEnterpriseCreate" aria-expanded="true">
            <div class="collapse-response-message"></div>
            <div class="well">
                <div class="row"></div>
            </div>
        </div>
    <?php } ?>

    <div id="no-more-tables">
        <table class="col-xs-12 table-bordered table-striped table-condensed cf table-list-enterprise">
            <thead class="cf">
                <tr>
                    <?php foreach($license_management_enterprise_fields as $license_management_enterprise_fields_label){  ?>
                    <th><?php echo $license_management_enterprise_fields_label ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($license_management_enterprise as $license_management_enterprise_val) {?>
                <tr>
                    <?php foreach($license_management_enterprise_fields as $license_management_customers_fields_labels_field => $license_management_enterprise_fields_label){  ?>
                        <td data-title="<?php echo $license_management_enterprise_fields_label ?>"><?php echo $license_management_enterprise_val[$license_management_customers_fields_labels_field]?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>