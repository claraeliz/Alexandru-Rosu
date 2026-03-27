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

## Local development

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop)

### Start

```bash
docker compose up -d
```

| Service | URL |
|---|---|
| WordPress | http://localhost:8001 |
| phpMyAdmin | http://localhost:9300 |

### Stop

```bash
docker compose down
```

### Database credentials

| Key | Value |
|---|---|
| Host | `db` |
| Database | `alex_rosu` |
| User | `root` |
| Password | `root` |

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

## Importing the database

1. Open phpMyAdmin at http://localhost:9300
2. Select the `alex_rosu` database
3. Go to **Import** and upload `backup.sql`
