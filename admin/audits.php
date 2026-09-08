<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include '../api/config.php';

$stmt = $conn->query("SELECT * FROM audit ORDER BY id DESC");
$rows = $stmt ? $stmt->fetch_all(MYSQLI_ASSOC) : [];

if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="audits.csv"');
    $csv = fopen('php://output', 'w');
    fputcsv($csv, array_keys($rows[0] ?? ['id', 'first_name', 'last_name', 'business_name', 'email', 'website_url', 'message']), ',', '"', '');
    foreach ($rows as $row) {
        fputcsv($csv, array_values($row), ',', '"', '');
    }
    fclose($csv);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Audit Requests - Prismabit</title>
    <script src="../assets/js/cdn.tailwindcss.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: "Inter", sans-serif; }
        body { background: #0b0b0b; }
        .sidebar { background: #111111; border-right: 1px solid rgba(255,255,255,0.08); }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(0,123,255,0.1); color: #007BFF; }
        .topbar { background: rgba(11,11,11,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.08); }
        .data-card { background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); }
        .table-wrap { overflow-x: auto; }
        .table-row:hover { background: rgba(255,255,255,0.03); }
    </style>
</head>
<body class="text-white">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar w-64 flex-shrink-0 flex flex-col fixed h-full">
            <div class="p-6 border-b border-white/5">
                <img src="../assets/images/prisma-logo.png" alt="Prismabit" class="h-8">
                <p class="text-xs text-gray-500 mt-1">Admin Panel</p>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="contacts.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contacts
                </a>
                <a href="careers.php" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Careers
                </a>
                <a href="audits.php" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Audits
                </a>
            </nav>
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-8 h-8 rounded-full bg-[#007BFF] flex items-center justify-center text-sm font-bold">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['username']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['role']) ?></p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <header class="topbar sticky top-0 z-30 px-8 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Digital Audit Requests</h2>
                <a href="logout.php" class="text-sm text-gray-400 hover:text-red-400 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </header>

            <div class="p-8">
                <div class="data-card rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-semibold">All Audit Requests (<?= count($rows) ?>)</h3>
                        <a href="?download=csv" class="text-sm text-[#007BFF] hover:underline">Export CSV</a>
                    </div>

                    <?php if (empty($rows)): ?>
                        <p class="text-gray-500 text-sm py-10 text-center">No audit requests yet.</p>
                    <?php else: ?>
                    <div class="table-wrap">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-400 border-b border-white/10">
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Business</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Website</th>
                                    <th class="px-4 py-3">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                <tr class="table-row border-b border-white/5">
                                    <td class="px-4 py-3 text-gray-500"><?= $row['id'] ?></td>
                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                                    </td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['business_name']) ?></td>
                                    <td class="px-4 py-3">
                                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-[#007BFF] hover:underline"><?= htmlspecialchars($row['email']) ?></a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="<?= htmlspecialchars($row['website_url']) ?>" target="_blank" class="text-[#007BFF] hover:underline" rel="noopener"><?= htmlspecialchars($row['website_url']) ?></a>
                                    </td>
                                    <td class="px-4 py-3 max-w-xs truncate"><?= htmlspecialchars($row['message']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
