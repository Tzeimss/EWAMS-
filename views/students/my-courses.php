<?php
$pageTitle = 'My Courses';
$currentPage = 'my-courses';
?>

<div class="page-content">
    <h1>My Courses</h1>

    <?php if (empty($dashboardData['enrollments'])): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <p>You are not enrolled in any courses</p>
                <p>Contact your advisor or administrator to enroll in courses.</p>
            </div>
        </div>
    </div>
    <?php return; endif; ?>

    <div class="courses-grid">
        <?php foreach ($dashboardData['enrollments'] as $section): 
            $grade = $dashboardData['grades'][$section['id']] ?? ['total' => 0, 'letter' => 'N/A'];
        ?>
        <div class="course-card">
            <div class="course-card-header">
                <span class="course-code"><?php echo $section['course_code']; ?></span>
                <span class="course-credits"><?php echo $section['credits']; ?> Credits</span>
            </div>
            <div class="course-card-body">
                <h3><?php echo $section['course_name']; ?></h3>
                <p class="course-meta">
                    Section <?php echo $section['section_number']; ?>
                </p>
                <p class="course-meta">
                    <?php echo $section['instructor_first'] . ' ' . $section['instructor_last']; ?>
                </p>
                <p class="course-meta">
                    <?php echo $section['term_name']; ?>
                </p>
                <?php if ($section['schedule']): ?>
                <p class="course-meta">
                    <?php echo $section['schedule']; ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="course-card-footer">
                <div class="course-grade">
                    <span class="grade-label">Current Grade</span>
                    <span class="grade-value <?php echo strtolower($grade['letter']); ?>"><?php echo $grade['letter']; ?></span>
                </div>
                <a href="<?php echo BASE_URL; ?>/my-grades?section=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline">
                    View Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
.course-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.course-card-header { background: var(--primary); color: white; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
.course-code { font-weight: bold; font-size: 18px; }
.course-credits { font-size: 14px; opacity: 0.9; }
.course-card-body { padding: 20px; }
.course-card-body h3 { margin-bottom: 12px; font-size: 18px; }
.course-meta { color: var(--text-secondary); font-size: 14px; margin-bottom: 8px; }
.course-card-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.course-grade { display: flex; flex-direction: column; }
.grade-label { font-size: 12px; color: var(--text-secondary); }
.grade-value { font-size: 24px; font-weight: bold; }
.grade-value.A, .grade-value.a { color: var(--success); }
.grade-value.B, .grade-value.b { color: #4299e1; }
.grade-value.C, .grade-value.c { color: var(--warning); }
.grade-value.D, .grade-value.d { color: #ed8936; }
.grade-value.F, .grade-value.f { color: var(--danger); }
</style>
