<style>
    html {
        scroll-behavior: smooth;
    }

    /* Transition common properties */
    *, ::before, ::after {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-duration: 200ms;
    }

    /* Dark Mode - Body and Text */
    html.dark body {
        background-color: #020617;
        color: #e5e7eb;
    }

    /* Dark Mode - Header */
    html.dark header {
        background-color: rgba(15, 23, 42, 0.9) !important;
        border-bottom-color: #1f2937 !important;
        backdrop-filter: blur(10px);
    }

    html.dark header .text-slate-600,
    html.dark header .text-slate-700,
    html.dark header .text-slate-800,
    html.dark header .text-slate-900 {
        color: #e5e7eb;
    }

    html.dark header .border-slate-100,
    html.dark header .border-slate-200 {
        border-color: #1f2937;
    }

    html.dark header .bg-slate-100 {
        background-color: #020617;
    }

    html.dark header .text-slate-400 {
        color: #9ca3af;
    }

    /* Dark Mode - Sections and Backgrounds */
    html.dark .bg-white {
        background-color: #1a1f2e;
        border-color: #2d3748;
    }

    html.dark .bg-slate-50 {
        background-color: #020617;
    }

    html.dark .bg-slate-100 {
        background-color: #111827;
    }

    html.dark .border-slate-100,
    html.dark .border-slate-200 {
        border-color: #1f2937;
    }

    /* Dark Mode - Text Colors */
    html.dark .text-slate-500,
    html.dark .text-slate-600,
    html.dark .text-slate-700,
    html.dark .text-slate-800,
    html.dark .text-slate-900 {
        color: #e5e7eb;
    }

    html.dark .text-slate-400 {
        color: #9ca3af;
    }

    /* Dark Mode - Forms */
    html.dark input,
    html.dark select,
    html.dark textarea {
        background-color: #111827;
        color: #e5e7eb;
        border-color: #2d3748;
    }

    html.dark input::placeholder,
    html.dark select::placeholder,
    html.dark textarea::placeholder {
        color: #6b7280;
    }

    html.dark input:focus,
    html.dark select:focus,
    html.dark textarea:focus {
        background-color: #1a1f2e;
        border-color: #4f46e5;
    }

    /* Select Options */
    select option {
        background-color: #1e293b;
        color: #f1f5f9;
    }

    select option:checked {
        background-color: #6366f1;
        color: white;
    }

    html.dark select option {
        background-color: #1e293b;
        color: #f1f5f9;
    }

    /* Dark Mode - Cards and Shadows */
    html.dark .shadow-lg,
    html.dark .shadow-xl,
    html.dark .shadow-sm {
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
    }

    /* Dark Mode - Menus and Dropdowns */
    html.dark .preferences-menu {
        background-color: #020617 !important;
        border-color: #1f2937 !important;
    }

    html.dark .preferences-menu .text-slate-700,
    html.dark .preferences-menu .text-slate-500 {
        color: #e5e7eb;
    }

    html.dark .preferences-menu .text-indigo-600 {
        color: #a5b4fc;
    }

    html.dark .preferences-menu .bg-slate-100 {
        background-color: #111827;
    }

    html.dark .preferences-menu .hover\:bg-slate-50:hover {
        background-color: #111827;
    }

    /* Modal Styling */
    html.dark .bg-black.bg-opacity-50 {
        background-color: rgba(0, 0, 0, 0.8);
    }
</style>
