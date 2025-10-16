@push('styles')
    <style>
        /* Base calendar styling */
        #calendar {
            max-width: 1500px;
            margin: 40px auto;
            background-color: white;
            padding: 0;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .fc-col-header,
        .fc-timegrid-axis,
        .fc-timegrid-slot-label {
            color: inherit;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Light mode FullCalendar button styling */
        .fc-button {
            background-color: #f3f4f6 !important;
            border-color: #d1d5db !important;
            color: #374151 !important;
            font-weight: 500 !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.375rem !important;
            transition: all 0.2s ease !important;
        }

        .fc-button:hover {
            background-color: #e5e7eb !important;
            border-color: #9ca3af !important;
            color: #1f2937 !important;
        }

        .fc-button-active,
        .fc-button:focus {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
            color: white !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5) !important;
        }

        .fc-button:disabled {
            background-color: #f9fafb !important;
            border-color: #e5e7eb !important;
            color: #9ca3af !important;
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }

        /* Light mode toolbar styling */
        .fc-toolbar {
            background-color: transparent !important;
            padding: 1rem !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .fc-toolbar-title {
            color: #1f2937 !important;
            font-weight: 600 !important;
            font-size: 1.25rem !important;
        }

        /* Dark mode base styling */
        .dark #calendar,
        .dark .fc {
            background-color: #1f2937;
            color: white;
        }

        .dark .fc-timegrid-slot-label,
        .dark .fc-col-header-cell-cushion,
        .dark .fc-scrollgrid-sync-inner {
            color: #e5e7eb;
        }

        .dark .fc-timegrid-slot {
            border-color: #374151;
        }

        .dark .fc-scrollgrid {
            border-color: #25282c;
        }

        /* Dark mode FullCalendar button styling */
        .dark .fc-button {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
            font-weight: 500 !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.375rem !important;
            transition: all 0.2s ease !important;
        }

        .dark .fc-button:hover {
            background-color: #4b5563 !important;
            border-color: #6b7280 !important;
            color: #f9fafb !important;
        }

        .dark .fc-button-active,
        .dark .fc-button:focus {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
            color: white !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5) !important;
        }

        .dark .fc-button:disabled {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #6b7280 !important;
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }

        /* Dark mode toolbar styling */
        .dark .fc-toolbar {
            background-color: transparent !important;
            padding: 1rem !important;
            border-bottom: 1px solid #374151 !important;
        }

        .dark .fc-toolbar-title {
            color: #f9fafb !important;
            font-weight: 600 !important;
            font-size: 1.25rem !important;
        }

        /* Additional styling for better button grouping */
        .fc-button-group .fc-button {
            margin-right: 0 !important;
        }

        .fc-button-group .fc-button:first-child {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .fc-button-group .fc-button:last-child {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .fc-button-group .fc-button:not(:first-child):not(:last-child) {
            border-radius: 0 !important;
        }

        /* Ensure proper spacing between button groups */
        .fc-toolbar-chunk {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        /* Compact calendar mode */
        .fc-compact .fc .fc-timegrid-slot-label {
            font-size: 0.75rem;
        }

        .fc-compact .fc .fc-col-header-cell-cushion {
            padding: 2px 4px;
            font-size: 0.8rem;
        }

        .fc-compact .fc .fc-timegrid-slot {
            height: 1.5rem;
        }

        .fc-compact .fc .fc-event {
            padding: 0 2px;
            font-size: 0.75rem;
        }

        /* Hovercard styles */
        .fc-hovercard {
            position: fixed;
            z-index: 10000;
            min-width: 240px;
            max-width: 320px;
            background: #ffffff;
            color: #111827;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 0.75rem 0.75rem;
        }

        .dark .fc-hovercard {
            background: #111827;
            color: #e5e7eb;
            border-color: #374151;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        }

        .fc-hovercard .hc-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .fc-hovercard .hc-meta {
            font-size: 0.85rem;
            color: #4b5563;
        }

        .dark .fc-hovercard .hc-meta {
            color: #9ca3af;
        }

        .fc-hovercard .hc-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.25rem;
        }

        .fc-hovercard .hc-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .fc-hovercard a.hc-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .fc-hovercard a.hc-link:hover {
            text-decoration: underline;
        }

        /* Modal styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 11000;
        }

        .modal {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            color: #111827;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .modal .modal-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        .modal .modal-body {
            padding: 1rem;
        }

        .modal .modal-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .dark .modal {
            background: #111827;
            color: #e5e7eb;
            border-color: #374151;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        }

        .dark .modal .modal-header,
        .dark .modal .modal-footer {
            border-color: #374151;
        }

        .btn {
            border: 1px solid #d1d5db;
            padding: 0.4rem 0.7rem;
            border-radius: 0.375rem;
            background: #f9fafb;
            color: #111827;
        }

        .btn:hover {
            background: #f3f4f6;
        }

        .btn-primary {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
        }

        .btn-primary:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        .btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endpush
