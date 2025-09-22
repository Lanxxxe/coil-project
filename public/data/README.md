This folder holds large public datasets (e.g., GeoJSON boundary files) used by the frontend maps.

These files are intentionally ignored by Git to keep the repository lightweight.

To obtain the data locally:
- Place GeoJSON and CSV files under `public/data/geo/` following the same filenames used in the code.
- Alternatively, adjust the app to fetch from a remote URL or compressed assets.

If you need to share a canonical dataset, prefer Git LFS or an external object store (e.g., release assets, S3, GCS) and document the download script.
