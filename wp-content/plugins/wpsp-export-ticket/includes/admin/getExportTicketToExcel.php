<?php
    $advancedSettings=get_option( 'wpsp_advanced_settings' );
?>
<html>
    <head>        
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#to_export').datepicker({
                    dateFormat : 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true, 
                    yearRange: '2000:2050',                       
                    defaultDate:'+0',                       
                    onSelect: function (selected) {
                        var dt1 = new Date(selected);
                        dt1.setDate(dt1.getDate());
                        $("#to_export").datepicker(dt1);
                        dt1.setDate(dt1.getDate()-1);
                        $("#from_export").datepicker("option", "maxDate",dt1);
                    }
                });
                $('#from_export').datepicker({
                    dateFormat : 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true, 
                    yearRange: '2000:2050',                       
                    defaultDate:'+0',                       
                    onSelect: function (selected) {
                        var dt1 = new Date(selected);
                        dt1.setDate(dt1.getDate());
                        $("#from_export").datepicker(dt1);
                        dt1.setDate(dt1.getDate()+1);
                        $("#to_export").datepicker("option", "minDate",dt1);
                    }
                });
            });
        </script>
    </head>
    <div id="catDisplayTableContainer" class="table-responsive">
	<table>
            <tr>
                <td><?php _e('From Date','wpsp_export');?></td>
                <td>:</td>
                <td><input type="text" class="custom_date" name="from_date" id="from_export" value="" placeholder="<?php _e('Click here to select date','wpsp_export');?>"/></td>
            </tr>
            <tr>
                <td><?php _e('To Date','wpsp_export');?></td>
                <td>:</td>
                <td><input type="text" class="custom_date" name="To_date" id="to_export" value="" placeholder="<?php _e('Click here to select date','wpsp_export');?>"/></td>
            </tr>
        </table>
    </div>
    <hr>
    <input type="button" onclick="setExportTicketToExcel();" class="btn btn-success" value="<?php _e('Export To CSV','wpsp_export');?>">       
</html>