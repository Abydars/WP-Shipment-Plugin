<div id="list-cc-customers" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Fatt Customers <a
                        href="<?= admin_url( 'admin.php?page=create_fatt_customer' ) ?>">Create
                    Customer</a></h1>
        </div>
        <div class="row">
            <table class="wpsp-datatable">
                <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Creation Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ( $customers as $customer ): ?>
                    <tr>
                        <td style="text-align: center"><?= $customer->ID ?></td>
                        <td style="text-align: center"><?= $customer->display_name ?></td>
                        <td style="text-align: center"><?= get_user_meta( $customer->ID, 'fatt_token_last_updated', true ) ?></td>
                        <td class="address_actions" style="text-align: center;">
                            <a href="<?= admin_url( "admin.php?page=wpcc-fatt-customers&action=delete&id={$customer->ID}" ) ?>"
                               class="btn-delete-fatt-customer">
                                <i class="fa fa-trash"></i>
                            </a><a href="#" class="btn-view-fatt-logs" data-id="<?= $customer->ID ?>">
                                <i class="fa fa-eye"></i>
                            </a></td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="logsModal" class="wpsp-modal modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Fatt Logs</h2>
                <div id="wpcc-logs"></div>
            </div>
        </div>
    </div>
</div>