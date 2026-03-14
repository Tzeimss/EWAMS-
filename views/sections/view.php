<?php
$pageTitle = 'Section Details';
$currentPage = 'sections';
?>

<div class="page-content">
    <div class="breadcrumb">
        <a href="<?php echo BASE_URL; ?>/sections">Sections</a>
        <span class="breadcrumb-separator">/</span>
        <span>View Section</span>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($section)): ?>
    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Section Information</h2>
            </div>
            <div class="card-body">
                <table class="data-table">
                    <tr>
                        <th>Section</th>
                        <td><?php echo $section['section_number']; ?></td>
                    </tr>
                    <tr>
                        <th>Course</th>
                        <td><?php echo $section['course_code']; ?> - <?php echo $section['course_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Term</th>
                        <td><?php echo $section['term_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Instructor</th>
                        <td><?php echo $section['instructor_name'] ?? 'Unassigned'; ?></td>
                    </tr>
                    <tr>
                        <th>Capacity</th>
                        <td><?php echo $section['capacity']; ?></td>
                    </tr>
                    <tr>
                        <th>Schedule</th>
                        <td><?php echo $section['schedule'] ?? 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Room</th>
                        <td><?php echo $section['room'] ?? 'N/A'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Enrolled Students (<?php echo count($enrolledStudents); ?>)</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($enrolledStudents)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolledStudents as $student): ?>
                        <tr>
                            <td><?php echo $student['first_name']; ?> <?php echo $student['last_name']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo ucfirst($student['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No students enrolled</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">Section not found.</div>
    <?php endif; ?>
</div>