/**
 * script.js - Client-side interactions
 */
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const btnInteract = document.getElementById('btnInteract');

    // 1. Registration Logic
    if (registerForm) {
        const password     = document.getElementById('regPassword');
        const confirm      = document.getElementById('regConfirm');
        const strengthMeter = document.getElementById('strengthMeter');
        const strengthText  = document.getElementById('strengthText');

        // Visual: password strength meter (UI helper only — server validates)
        const updateStrength = () => {
            const val = password.value;
            let strength = 0;

            if (val.length >= 6)            strength++;
            if (/[A-Z]/.test(val))          strength++;
            if (/[0-9]/.test(val))          strength++;
            if (/[^A-Za-z0-9]/.test(val))  strength++;

            const colors = ['#f8f9fa', '#dc3545', '#ffc107', '#0dcaf0', '#198754'];
            const labels = ['Too short', 'Weak', 'Fair', 'Good', 'Strong'];

            strengthMeter.style.backgroundColor = colors[strength];
            strengthMeter.style.width = (strength * 25) + '%';
            strengthText.innerText = labels[strength];
        };

        // Visual: password match indicator (UI helper only — server validates)
        const updateMatch = () => {
            if (confirm.value === '') return;
            const isMatch = password.value === confirm.value;
            confirm.classList.toggle('is-invalid', !isMatch);
            confirm.classList.toggle('is-valid', isMatch);
        };

        password.addEventListener('input', () => { updateStrength(); updateMatch(); });
        confirm.addEventListener('input', updateMatch);

        // jQuery AJAX — registration form submission
        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            const msgDiv  = $('#registerMsg');
            const btnReg  = $('#btnSubmitReg');

            // Reset message and disable button while request is in flight
            msgDiv.hide().removeClass('alert-success alert-danger').text('');
            btnReg.prop('disabled', true).text('Registering...');

            $.ajax({
                url: 'auth.php?action=register',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        msgDiv
                            .addClass('alert alert-success')
                            .text('✅ ' + response.message)
                            .show();
                        // Redirect after a short delay so the user sees the message
                        setTimeout(() => { window.location.href = response.redirect; }, 1500);
                    } else {
                        msgDiv
                            .addClass('alert alert-danger')
                            .text('❌ ' + response.message)
                            .show();
                        btnReg.prop('disabled', false).text('Register Now');
                    }
                },
                error: function () {
                    msgDiv
                        .addClass('alert alert-danger')
                        .text('❌ Something went wrong. Please try again.')
                        .show();
                    btnReg.prop('disabled', false).text('Register Now');
                }
            });
        });
    }

    // 2. Profile Modal — About Me (AJAX: get_profile.php)
    const btnAboutModal = document.getElementById('btnAboutModal');
    if (btnAboutModal) {
        btnAboutModal.addEventListener('click', () => {
            // Step 1: grab the modal element and show it immediately with a spinner
            const modalEl = document.getElementById('aboutModal');
            const modal   = new bootstrap.Modal(modalEl);
            modal.show();

            // Step 2: reset body to loading state on every open
            $('#aboutModalBody').html(
                '<div class="text-center py-3">' +
                '  <div class="spinner-border spinner-border-sm text-primary" role="status"></div>' +
                '  <span class="ms-2 text-muted">Loading profile...</span>' +
                '</div>'
            );

            // Step 3: fetch profile data from the server
            $.ajax({
                url:      'get_profile.php',
                type:     'GET',
                dataType: 'json',

                success: function (response) {
                    if (response.status === 'success') {
                        const d = response.data;  
                      
                        var rows =
                            '<li class="list-group-item d-flex justify-content-between px-0">' +
                            '  <span class="text-muted">Username</span>' +
                            '  <strong>' + (d.username    || '—') + '</strong>' +
                            '</li>' +
                            '<li class="list-group-item d-flex justify-content-between px-0">' +
                            '  <span class="text-muted">Full Name</span>' +
                            '  <strong>' + (d.full_name  || '—') + '</strong>' +
                            '</li>' +
                            '<li class="list-group-item d-flex justify-content-between px-0">' +
                            '  <span class="text-muted">Course</span>' +
                            '  <strong>' + (d.course     || '—') + '</strong>' +
                            '</li>' +
                            '<li class="list-group-item d-flex justify-content-between px-0">' +
                            '  <span class="text-muted">Year Level</span>' +
                            '  <strong>' + (d.year_level || '—') + '</strong>' +
                            '</li>' +
                            '<li class="list-group-item d-flex justify-content-between px-0">' +
                            '  <span class="text-muted">Email</span>' +
                            '  <strong>' + (d.email      || '—') + '</strong>' +
                            '</li>' +
                            '<li class="list-group-item d-flex justify-content-between px-0">' +
                            '  <span class="text-muted">Member Since</span>' +
                            '  <strong>' + (d.created_at  || '—') + '</strong>' +
                            '</li>';

                        $('#aboutModalBody').html(
                            '<ul class="list-group list-group-flush">' + rows + '</ul>'
                        );
                    } else {
                        $('#aboutModalBody').html(
                            '<p class="text-danger text-center py-2">⚠️ ' + response.message + '</p>'
                        );
                    }
                },

                error: function () {
                    $('#aboutModalBody').html(
                        '<p class="text-danger text-center py-2">⚠️ Failed to load profile. Please try again.</p>'
                    );
                }
            }); // end $.ajax
        });
    }

    // 3. Profile Modal — My Skills (AJAX: get_skills.php)
    const btnSkillsModal = document.getElementById('btnSkillsModal');
    if (btnSkillsModal) {
        btnSkillsModal.addEventListener('click', () => {
            // Step 1: grab the modal element and show it immediately with a spinner
            const modalEl = document.getElementById('skillsModal');
            const modal   = new bootstrap.Modal(modalEl);
            modal.show();

            // Step 2: reset body to loading state on every open
            $('#skillsModalBody').html(
                '<div class="text-center py-3">' +
                '  <div class="spinner-border spinner-border-sm text-success" role="status"></div>' +
                '  <span class="ms-2 text-muted">Loading skills...</span>' +
                '</div>'
            );

            // Step 3: fetch skills data from the server
            $.ajax({
                url:      'get_skills.php',
                type:     'GET',
                dataType: 'json',

                success: function (response) {
                    if (response.status === 'success' && response.data.length > 0) {

                        // Group the flat array of skills by their 'category' field.
                        // Each item in response.data should have: { category, name, color }
                        // TODO: if your column names differ, update the keys below.
                        var grouped = {};
                        $.each(response.data, function (_i, skill) {
                            var cat = skill.category || 'General'; // TODO: update key if needed
                            if (!grouped[cat]) grouped[cat] = [];
                            grouped[cat].push(skill);
                        });

                        // Build HTML — one labeled section per category
                        var html = '';
                        $.each(grouped, function (category, items) {
                            html += '<p class="fw-semibold text-muted small text-uppercase mb-2" ' +
                                    '   style="letter-spacing:.07em;">' + category + '</p>';
                            html += '<div class="mb-3">';
                            $.each(items, function (_i, skill) {
                                // Falls back to bg-secondary if color is not provided
                                var color = skill.color || 'bg-secondary'; // TODO: update key if needed
                                html += '<span class="badge ' + color + ' me-1 mb-1 px-3 py-2" ' +
                                        '      style="border-radius:50px;font-size:.8rem;">' +
                                        skill.name +                       // TODO: update key if needed
                                        '</span>';
                            });
                            html += '</div>';
                        });

                        $('#skillsModalBody').html(html);
                    } else if (response.status === 'success') {
                        $('#skillsModalBody').html(
                            '<p class="text-muted text-center py-2">No skills found.</p>'
                        );
                    } else {
                        $('#skillsModalBody').html(
                            '<p class="text-danger text-center py-2">⚠️ ' + response.message + '</p>'
                        );
                    }
                },

                error: function () {
                    $('#skillsModalBody').html(
                        '<p class="text-danger text-center py-2">⚠️ Failed to load skills. Please try again.</p>'
                    );
                }
            }); // end $.ajax
        });
    }

    // 4. Dashboard Interaction
    if (btnInteract) {
        btnInteract.addEventListener('click', () => {
            const container = document.getElementById('mainContainer');
            const toast = document.createElement('div');
            toast.className = 'alert alert-info mt-3 fade-in';
            toast.textContent = "External JS successfully manipulated the DOM!";
            
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        });
    }
});