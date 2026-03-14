<?php
$pageTitle = 'My Grades';
$currentPage = 'my-grades';
?>

<div class="page-content">
    <h1>My Grades</h1>

    <?php if (empty($dashboardData['enrollments'])): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <p>You are not enrolled in any courses</p>
                <a href="<?php echo BASE_URL; ?>/my-courses" class="btn btn-primary">View Courses</a>
            </div>
        </div>
    </div>
    <?php return; endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Current Semester Grades</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Instructor</th>
                        <th>Grade</th>
                        <th>Percentage</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dashboardData['enrollments'] as $section): 
                        $grade = $dashboardData['grades'][$section['id']] ?? ['total' => 0, 'letter' => 'N/A'];
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo $section['course_code']; ?></strong><br>
                            <?php echo $section['course_name']; ?>
                        </td>
                        <td><?php echo $section['section_number']; ?></td>
                        <td><?php echo $section['instructor_first'] . ' ' . $section['instructor_last']; ?></td>
                        <td>
                            <span class="grade-large <?php echo strtolower($grade['letter']); ?>"><?php echo $grade['letter']; ?></span>
                        </td>
                        <td><?php echo $grade['total']; ?>%</td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/my-grades?section=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline">
                                View Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Grade Summary</h2>
        </div>
        <div class="card-body">
            <?php 
            $total = 0;
            $count = 0;
            foreach ($dashboardData['grades'] as $g) {
                if ($g['total'] > 0) {
                    $total += $g['total'];
                    $count++;
                }
            }
            $avg = $count > 0 ? round($total / $count, 1) : 0;
            ?>
            <div class="grade-summary">
                <div class="summary-item">
                    <span class="summary-value"><?php echo $avg; ?>%</span>
                    <span class="summary-label">Overall Average</span>
                </div>
                <div class="summary-item">
                    <span class="summary-value"><?php echo $count; ?>/<?php echo count($dashboardData['enrollments']); ?></span>
                    <span class="summary-label">Courses Graded</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.grade-large { font-size: 24px; font-weight: bold; }
.grade-large.A, .grade-large.a { color: var(--success); }
.grade-large.B, .grade-large.b { color: #4299e1; }
.grade-large.C, .grade-large.c { color: var(--warning); }
.grade-large.D, .grade-large.d { color: #ed8936; }
.grade-large.F, .grade-large.f { color: var(--danger); }
.grade-summary { display: flex; justify-content: center; gap: 48px; }
.summary-item { text-align: center; }
.summary-value { display: block; font-size: 36px; font-weight: bold; color: var(--primary); }
.summary-label { color: var(--text-secondary); }
</style>
