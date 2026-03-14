<?php
$pageTitle = 'Courses';
$currentPage = 'courses';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Course Management</h1>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openModal('createCourseModal')">
                <i class="fas fa-plus"></i> Add Course
            </button>
        </div>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Program</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><strong><?php echo $course['code']; ?></strong></td>
                        <td><?php echo $course['name']; ?></td>
                        <td><?php echo $course['credits']; ?></td>
                        <td><?php echo $course['program_name'] ?? 'N/A'; ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/sections?course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline">
                                <i class="fas fa-layer-group"></i> Sections
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal-overlay" id="createCourseModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add New Course</h3>
            <button class="modal-close" onclick="closeModal('createCourseModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="create_course">
                <div class="form-group">
                    <label>Course Code</label>
                    <input type="text" name="code" class="form-control" required placeholder="e.g., CS101">
                </div>
                <div class="form-group">
                    <label>Course Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Credits</label>
                    <input type="number" name="credits" class="form-control" required min="1" max="6" value="3">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" onclick="closeModal('createCourseModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Course</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>
