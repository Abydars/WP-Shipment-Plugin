<div id="list-cc-customers" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Forte Customers <a
                        href="<?= admin_url( 'admin.php?page=create_forte_customer' ) ?>">Create
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
                        <td style="text-align: center"><?= get_user_meta( $customer->ID, 'forte_token_last_updated', true ) ?></td>
                        <td class="address_actions" style="text-align: center;">
                            <a href="<?= admin_url( "admin.php?page=wpcc-customers&action=delete&id={$customer->ID}" ) ?>"
                               class="btn-delete-forte-customer">
                                <i class="fa fa-trash"></i>
                            </a><a href="#" class="btn-view-forte-logs" data-id="<?= $customer->ID ?>">
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
                <h2>Forte Logs</h2>
                <div id="wpcc-logs"></div>
            </div>
        </div>
    </div>
</div>