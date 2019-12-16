<h2>Account Funds $<?= number_format($account_funds, 2) ?></h2>
<table class="table responsive">
    <thead>
    <tr>
        <th>Order ID#</th>
        <th>Description</th>
        <th>Amount</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
	<?php foreach ( $results as $result ) : ?>
        <tr>
            <td><?= $result->id ?></td>
            <td><?= $result->notes ?></td>
            <td>$<?= number_format( $result->amount, 2 ) ?></td>
            <td><?= $result->date ?></td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>