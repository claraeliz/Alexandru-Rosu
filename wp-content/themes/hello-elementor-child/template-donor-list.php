<?php
/**
 * Template Name: Photo Donors by Location
 */

get_header();

// Fetch all attachments that have a photo_donor field
$args = [
  'post_type'      => 'attachment',
  'post_status'    => 'inherit',
  'posts_per_page' => -1,
  'meta_query'     => [
    [
      'key'     => 'photo_donor',
      'compare' => 'EXISTS'
    ]
  ]
];
$photos = get_posts($args);

$donors_by_location = [];

// Normalize strings for consistency
function normalize_text($text) {
    return trim(mb_strtolower(remove_accents($text)));
}

foreach ($photos as $photo) {
    $raw_location = get_field('location', $photo->ID);
    $raw_donor = get_field('photo_donor', $photo->ID);

    if (!$raw_location || !$raw_donor) continue;

    // Normalize to merge Bistrita / Bistrița etc.
    $location_key = normalize_text($raw_location);
    $donor_key = normalize_text($raw_donor);

    if (!isset($donors_by_location[$location_key])) {
        $donors_by_location[$location_key] = [
            'display_name' => $raw_location,
            'donors' => []
        ];
    }

    if (!isset($donors_by_location[$location_key]['donors'][$donor_key])) {
        $donors_by_location[$location_key]['donors'][$donor_key] = [
            'display_name' => $raw_donor,
            'count' => 0
        ];
    }

    $donors_by_location[$location_key]['donors'][$donor_key]['count']++;
}

// Sort locations alphabetically
ksort($donors_by_location);
?>

<div class="donors-page container">
  <h1 class="page-title">Photo Donors by Location</h1>
  <div class="donors-grid">
    <?php foreach ($donors_by_location as $location_data): ?>
      <div class="location-column">
        <h2 class="location-title"><?php echo esc_html($location_data['display_name']); ?></h2>
        <ul class="donor-list">
          <?php
          // Sort donors alphabetically inside each location
          uasort($location_data['donors'], fn($a, $b) => strcmp($a['display_name'], $b['display_name']));
          foreach ($location_data['donors'] as $donor):
          ?>
            <li>
              <a href="<?php echo esc_url(home_url('/ambasador/' . sanitize_title($donor['display_name']))); ?>">
                <?php echo esc_html($donor['display_name']); ?>
              </a>
              (<?php echo intval($donor['count']); ?>)
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php get_footer(); ?>
