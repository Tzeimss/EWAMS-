<?php
$pageTitle = 'Sections';
$currentPage = 'sections';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Section Management</h1>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openModal('createSectionModal')">
                <i class="fas fa-plus"></i> Add Section
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
                        <th>Course</th>
                        <th>Section</th>
                        <th>Term</th>
                        <th>Instructor</th>
                        <th>Enrolled</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                    <tr>
                        <td>
                            <strong><?php echo $section['course_code']; ?></strong><br>
                            <small><?php echo $section['course_name']; ?></small>
                        </td>
                        <td><?php echo $section['section_number']; ?></td>
                        <td><?php echo $section['term_name']; ?></td>
                        <td><?php echo $section['instructor_first'] ? $section['instructor_first'] . ' ' . $section['instructor_last'] : 'Not Assigned'; ?></td>
                        <td><?php echo $section['enrolled_count']; ?></td>
                        <td><?php echo $section['capacity']; ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/grades?section_id=<?php echo $section['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-clipboard-list"></i> Grades
                            </a>
                            <a href="<?php echo BASE_URL; ?>/sections/view?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline">
                                <i class="fas fa-users"></i> Students
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Section Modal -->
<div class="modal-overlay" id="createSectionModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add New Section</h3>
            <button class="modal-close" onclick="closeModal('createSectionModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="create_section">
                <div class="form-group">
                    <label>Course</label>
                    <select name="course_id" class="form-control" required>
                        <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['code']; ?> - <?php echo $course['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Term</label>
                    <select name="term_id" class="form-control" required>
                        <?php foreach ($terms as $term): ?>
                        <option value="<?php echo $term['id']; ?>" <?php echo $term['is_current'] ? 'selected' : ''; ?>><?php echo $term['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Section Number</label>
                    <input type="text" name="section_number" class="form-control" required placeholder="e.g., A">
                </div>
                <div class="form-group">
                    <label>Instructor</label>
                    <select name="instructor_id" class="form-control">
                        <option value="">Select Instructor</option>
                        <?php foreach ($faculty as $f): ?>
                        <option value="<?php echo $f['id']; ?>"><?php echo $f['first_name'] . ' ' . $f['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" name="capacity" class="form-control" value="30" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" onclick="closeModal('createSectionModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Section</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>
