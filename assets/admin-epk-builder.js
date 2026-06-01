(function () {
    function parsePayload(value) {
        if (!value) {
            return null;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            return null;
        }
    }

    function defaultPayload() {
        return {
            kicker: 'EPK',
            title: 'Electronic Press Kit',
            description: '',
            imageUrl: '',
            imageAlt: '',
            zones: [],
            updatedAt: '',
            publishedAt: ''
        };
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function normalizePayload(payload) {
        const state = Object.assign(defaultPayload(), payload || {});
        state.zones = Array.isArray(state.zones) ? state.zones : [];
        state.zones = state.zones.map((zone, index) => ({
            id: zone.id || 'zone_' + index + '_' + Date.now(),
            label: zone.label || '',
            hrefType: zone.hrefType === 'anchor' ? 'anchor' : 'url',
            hrefValue: zone.hrefValue || '',
            x: clamp(Number(zone.x) || 0, 0, 100),
            y: clamp(Number(zone.y) || 0, 0, 100),
            width: clamp(Number(zone.width) || 0, 0, 100),
            height: clamp(Number(zone.height) || 0, 0, 100)
        })).filter((zone) => zone.width > 0 && zone.height > 0);
        return state;
    }

    function formatTimestamp(value) {
        if (!value) {
            return 'Non défini';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return 'Non défini';
        }

        return date.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getHref(zone) {
        const value = String(zone.hrefValue || '').trim();
        if (!value) {
            return '';
        }

        if (zone.hrefType === 'anchor') {
            return value.charAt(0) === '#' ? value : '#' + value;
        }

        return value;
    }

    function initBuilder(root) {
        if (root.dataset.ready === '1') {
            return;
        }

        root.dataset.ready = '1';

        const draftInput = document.querySelector('textarea[name="mayami_landing_options[epk_draft_payload]"]');
        const publishedInput = document.querySelector('textarea[name="mayami_landing_options[epk_published_payload]"]');
        const validationInput = document.querySelector('input[name="mayami_landing_options[epk_validation_ready]"]');

        if (!draftInput || !publishedInput || !validationInput) {
            return;
        }

        const publishedPayload = normalizePayload(parsePayload(publishedInput.value));
        let state = normalizePayload(parsePayload(draftInput.value) || (publishedInput.value ? publishedPayload : defaultPayload()));
        const initialDraftValue = draftInput.value || JSON.stringify(state);
        const initialValidationState = !!validationInput.checked;
        let activeZoneId = null;
        let isDrawing = false;
        let startX = 0;
        let startY = 0;
        let currentBox = null;
        let mediaFrame = null;

        const elements = {
            draftStatus: root.querySelector('.mayami-epk-draft-status'),
            publishedStatus: root.querySelector('.mayami-epk-published-status'),
            kicker: root.querySelector('[data-epk-field="kicker"]'),
            title: root.querySelector('[data-epk-field="title"]'),
            description: root.querySelector('[data-epk-field="description"]'),
            imageAlt: root.querySelector('[data-epk-field="imageAlt"]'),
            selectImage: root.querySelector('.mayami-epk-select-image'),
            clearImage: root.querySelector('.mayami-epk-clear-image'),
            canvasEmpty: root.querySelector('.mayami-epk-canvas-empty'),
            canvasWrapper: root.querySelector('.mayami-epk-canvas-wrapper'),
            canvasImage: root.querySelector('.mayami-epk-canvas-image'),
            canvasOverlay: root.querySelector('.mayami-epk-canvas-overlay'),
            zonesList: root.querySelector('.mayami-epk-zones-list'),
            resetZones: root.querySelector('.mayami-epk-reset-zones'),
            livePreview: root.querySelector('.mayami-epk-live-preview'),
            previewLink: root.querySelector('.mayami-epk-preview-link'),
            publishButton: root.querySelector('.mayami-epk-publish-button'),
            unpublishButton: root.querySelector('.mayami-epk-unpublish-button')
        };

        function syncHiddenValue(markUpdated) {
            if (markUpdated) {
                state.updatedAt = new Date().toISOString();
            }
            draftInput.value = JSON.stringify(state);
            if (elements.draftStatus) {
                elements.draftStatus.textContent = formatTimestamp(state.updatedAt);
            }
        }

        function hasUnsavedChanges() {
            return draftInput.value !== initialDraftValue || !!validationInput.checked !== initialValidationState;
        }

        function setFieldValues() {
            elements.kicker.value = state.kicker || '';
            elements.title.value = state.title || '';
            elements.description.value = state.description || '';
            elements.imageAlt.value = state.imageAlt || '';
        }

        function setActiveZone(zoneId) {
            activeZoneId = zoneId;
            renderZones();
            renderCanvas();
        }

        function renderCanvas() {
            elements.canvasOverlay.innerHTML = '';

            if (!state.imageUrl) {
                elements.canvasEmpty.hidden = false;
                elements.canvasWrapper.hidden = true;
                return;
            }

            elements.canvasEmpty.hidden = true;
            elements.canvasWrapper.hidden = false;
            if (elements.canvasImage.getAttribute('src') !== state.imageUrl) {
                elements.canvasImage.setAttribute('src', state.imageUrl);
            }
            elements.canvasImage.setAttribute('alt', state.imageAlt || 'Visuel EPK');

            state.zones.forEach((zone) => {
                const box = document.createElement('button');
                box.type = 'button';
                box.className = 'mayami-epk-zone-box' + (activeZoneId === zone.id ? ' is-active' : '');
                box.style.left = zone.x + '%';
                box.style.top = zone.y + '%';
                box.style.width = zone.width + '%';
                box.style.height = zone.height + '%';
                box.addEventListener('click', function (event) {
                    event.preventDefault();
                    setActiveZone(zone.id);
                });
                elements.canvasOverlay.appendChild(box);
            });
        }

        function renderZones() {
            if (!state.zones.length) {
                elements.zonesList.innerHTML = '<p style="margin:0;color:#64748b;">Aucune zone pour le moment.</p>';
                return;
            }

            elements.zonesList.innerHTML = state.zones.map((zone, index) => {
                const href = getHref(zone);
                return '' +
                    '<div class="mayami-epk-zone-item' + (activeZoneId === zone.id ? ' is-active' : '') + '" data-zone-id="' + escapeHtml(zone.id) + '">' +
                        '<div class="mayami-epk-zone-header">' +
                            '<span class="mayami-epk-zone-title">Zone ' + (index + 1) + '</span>' +
                            '<button type="button" class="button-link-delete" data-zone-action="delete">Supprimer</button>' +
                        '</div>' +
                        '<div class="mayami-epk-zone-coords">x:' + zone.x.toFixed(2) + '% · y:' + zone.y.toFixed(2) + '% · l:' + zone.width.toFixed(2) + '% · h:' + zone.height.toFixed(2) + '%</div>' +
                        '<div class="mayami-epk-zone-fields">' +
                            '<input type="text" class="regular-text mayami-epk-zone-label" data-zone-field="label" value="' + escapeHtml(zone.label) + '" placeholder="Libellé accessible de la zone">' +
                            '<select data-zone-field="hrefType">' +
                                '<option value="url"' + (zone.hrefType === 'url' ? ' selected' : '') + '>Lien</option>' +
                                '<option value="anchor"' + (zone.hrefType === 'anchor' ? ' selected' : '') + '>Ancre</option>' +
                            '</select>' +
                            '<input type="text" class="regular-text" data-zone-field="hrefValue" value="' + escapeHtml(zone.hrefValue) + '" placeholder="' + escapeHtml(zone.hrefType === 'anchor' ? '#section' : 'https://exemple.com') + '">' +
                        '</div>' +
                        (href ? '<p style="margin:10px 0 0;font-size:12px;color:#334155;">Cible: ' + escapeHtml(href) + '</p>' : '') +
                    '</div>';
            }).join('');
        }

        function renderPreview() {
            if (!state.imageUrl) {
                elements.livePreview.innerHTML = '<p style="margin:0;color:#64748b;">La prévisualisation rapide apparaîtra ici dès qu’un visuel sera sélectionné.</p>';
                return;
            }

            const previewZones = state.zones.map((zone, index) => {
                const label = escapeHtml(zone.label || ('Zone ' + (index + 1)));
                return '<span class="mayami-epk-preview-hotspot" style="left:' + zone.x + '%;top:' + zone.y + '%;width:' + zone.width + '%;height:' + zone.height + '%;"><span>' + label + '</span></span>';
            }).join('');

            elements.livePreview.innerHTML = '' +
                '<div class="mayami-epk-preview-copy">' +
                    '<p><strong>' + escapeHtml(state.kicker || 'EPK') + '</strong></p>' +
                    '<p style="font-size:24px;font-weight:700;line-height:1.1;">' + escapeHtml(state.title || 'Electronic Press Kit') + '</p>' +
                    (state.description ? '<p>' + escapeHtml(state.description) + '</p>' : '') +
                '</div>' +
                '<div class="mayami-epk-preview-surface">' +
                    '<img src="' + escapeHtml(state.imageUrl) + '" alt="' + escapeHtml(state.imageAlt || 'Visuel EPK') + '">' +
                    previewZones +
                '</div>';
        }

        function refreshWorkflowState() {
            const canPublish = !!state.imageUrl && !!validationInput.checked;
            elements.publishButton.disabled = !canPublish;
            elements.unpublishButton.disabled = !(publishedInput.value || '').trim();
            if (elements.publishedStatus) {
                const published = normalizePayload(parsePayload(publishedInput.value));
                elements.publishedStatus.textContent = formatTimestamp(published.publishedAt);
            }
        }

        function redrawAll() {
            setFieldValues();
            renderCanvas();
            renderZones();
            renderPreview();
            refreshWorkflowState();
        }

        function updateState(mutator) {
            mutator(state);
            state = normalizePayload(state);
            syncHiddenValue(true);
            redrawAll();
        }

        function buildActionRequest(action, nonce) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = root.dataset.actionEndpoint;

            const actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = 'action';
            actionField.value = action;
            form.appendChild(actionField);

            const nonceField = document.createElement('input');
            nonceField.type = 'hidden';
            nonceField.name = '_wpnonce';
            nonceField.value = nonce;
            form.appendChild(nonceField);

            document.body.appendChild(form);
            form.submit();
        }

        elements.kicker.addEventListener('input', function () {
            updateState(function (draft) {
                draft.kicker = elements.kicker.value;
            });
        });

        elements.title.addEventListener('input', function () {
            updateState(function (draft) {
                draft.title = elements.title.value;
            });
        });

        elements.description.addEventListener('input', function () {
            updateState(function (draft) {
                draft.description = elements.description.value;
            });
        });

        elements.imageAlt.addEventListener('input', function () {
            updateState(function (draft) {
                draft.imageAlt = elements.imageAlt.value;
            });
        });

        elements.selectImage.addEventListener('click', function () {
            if (!window.wp || !wp.media) {
                return;
            }

            if (!mediaFrame) {
                mediaFrame = wp.media({
                    title: 'Choisir le visuel EPK',
                    library: { type: 'image' },
                    button: { text: 'Utiliser cette image' },
                    multiple: false
                });

                mediaFrame.on('select', function () {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    updateState(function (draft) {
                        draft.imageUrl = attachment.url || '';
                        draft.imageAlt = draft.imageAlt || attachment.alt || attachment.title || '';
                        draft.zones = [];
                    });
                });
            }

            mediaFrame.open();
        });

        elements.clearImage.addEventListener('click', function () {
            activeZoneId = null;
            updateState(function (draft) {
                draft.imageUrl = '';
                draft.imageAlt = '';
                draft.zones = [];
            });
        });

        elements.resetZones.addEventListener('click', function () {
            if (!state.zones.length) {
                return;
            }
            if (!window.confirm('Supprimer toutes les zones du brouillon EPK ?')) {
                return;
            }
            updateState(function (draft) {
                draft.zones = [];
            });
            activeZoneId = null;
        });

        elements.zonesList.addEventListener('click', function (event) {
            const item = event.target.closest('[data-zone-id]');
            if (!item) {
                return;
            }

            const zoneId = item.getAttribute('data-zone-id');
            if (event.target.matches('[data-zone-action="delete"]')) {
                event.preventDefault();
                updateState(function (draft) {
                    draft.zones = draft.zones.filter(function (zone) {
                        return zone.id !== zoneId;
                    });
                });
                if (activeZoneId === zoneId) {
                    activeZoneId = null;
                }
                return;
            }

            setActiveZone(zoneId);
        });

        elements.zonesList.addEventListener('input', function (event) {
            const item = event.target.closest('[data-zone-id]');
            if (!item) {
                return;
            }

            const zoneId = item.getAttribute('data-zone-id');
            const field = event.target.getAttribute('data-zone-field');
            if (!field) {
                return;
            }

            updateState(function (draft) {
                const zone = draft.zones.find(function (entry) {
                    return entry.id === zoneId;
                });
                if (!zone) {
                    return;
                }
                zone[field] = event.target.value;
            });
        });

        validationInput.addEventListener('change', refreshWorkflowState);

        elements.previewLink.addEventListener('click', function (event) {
            if (hasUnsavedChanges() && !window.confirm('Le brouillon a changé dans cette page mais n’est pas encore enregistré. Ouvrir malgré tout la dernière version sauvegardée ?')) {
                event.preventDefault();
            }
        });

        elements.publishButton.addEventListener('click', function () {
            if (hasUnsavedChanges()) {
                window.alert('Enregistrez d’abord la page Mayami Landing pour publier le dernier brouillon EPK.');
                return;
            }

            if (!validationInput.checked) {
                window.alert('Cochez d’abord la validation finale EPK puis enregistrez la page.');
                return;
            }

            buildActionRequest('mayami_publish_epk_draft', root.dataset.publishNonce);
        });

        elements.unpublishButton.addEventListener('click', function () {
            if (!window.confirm('Retirer l’EPK du front public ?')) {
                return;
            }

            buildActionRequest('mayami_unpublish_epk', root.dataset.unpublishNonce);
        });

        elements.canvasImage.addEventListener('mousedown', function (event) {
            if (!state.imageUrl) {
                return;
            }

            const rect = elements.canvasImage.getBoundingClientRect();
            startX = clamp(event.clientX - rect.left, 0, rect.width);
            startY = clamp(event.clientY - rect.top, 0, rect.height);
            isDrawing = true;

            currentBox = document.createElement('span');
            currentBox.className = 'mayami-epk-zone-box';
            currentBox.style.left = startX + 'px';
            currentBox.style.top = startY + 'px';
            currentBox.style.width = '0px';
            currentBox.style.height = '0px';
            elements.canvasOverlay.appendChild(currentBox);
        });

        window.addEventListener('mousemove', function (event) {
            if (!isDrawing || !currentBox) {
                return;
            }

            const rect = elements.canvasImage.getBoundingClientRect();
            const currentX = clamp(event.clientX - rect.left, 0, rect.width);
            const currentY = clamp(event.clientY - rect.top, 0, rect.height);
            const left = Math.min(startX, currentX);
            const top = Math.min(startY, currentY);
            const width = Math.abs(currentX - startX);
            const height = Math.abs(currentY - startY);

            currentBox.style.left = left + 'px';
            currentBox.style.top = top + 'px';
            currentBox.style.width = width + 'px';
            currentBox.style.height = height + 'px';
        });

        window.addEventListener('mouseup', function (event) {
            if (!isDrawing || !currentBox) {
                return;
            }

            const rect = elements.canvasImage.getBoundingClientRect();
            const currentX = clamp(event.clientX - rect.left, 0, rect.width);
            const currentY = clamp(event.clientY - rect.top, 0, rect.height);
            const left = Math.min(startX, currentX);
            const top = Math.min(startY, currentY);
            const width = Math.abs(currentX - startX);
            const height = Math.abs(currentY - startY);

            currentBox.remove();
            currentBox = null;
            isDrawing = false;

            if (width < 12 || height < 12) {
                return;
            }

            const newZone = {
                id: 'zone_' + Date.now(),
                label: '',
                hrefType: 'url',
                hrefValue: '',
                x: Number(((left / rect.width) * 100).toFixed(4)),
                y: Number(((top / rect.height) * 100).toFixed(4)),
                width: Number(((width / rect.width) * 100).toFixed(4)),
                height: Number(((height / rect.height) * 100).toFixed(4))
            };

            updateState(function (draft) {
                draft.zones.push(newZone);
            });
            setActiveZone(newZone.id);
        });

        syncHiddenValue(false);
        redrawAll();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.mayami-epk-builder').forEach(initBuilder);
    });
})();