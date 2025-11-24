# RSMS Public API

A clean, JSON-first API for powering web and mobile clients for results, events, blogs, calendar, and site content.

## Base URL
- Local: `http://localhost:8000`

## Conventions
- Content-Type: `application/json`
- Authentication: None (public endpoints)
- Date formats: `YYYY-MM-DD` (unless stated)
- All local files are served under `/storage/...` and can be embedded or downloaded directly.

---

## Color Palette (Brand Guide)
These colors are used across RSMS UI and recommended for API docs visual accents.

- Primary Green: `#0B6B3A`
- Accent Green: `#0AA74A`
- Lime Highlight: `#E6FF8A`
- Text Dark: `#1B1B18`
- Subtext: `#6B6A67`
- Border: `#ECEBEA`

Example badges:

- Status badge: background `#E6FF8A`, text `#0B6B3A`
- Primary CTA: background `#0AA74A`, hover `#089543`

---

## System Status & Site Info

### GET `/api/status`
Returns maintenance and basic site branding.

Response
```json
{
  "site_name": "RSMS",
  "maintenance": false,
  "maintenance_ends_at": null,
  "maintenance_message": null,
  "favicon_url": "/storage/assets/favicon.ico",
  "hero_url": "/storage/assets/hero.jpg"
}
```

### GET `/api/hero`
Minimal hero/branding assets.

Response
```json
{
  "site_name": "RSMS",
  "favicon_url": "/storage/assets/favicon.ico",
  "hero_url": "/storage/assets/hero.jpg"
}
```

### GET `/api/about`
Returns About content.

Response
```json
{
  "title": "About Us",
  "body": "This is the RSMS information portal..."
}
```

---

## Exams & Results

### GET `/api/exams/types`
List exam types with associated years.

Response
```json
[
  {
    "id": 1,
    "code": "CSEE",
    "name": "Certificate of Secondary Education",
    "description": "",
    "years": [2025, 2024, 2023],
    "link": "http://localhost:8000/exams/csee"
  }
]
```

### GET `/api/exams/{code}`
Detail for one exam type.

Response
```json
{
  "id": 1,
  "code": "CSEE",
  "name": "Certificate of Secondary Education",
  "description": "",
  "years": [2025, 2024, 2023],
  "content": null,
  "link": "http://localhost:8000/exams/csee"
}
```

### Results discovery flow

1) GET `/api/results/exams` → `["SFNA","PSLE","FTNA","CSEE","ACSEE"]`
2) GET `/api/results/regions` → list regions
3) GET `/api/results/districts?region_id=:id` → list districts
4) GET `/api/results/schools?district_id=:id[&q=term]` → list/search schools
5) GET `/api/results/district-schools?exam=CSEE&year=2025&region=Mwanza&district=Nyamagana` → documents grouped by type (district-level)
6) GET `/api/results/school-docs?exam=CSEE&year=2025&region=Mwanza&district=Nyamagana&school_id=:id` → documents grouped by type (per school)

Notes
- All document `url` fields are normalized to absolute URLs (e.g., `/storage/results/...pdf` or external `https://...`).
- Use the viewer to render PDFs as a full web page: `/view/pdf?src=<encoded-url>`.

### GET `/api/results/list?exam=CSEE[&region_id=][&district_id=][&school_id=]`
Returns yearly buckets with tidy entries.

Response
```json
{
  "2025": [
    {
      "title": "CSEE 2025",
      "url": "/storage/results/some.pdf",
      "year": 2025,
      "created_at": "2025-11-24T08:50:00",
      "type_name": "Mid Term Exam",
      "type_code": "CSEE-MID"
    }
  ]
}
```

---

## Calendar & Events

### GET `/api/calendar?month=YYYY-MM`
Monthly grid plus events overlapping the month.

Response
```json
{
  "month": "2025-11",
  "weeks": [[ { "date": "2025-10-27", "day": 27, "in_month": false, "is_today": false, "has_event": false } ]],
  "events": [ { "id": 1, "title": "Release", "start": "2025-11-20", "end": "2025-11-20", "location": "NECTA" } ]
}
```

### GET `/api/events/recent?limit=10`
Recent events list.

Response
```json
[
  { "id": 1, "title": "Release", "start": "2025-11-20", "end": "2025-11-20", "location": "NECTA" }
]
```

---

## Blogs & News

### GET `/api/blogs?limit=6&page=1`
Paginated blog posts.

Response
```json
{
  "items": [
    { "title": "CSEE 2025 Exam Timetable", "slug": "csee-2025-timetable", "excerpt": "...", "date": "2025-11-04", "image": "/storage/blog/1.jpg", "link": "/blogs/csee-2025-timetable" }
  ],
  "total": 12,
  "page": 1,
  "limit": 6
}
```

### GET `/api/blogs/{slug}`
Single post.

### GET `/api/news`
Latest news (lightweight feed):
```json
[
  { "title": "PSLE 2025 Released", "slug": "psle-2025-released", "date": "2025-11-05" }
]
```

---

## FAQ & Contacts

### GET `/api/faq`
Array of common questions.
```json
[
  { "q": "How can I check my exam results?", "a": "Use the Results Center and select your exam, year, and school to view documents." }
]
```

### GET `/api/contacts`
Contact details and socials.
```json
{
  "email": "support@example.org",
  "phone": "+255 700 000 000",
  "address": "Dar es Salaam, Tanzania",
  "hours": "Mon–Fri, 08:00–17:00",
  "socials": [ { "name": "Twitter", "url": "https://x.com/example" } ]
}
```

---

## PDF Viewer (Web-based)
Open any PDF in a clean, full-page viewer:
- GET `/view/pdf?src=<encoded-url>`

Examples
- `/view/pdf?src=/storage/results/your.pdf`
- `/view/pdf?src=https%3A%2F%2Fexample.com%2Fdocument.pdf`

---

## Curl Examples

```bash
# Exams
curl http://localhost:8000/api/results/exams

# Exam Types list
curl http://localhost:8000/api/exams/types

# District documents (grouped by type)
curl "http://localhost:8000/api/results/district-schools?exam=CSEE&year=2025&region=Mwanza&district=Nyamagana"

# Blogs (page 1)
curl "http://localhost:8000/api/blogs?limit=6&page=1"

# Calendar month
date +%Y-%m # e.g., 2025-11
curl "http://localhost:8000/api/calendar?month=2025-11"
```

---

## Notes & Best Practices
- Encode URLs when passing to the PDF viewer (`encodeURIComponent`).
- Ensure `/public/storage` symlink exists so `/storage/...` URLs resolve.
- Prefer the newest endpoints above; older/legacy paths may be redirected internally.

---

Made with `#0B6B3A` energy.
