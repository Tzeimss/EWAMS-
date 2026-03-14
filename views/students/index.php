<?php
$pageTitle = 'Students';
$currentPage = 'students';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Student Management</h1>
        <?php if (getUserRole() === 'administrator'): ?>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openModal('createStudentModal')">
                <i class="fas fa-plus"></i> Add Student
            </button>
            <button class="btn btn-secondary" onclick="openModal('importModal')">
                <i class="fas fa-upload"></i> Import CSV
            </button>
        </div>
        <?php endif; ?>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo $success; ?>
    </div>
    <?php endif; ?>

    <div class="search-bar">
        <input type="text" class="search-input" placeholder="Search students..." id="studentSearch">
        <select class="filter-select" id="riskFilter">
            <option value="">All Risk Levels</option>
            <option value="low">Low Risk</option>
            <option value="moderate">Moderate Risk</option>
            <option value="high">High Risk</option>
        </select>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="data-table" id="studentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Program</th>
                        <th>Risk Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['student_id'] ?? $student['id']; ?></td>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar-sm">
                                    <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="student-name"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo $student['program_name'] ?? 'N/A'; ?></td>
                        <td>
                            <?php 
                            $riskModel = new RiskAssessment();
                            $risk = $riskModel->getRiskAssessment($student['id'], $currentTerm['id'] ?? null);
                            if ($risk): 
                            ?>
                            <span class="risk-badge <?php echo $risk['risk_level']; ?>">
                                <?php echo ucfirst($risk['risk_level']); ?>
                            </span>
                            <?php else: ?>
                            <span class="risk-badge low">Not Calculated</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $student['is_active'] ? 'active' : 'inactive'; ?>">
                                <?php echo $student['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?php echo BASE_URL; ?>/students?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <?php if (getUserRole() === 'advisor'): ?>
                            <a href="<?php echo BASE_URL; ?>/interventions?student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-hand-holding-heart"></i> Intervene
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Student Modal -->
<div class="modal-overlay" id="createStudentModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add New Student</h3>
            <button class="modal-close" onclick="closeModal('createStudentModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="create_student">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" onclick="closeModal('createStudentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div class="modal-overlay" id="importModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Import Students from CSV</h3>
            <button class="modal-close" onclick="closeModal('importModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="action" value="import_students">
                <div class="file-upload">
                    <input type="file" name="csv_file" accept=".csv" required>
                    <div class="file-upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="file-upload-text">
                        <strong>Click to upload</strong> or drag and drop<br>
                        CSV files only
                    </div>
                </div>
                <p class="text-muted mt-3">CSV format: username, email, first_name, last_name, phone, password</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" onclick="closeModal('importModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.getElementById('studentSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#studentsTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
