# Alexandru Rosu — WordPress Site

Personal website for Alexandru Rosu, built with WordPress + Elementor and a custom child theme. The project runs locally via Docker.

---

## Stack

| Layer | Technology |
|---|---|
| CMS | WordPress (latest) |
| Page builder | Elementor + Hello Elementor child theme |
| Database | MySQL 5.7 |
| DB management | phpMyAdmin |
| Local environment | Docker Compose |
| Custom fields | ACF (Advanced Custom Fields) |
| Extra plugins | Header Footer Elementor, Premium Addons for Elementor |

---

## Theme

Custom child theme located at `wp-content/themes/hello-elementor-child/`.

Key template files:

| File | Purpose |
|---|---|
| `template-homepage.php` | Homepage with multilingual text slider (RO / DE / HU) |
| `template-donor-list.php` | Donor listing page |
| `template-photo-donor.php` | Photo donor page |
| `template-expozitii.php` | Exhibitions archive |
| `single-expozitie.php` | Single exhibition page |
| `template-exercises.php` | Exercises page |
| `timeline-archive-new.php` | Timeline archive |

---

## Archive page — media & metadata

The archive page fetches images directly from the WordPress **Media Library** using the REST API (`/wp-json/wp/v2/media`). This keeps the archive decoupled from page builder content — images are managed in one place and queried on demand.

Each media item carries additional metadata attached via **ACF (Advanced Custom Fields)**. Fields such as donor name, year, category, or description are registered on the `attachment` post type in ACF and exposed through the REST API, so the same endpoint returns both the image URLs and all custom meta in a single request.

```
GET /wp-json/wp/v2/media?per_page=100&_fields=id,source_url,acf
```

This approach means editors upload a photo once, fill in the ACF fields, and the archive page reflects the changes immediately — no template edits needed.
