# Firbrigs cleanup report (2026-01-29)

## Backup created
- `__backup__/firbrigs-backup-20260129-221354.zip`

## Summary
- **Removed macOS metadata**: 6 `.DS_Store` files deleted
- **Deleted (known unused)**: 5 files deleted
- **Moved (computed unused)**: 46 assets moved into `__unused__/` (same relative paths preserved)
- **Post-cleanup fixes (to avoid broken refs)**:
  - Restored `assets/images/footer-logo.png` (was missing; now present)
  - Updated blog pagination links to avoid missing `blog-grid.html`
  - Removed missing image reference `owl.video.play.png` from `assets/css/owl.css`

## Deleted
- `.DS_Store`
- `assets/.DS_Store`
- `assets/images/.DS_Store`
- `assets/images/background/.DS_Store`
- `assets/images/banner/.DS_Store`
- `assets/images/product/.DS_Store`
- `assets/css/rtl.css`
- `assets/fonts/flaticon.css`
- `assets/js/circle-progress.js`
- `assets/js/isotope.js`
- `assets/js/pagenav.js`

## Moved to `__unused__/` (safe, reversible)
- `assets/images/background/history-bg-1.jpg`
- `assets/images/background/page-title.jpg`
- `assets/images/background/service-bg-1.jpg`
- `assets/images/background/service-bg.jpg`
- `assets/images/background/skrills-bg-1.jpg`
- `assets/images/background/video-bg-1.jpg`
- `assets/images/gallery/gallery-10.jpg`
- `assets/images/gallery/gallery-11.jpg`
- `assets/images/gallery/gallery-12.jpg`
- `assets/images/gallery/gallery-13.jpg`
- `assets/images/gallery/gallery-5.jpg`
- `assets/images/gallery/gallery-6.jpg`
- `assets/images/gallery/gallery-7.jpg`
- `assets/images/gallery/gallery-8.jpg`
- `assets/images/gallery/gallery-9.jpg`
- `assets/images/icons/map-marker.png`
- `assets/images/logo-2.png`
- `assets/images/logo.png`
- `assets/images/resource/about-1.jpg`
- `assets/images/resource/about-2.jpg`
- `assets/images/resource/about-3.jpg`
- `assets/images/resource/advice-1.jpg`
- `assets/images/resource/funfact-1.jpg`
- `assets/images/resource/skrills-1.jpg`
- `assets/images/service-bg-1 2.jpg`
- `assets/images/service-bg-1.jpg`
- `assets/images/service/service-1.jpg`
- `assets/images/service/service-10.jpg`
- `assets/images/service/service-11.jpg`
- `assets/images/service/service-12.jpg`
- `assets/images/service/service-13.jpg`
- `assets/images/service/service-2.jpg`
- `assets/images/service/service-3.jpg`
- `assets/images/service/service-7.jpg`
- `assets/images/service/service-8.jpg`
- `assets/images/service/service-9.jpg`
- `assets/images/shape/pattern-3.png`
- `assets/images/shape/pattern-4.png`
- `assets/images/small-logo.png`
- `assets/images/team/team-1.jpg`
- `assets/images/team/team-2.jpg`
- `assets/images/team/team-3.jpg`
- `assets/images/team/team-4.jpg`
- `assets/images/team/team-5.jpg`
- `assets/images/team/team-6.jpg`

## Fixes applied
- **Footer logo**:
  - Added `assets/images/footer-logo.png` (moved from `__unused__/assets/images/resource/footer-logo.png`) so all pages’ footers load correctly.
- **Blog pagination**:
  - `blog.html` and `blog-2.html`: replaced `blog-grid.html` links with `blog.html` / `blog-2.html` so pagination no longer points to a non-existent page.
- **OwlCarousel CSS**:
  - `assets/css/owl.css`: removed `background: url("owl.video.play.png")` from `.owl-video-play-icon` because that file does not exist.

## How to undo
- **Undo everything**: unzip the backup zip and replace the folder contents.
- **Undo just the asset cleanup**: move items from `__unused__/assets/...` back into `assets/...`.

