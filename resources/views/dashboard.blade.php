<x-layouts.app :title="__('Dashboard')">
    @push('styles')
        
        <style>
            /* Base calendar styling */
            #calendar { max-width: 1500px; margin: 40px auto; background-color: white; padding: 0; border-radius: 6px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
            .fc-col-header, .fc-timegrid-axis, .fc-timegrid-slot-label { color: inherit; font-weight: 500; font-size: 0.85rem; }
            
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
            
            .fc-button-active, .fc-button:focus {
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
            .dark #calendar, .dark .fc { 
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
            .fc-compact .fc .fc-timegrid-slot-label { font-size: 0.75rem; }
            .fc-compact .fc .fc-col-header-cell-cushion { padding: 2px 4px; font-size: 0.8rem; }
            .fc-compact .fc .fc-timegrid-slot { height: 1.5rem; }
            .fc-compact .fc .fc-event { padding: 0 2px; font-size: 0.75rem; }

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
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                padding: 0.75rem 0.75rem;
            }
            .dark .fc-hovercard {
                background: #111827;
                color: #e5e7eb;
                border-color: #374151;
                box-shadow: 0 10px 25px rgba(0,0,0,0.35);
            }
            .fc-hovercard .hc-title { font-weight: 600; margin-bottom: 0.25rem; }
            .fc-hovercard .hc-meta { font-size: 0.85rem; color: #4b5563; }
            .dark .fc-hovercard .hc-meta { color: #9ca3af; }
            .fc-hovercard .hc-row { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem; }
            .fc-hovercard .hc-actions { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
            .fc-hovercard a.hc-link { color: #2563eb; text-decoration: none; font-weight: 500; }
            .fc-hovercard a.hc-link:hover { text-decoration: underline; }

            /* Modal styles */
            .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 11000; }
            .modal { width: 100%; max-width: 420px; background: #ffffff; color: #111827; border-radius: 0.5rem; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
            .modal .modal-header { padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 600; }
            .modal .modal-body { padding: 1rem; }
            .modal .modal-footer { padding: 0.75rem 1rem; border-top: 1px solid #e5e7eb; display: flex; gap: 0.5rem; justify-content: flex-end; }
            .dark .modal { background: #111827; color: #e5e7eb; border-color: #374151; box-shadow: 0 10px 25px rgba(0,0,0,0.35); }
            .dark .modal .modal-header, .dark .modal .modal-footer { border-color: #374151; }
            .btn { border: 1px solid #d1d5db; padding: 0.4rem 0.7rem; border-radius: 0.375rem; background: #f9fafb; color: #111827; }
            .btn:hover { background: #f3f4f6; }
            .btn-primary { background: #ef4444; border-color: #ef4444; color: white; }
            .btn-primary:hover { background: #dc2626; border-color: #dc2626; }
            .btn[disabled] { opacity: 0.6; cursor: not-allowed; }
        </style>
    @endpush

    @push('scripts')
        
        <script>
            (function(){
                const MAX_ATTEMPTS = 20; // ~2s with 100ms interval
                let attempts = 0;

                // Preserve the original list of events for client-side filtering
                window.dashboardOriginalEvents = @json($shifts);

                const buildCalendar = () => {
                    const calendarEl = document.getElementById('calendar');
                    if (!calendarEl || !window.FullCalendar) return false;
                    if (window.dashboardCalendar) {
                        try { window.dashboardCalendar.destroy(); } catch(e) {}
                    }
                    const today = new Date().toISOString().slice(0,10);
                    try {
                        window.dashboardCalendar = new FullCalendar.Calendar(calendarEl, {
                            plugins: [FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.listPlugin, FullCalendar.interactionPlugin],
                            initialView: 'timeGridWeek',
                            initialDate: today,
                            slotMinTime: '07:30:00',
                            slotMaxTime: '19:00:00',
                            snapDuration: '00:15:00',
                            allDaySlot: false,
                            slotDuration: '00:30:00',
                            slotLabelFormat: { hour: '2-digit', minute: '2-digit', meridiem: false, hour12: false },
                            headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,timeGridDay' },
                            events: window.dashboardOriginalEvents,
                            editable: true,
                            nowIndicator: true,
                            businessHours: {
                                daysOfWeek: [1,2,3,4,5],
                                startTime: '09:00',
                                endTime: '17:00',
                            },
                            weekends: true,
                            weekNumbers: false,
                            selectable: true,
                            selectMirror: true,
                            select: info => {
                                const start = info.start;
                                const end = info.end;
                                const toParts = d => ({
                                    date: d.toISOString().slice(0,10),
                                    time: d.toISOString().slice(11,16),
                                });
                                const s = toParts(start);
                                const e = toParts(end);
                                const params = new URLSearchParams({
                                    start_date: s.date,
                                    start_time: s.time,
                                    end_date: e.date,
                                    end_time: e.time,
                                });
                                window.location.href = `/schedule/new?${params.toString()}`;
                            },
                            eventDidMount: info => {
                                if (info.event.extendedProps.is_editable) {
                                    info.el.style.cursor = 'pointer';
                                    info.el.title = 'Click to edit your shift';
                                }
                                // Make events keyboard-activatable
                                info.el.setAttribute('tabindex', '0');
                                info.el.setAttribute('role', 'button');
                                info.el.addEventListener('keydown', (e) => {
                                    if (e.key === 'Enter' || e.key === ' ') {
                                        e.preventDefault();
                                        if (info.event.extendedProps.is_editable) {
                                            window.location.href = `/schedule/${info.event.id}/edit`;
                                        }
                                    }
                                });
                                // Show hovercard on focus
                                info.el.addEventListener('focus', () => {
                                    const ev = info.event; const p = ev.extendedProps || {};
                                    const start = ev.start; const end = ev.end;
                                    const timeFmt = (d) => d ? d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                                    const dateFmt = (d) => d ? d.toLocaleDateString([], { year:'numeric', month:'short', day:'numeric' }) : '';
                                    const html = `
                                        <div class="hc-title">${(p.name || '').replace(/</g,'&lt;')}</div>
                                        <div class="hc-meta">${dateFmt(start)} • ${timeFmt(start)} – ${timeFmt(end)}</div>
                                        <div class="hc-row"><span class="opacity-70">Location:</span><span>${(p.location_display || '').replace(/</g,'&lt;')}</span></div>
                                        <div class="hc-row"><span class="opacity-70">Team:</span><span>${(p.team_name_display || '').replace(/</g,'&lt;')}</span></div>
                                        ${p.has_key ? '<div class="hc-row"><span class="opacity-70">Key holder:</span><span>Yes</span></div>' : ''}
                                        <div class="hc-row"><span class="opacity-70">Type:</span><span>${(p.type || 'work')}</span></div>
                                        <div class="hc-actions">${p.is_editable ? `<a class="hc-link" href="/schedule/${ev.id}/edit">Edit</a>` : ''}</div>
                                    `;
                                    const r = info.el.getBoundingClientRect();
                                    const x = r.left + r.width + 8; const y = r.top + 8;
                                    window.__showHovercard(html, x, y);
                                });
                                info.el.addEventListener('blur', () => window.__hideHovercard());
                            },
                            eventClick: info => { if (info.event.extendedProps.is_editable) { window.location.href = `/schedule/${info.event.id}/edit`; } },
                            eventMouseEnter: info => {
                                const ev = info.event;
                                const p = ev.extendedProps || {};
                                const start = ev.start; const end = ev.end;
                                const timeFmt = (d) => d ? d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                                const dateFmt = (d) => d ? d.toLocaleDateString([], { year:'numeric', month:'short', day:'numeric' }) : '';
                                const content = `
                                    <div class="hc-title">${(p.name || '').replace(/</g,'&lt;')}</div>
                                    <div class="hc-meta">${dateFmt(start)} • ${timeFmt(start)} – ${timeFmt(end)}</div>
                                    <div class="hc-row"><span class="opacity-70">Location:</span><span>${(p.location_display || '').replace(/</g,'&lt;')}</span></div>
                                    <div class="hc-row"><span class="opacity-70">Team:</span><span>${(p.team_name_display || '').replace(/</g,'&lt;')}</span></div>
                                    ${p.has_key ? '<div class="hc-row"><span class="opacity-70">Key holder:</span><span>Yes</span></div>' : ''}
                                    <div class="hc-row"><span class="opacity-70">Type:</span><span>${(p.type || 'work')}</span></div>
                                    <div class="hc-actions">${p.is_editable ? `<a class="hc-link" href="/schedule/${ev.id}/edit">Edit</a>` : ''}</div>
                                `;
                                window.__showHovercard(content, info.jsEvent.clientX, info.jsEvent.clientY);
                            },
                            eventMouseLeave: () => { window.__hideHovercard(); },
                            eventAllow: (dropInfo, draggedEvent) => {
                                // Only allow if event itself is editable
                                if (!draggedEvent.extendedProps.is_editable) return false;
                                // Prevent overlaps with same user's other events
                                const start = dropInfo.start;
                                const end = dropInfo.end;
                                const uid = draggedEvent.extendedProps.user_id;
                                const evts = window.dashboardCalendar.getEvents();
                                return !evts.some(e => {
                                    if (e.id == draggedEvent.id) return false;
                                    const p = e.extendedProps || {};
                                    if (p.user_id !== uid) return false;
                                    const es = e.start, ee = e.end;
                                    return es && ee && start < ee && end > es; // overlap
                                });
                            },
                            eventDrop: info => {
                                // Confirm before persisting time change (custom modal)
                                const fmt = (d) => d ? d.toLocaleString([], { year:'numeric', month:'short', day:'numeric', hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                                const oldStart = (info.oldEvent && info.oldEvent.start) ? info.oldEvent.start : new Date(info.event.start.getTime() - info.delta.milliseconds);
                                const oldEnd = (info.oldEvent && info.oldEvent.end) ? info.oldEvent.end : (info.event.end ? new Date(info.event.end.getTime() - info.delta.milliseconds) : null);
                                const newStart = info.event.start;
                                const newEnd = info.event.end || new Date(info.event.start.getTime()+60*60*1000);
                                const message = `From: ${fmt(oldStart)}\nTo:     ${fmt(newStart)}\nNew end: ${fmt(newEnd)}`;
                                window.openConfirmModal({
                                    title: 'Move this shift?',
                                    message,
                                    onConfirm: (ui) => {
                                        const csrf = '{{ csrf_token() }}';
                                        ui.setLoading(true);
                                        fetch(`/schedule/${info.event.id}/time`, {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                            body: JSON.stringify({ start: newStart.toISOString(), end: newEnd.toISOString() })
                                        }).then(r => r.json()).then(d => {
                                            ui.setLoading(false);
                                            if (!d.ok) { alert(d.message || 'Unable to update shift time'); info.revert(); }
                                            ui.close();
                                        }).catch(() => { ui.setLoading(false); info.revert(); ui.close(); });
                                    },
                                    onCancel: () => { info.revert(); }
                                });
                            },
                            eventResize: info => {
                                // Confirm before persisting duration change (custom modal)
                                const fmt = (d) => d ? d.toLocaleString([], { year:'numeric', month:'short', day:'numeric', hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                                const prev = info.prevEvent || info.oldEvent;
                                const oldStart = prev ? prev.start : info.event.start;
                                const oldEnd = prev ? prev.end : info.event.end;
                                const newStart = info.event.start;
                                const newEnd = info.event.end;
                                const message = `Start: ${fmt(newStart)}\nFrom end: ${fmt(oldEnd)}\nTo end:     ${fmt(newEnd)}`;
                                window.openConfirmModal({
                                    title: 'Change shift duration?',
                                    message,
                                    onConfirm: (ui) => {
                                        const csrf = '{{ csrf_token() }}';
                                        ui.setLoading(true);
                                        fetch(`/schedule/${info.event.id}/time`, {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                            body: JSON.stringify({ start: newStart.toISOString(), end: newEnd.toISOString() })
                                        }).then(r => r.json()).then(d => {
                                            ui.setLoading(false);
                                            if (!d.ok) { alert(d.message || 'Unable to update shift duration'); info.revert(); }
                                            ui.close();
                                        }).catch(() => { ui.setLoading(false); info.revert(); ui.close(); });
                                    },
                                    onCancel: () => { info.revert(); }
                                });
                            },
                            eventContent: arg => {
                                const props = arg.event.extendedProps;
                                const name = props.name || '';
                                const location = props.location_display || '';
                                const team = props.team_name_display || '';
                                const hasKey = props.has_key;
                                const isEditable = props.is_editable;

                                const container = document.createElement('div');
                                container.style.fontWeight = 'bold';

                                const nameSpan = document.createElement('span');
                                nameSpan.textContent = name;
                                container.appendChild(nameSpan);

                                if (hasKey) {
                                    const keyIcon = document.createElement('span');
                                    keyIcon.title = 'Holds key';
                                    keyIcon.setAttribute('aria-hidden', 'true');
                                    keyIcon.textContent = ' 🔑';
                                    container.appendChild(keyIcon);
                                    const keySr = document.createElement('span');
                                    keySr.className = 'sr-only';
                                    keySr.textContent = ' Key holder';
                                    container.appendChild(keySr);
                                }

                                if (isEditable) {
                                    const editIcon = document.createElement('span');
                                    editIcon.title = 'Click to edit';
                                    editIcon.style.color = '#fbbf24';
                                    editIcon.setAttribute('aria-hidden', 'true');
                                    editIcon.textContent = ' ✏️';
                                    container.appendChild(editIcon);
                                    const editSr = document.createElement('span');
                                    editSr.className = 'sr-only';
                                    editSr.textContent = ' Editable';
                                    container.appendChild(editSr);
                                }

                                const detailsSpan = document.createElement('span');
                                detailsSpan.textContent = ` - ${location} (${team})`;
                                container.appendChild(detailsSpan);

                                return { domNodes: [container] };
                            },
                            eventOverlap: false,
                            eventMaxStack: 20,
                            dayMaxEvents: false,
                            dayMaxEventRows: false,
                        });
                        window.dashboardCalendar.render();
                        return true;
                    } catch (e) {
                        console.error('Calendar init error:', e);
                        return false;
                    }
                };

                // Simple hovercard utilities
                (function initHovercard(){
                    if (window.__hovercardEl) return;
                    const el = document.createElement('div');
                    el.className = 'fc-hovercard';
                    el.setAttribute('role','tooltip');
                    el.style.display = 'none';
                    document.body.appendChild(el);
                    let hideTimer = null;
                    function position(x, y) {
                        const margin = 12;
                        const rect = el.getBoundingClientRect();
                        let left = x + margin;
                        let top = y + margin;
                        if (left + rect.width > window.innerWidth - 8) left = x - rect.width - margin;
                        if (top + rect.height > window.innerHeight - 8) top = y - rect.height - margin;
                        el.style.left = left + 'px';
                        el.style.top = top + 'px';
                    }
                    window.__showHovercard = (html, x, y) => {
                        clearTimeout(hideTimer);
                        el.innerHTML = html;
                        el.style.display = 'block';
                        el.setAttribute('aria-hidden','false');
                        position(x, y);
                    };
                    window.__hideHovercard = () => {
                        hideTimer = setTimeout(() => {
                            el.style.display = 'none';
                            el.setAttribute('aria-hidden','true');
                        }, 100);
                    };
                    window.addEventListener('scroll', () => window.__hideHovercard(), { passive: true });
                })();

                // Confirm modal implementation
                ;(function initConfirmModal(){
                    if (window.openConfirmModal) return;
                    const overlay = document.createElement('div');
                    overlay.className = 'modal-overlay';
                    overlay.innerHTML = `
                        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title" aria-describedby="confirm-modal-desc">
                            <div class="modal-header" id="confirm-modal-title">Confirm</div>
                            <div class="modal-body"><pre id="confirm-modal-desc" style="white-space: pre-wrap; font-family: inherit;"></pre></div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-role="cancel">Cancel</button>
                                <button type="button" class="btn btn-primary" data-role="confirm">Confirm</button>
                            </div>
                        </div>`;
                    document.body.appendChild(overlay);
                    const modal = overlay.querySelector('.modal');
                    const titleEl = overlay.querySelector('#confirm-modal-title');
                    const msgEl = overlay.querySelector('#confirm-modal-desc');
                    const btnCancel = overlay.querySelector('[data-role="cancel"]');
                    const btnConfirm = overlay.querySelector('[data-role="confirm"]');
                    let onCancelCb = null; let onConfirmCb = null; let lastActive = null;
                    function open(opts){
                        titleEl.textContent = opts.title || 'Confirm';
                        msgEl.textContent = opts.message || '';
                        onCancelCb = opts.onCancel || null;
                        onConfirmCb = opts.onConfirm || null;
                        overlay.style.display = 'flex';
                        lastActive = document.activeElement;
                        btnConfirm.focus();
                        document.addEventListener('keydown', onKey);
                    }
                    function close(){
                        overlay.style.display = 'none';
                        document.removeEventListener('keydown', onKey);
                        if (lastActive && lastActive.focus) setTimeout(() => lastActive.focus(), 0);
                    }
                    function setLoading(loading){
                        btnConfirm.disabled = !!loading; btnCancel.disabled = !!loading;
                        btnConfirm.textContent = loading ? 'Saving…' : 'Confirm';
                    }
                    function onKey(e){ if (e.key === 'Escape'){ if (onCancelCb) onCancelCb(); close(); } }
                    btnCancel.addEventListener('click', () => { if (onCancelCb) onCancelCb(); close(); });
                    btnConfirm.addEventListener('click', () => { if (onConfirmCb) onConfirmCb({ setLoading, close }); });
                    window.openConfirmModal = (opts) => open(opts || {});
                })();

                const attemptInit = () => {
                    if (buildCalendar()) { applyFilters(); return; }
                    attempts++;
                    if (attempts < MAX_ATTEMPTS) setTimeout(attemptInit, 100);
                };

                // Filtering helpers
                const getFilterState = () => {
                    const myOnly = document.getElementById('filter-my')?.checked || false;
                    const team = document.getElementById('filter-team')?.value || '';
                    const types = Array.from(document.querySelectorAll('.filter-type:checked')).map(i => i.value);
                    const locations = Array.from(document.querySelectorAll('.filter-location:checked')).map(i => i.value);
                    return { myOnly, team, types, locations };
                };

                const applyFilters = () => {
                    if (!window.dashboardCalendar) return;
                    const { myOnly, team, types, locations } = getFilterState();
                    const filtered = (window.dashboardOriginalEvents || []).filter(ev => {
                        const p = ev.extendedProps || {};
                        if (myOnly && !p.is_own_shift) return false;
                        if (team && p.user_team !== team) return false;
                        if (types.length && !types.includes(p.type || 'work')) return false;
                        if (locations.length && !locations.includes(p.location)) return false;
                        return true;
                    });
                    window.dashboardCalendar.removeAllEvents();
                    window.dashboardCalendar.addEventSource(filtered);
                    const status = document.getElementById('calendar-status');
                    if (status) status.textContent = `Showing ${filtered.length} events`;
                };

                const bindFilterEvents = () => {
                    const my = document.getElementById('filter-my');
                    const team = document.getElementById('filter-team');
                    const typeBoxes = document.querySelectorAll('.filter-type');
                    const locBoxes = document.querySelectorAll('.filter-location');
                    const resetBtn = document.getElementById('filter-reset');
                    const jumpDate = document.getElementById('jump-date');
                    const toggleWeekends = document.getElementById('toggle-weekends');
                    const toggleWeeknums = document.getElementById('toggle-weeknums');
                    const toggleCompact = document.getElementById('toggle-compact');

                    my && my.addEventListener('change', applyFilters);
                    team && team.addEventListener('change', applyFilters);
                    typeBoxes.forEach(cb => cb.addEventListener('change', applyFilters));
                    locBoxes.forEach(cb => cb.addEventListener('change', applyFilters));
                    resetBtn && resetBtn.addEventListener('click', () => {
                        if (my) my.checked = false;
                        if (team) team.value = '';
                        typeBoxes.forEach(cb => cb.checked = true);
                        locBoxes.forEach(cb => cb.checked = true);
                        applyFilters();
                    });

                    jumpDate && jumpDate.addEventListener('change', (e) => {
                        if (window.dashboardCalendar && e.target.value) {
                            window.dashboardCalendar.gotoDate(e.target.value);
                        }
                    });
                    toggleWeekends && toggleWeekends.addEventListener('change', (e) => {
                        window.dashboardCalendar && window.dashboardCalendar.setOption('weekends', !!e.target.checked);
                    });
                    toggleWeeknums && toggleWeeknums.addEventListener('change', (e) => {
                        window.dashboardCalendar && window.dashboardCalendar.setOption('weekNumbers', !!e.target.checked);
                    });
                    toggleCompact && toggleCompact.addEventListener('change', (e) => {
                        const el = document.getElementById('calendar');
                        if (!el) return;
                        if (e.target.checked) el.classList.add('fc-compact');
                        else el.classList.remove('fc-compact');
                    });
                };

                // Bind confirm modal to destructive forms (Delete shift etc.)
                function bindConfirmForms(){
                    const forms = document.querySelectorAll('form[data-confirm]:not([data-confirm-bound])');
                    forms.forEach(form => {
                        form.setAttribute('data-confirm-bound','1');
                        form.addEventListener('submit', function(e){
                            if (form.getAttribute('data-confirmed') === '1') return; // already confirmed
                            e.preventDefault();
                            const title = form.getAttribute('data-confirm-title') || 'Are you sure?';
                            const message = form.getAttribute('data-confirm-message') || '';
                            window.openConfirmModal({
                                title, message,
                                onConfirm: (ui) => {
                                    ui.setLoading(true);
                                    form.setAttribute('data-confirmed','1');
                                    form.submit();
                                    ui.close();
                                },
                                onCancel: () => {}
                            });
                        });
                    });
                }

                // Immediate attempt (script placed after DOM for this view)
                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    attemptInit();
                    bindFilterEvents();
                    bindConfirmForms();
                } else {
                    document.addEventListener('DOMContentLoaded', () => { attemptInit(); bindFilterEvents(); bindConfirmForms(); }, { once: true });
                }

                // Re-init after Livewire navigation
                document.addEventListener('livewire:navigated', () => setTimeout(() => { attemptInit(); applyFilters(); }, 0));

                // Optional: expose manual refresh
                window.refreshDashboardCalendar = () => { attemptInit(); applyFilters(); bindConfirmForms(); };
            })();
        </script>
    @endpush

    <a href="#calendar" class="skip-link">Filters</a>
    <div class="container mx-auto px-4">
        <!-- Filters -->
        <div class="mb-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-sm">
            <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">Filters</h3>
            <div class="flex flex-wrap items-end gap-4">
                <!-- My Shifts Toggle -->
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" id="filter-my" class="rounded border-gray-300 dark:border-gray-600" checked>
                    <span class="text-gray-700 dark:text-gray-300">My shifts only</span>
                </label>

                <!-- Team Select -->
                <div>
                    <label for="filter-team" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Team</label>
                    <select id="filter-team" class="min-w-[10rem] border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm">
                        <option value="">All teams</option>
                        @foreach($teams as $teamKey => $teamName)
                            <option value="{{ $teamKey }}">{{ $teamName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Checkboxes -->
                <div>
                    <div class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Type</div>
                    <div class="flex flex-wrap gap-3">
                        @php $types = ['work' => 'Work', 'holiday' => 'Holiday', 'meeting' => 'Meeting']; @endphp
                        @foreach($types as $tKey => $tLabel)
                            <label class="inline-flex items-center gap-1">
                                <input type="checkbox" class="filter-type rounded border-gray-300 dark:border-gray-600" value="{{ $tKey }}" checked>
                                <span class="text-gray-700 dark:text-gray-300">{{ $tLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Location Checkboxes -->
                <div>
                    <div class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Location</div>
                    <div class="flex flex-wrap gap-3">
                        @php $locations = ['home' => 'Home', 'office' => 'Office', 'meeting' => 'Meeting']; @endphp
                        @foreach($locations as $lKey => $lLabel)
                            <label class="inline-flex items-center gap-1">
                                <input type="checkbox" class="filter-location rounded border-gray-300 dark:border-gray-600" value="{{ $lKey }}" checked>
                                <span class="text-gray-700 dark:text-gray-300">{{ $lLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Date Picker (Jump to date) -->
                <div>
                    <label for="jump-date" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Jump to date</label>
                    <input type="date" id="jump-date" class="border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm" value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Toggles: Weekends, Week numbers, Compact -->
                <div class="flex items-center gap-4 ml-auto">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="toggle-weekends" class="rounded border-gray-300 dark:border-gray-600" checked>
                        <span class="text-gray-700 dark:text-gray-300">Show weekends</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="toggle-weeknums" class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-gray-700 dark:text-gray-300">Week numbers</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="toggle-compact" class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-gray-700 dark:text-gray-300">Compact</span>
                    </label>
                </div>

                <button id="filter-reset" type="button" class="ml-auto text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 underline-offset-2 hover:underline focus:outline-none">
                    Reset filters
                </button>
            </div>
        </div>

        <div id='calendar' aria-label="Team schedule calendar"></div>
        <div id="calendar-status" class="sr-only" role="status" aria-live="polite"></div>
        <!-- Calendar Legend / Key -->
        <div class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-sm">
            <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">Key</h3>
            
            <!-- Location Legend -->
            <div class="mb-4">
                <h4 class="font-medium mb-2 text-gray-600 dark:text-gray-300">Shift Locations</h4>
                <ul class="flex flex-wrap gap-x-8 gap-y-3">
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#2563eb"></span>
                        <span class="text-gray-600 dark:text-gray-300">Home shift</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#22c55e"></span>
                        <span class="text-gray-600 dark:text-gray-300">Office shift</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#FF9800"></span>
                        <span class="text-gray-600 dark:text-gray-300">Holiday</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#9C27B0"></span>
                        <span class="text-gray-600 dark:text-gray-300">Meeting</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span>🔑</span>
                        <span class="text-gray-600 dark:text-gray-300">Key holder</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="text-yellow-500">✏️</span>
                        <span class="text-gray-600 dark:text-gray-300">Editable (your shifts)</span>
                    </li>
                </ul>
            </div>

            
        </div>
            
            <!-- User's Upcoming Shifts Section -->
            <div class="mt-6 bg-white dark:bg-gray-900 p-4 rounded-lg border border-gray-300 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">My Upcoming Shifts</h2>
                @php
                    $userShifts = collect($shifts)->where('extendedProps.is_own_shift', true)
                                                 ->where('extendedProps.is_upcoming', true)
                                                 ->sortBy('start')
                                                 ->take(5);
                @endphp
                
                @if($userShifts->count() > 0)
                    <div class="space-y-2">
                        @foreach($userShifts as $shift)
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $shift['backgroundColor'] }}"></div>
                                    <div>
                                        <span class="text-gray-900 dark:text-gray-200 font-medium">
                                            {{ \Carbon\Carbon::parse($shift['start'])->format('M j, Y') }}
                                        </span>
                                        <span class="text-gray-600 dark:text-gray-400 text-sm">
                                            {{ \Carbon\Carbon::parse($shift['start'])->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($shift['end'])->format('g:i A') }}
                                        </span>
                                        @php
                                            $type = $shift['extendedProps']['type'] ?? 'work';
                                            $location = $shift['extendedProps']['location'];
                                        @endphp
                                        <span class="text-gray-600 dark:text-gray-400 text-sm ml-2">
                                            @if($type === 'holiday')
                                                (Holiday)
                                            @elseif($type === 'meeting')
                                                (Meeting{{ $location === 'meeting' ? '' : ' - ' . ucfirst($location) }})
                                            @else
                                                ({{ ucfirst($location) }})
                                            @endif
                                        </span>
                                        @if($shift['extendedProps']['team_name'] !== 'No Team')
                                            <span class="text-indigo-600 dark:text-indigo-400 text-sm ml-2">
                                                [{{ $shift['extendedProps']['team_name'] }}]
                                            </span>
                                        @endif
                                        @if($shift['extendedProps']['has_key'])
                                            <span class="ml-2" title="Key holder">🔑</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('schedule.edit', $shift['id']) }}" 
                                       class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('schedule.destroy', $shift['id']) }}" 
                                          class="inline" data-confirm data-confirm-title="Delete this shift?" data-confirm-message="This action cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400">You have no upcoming shifts scheduled.</p>
                @endif
            </div>
    </div>
</x-layouts.app>
