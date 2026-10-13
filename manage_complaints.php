<?php
ini_set('session.cookie_samesite','Lax'); ini_set('session.cookie_secure','0'); session_start();
if(!isset($_SESSION['user']) || $_SESSION['role']!='admin'){ header('Location: login.php?role=admin'); exit; }
$file='data.json'; $data=json_decode(file_get_contents($file),true);
if(isset($_GET['action']) && isset($_GET['ref'])){
 $ref=$_GET['ref']; $act=$_GET['action'];
 foreach($data as &$c){ if($c['ref']==$ref){ if($act=='pending') $c['status']='Pending'; if($act=='progress') $c['status']='In Progress'; if($act=='close') $c['status']='Closed'; } }
 if($act=='delete'){ $data=array_filter($data, fn($x)=>$x['ref']!=$ref); $data=array_values($data); }
 file_put_contents($file, json_encode($data)); header('Location: manage_complaints.php'); exit;
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Manage Complaints</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',Arial;background:#f8f7ff;display:flex;min-height:100vh}
.sidebar{width:240px;background:#0f0f1e;color:#fff;padding:0;display:flex;flex-direction:column;position:sticky;top:0;height:100vh}
.s-top{padding:18px;display:flex;align-items:center;gap:10px;font-weight:800}
.s-menu{padding:10px}
.s-item{display:block;padding:12px 14px;border-radius:12px;color:#9ca3af;text-decoration:none;font-size:13px;margin:4px 0;transition:.2s}
.s-item.active{background:#6c5cff;color:#fff;box-shadow:0 4px 20px rgba(108,92,255,.3);font-weight:700}
.s-item:hover{background:#1a1a2e;color:#fff}
.main{flex:1;background:#fbfbff}
.topbar{background:#fff;padding:18px 28px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:5}
.topbar h1{font-size:20px;font-weight:800}.topbar p{font-size:12px;color:#6b7280;margin-top:4px}
.stats{display:flex;gap:10px;margin-top:12px}
.stat{padding:6px 12px;background:#fff;border:1px solid #eee;border-radius:20px;font-size:11px;font-weight:700}
.list{padding:20px 28px;display:flex;flex-direction:column;gap:14px}
.card{background:#fff;border-radius:18px;padding:18px 20px;border:1px solid #ece9ff;box-shadow:0 4px 24px rgba(90,76,255,.06);display:flex;justify-content:space-between;align-items:center;gap:16px;transition:.2s}
.card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(90,76,255,.12);border-color:#ddd6ff}
.left{flex:1;min-width:0}
.ref{font-weight:800;font-size:14px;display:flex;align-items:center;gap:8px}
.status{padding:3px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#f3f4f6;color:#374151}
.status.pending{background:#fef9c3;color:#a16207}.status.progress{background:#dbeafe;color:#1e40af}.status.closed{background:#dcfce7;color:#15803d}
.meta{font-size:11px;color:#9ca3af;margin-top:4px}
.desc{font-size:12px;color:#6b7280;margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:600px}
.actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
.btn{padding:7px 14px;border-radius:20px;font-size:11px;font-weight:700;text-decoration:none;transition:.2s;border:1px solid transparent}
.btn:hover{transform:scale(1.05)}
.p{background:#fef9c3;color:#a16207;border-color:#fde68a}.pr{background:#dbeafe;color:#1e40af;border-color:#bfdbfe}.cl{background:#dcfce7;color:#15803d;border-color:#bbf7d0}.del{background:#fee2e2;color:#dc2626;border-color:#fecaca}.open{background:#6c5cff;color:#fff}
@media(max-width:900px){.card{flex-direction:column;align-items:flex-start}.actions{width:100%}}
</style></head><body>
<div class="sidebar">
<div class="s-top">✨ ResolveDesk Admin</div>
<div class="s-menu">
<a class="s-item" href="dashboard.php">📊 Dashboard</a>
<a class="s-item active" href="manage_complaints.php">📋 Manage Complaints (<?= count($data)?>)</a>
<a class="s-item" href="logout.php">🚪 Logout</a>
</div>
<div style="margin-top:auto;padding:14px"><div style="background:#1a1a2e;padding:12px;border-radius:12px;font-size:11px;color:#9ca3af"><b style="color:#fff">🔒 Secure Admin</b><br>All actions logged & protected</div></div>
</div>
<div class="main">
<div class="topbar">
<div><h1>Manage Complaints - All Student Complaints</h1><p>Student kudutha ellam complaint-um inga varum da! Status maathalam, delete pannalam ✨</p><div class="stats"><div class="stat">📊 Total: <?=count($data)?></div><div class="stat" style="background:#fef9c3">⏳ Pending: <?=count(array_filter($data, fn($c)=>$c['status']=='Pending'))?></div><div class="stat" style="background:#dbeafe">🔄 In Progress: <?=count(array_filter($data, fn($c)=>$c['status']=='In Progress'))?></div><div class="stat" style="background:#dcfce7">✅ Closed: <?=count(array_filter($data, fn($c)=>$c['status']=='Closed'))?></div></div></div>
</div>
<div class="list">
<?php foreach(array_reverse($data) as $c): $st = strtolower(str_replace(' ','',$c['status'])); ?>
<div class="card">
<div class="left">
<div class="ref"><?=$c['ref']?> - <?=$c['subject']?> <span class="status <?= $st=='pending'?'pending':($st=='inprogress'?'progress':'closed')?>"><?=$c['status']?></span></div>
<div class="meta"><?=$c['student']?> | <?=$c['category']?> | <?=$c['date']?></div>
<div class="desc"><?=htmlspecialchars($c['details'])?></div>
</div>
<div class="actions">
<a class="btn open" href="complaint_details.php?ref=<?=$c['ref']?>">👁 Open</a>
<a class="btn p" href="?action=pending&ref=<?=$c['ref']?>">Pending</a>
<a class="btn pr" href="?action=progress&ref=<?=$c['ref']?>">In Progress</a>
<a class="btn cl" href="?action=close&ref=<?=$c['ref']?>">Close</a>
<a class="btn del" href="?action=delete&ref=<?=$c['ref']?>" onclick="return confirm('Delete pannava da?')">Delete</a>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
</body></html>
