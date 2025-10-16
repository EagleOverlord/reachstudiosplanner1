@push('scripts')
    <script>
        (function() {
            const MAX_ATTEMPTS = 20; // ~2s with 100ms interval
            let attempts = 0;

            // Preserve the original list of events for client-side filtering
            window.dashboardOriginalEvents = @json($shifts);

            const buildCalendar = () => {
                const calendarEl = document.getElementById('calendar');
                if (!calendarEl || !window.FullCalendar) return false;
                if (window.dashboardCalendar) {
                    try {
                        window.dashboardCalendar.destroy();
                    } catch (e) {}
                }
                const today = new Date().toISOString().slice(0, 10);
                try {
                    window.dashboardCalendar = new FullCalendar.Calendar(calendarEl, {
                        plugins: [
                            FullCalendar.dayGridPlugin,
                            FullCalendar.timeGridPlugin,
                            FullCalendar.listPlugin,
                            FullCalendar.interactionPlugin
                        ],
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
                            daysOfWeek: [1, 2, 3, 4, 5],
                            startTime: '09:00',
                            endTime: '17:00'
                        },
                        weekends: true,
                        weekNumbers: false,
                        selectable: true,
                        selectMirror: true,
                        select: info => {
                            const toParts = d => ({
                                date: d.toISOString().slice(0, 10),
                                time: d.toISOString().slice(11, 16)
                            });
                            const s = toParts(info.start);
                            const e = toParts(info.end);
                            const params = new URLSearchParams({
                                start_date: s.date,
                                start_time: s.time,
                                end_date: e.date,
                                end_time: e.time
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
                            info.el.addEventListener('keydown', e => {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    if (info.event.extendedProps.is_editable) {
                                        window.location.href = `/schedule/${info.event.id}/edit`;
                                    }
                                }
                            });
                            // Show hovercard on focus
                            info.el.addEventListener('focus', () => {
                                const ev = info.event;
                                const p = ev.extendedProps || {};
                                const start = ev.start;
                                const end = ev.end;
                                const timeFmt = d =>
                                    d
                                        ? d.toLocaleTimeString([], {
                                              hour: '2-digit',
                                              minute: '2-digit',
                                              hour12: false
                                          })
                                        : '';
                                const dateFmt = d =>
                                    d
                                        ? d.toLocaleDateString([], {
                                              year: 'numeric',
                                              month: 'short',
                                              day: 'numeric'
                                          })
                                        : '';
                                const html = `
                                    <div class="hc-title">${(p.name || '').replace(/</g, '&lt;')}</div>
                                    <div class="hc-meta">${dateFmt(start)} • ${timeFmt(start)} – ${timeFmt(end)}</div>
                                    <div class="hc-row"><span class="opacity-70">Location:</span><span>${(p.location_display || '').replace(/</g, '&lt;')}</span></div>
                                    <div class="hc-row"><span class="opacity-70">Team:</span><span>${(p.team_name_display || '').replace(/</g, '&lt;')}</span></div>
                                    ${
                                        p.description
                                            ? `<div class="hc-row"><span class="opacity-70">Notes:</span><span>${(p.description || '')
                                                  .replace(/</g, '&lt;')
                                                  .replace(/\n/g, '<br>')}</span></div>`
                                            : ''
                                    }
                                `;
                                const rect = info.el.getBoundingClientRect();
                                window.__showHovercard(html, rect.right, rect.top);
                            });
                            info.el.addEventListener('blur', () => window.__hideHovercard?.());
                            info.el.addEventListener('mouseenter', event => {
                                const ev = info.event;
                                const p = ev.extendedProps || {};
                                const start = ev.start;
                                const end = ev.end;
                                const timeFmt = d =>
                                    d
                                        ? d.toLocaleTimeString([], {
                                              hour: '2-digit',
                                              minute: '2-digit',
                                              hour12: false
                                          })
                                        : '';
                                const dateFmt = d =>
                                    d
                                        ? d.toLocaleDateString([], {
                                              year: 'numeric',
                                              month: 'short',
                                              day: 'numeric'
                                          })
                                        : '';
                                const html = `
                                    <div class="hc-title">${(p.name || '').replace(/</g, '&lt;')}</div>
                                    <div class="hc-meta">${dateFmt(start)} • ${timeFmt(start)} – ${timeFmt(end)}</div>
                                    <div class="hc-row"><span class="opacity-70">Location:</span><span>${(p.location_display || '').replace(/</g, '&lt;')}</span></div>
                                    <div class="hc-row"><span class="opacity-70">Team:</span><span>${(p.team_name_display || '').replace(/</g, '&lt;')}</span></div>
                                    ${
                                        p.description
                                            ? `<div class="hc-row"><span class="opacity-70">Notes:</span><span>${(p.description || '')
                                                  .replace(/</g, '&lt;')
                                                  .replace(/\n/g, '<br>')}</span></div>`
                                            : ''
                                    }
                                `;
                                window.__showHovercard?.(html, event.clientX, event.clientY);
                            });
                            info.el.addEventListener('mouseleave', () => window.__hideHovercard?.());
                            info.el.addEventListener('click', () => {
                                if (info.event.extendedProps.is_editable) {
                                    window.location.href = `/schedule/${info.event.id}/edit`;
                                }
                            });
                        },
                        eventDrop: info => {
                            // Confirm before persisting (custom modal)
                            const fmt = d =>
                                d
                                    ? d.toLocaleString([], {
                                          year: 'numeric',
                                          month: 'short',
                                          day: 'numeric',
                                          hour: '2-digit',
                                          minute: '2-digit',
                                          hour12: false
                                      })
                                    : '';
                            const prev = info.prevEvent || info.oldEvent;
                            const oldStart = prev ? prev.start : info.event.start;
                            const newStart = info.event.start;
                            const newEnd = info.event.end || new Date(info.event.start.getTime() + 60 * 60 * 1000);
                            const message = `From: ${fmt(oldStart)}\nTo:     ${fmt(newStart)}\nNew end: ${fmt(newEnd)}`;
                            window.openConfirmModal({
                                title: 'Move this shift?',
                                message,
                                onConfirm: ui => {
                                    const csrf = '{{ csrf_token() }}';
                                    ui.setLoading(true);
                                    fetch(`/schedule/${info.event.id}/time`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                            Accept: 'application/json'
                                        },
                                        body: JSON.stringify({ start: newStart.toISOString(), end: newEnd.toISOString() })
                                    })
                                        .then(r => r.json())
                                        .then(d => {
                                            ui.setLoading(false);
                                            if (!d.ok) {
                                                alert(d.message || 'Unable to update shift time');
                                                info.revert();
                                            }
                                            ui.close();
                                        })
                                        .catch(() => {
                                            ui.setLoading(false);
                                            info.revert();
                                            ui.close();
                                        });
                                },
                                onCancel: () => {
                                    info.revert();
                                }
                            });
                        },
                        eventResize: info => {
                            const fmt = d =>
                                d
                                    ? d.toLocaleString([], {
                                          year: 'numeric',
                                          month: 'short',
                                          day: 'numeric',
                                          hour: '2-digit',
                                          minute: '2-digit',
                                          hour12: false
                                      })
                                    : '';
                            const prev = info.prevEvent || info.oldEvent;
                            const oldEnd = prev ? prev.end : info.event.end;
                            const newStart = info.event.start;
                            const newEnd = info.event.end;
                            const message = `Start: ${fmt(newStart)}\nFrom end: ${fmt(oldEnd)}\nTo end:     ${fmt(newEnd)}`;
                            window.openConfirmModal({
                                title: 'Change shift duration?',
                                message,
                                onConfirm: ui => {
                                    const csrf = '{{ csrf_token() }}';
                                    ui.setLoading(true);
                                    fetch(`/schedule/${info.event.id}/time`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                            Accept: 'application/json'
                                        },
                                        body: JSON.stringify({ start: newStart.toISOString(), end: newEnd.toISOString() })
                                    })
                                        .then(r => r.json())
                                        .then(d => {
                                            ui.setLoading(false);
                                            if (!d.ok) {
                                                alert(d.message || 'Unable to update shift duration');
                                                info.revert();
                                            }
                                            ui.close();
                                        })
                                        .catch(() => {
                                            ui.setLoading(false);
                                            info.revert();
                                            ui.close();
                                        });
                                },
                                onCancel: () => {
                                    info.revert();
                                }
                            });
                        },
                        eventContent: arg => {
                            const props = arg.event.extendedProps;
                            const container = document.createElement('div');
                            container.style.fontWeight = 'bold';

                            const nameSpan = document.createElement('span');
                            nameSpan.textContent = props.name || '';
                            container.appendChild(nameSpan);

                            if (props.has_key) {
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

                            if (props.is_editable) {
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
                            const location = props.location_display || '';
                            const team = props.team_name_display || '';
                            detailsSpan.textContent = ` - ${location} (${team})`;
                            container.appendChild(detailsSpan);

                            return { domNodes: [container] };
                        },
                        eventOverlap: false,
                        eventMaxStack: 20,
                        dayMaxEvents: false,
                        dayMaxEventRows: false
                    });
                    window.dashboardCalendar.render();
                    return true;
                } catch (e) {
                    console.error('Calendar init error:', e);
                    return false;
                }
            };

            const attemptInit = () => {
                if (buildCalendar()) return;
                if (attempts >= MAX_ATTEMPTS) return;
                attempts += 1;
                setTimeout(attemptInit, 100);
            };

            // Simple hovercard utilities
            (function initHovercard() {
                if (window.__hovercardEl) return;
                const el = document.createElement('div');
                el.className = 'fc-hovercard';
                el.setAttribute('role', 'tooltip');
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
                    el.style.left = `${left}px`;
                    el.style.top = `${top}px`;
                }
                window.__showHovercard = (html, x, y) => {
                    clearTimeout(hideTimer);
                    el.innerHTML = html;
                    el.style.display = 'block';
                    el.setAttribute('aria-hidden', 'false');
                    position(x, y);
                };
                window.__hideHovercard = () => {
                    hideTimer = setTimeout(() => {
                        el.style.display = 'none';
                        el.setAttribute('aria-hidden', 'true');
                    }, 100);
                };
                window.addEventListener('scroll', () => window.__hideHovercard(), { passive: true });
            })();

            // Confirm modal implementation
            (function initConfirmModal() {
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
                const cancelBtn = overlay.querySelector('[data-role="cancel"]');
                const confirmBtn = overlay.querySelector('[data-role="confirm"]');
                let onConfirm = null;
                let onCancel = null;
                function close() {
                    overlay.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm';
                }
                window.openConfirmModal = ({ title, message, onConfirm: confirm, onCancel: cancel }) => {
                    titleEl.textContent = title || 'Confirm';
                    msgEl.textContent = message || '';
                    overlay.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm';
                    onConfirm = confirm;
                    onCancel = cancel;
                };
                cancelBtn.addEventListener('click', () => {
                    close();
                    onCancel && onCancel();
                });
                confirmBtn.addEventListener('click', () => {
                    const ui = {
                        setLoading(state) {
                            confirmBtn.disabled = !!state;
                            confirmBtn.textContent = state ? 'Working…' : 'Confirm';
                        },
                        close
                    };
                    onConfirm && onConfirm(ui);
                });
            })();

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
                resetBtn &&
                    resetBtn.addEventListener('click', () => {
                        if (my) my.checked = false;
                        if (team) team.value = '';
                        typeBoxes.forEach(cb => (cb.checked = true));
                        locBoxes.forEach(cb => (cb.checked = true));
                        applyFilters();
                    });

                jumpDate &&
                    jumpDate.addEventListener('change', e => {
                        if (window.dashboardCalendar && e.target.value) {
                            window.dashboardCalendar.gotoDate(e.target.value);
                        }
                    });
                toggleWeekends &&
                    toggleWeekends.addEventListener('change', e => {
                        window.dashboardCalendar && window.dashboardCalendar.setOption('weekends', !!e.target.checked);
                    });
                toggleWeeknums &&
                    toggleWeeknums.addEventListener('change', e => {
                        window.dashboardCalendar && window.dashboardCalendar.setOption('weekNumbers', !!e.target.checked);
                    });
                toggleCompact &&
                    toggleCompact.addEventListener('change', e => {
                        const el = document.getElementById('calendar');
                        if (!el) return;
                        if (e.target.checked) el.classList.add('fc-compact');
                        else el.classList.remove('fc-compact');
                    });
            };

            function bindConfirmForms() {
                const forms = document.querySelectorAll('form[data-confirm]:not([data-confirm-bound])');
                forms.forEach(form => {
                    form.setAttribute('data-confirm-bound', '1');
                    form.addEventListener('submit', e => {
                        if (form.getAttribute('data-confirmed') === '1') return;
                        e.preventDefault();
                        const title = form.getAttribute('data-confirm-title') || 'Are you sure?';
                        const message = form.getAttribute('data-confirm-message') || '';
                        window.openConfirmModal({
                            title,
                            message,
                            onConfirm: ui => {
                                ui.setLoading(true);
                                form.setAttribute('data-confirmed', '1');
                                form.submit();
                                ui.close();
                            },
                            onCancel: () => {}
                        });
                    });
                });
            }

            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                attemptInit();
                bindFilterEvents();
                bindConfirmForms();
            } else {
                document.addEventListener(
                    'DOMContentLoaded',
                    () => {
                        attemptInit();
                        bindFilterEvents();
                        bindConfirmForms();
                    },
                    { once: true }
                );
            }

            document.addEventListener('livewire:navigated', () => setTimeout(() => {
                attemptInit();
                applyFilters();
            }, 0));

            window.refreshDashboardCalendar = () => {
                attemptInit();
                applyFilters();
                bindConfirmForms();
            };
        })();
    </script>
@endpush
