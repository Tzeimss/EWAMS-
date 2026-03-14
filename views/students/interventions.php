<?php
$pageTitle = 'Interventions';
$currentPage = 'interventions';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Intervention Management</h1>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openModal('createInterventionModal')">
                <i class="fas fa-plus"></i> New Intervention
            </button>
        </div>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h2><i class="fas fa-chart-bar"></i> Intervention Overview</h2>
        </div>
        <div class="card-body">
            <canvas id="interventionChart" height="100"></canvas>
        </div>
    </div>

    <script>
    var intCtx = document.getElementById('interventionChart').getContext('2d');
    new Chart(intCtx, {
        type: 'bar',
        data: {
            labels: ['Planned', 'In Progress', 'Completed', 'Cancelled'],
            datasets: [{
                label: 'Interventions',
                data: [
                    <?php echo $chartData['planned']; ?>,
                    <?php echo $chartData['in_progress']; ?>,
                    <?php echo $chartData['completed']; ?>,
                    <?php echo $chartData['cancelled']; ?>
                ],
                backgroundColor: ['#3182ce', '#805ad5', '#38a169', '#718096']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $interventionStats['planned'] ?? 0; ?></h3>
                <p>Planned</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $interventionStats['in_progress'] ?? 0; ?></h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $interventionStats['completed'] ?? 0; ?></h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>All Interventions</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Follow-up</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($interventions)): ?>
                    <?php foreach ($interventions as $intervention): ?>
                    <tr>
                        <td>
                            <?php echo $intervention['first_name'] . ' ' . $intervention['last_name']; ?>
                            <?php if (isset($intervention['risk_level'])): ?>
                            <br><span class="risk-badge <?php echo $intervention['risk_level']; ?>"><?php echo ucfirst($intervention['risk_level']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $intervention['type']; ?></td>
                        <td><?php echo substr($intervention['description'] ?? '', 0, 50); ?>...</td>
                        <td>
                            <span class="status-badge <?php echo $intervention['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $intervention['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo !empty($intervention['follow_up_date']) ? date('M d, Y', strtotime($intervention['follow_up_date'])) : 'N/A'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline" onclick="viewIntervention(<?php echo $intervention['id']; ?>)">
                                View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No interventions found. Create one to get started.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Intervention Modal -->
<div class="modal-overlay" id="createInterventionModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">New Intervention</h3>
            <button class="modal-close" onclick="closeModal('createInterventionModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="create_intervention">
                <div class="form-group">
                    <label>Student</label>
                    <select name="student_id" class="form-control" required>
                        <?php if (!empty($students)): ?>
                        <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></option>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <option value="">No students available</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Intervention Type</label>
                    <select name="type" class="form-control" required>
                        <option value="Academic Counseling">Academic Counseling</option>
                        <option value="Tutoring">Tutoring</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Email Communication">Email Communication</option>
                        <option value="Referral">Referral</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" onclick="closeModal('createInterventionModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function viewIntervention(id) { alert('View intervention ' + id); }
</script>
