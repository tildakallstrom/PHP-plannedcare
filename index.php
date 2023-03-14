<?php
require_once('config.php');
include('includes/header.php');
?>
<h1>Antal personer som har fått planerad vård utomlands</h1>
<p class="info">Antalen personer visas i procent i nedan diagram, där totalen visas i svart, kvinnor i rosa och män i blått.</p>
<?php
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
  <?php
  $year_total = 0;
  $year_women = 0;
  $year_men = 0;
//loop through listings with countrycode all
  foreach ($all as $listing) {
    if ($listing['year'] == $year && $listing['countrycode'] == 'ALL') {
      $year_total = $listing['total'];
      $year_women = $listing['women'];
      $year_men = $listing['men'];
      break;
    }
  }
  ?>
    <h2>Totalt antal personer som fått vård utomlands <?= $year ?>: <?= $year_total ?>st, varav antal kvinnor: <?= $year_women ?>st, och antal män: <?= $year_men ?>st.</h2>
  <!-- Add the percent scale -->
  <span class="percent">%</span>
 <div class="bar-container1">
<div class="percent-scale">
  <?php for ($i = 0; $i <= 10; $i++) : ?>
    <span class=""><?= $i * 10 ?></span>
  <?php endfor; ?>
</div>
</div>
<div class="data-list">
  <ul class="data-list stripes">
      <?php  //loop through listings grouped by year
      foreach ($year_listings as $listing) : ?>
  <?php
  $total_percent = $year_total > 0 ? round($listing['total'] / $year_total * 100) : 0;
  $women_percent = $year_women > 0 ? round($listing['women'] / $year_total * 100) : 0;
  $men_percent = $year_men > 0 ? round($listing['men'] / $year_total * 100) : 0;
  ?>
  <li>
    <div class="country-code"><?= $listing['countrycode'] ?></div>
    <div class="bar-container">
      <div class="bar total" style="width: <?= $total_percent ?>%"></div>
      <div class="bar women" style="width: <?= $women_percent ?>%"></div>
      <div class="bar men" style="width: <?= $men_percent ?>%"><span></span></div>
    </div>
  </li>
<?php endforeach; ?>
  </ul>
<?php endforeach; ?>
</div>
</div>
</div>
</div>

