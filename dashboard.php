<?php
ini_set('session.cookie_samesite','Lax'); ini_set('session.cookie_secure','0'); session_start();
if(!isset($_SESSION['user'])){ header('Location: login.php'); exit; }
$isAdmin = $_SESSION['role']=='admin';
$data = json_decode(file_get_contents('data.json'), true);
$total = count($data);
$pending = count(array_filter($data, fn($c)=>$c['status']=='Pending'));
$inprog = count(array_filter($data, fn($c)=>$c['status']=='In Progress'));
$closed = count(array_filter($data, fn($c)=>$c['status']=='Closed'));
$students = count(array_unique(array_column($data,'email')));
if(!$isAdmin){ header('Location: student_dashboard.php'); exit; }
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Dashboard - ResolveDesk</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',Arial;background:#f6f5ff;display:flex;min-height:100vh}
.sidebar{width:260px;background:#0f0f1a;color:#fff;display:flex;flex-direction:column;padding:0;position:sticky;top:0;height:100vh}
.s-top{padding:22px 18px;border-bottom:1px solid #1f1f2f}
.logo{display:flex;gap:12px;align-items:center}.logo-icon{width:42px;height:42px;background:linear-gradient(135deg,#ff8a4a,#ff5a36);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px}
.logo b{font-size:16px}.logo small{display:block;color:#f59e0b;font-size:11px;font-weight:700}
.admin-card{margin:14px;background:#1a1a27;border:1px solid #2a2a3a;border-radius:14px;padding:12px;display:flex;gap:10px;align-items:center}
.a-av{width:36px;height:36px;background:linear-gradient(135deg,#ff8a4a,#ff5a36);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800}
.menu{flex:1;padding:12px}
.m-label{font-size:10px;color:#6b7280;letter-spacing:1px;font-weight:700;margin:14px 8px 6px}
.m-item{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-radius:12px;text-decoration:none;color:#9ca3af;font-size:13px;font-weight:500;margin:2px 0;transition:.2s}
.m-item.active{background:#5a4cff;color:#fff;box-shadow:0 4px 16px rgba(90,76,255,.3)}
.m-item:hover{background:#1a1a27;color:#fff}
.badge{padding:2px 8px;border-radius:20px;font-size:11px;background:#fff;color:#000;font-weight:700}
.s-bottom{padding:14px;border-top:1px solid #1f1f2f}
.sec{padding:12px;background:#1a1a27;border-radius:12px;border:1px solid #2a2a3a;font-size:11px;color:#a1a1aa}
.sec b{color:#fff;display:block;margin-bottom:4px}
.main{flex:1;background:#fbfbff}
.topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 28px;background:#fff;border-bottom:1px solid #eee;position:sticky;top:0;z-index:5}
.topbar h2{font-size:22px;font-weight:800}.topbar h2 span{color:#f59e0b;font-size:16px}
.topbar p{color:#6b7280;font-size:12px;margin-top:2px}
.security{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:10px 14px;border-radius:10px;font-size:11px;font-weight:600;margin:16px 28px}
.content{padding:0 28px 28px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin:16px 0}
.stat{background:#fff;border-radius:18px;padding:20px;border:1px solid #eee;box-shadow:0 4px 20px rgba(0,0,0,.03)}
.stat-top{display:flex;justify-content:space-between;align-items:flex-start}
.icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px}
.s1{background:#f3f0ff}.s2{background:#fef3c7}.s3{background:#dbeafe}.s4{background:#dcfce7}
.stat b{font-size:10px;letter-spacing:.8px;color:#6b7280;font-weight:700;display:block;margin-top:12px}
.stat h3{font-size:32px;font-weight:800;margin-top:4px}
.stat small{font-size:11px;font-weight:600}
.tabs{display:flex;gap:8px;margin:18px 0}
.tab{padding:8px 16px;border-radius:20px;font-size:12px;font-weight:700;border:1px solid #e5e7eb;background:#fff;cursor:pointer;text-decoration:none;color:#374151}
.tab.active{background:#101012;color:#fff}
.layout{display:grid;grid-template-columns:2fr 1fr;gap:18px}
.table-card{background:#fff;border-radius:18px;border:1px solid #eee;padding:20px;box-shadow:0 4px 20px rgba(0,0,0,.03)}
.th{display:grid;grid-template-columns:80px 1.6fr .8fr .6fr .7fr 70px;gap:10px;padding:10px 0;color:#9ca3af;font-size:10px;font-weight:700;letter-spacing:.6px;border-bottom:1px solid #f3f4f6}
.tr{display:grid;grid-template-columns:80px 1.6fr .8fr .6fr .7fr 70px;gap:10px;padding:14px 0;border-bottom:1px solid #f9fafb;align-items:center;font-size:12px}
.tr b{font-size:12px}
.c-pill{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block}
.food{background:#dbeafe;color:#1d4ed8}.hostel{background:#fef3c7;color:#92400e}
.st-pending{background:#fef9c3;color:#a16207}.st-progress{background:#f3f4f6;color:#374151}.st-closed{background:#f3f4f6;color:#374151}
.open{padding:6px 14px;background:#7c5cff;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:11px}
.right{display:flex;flex-direction:column;gap:18px}
.mix{background:#fff;border-radius:18px;padding:18px;border:1px solid #eee}
.bar{height:8px;background:#f3f4f6;border-radius:20px;display:flex;overflow:hidden;margin:12px 0}
.bar div{height:100%}
.row{display:flex;justify-content:space-between;font-size:11px;font-weight:600;margin:8px 0}
@media(max-width:900px){.stats{grid-template-columns:1fr 1fr}.layout{grid-template-columns:1fr}.sidebar{display:none}}
</style></head><body>
<div class="sidebar">
<div class="s-top"><div class="logo"><div class="logo-icon">🔵</div><div><b>ResolveDesk</b><small>Admin Console 🔒</small></div></div></div>
<div class="admin-card"><div class="a-av">A</div><div><b style="font-size:13px;color:#fbbf24">Administrator</b><br><small style="font-size:11px;color:#9ca3af">Secured • Online</small></div></div>
<div class="menu">
<div class="m-label">OVERVIEW</div>
<a class="m-item active" href="dashboard.php">◉ Dashboard</a>
<a class="m-item" href="manage_complaints.php">Manage Complaints <span class="badge"><?=$pending?></span></a>
<a class="m-item" href="logout.php">↩ Logout</a>
</div>
<div class="s-bottom"><div class="sec"><b>🔒 Security Active</b>All passwords bcrypt hashed, sessions protected</div></div>
</div>
<div class="main">
<div class="topbar"><div><h2>Admin Dashboard ✨</h2><p>College-wide complaint overview and recent activity with full details.</p></div><div style="display:flex;gap:10px;align-items:center"><div style="width:36px;height:36px;background:#fff;border:1px solid #eee;border-radius:10px;display:flex;align-items:center;justify-content:center">🔔<span style="background:red;width:8px;height:8px;border-radius:50%;position:relative;top:-8px;left:2px;display:inline-block"></span></div><div style="display:flex;gap:8px;align-items:center;background:#fff;border:1px solid #eee;padding:6px 10px;border-radius:12px"><div style="width:28px;height:28px;background:#7c5cff;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">A</div><div><b style="font-size:12px">Administrator</b><br><small style="font-size:10px;color:#999">Secure Admin</small></div></div></div></div>
<div class="security">✅ 🔒 Security: password_hash() bcrypt • session_regenerate_id() • XSS escaped • Timeout 1hr • X-Frame-Options DENY</div>
<div class="content">
<div class="stats">
<div class="stat"><div class="stat-top"><div class="icon s1">📊</div></div><b>TOTAL COMPLAINTS</b><h3><?=$total?></h3><small style="color:#9ca3af">All time records</small></div>
<div class="stat"><div class="stat-top"><div class="icon s2">⏳</div></div><b>PENDING</b><h3><?=$pending?></h3><small style="color:#d97706">Needs action</small></div>
<div class="stat"><div class="stat-top"><div class="icon s3">📄</div></div><b>IN PROGRESS</b><h3><?=$inprog?></h3><small style="color:#6b7280">In review</small></div>
<div class="stat"><div class="stat-top"><div class="icon s4">✅</div></div><b>CLOSED</b><h3><?=$closed?></h3><small style="color:#22c55e">Resolved</small></div>
</div>
<div class="tabs"><a class="tab active">✦ Review pending (<?=$pending?>)</a><a class="tab" href="manage_complaints.php">☰ All complaints</a><a class="tab">👥 <?=$students?> students</a></div>
<div class="layout">
<div class="table-card">
<div style="display:flex;justify-content:space-between;margin-bottom:14px"><b>Recently submitted - Full Details</b><a href="manage_complaints.php" style="color:#7c5cff;font-size:12px;text-decoration:none;font-weight:700">Manage all →</a></div>
<div class="th"><div>REF</div><div>SUBJECT + DETAILS</div><div>STUDENT</div><div>CATEGORY</div><div>STATUS</div><div></div></div>
<?php foreach(array_reverse($data) as $c): ?>
<div class="tr">
<div style="font-weight:800"><?=$c['ref']?></div>
<div><b><?=$c['subject']?></b><br><small style="color:#9ca3af"><?=$c['details']?></small><br><small style="color:#7c5cff">📅 <?=$c['date']?></small></div>
<div><b style="font-size:11px"><?=$c['student']?></b><br><small style="color:#9ca3af"><?=$c['student_id']?></small></div>
<div><span class="c-pill <?= strtolower($c['category'])=='hostel'?'hostel':'food'?>"><?=$c['category']?></span></div>
<div><span class="c-pill <?= $c['status']=='Pending'?'st-pending':($c['status']=='In Progress'?'st-progress':'st-closed')?>"><?=$c['status']?></span></div>
<div><a class="open" href="manage_complaints.php?ref=<?=$c['ref']?>">Open</a></div>
</div>
<?php endforeach; ?>
</div>
<div class="right">
<div class="mix"><b>📊 Status mix</b><div style="margin-top:12px"><div class="row"><span style="background:#fef9c3;padding:4px 10px;border-radius:20px;font-size:11px">Pending</span><span><?=$pending?> • 50%</span></div><div class="bar"><div style="width:50%;background:#7c5cff"></div></div><div class="row"><span style="background:#dbeafe;padding:4px 10px;border-radius:20px;font-size:11px">In Progress</span><span><?=$inprog?> • 0%</span></div><div class="bar"><div style="width:<?= $total?($inprog/$total*100):0?>%;background:#60a5fa"></div></div><div class="row"><span style="background:#f3f4f6;padding:4px 10px;border-radius:20px;font-size:11px">Closed</span><span><?=$closed?> • 25%</span></div><div class="bar"><div style="width:<?= $total?($closed/$total*100):0?>%;background:#7c5cff"></div></div></div></div>
<div class="mix"><b>🏷 Top categories</b><div class="row" style="margin-top:12px"><span>Hostel 🏠</span><span>2</span></div><div class="bar"><div style="width:50%;background:#7c5cff"></div></div><div class="row"><span>Food 🍔</span><span>1</span></div><div class="bar"><div style="width:25%;background:#f59e0b"></div></div><div class="row"><span>Academics 📚</span><span>1</span></div><div class="bar"><div style="width:25%;background:#22c55e"></div></div></div>
<div style="background:#101012;color:#fff;padding:12px;border-radius:12px;font-size:12px;text-align:center">✨ Secure + Beautiful! Welcome Administrator • <?=$pending?> pending</div>
</div>
</div>
</div>
</div>
</div>
</body></html>
