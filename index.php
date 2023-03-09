<?php
require_once('config.php');

// Create an instance of the Care class
$care = new Care();


// Fetch the data from the database
$listings = $care->getListings();
?>


<table>
    <thead>
        <tr>
            <th>Country Code</th>
            <th>Year</th>
            <th>Total</th>
            <th>Women</th>
            <th>Men</th>
    
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listings as $listing) : ?>
            <tr>
                <td><?= $listing['countrycode'] ?></td>
                <td><?= $listing['year'] ?></td>
                <td><?= $listing['total'] ?></td>
                <td><?= $listing['women'] ?></td>
                <td><?= $listing['men'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
