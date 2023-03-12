<?php
require_once('config.php');
include('includes/header.php');

// Create an instance of the Care class
$care = new Care();

// Fetch the data from the database
$listings = $care->getnonAllListings();

$all = $care->getAllListings();



// Group the listings by year
$grouped_listings = [];
foreach ($listings as $listing) {
  $year = $listing['year'];
  if (!isset($grouped_listings[$year])) {
    $grouped_listings[$year] = [];
  }
  $grouped_listings[$year][] = $listing;
}
// Find the maximum value for each category
$max_total = 0;
$max_women = 0;
$max_men = 0;
foreach ($listings as $listing) {
  $max_total = max($max_total, $listing['total']);
  $max_women = max($max_women, $listing['women']);
  $max_men = max($max_men, $listing['men']);
}

$current_year = null;
?>


<?php foreach ($grouped_listings as $year => $year_listings) : ?>
 <h2><?= $year ?></h2>
 <?php 
 
$year_total = 0;
$year_women = 0;
$year_men = 0;

foreach ($all as $listing) {
  if ($listing['year'] == $year && $listing['countrycode'] == 'ALL') {
    $year_total = $listing['total'];
    $year_women = $listing['women'];
    $year_men = $listing['men'];
    break;
  }
}
?>

<h2>Total for <?= $year ?>: <?= $year_total ?>, Women: <?= $year_women ?>, Men: <?= $year_men ?></h2>


  <table class="diagram">
    <thead>
      <tr>
        <th>Country Code</th>
        <th>Total</th>
        <th>Women</th>
        <th>Men</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($year_listings as $listing) : ?>
        <tr>
          <td><?= $listing['countrycode'] . ' ' . $listing['year'] ?></td>
          <td class="bar">
            <div class="inner" style="width: <?= round($listing['total'] / $max_total * 100) ?>%">
              <?php if (is_null($listing['total'])) {
                echo "0";
              } else {
                echo $listing['total'];
              } ?>
            </div>
            <div class="label"><?= $listing['total'] ?></div>
          </td>
          <td class="bar">
            <div class="inner" style="width: <?= round($listing['women'] / $max_women * 100) ?>%">
              <?php if (is_null($listing['women'])) {
                echo "0";
              } else {
                echo $listing['women'];
              } ?>
            </div>
            <div class="label"><?= $listing['women'] ?></div>
          </td>
          <td class="bar">
            <div class="inner" style="width: <?= round($listing['men'] / $max_men * 100) ?>%">
              <?php if (is_null($listing['men'])) {
                echo "0";
              } else {
                echo $listing['men'];
              } ?>
            </div>
            <div class="label"><?= $listing['men'] ?></div>
          </td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td></td>
        <td class="bar">
          <div class="label bottom"><?= floor($max_total / 10) ?>0</div>
        </td>
      </tr>
    </tbody>
  </table>
<?php endforeach; ?>
