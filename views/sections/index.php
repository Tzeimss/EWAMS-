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
                        <th>Section</th>
                        <th>Course</th>
                        <th>Term</th>
                        <th>Instructor</th>
                        <th>Capacity</th>
                        <th>Schedule</th>
                        <th>Room</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><strong><?php echo $section['section_number']; ?></strong></td>
                        <td><?php echo $section['course_code']; ?> - <?php echo $section['course_name']; ?></td>
                        <td><?php echo $section['term_name']; ?></td>
                        <td><?php echo $section['instructor_name'] ?? 'Unassigned'; ?></td>
                        <td><?php echo $section['capacity']; ?></td>
                        <td><?php echo $section['schedule'] ?? 'N/A'; ?></td>
                        <td><?php echo $section['room'] ?? 'N/A'; ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/sections/view?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline">
                                <i class="fas fa-eye"></i> View
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
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['code']; ?> - <?php echo $course['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Term</label>
                    <select name="term_id" class="form-control" required>
                        <option value="">Select Term</option>
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
                        <option value="<?php echo $f['id']; ?>"><?php echo $f['first_name']; ?> <?php echo $f['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" name="capacity" class="form-control" value="30" min="1">
                </div>
                <div class="form-group">
                    <label>Schedule</label>
                    <input type="text" name="schedule" class="form-control" placeholder="e.g., MWF 9:00-10:00">
                </div>
                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" class="form-control" placeholder="e.g., Room 101">
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