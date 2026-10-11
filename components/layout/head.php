<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery (for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- Tailwind Theme Configuration -->
<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            "colors": {
                "on-secondary-fixed": "#0b1c30",
                "on-error": "#ffffff",
                "primary-container": "#1e3a8a",
                "background": "#faf8ff",
                "on-tertiary-fixed-variant": "#773205",
                "on-tertiary": "#ffffff",
                "on-primary": "#ffffff",
                "secondary-container": "#d0e1fb",
                "surface": "#faf8ff",
                "on-tertiary-container": "#f39461",
                "on-secondary": "#ffffff",
                "on-primary-fixed": "#00164e",
                "surface-dim": "#dad9e1",
                "surface-container-low": "#f4f3fa",
                "surface-container-high": "#e9e7ef",
                "primary-fixed-dim": "#b6c4ff",
                "surface-container": "#eeedf4",
                "secondary-fixed-dim": "#b7c8e1",
                "on-primary-container": "#90a8ff",
                "tertiary-fixed": "#ffdbcb",
                "surface-bright": "#faf8ff",
                "tertiary-fixed-dim": "#ffb691",
                "primary-fixed": "#dce1ff",
                "error-container": "#ffdad6",
                "surface-variant": "#e3e1e9",
                "on-secondary-container": "#54647a",
                "tertiary-container": "#6e2c00",
                "on-secondary-fixed-variant": "#38485d",
                "secondary": "#505f76",
                "outline": "#757682",
                "inverse-primary": "#b6c4ff",
                "on-error-container": "#93000a",
                "primary": "#00236f",
                "secondary-fixed": "#d3e4fe",
                "inverse-surface": "#2f3036",
                "surface-tint": "#4059aa",
                "on-tertiary-fixed": "#341100",
                "outline-variant": "#c5c5d3",
                "error": "#ba1a1a",
                "on-background": "#1a1b21",
                "tertiary": "#4b1c00",
                "on-primary-fixed-variant": "#264191",
                "inverse-on-surface": "#f1f0f7",
                "on-surface": "#1a1b21",
                "surface-container-lowest": "#ffffff",
                "surface-container-highest": "#e3e1e9",
                "on-surface-variant": "#444651"
            },
            "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            "spacing": {
                "container-max": "1440px",
                "gutter": "24px",
                "lg": "40px",
                "sm": "12px",
                "xs": "4px",
                "md": "24px",
                "base": "8px",
                "xl": "64px"
            },
            "fontFamily": {
                "headline-lg-mobile": ["Hanken Grotesk"],
                "headline-xl": ["Hanken Grotesk"],
                "body-sm": ["Inter"],
                "headline-lg": ["Hanken Grotesk"],
                "body-lg": ["Inter"],
                "body-md": ["Inter"],
                "headline-md": ["Hanken Grotesk"],
                "label-bold": ["Inter"],
                "label-sm": ["Inter"]
            },
            "fontSize": {
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                "headline-xl": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "label-bold": ["14px", {"lineHeight": "20px", "fontWeight": "600"}],
                "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}]
            }
        }
    }
}
</script>
<link rel="stylesheet" href="assets/css/style.css">
